<?php //nomefile: ria.php
 include("header.php"); ?>
<body onload="scrollToContent()">
<?php include("menubar.php"); ?>
<link href="../css/style.css" rel="stylesheet">
<link href="../css/richiesta_mezzo.css" rel="stylesheet">
<link href="../css/ria_altre_informazioni.css" rel="stylesheet">
<link href="../css/priorita.css" rel="stylesheet">


<main>
  <?php include("ria_dos.php"); ?>
  <?php include("ria_localizzazione.php"); ?>
  <?php include("ria_dettagli_incendio.php"); ?>
  <?php include("ria_altre_informazioni.php"); ?>
  <?php include("ria_personale_mezzi.php"); ?>
  <?php include("ria_priorita.php"); ?>
  <?php include("ria_nota_tecnica.php"); ?>
  <?php include("ria_note.php"); ?>
  <?php include("ria_form.php"); ?>
</main>

<!-- Librerie Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-omnivore@0.3.4/leaflet-omnivore.min.js"></script>

<?php include("autofill_dos.php"); ?>
<?php include("footer.php"); ?>

<!-- Script JavaScript -->
<script src="../js/comuni_per_provincia.js"></script>
<script src="../js/comuni_dinamici.js"></script>
<script src="../js/richiesta_mezzo.js"></script>
<script src="../js/ria_altre_informazioni.js"></script>
<script src="../js/ria_nota_tecnica.js"></script>
<script src="../js/comuni_data.js"></script>
<script src="../js/validarichiesta.js"></script>
</body>
