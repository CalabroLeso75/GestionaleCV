<?php
$files = glob(__DIR__ . '/storage/framework/sessions/*');
foreach($files as $file){
  if(is_file($file))
    unlink($file);
}
echo "Sessioni cancellate.";
