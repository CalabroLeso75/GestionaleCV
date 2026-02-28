<?php
echo "Server IP Address: " . gethostbyname(gethostname()) . "\n";
echo "Interfaces:\n";
exec('ipconfig', $output);
echo implode("\n", $output);
