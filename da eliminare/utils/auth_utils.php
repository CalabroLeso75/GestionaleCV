<?php

function isUtenteAutorizzato() {
    return isset($_SESSION['email'], $_SESSION['ruolo']) &&
           $_SESSION['email'] === 'raffaele.cusano@calabriaverde.eu' &&
           $_SESSION['ruolo'] === 'admin';
}
