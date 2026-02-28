<?php
header('Content-Type: image/svg+xml');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: max-age=86400');
echo file_get_contents('https://cdn.jsdelivr.net/npm/bootstrap-italia@2.9.0/dist/svg/sprites.svg');
