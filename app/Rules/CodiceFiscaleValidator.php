<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

/**
 * Validatore Codice Fiscale Italiano.
 * Verifica la coerenza del CF con: cognome, nome, data di nascita, genere, luogo di nascita.
 */
class CodiceFiscaleValidator implements ValidationRule, DataAwareRule
{
    protected array $data = [];
    protected array $errors = [];

    // Mese CF → numero
    private const MONTH_MAP = [
        'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'H' => 6,
        'L' => 7, 'M' => 8, 'P' => 9, 'R' => 10, 'S' => 11, 'T' => 12,
    ];

    // Tabella valori dispari per check digit
    private const ODD_VALUES = [
        '0' => 1, '1' => 0, '2' => 5, '3' => 7, '4' => 9, '5' => 13, '6' => 15, '7' => 17, '8' => 19, '9' => 21,
        'A' => 1, 'B' => 0, 'C' => 5, 'D' => 7, 'E' => 9, 'F' => 13, 'G' => 15, 'H' => 17, 'I' => 19, 'J' => 21,
        'K' => 2, 'L' => 4, 'M' => 18, 'N' => 20, 'O' => 11, 'P' => 3, 'Q' => 6, 'R' => 8, 'S' => 12, 'T' => 14,
        'U' => 16, 'V' => 10, 'W' => 22, 'X' => 25, 'Y' => 24, 'Z' => 23,
    ];

    // Tabella valori pari per check digit
    private const EVEN_VALUES = [
        '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
        'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6, 'H' => 7, 'I' => 8, 'J' => 9,
        'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19,
        'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24, 'Z' => 25,
    ];

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cf = strtoupper(trim($value));

        // Format check
        if (!preg_match('/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/', $cf)) {
            $fail('Il formato del codice fiscale non è valido.');
            return;
        }

        // Check digit verification
        if (!$this->verifyCheckDigit($cf)) {
            $fail('Il carattere di controllo del codice fiscale non è corretto.');
            return;
        }

        // Validate surname (chars 1-3)
        if (!empty($this->data['surname'])) {
            $expectedSurname = $this->computeSurnameCode($this->data['surname']);
            $actualSurname = substr($cf, 0, 3);
            if ($expectedSurname !== $actualSurname) {
                $fail("Il cognome non corrisponde al codice fiscale (atteso: {$expectedSurname}, trovato: {$actualSurname}).");
            }
        }

        // Validate name (chars 4-6)
        if (!empty($this->data['name'])) {
            $expectedName = $this->computeNameCode($this->data['name']);
            $actualName = substr($cf, 3, 3);
            if ($expectedName !== $actualName) {
                $fail("Il nome non corrisponde al codice fiscale (atteso: {$expectedName}, trovato: {$actualName}).");
            }
        }

        // Validate birth date + gender (chars 7-11)
        if (!empty($this->data['birth_date']) && !empty($this->data['gender'])) {
            $birthDate = \Carbon\Carbon::parse($this->data['birth_date']);
            $yearCode = substr($cf, 6, 2);
            $monthChar = substr($cf, 8, 1);
            $dayCode = (int) substr($cf, 9, 2);

            $expectedYear = str_pad($birthDate->year % 100, 2, '0', STR_PAD_LEFT);
            if ($yearCode !== $expectedYear) {
                $fail("L'anno di nascita non corrisponde al codice fiscale.");
            }

            if (isset(self::MONTH_MAP[$monthChar])) {
                if (self::MONTH_MAP[$monthChar] !== $birthDate->month) {
                    $fail("Il mese di nascita non corrisponde al codice fiscale.");
                }
            } else {
                $fail("Carattere mese non valido nel codice fiscale.");
            }

            // Day: females add 40
            $expectedDay = $birthDate->day;
            if ($this->data['gender'] === 'female') {
                $expectedDay += 40;
            }
            if ($dayCode !== $expectedDay) {
                $gender = $this->data['gender'] === 'female' ? 'donna' : 'uomo';
                $fail("Il giorno di nascita o il genere ({$gender}) non corrisponde al codice fiscale.");
            }
        }

