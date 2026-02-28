<?php

namespace App\Skills;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Skill: FiscalCodeUtility
 * Provides expert logic for calculating and decoding Italian Fiscal Codes (Codice Fiscale).
 */
class FiscalCodeSkill
{
    private const MONTH_MAP = [
        1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'H',
        7 => 'L', 8 => 'M', 9 => 'P', 10 => 'R', 11 => 'S', 12 => 'T',
    ];

    private const REVERSE_MONTH_MAP = [
        'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'H' => 6,
        'L' => 7, 'M' => 8, 'P' => 9, 'R' => 10, 'S' => 11, 'T' => 12,
    ];

    private const ODD_VALUES = [
        '0' => 1, '1' => 0, '2' => 5, '3' => 7, '4' => 9, '5' => 13, '6' => 15, '7' => 17, '8' => 19, '9' => 21,
        'A' => 1, 'B' => 0, 'C' => 5, 'D' => 7, 'E' => 9, 'F' => 13, 'G' => 15, 'H' => 17, 'I' => 19, 'J' => 21,
        'K' => 2, 'L' => 4, 'M' => 18, 'N' => 20, 'O' => 11, 'P' => 3, 'Q' => 6, 'R' => 8, 'S' => 12, 'T' => 14,
        'U' => 16, 'V' => 10, 'W' => 22, 'X' => 25, 'Y' => 24, 'Z' => 23,
    ];

    private const EVEN_VALUES = [
        '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
        'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6, 'H' => 7, 'I' => 8, 'J' => 9,
        'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19,
        'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24, 'Z' => 25,
    ];

    /**
     * Calculate Codice Fiscale.
     */
    public function calculate(string $name, string $surname, string $gender, string $birthDate, string $cadastralCode): string
    {
        $cf = "";
        $cf .= $this->computeSurnameCode($surname);
        $cf .= $this->computeNameCode($name);
        
        $date = Carbon::parse($birthDate);
        $cf .= str_pad($date->year % 100, 2, '0', STR_PAD_LEFT);
        $cf .= self::MONTH_MAP[$date->month] ?? 'A';
        
        $day = $date->day;
        if (strtolower($gender) === 'female' || strtolower($gender) === 'donna' || $gender === 'F') {
            $day += 40;
        }
        $cf .= str_pad($day, 2, '0', STR_PAD_LEFT);
        
        $cf .= strtoupper($cadastralCode);
        
        $cf .= $this->computeCheckDigit($cf);
        
        return $cf;
    }

    /**
     * Reverse Codice Fiscale to personal data.
     */
    public function reverse(string $cf): array
    {
        $cf = strtoupper(trim($cf));
        if (strlen($cf) !== 16) {
            throw new \Exception("Formato Codice Fiscale non valido.");
        }

        $yearPart = substr($cf, 6, 2);
        $monthPart = substr($cf, 8, 1);
        $dayPart = (int) substr($cf, 9, 2);
        $cadastralPart = substr($cf, 11, 4);

        $gender = $dayPart > 40 ? 'female' : 'male';
        $day = $dayPart > 40 ? $dayPart - 40 : $dayPart;
        $month = self::REVERSE_MONTH_MAP[$monthPart] ?? 1;
        
        // Year logic: guess century (simple heuristic)
        $currentYear = (int) date('y');
        $year = (int) $yearPart;
        $fullYear = ($year <= $currentYear) ? 2000 + $year : 1900 + $year;

        // Search birthplace
        $birthPlace = $this->findPlaceByCadastral($cadastralPart);

        return [
            'gender' => $gender,
            'birth_date' => sprintf('%02d/%02d/%04d', $day, $month, $fullYear),
            'birth_place' => $birthPlace,
            'cadastral_code' => $cadastralPart
        ];
    }

    private function extractLetters(string $str): array
    {
        $str = strtoupper(preg_replace('/[^A-Za-z]/', '', $str));
        $consonants = preg_replace('/[AEIOU]/', '', $str);
        $vowels = preg_replace('/[^AEIOU]/', '', $str);
        return [str_split($consonants), str_split($vowels)];
    }

    private function computeSurnameCode(string $surname): string
    {
        [$cons, $vow] = $this->extractLetters($surname);
        $code = array_merge($cons, $vow);
        while (count($code) < 3) $code[] = 'X';
        return implode('', array_slice($code, 0, 3));
    }

    private function computeNameCode(string $name): string
    {
        [$cons, $vow] = $this->extractLetters($name);
        if (count($cons) >= 4) {
            return $cons[0] . $cons[2] . $cons[3];
        }
        $code = array_merge($cons, $vow);
        while (count($code) < 3) $code[] = 'X';
        return implode('', array_slice($code, 0, 3));
    }

    private function computeCheckDigit(string $cf15): string
    {
        $sum = 0;
        for ($i = 0; $i < 15; $i++) {
            $char = $cf15[$i];
            if (($i + 1) % 2 === 1) {
                $sum += self::ODD_VALUES[$char] ?? 0;
            } else {
                $sum += self::EVEN_VALUES[$char] ?? 0;
            }
        }
        $remainder = $sum % 26;
        return chr(65 + $remainder);
    }

    private function findPlaceByCadastral(string $code): ?array
    {
        $city = DB::table('localizz_comune')
            ->leftJoin('localizz_provincia', 'localizz_comune.province_id', '=', 'localizz_provincia.id')
            ->where('localizz_comune.cadastral_code', $code)
            ->select('localizz_comune.name', 'localizz_provincia.short_code as province_acronym')
            ->first();

        if ($city) {
            return ['type' => 'city', 'name' => $city->name, 'province_acronym' => $city->province_acronym];
        }

        $country = DB::table('localizz_statoestero')->where('cadastral_code', $code)->first();
        if ($country) {
            return ['type' => 'country', 'name' => $country->name_it];
        }

        return null;
    }
}
