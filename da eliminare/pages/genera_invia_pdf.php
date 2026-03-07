<?php
// nomefile: genera_invia_pdf.php

// Avvia la sessione per accedere ai dati salvati (es. email, nome DOS ecc.)
session_start();

// Inclusione dei file di configurazione per l'email e le librerie necessarie
require '../config/email_config.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';
require '../fpdf/fpdf.php';

// Namespace per PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verifica se la richiesta è di tipo POST (invio modulo)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Decodifica i dati JSON ricevuti dal modulo
  $data = json_decode($_POST['formData'], true);

  // Se i dati non sono validi, interrompe l'esecuzione
  if (!$data) {
    die("Dati non validi.");
  }

  // Imposta data, ora e nome del file PDF
  $oggi = date('Ymd');
  $ora = date('H:i');
  $comuneSanificato = strtolower(preg_replace('/[^a-z0-9]/i', '', strtolower($data['comune'])));
  $nomeFilePDF = $oggi . "_" . $comuneSanificato . ".pdf";

  // Crea un nuovo PDF
  $pdf = new FPDF();
  $pdf->AddPage();
  $pdf->SetFont('Times','',12);

  // Titolo del documento
  $pdf->SetFont('Times', 'B', 16);
  $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Richiesta Intervento Aereo - ' . $data['input-richiesta']), 0, 1, 'C');
  $pdf->Ln(10);

  // Funzione per aggiungere un titolo di sezione
  function addSectionTitle($pdf, $title) {
    $pdf->SetFont('Times', 'B', 14);
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $title), 0, 1);
    $pdf->Ln(2);
  }

  // Funzione per aggiungere un campo di testo
  function addField($pdf, $valore) {
    $pdf->SetFont('Times', '', 12);
    $pdf->MultiCell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $valore));
    $pdf->Ln(2);
  }

  // Mappa per convertire il nome provincia nella sigla
  $provinceSigle = [
    'cosenza' => 'CS',
    'catanzaro' => 'CZ',
    'crotone' => 'KR',
    'vibo valentia' => 'VV',
    'reggio calabria' => 'RC',
    'reggio di calabria' => 'RC'
  ];
  $siglaProvincia = $provinceSigle[strtolower(trim($data['provincia']))] ?? '';

  // Sezione: DOS
  addSectionTitle($pdf, 'Direttore delle Operazioni di Spegnimento (DOS)');
  $pdf->MultiCell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "DOS {$_SESSION['sigla']} {$_SESSION['nome']} {$_SESSION['telefono']}"));

  // Sezione: Localizzazione
  addSectionTitle($pdf, 'Localizzazione');
  addField($pdf, "Localizzato a: {$data['localita']} - {$data['comune']} ({$siglaProvincia})");
  addField($pdf, "Coordinate: {$data['latitudine']} - {$data['longitudine']}");
  addField($pdf, "Quota: {$data['quota']} m slm - " . (!empty($data['input-area-impervia']) ? "Impervia" : "NON impervia"));

  // Sezione: Dettagli Incendio
  addSectionTitle($pdf, 'Dettagli Incendio');
  $vegBr = preg_replace('/^[IVXLCDM]+\.\s*/', '', $data['input-vegetazione-bruciata']);
  $valAmbBr = preg_replace('/^\d+\.\s*/', '', $data['input-valore-ambientale']);
  addField($pdf, "Superficie Bruciata: {$data['input-superficie-bruciata']} ha - {$vegBr} - valore ambientale: {$valAmbBr}");

  $vegR = preg_replace('/^[IVXLCDM]+\.\s*/', '', $data['input-vegetazione-rischio']);
  $valAmbR = preg_replace('/^\d+\.\s*/', '', $data['input-valore-rischio']);
  addField($pdf, "Superficie a Rischio: {$data['input-superficie-rischio']} ha - {$vegR} - valore ambientale: {$valAmbR}");

  addField($pdf, "Fronti: {$data['input-fronti']} - Lunghezza Totale: {$data['input-lunghezza-fronti']} m");
  addField($pdf, "Condizioni del vento: {$data['input-vento']}");
  if (!empty($data['input-dos-responsabile']) && $data['input-dos-responsabile'] === true) {
    addField($pdf, "Il DOS ha dichiarato che il mezzo aereo può operare in sicurezza ed efficacia minima garantita.");
  }

  // Sezione: Personale e Mezzi
  addSectionTitle($pdf, 'Personale e Mezzi');
  addField($pdf, "Squadre a terra: {$data['input-squadre']}, Numero persone: {$data['input-numero-persone']}");
  $elicotteri = !empty($data['input-elicotteri']) ? 'Sì' : 'No';
  addField($pdf, "Elicotteri regionali presenti: {$elicotteri}");

  // Sezione: Altre informazioni
  addSectionTitle($pdf, 'Altre Informazioni');
  addField($pdf, "Fonte idrica: {$data['input-fonte-idrica']}");
  addField($pdf, "Infrastrutture: {$data['input-infrastrutture']}");
  addField($pdf, "Insediamenti: {$data['input-insediamenti']}");
  addField($pdf, "Ostacoli: {$data['input-ostacoli']}");
  addField($pdf, "Elettrodotti: {$data['input-elettrodotti']}");
  addField($pdf, "Frequenza radio: {$data['input-radio']}");
  addField($pdf, "Uso ritardante: {$data['input-ritardante']}");
  addField($pdf, "Richiesta per: {$data['input-richiesta']}");

  // Sezione: Priorità
  addSectionTitle($pdf, 'Priorità');
  addField($pdf, $data['priorita'] ?? 'Non selezionata');

  // Sezione: Note aggiuntive (solo se diverse dalla nota automatica)
  if (!empty($data['note']) && $data['note'] !== $data['nota-critica']) {
    addSectionTitle($pdf, 'Note Aggiuntive');
    addField($pdf, $data['note']);
  }

  // Se presente, aggiunge la nota tecnica automatica
  if (!empty($data['nota-critica'])) {
    addSectionTitle($pdf, 'Nota tecnica automatica');
    addField($pdf, $data['nota-critica']);
  }

  // Footer con data e ora invio
  $pdf->Ln(5);
  $pdf->SetFont('Times', 'I', 10);
  $pdf->Cell(0, 10, 'Data invio: ' . date('d/m/Y') . ' - Ora: ' . $ora, 0, 1, 'R');

  // Salvataggio temporaneo del PDF
  $pdfPath = sys_get_temp_dir() . '/' . $nomeFilePDF;
  $pdf->Output('F', $pdfPath);

  // Invio email con PHPMailer
  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = EMAIL_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = $_SESSION['email'];
    $mail->Password = $_SESSION['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = EMAIL_PORT;

    $mail->setFrom($_SESSION['email'], $_SESSION['nome']);
    $mail->addCC("soup@calabriaverde.eu");
    $mail->addBCC($_SESSION['email']);
    $mail->addAddress(EMAIL_DEST_SOUP);
    $mail->addAttachment($pdfPath, $nomeFilePDF);
    $mail->Body = "In allegato la richiesta d'intervento del DOS {$_SESSION['nome']} {$_SESSION['cognome']}.";

    $mail->send();
    unlink($pdfPath); // Rimuove il file PDF dopo l'invio
    echo "<script>alert('Richiesta inviata con successo.'); window.location.href = 'ria.php';</script>";
  } catch (Exception $e) {
    echo "<script>alert('Errore invio email: {$mail->ErrorInfo}'); window.history.back();</script>";
  }

} else {
  // Accesso diretto non consentito
  echo "<script>alert('Accesso non consentito.'); window.location.href = 'ria.php';</script>";
}
?>
