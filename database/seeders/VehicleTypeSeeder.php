<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'Automobile', 'documentazione' => 'Carta di circolazione', 'certificazioni' => '—', 'patente' => 'B', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Uso privato', 'ente_controllo' => 'Motorizzazione Civile (MC)'],
            ['name' => 'Minibus (<9 posti)', 'documentazione' => 'Carta di circolazione', 'certificazioni' => '—', 'patente' => 'B', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Trasporto persone non professionale', 'ente_controllo' => 'MC'],
            ['name' => 'Camper / Motorhome', 'documentazione' => 'Carta di circolazione', 'certificazioni' => '—', 'patente' => 'B', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Veicolo speciale abitativo', 'ente_controllo' => 'MC'],
            ['name' => 'Furgone ≤3,5 t', 'documentazione' => 'Carta di circolazione', 'certificazioni' => '—', 'patente' => 'B', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Trasporto cose', 'ente_controllo' => 'MC'],
            ['name' => 'Autocarro >3,5 t', 'documentazione' => 'Carta di circolazione + tachigrafo', 'certificazioni' => 'CQC (se uso professionale)', 'patente' => 'C', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Trasporto merci', 'ente_controllo' => 'MC'],
            ['name' => 'Autoarticolato (TIR)', 'documentazione' => 'Licenza trasporto', 'certificazioni' => 'CQC merci', 'patente' => 'CE', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Trasporto pesante', 'ente_controllo' => 'MC'],
            ['name' => 'Autobotte / Autocisterna', 'documentazione' => 'Doc ADR', 'certificazioni' => 'Certificazione ADR', 'patente' => 'C/CE', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Merci pericolose', 'ente_controllo' => 'MC'],
            ['name' => 'Autocarro frigorifero', 'documentazione' => 'Carta di circolazione + ATP', 'certificazioni' => '—', 'patente' => 'B/C', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Trasporto alimentare', 'ente_controllo' => 'MC'],
            ['name' => 'Bisarca', 'documentazione' => 'Licenza trasporto', 'certificazioni' => 'CQC', 'patente' => 'CE', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Trasporto veicoli', 'ente_controllo' => 'MC'],
            ['name' => 'Camion ribaltabile', 'documentazione' => 'Carta di circolazione', 'certificazioni' => '—', 'patente' => 'C', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Cantiere/trasporto', 'ente_controllo' => 'MC'],
            ['name' => 'Mezzi eccezionali', 'documentazione' => 'Autorizzazione ANAS', 'certificazioni' => '—', 'patente' => 'C/CE', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Trasporti eccezionali', 'ente_controllo' => 'MC'],
            ['name' => 'Betoniera stradale', 'documentazione' => 'Carta di circolazione', 'certificazioni' => '—', 'patente' => 'C', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Opera civile', 'ente_controllo' => 'MC'],
            ['name' => 'Autogru stradale', 'documentazione' => 'Carta di circolazione', 'certificazioni' => 'Patentino gru (uso lavoro)', 'patente' => 'C', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Sollevamento mobile', 'ente_controllo' => 'MC'],
            ['name' => 'Carro attrezzi', 'documentazione' => 'Licenza soccorso', 'certificazioni' => '—', 'patente' => 'B/C', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Soccorso stradale', 'ente_controllo' => 'MC'],
            ['name' => 'Spazzatrice stradale', 'documentazione' => 'Carta di circolazione', 'certificazioni' => '—', 'patente' => 'B/C', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Igiene urbana', 'ente_controllo' => 'MC'],
            ['name' => 'Trattore agricolo gommato', 'documentazione' => 'Libretto UMA', 'certificazioni' => 'Patentino trattore', 'patente' => 'B (su strada)', 'revisione' => 'Sì*', 'assicurazione' => 'Sì*', 'tipo_abilitazione' => 'Macchina agricola', 'ente_controllo' => 'MC'],
            ['name' => 'Trattore compatto', 'documentazione' => 'Libretto', 'certificazioni' => 'Patentino trattore', 'patente' => 'B', 'revisione' => 'Sì*', 'assicurazione' => 'Sì*', 'tipo_abilitazione' => 'Agricolo', 'ente_controllo' => 'MC'],
            ['name' => 'Motoagricola', 'documentazione' => 'Carta di circolazione', 'certificazioni' => '—', 'patente' => 'B', 'revisione' => 'Sì', 'assicurazione' => 'RC Auto', 'tipo_abilitazione' => 'Trasporto agricolo', 'ente_controllo' => 'MC'],
            ['name' => 'Rimorchio agricolo', 'documentazione' => 'Immatricolazione', 'certificazioni' => '—', 'patente' => '—', 'revisione' => 'Sì*', 'assicurazione' => 'Sì*', 'tipo_abilitazione' => 'Trainato', 'ente_controllo' => 'MC'],
            ['name' => 'Atomizzatore semovente', 'documentazione' => 'Libretto macchina', 'certificazioni' => 'Formazione fitofarmaci', 'patente' => 'B', 'revisione' => 'Sì*', 'assicurazione' => 'Sì*', 'tipo_abilitazione' => 'Agricolo specializzato', 'ente_controllo' => 'MC'],
            ['name' => 'Mietitrebbia', 'documentazione' => 'Registro macchina', 'certificazioni' => 'Patentino trattore', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No**', 'tipo_abilitazione' => 'Operatrice agricola', 'ente_controllo' => 'Azienda / ASL'],
            ['name' => 'Vendemmiatrice', 'documentazione' => 'Registro macchina', 'certificazioni' => 'Formazione operatore', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No**', 'tipo_abilitazione' => 'Raccolta agricola', 'ente_controllo' => 'Azienda / ASL'],
            ['name' => 'Trincia semovente', 'documentazione' => 'Registro macchina', 'certificazioni' => 'Formazione sicurezza', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No**', 'tipo_abilitazione' => 'Agricolo', 'ente_controllo' => 'Azienda / ASL'],
            ['name' => 'Escavatore cingolato', 'documentazione' => 'Registro controlli', 'certificazioni' => 'Patentino escavatore', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Attrezzatura lavoro', 'ente_controllo' => 'ASL / Azienda'],
            ['name' => 'Miniescavatore', 'documentazione' => 'Registro', 'certificazioni' => 'Patentino MMT', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Movimento terra', 'ente_controllo' => 'ASL / Azienda'],
            ['name' => 'Bulldozer', 'documentazione' => 'Registro', 'certificazioni' => 'Abilitazione MMT', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Spinta terra', 'ente_controllo' => 'ASL / Azienda'],
            ['name' => 'Pala gommata', 'documentazione' => 'Registro', 'certificazioni' => 'Patentino pala', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Caricamento', 'ente_controllo' => 'ASL / Azienda'],
            ['name' => 'Pala cingolata', 'documentazione' => 'Registro', 'certificazioni' => 'Patentino MMT', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Movimento terra', 'ente_controllo' => 'ASL / Azienda'],
            ['name' => 'Terna', 'documentazione' => 'Registro', 'certificazioni' => 'Patentino terna', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Macchina combinata', 'ente_controllo' => 'ASL / Azienda'],
            ['name' => 'Dumper articolato', 'documentazione' => 'Registro', 'certificazioni' => 'Formazione operatore', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Trasporto cantiere', 'ente_controllo' => 'ASL / Azienda'],
            ['name' => 'Rullo compressore', 'documentazione' => 'Registro', 'certificazioni' => 'Formazione specifica', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Compattazione', 'ente_controllo' => 'ASL / Azienda'],
            ['name' => 'Livellatrice (grader)', 'documentazione' => 'Registro', 'certificazioni' => 'Patentino MMT', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Stradale', 'ente_controllo' => 'ASL / Azienda'],
            ['name' => 'Scraper', 'documentazione' => 'Registro', 'certificazioni' => 'Formazione operatore', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Movimento terra', 'ente_controllo' => 'ASL / Azienda'],
            ['name' => 'Carrello elevatore (muletto)', 'documentazione' => 'Registro manutenzione', 'certificazioni' => 'Patentino carrellista', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Accordo Stato-Regioni', 'ente_controllo' => 'ASL / Azienda'],
            ['name' => 'Transpallet elettrico', 'documentazione' => 'Registro', 'certificazioni' => 'Formazione interna', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Attrezzatura lavoro', 'ente_controllo' => 'Azienda'],
            ['name' => 'Transpallet manuale', 'documentazione' => '—', 'certificazioni' => 'Formazione base', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Uso manuale', 'ente_controllo' => 'Azienda'],
            ['name' => 'Sollevatore telescopico', 'documentazione' => 'Registro verifiche', 'certificazioni' => 'Patentino telescopico', 'patente' => '—', 'revisione' => 'No', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Sollevamento', 'ente_controllo' => 'ASL / INAIL'],
            ['name' => 'Gru mobile (non stradale)', 'documentazione' => 'Libretto gru', 'certificazioni' => 'Patentino gruista', 'patente' => '—', 'revisione' => 'Verifiche INAIL', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Sollevamento', 'ente_controllo' => 'INAIL / ASL'],
            ['name' => 'Gru a torre', 'documentazione' => 'Libretto impianto', 'certificazioni' => 'Abilitazione gru torre', 'patente' => '—', 'revisione' => 'Verifiche INAIL', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Cantiere', 'ente_controllo' => 'INAIL / ASL'],
            ['name' => 'Piattaforma aerea (PLE)', 'documentazione' => 'Registro controlli', 'certificazioni' => 'Patentino PLE', 'patente' => '—', 'revisione' => 'Verifiche periodiche', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Lavori in quota', 'ente_controllo' => 'INAIL / ASL'],
            ['name' => 'Carroponte', 'documentazione' => 'Registro impianto', 'certificazioni' => 'Formazione operatore', 'patente' => '—', 'revisione' => 'Verifiche periodiche', 'assicurazione' => 'No', 'tipo_abilitazione' => 'Sollevamento industriale', 'ente_controllo' => 'INAIL / ASL'],
        ];

        foreach ($types as $type) {
            VehicleType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