        // Validate birth place code (chars 12-15)
        $placeCode = substr($cf, 11, 4);
        $birthType = $this->data['birth_type'] ?? 'italy';

        if ($birthType === 'italy' && !empty($this->data['birth_city_id'])) {
            $city = DB::table('localizz_comune')
                ->where('id', $this->data['birth_city_id'])
                ->first();
            if ($city && strtoupper($city->cadastral_code) !== $placeCode) {
                $fail("Il comune di nascita non corrisponde al codice catastale nel CF (atteso: {$city->cadastral_code}, trovato: {$placeCode}).");
            }
        } elseif ($birthType === 'abroad' && !empty($this->data['birth_country_id'])) {
            $country = DB::table('localizz_statoestero')
                ->where('id', $this->data['birth_country_id'])
                ->first();
            if ($country && strtoupper($country->cadastral_code) !== $placeCode) {
                $fail("Lo stato estero di nascita non corrisponde al codice nel CF (atteso: {$country->cadastral_code}, trovato: {$placeCode}).");
            }
        }
    }

    /**
     * Extract consonants and vowels from a string.
     */
    private function extractLetters(string $str): array
    {
        $str = strtoupper(preg_replace('/[^A-Za-z]/', '', $str));
        $consonants = preg_replace('/[AEIOU]/', '', $str);
        $vowels = preg_replace('/[^AEIOU]/', '', $str);
        return [str_split($consonants), str_split($vowels)];
    }

    /**
     * Compute the 3-char surname code.
     * Rule: take consonants, then vowels, pad with X if needed.
     */
    private function computeSurnameCode(string $surname): string
    {
        [$cons, $vow] = $this->extractLetters($surname);
        $code = array_merge($cons, $vow);
        while (count($code) < 3) {
            $code[] = 'X';
        }
        return implode('', array_slice($code, 0, 3));
    }

    /**
     * Compute the 3-char name code.
     * Rule: if consonants >= 4, take 1st, 3rd, 4th. Otherwise same as surname.
     */
    private function computeNameCode(string $name): string
    {
        [$cons, $vow] = $this->extractLetters($name);
        if (count($cons) >= 4) {
            return $cons[0] . $cons[2] . $cons[3];
        }
        $code = array_merge($cons, $vow);
        while (count($code) < 3) {
            $code[] = 'X';
        }
        return implode('', array_slice($code, 0, 3));
    }

    /**
     * Verify the check digit (last character).
     */
    private function verifyCheckDigit(string $cf): bool
    {
        $sum = 0;
        for ($i = 0; $i < 15; $i++) {
            $char = $cf[$i];
            if (($i + 1) % 2 === 1) {
                // Odd position (1-indexed)
                $sum += self::ODD_VALUES[$char] ?? 0;
            } else {
                // Even position
                $sum += self::EVEN_VALUES[$char] ?? 0;
            }
        }
        $remainder = $sum % 26;
        $expectedCheck = chr(65 + $remainder); // A=0, B=1, ...
        return $cf[15] === $expectedCheck;
    }

    /**
     * Static helper: validate a CF without needing all form data.
     * Returns true if format and check digit are valid.
     */
    public static function isFormatValid(string $cf): bool
    {
        $cf = strtoupper(trim($cf));
        if (!preg_match('/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/', $cf)) {
            return false;
        }
        $instance = new self();
        return $instance->verifyCheckDigit($cf);
    }

    /**
     * Static helper: extract gender from CF.
     * Returns 'male' if day <= 31, 'female' if day > 40.
     */
    public static function extractGender(string $cf): ?string
    {
        $cf = strtoupper(trim($cf));
        if (strlen($cf) < 11) return null;
        $day = (int) substr($cf, 9, 2);
        return $day > 40 ? 'female' : 'male';
    }
}
