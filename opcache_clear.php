<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared!\n";
}
if (function_exists('opcache_invalidate')) {
    opcache_invalidate(__DIR__ . '/test_setup.php', true);
    opcache_invalidate(__DIR__ . '/setup_admin.php', true);
    opcache_invalidate(__DIR__ . '/migrate_anag_persone.php', true);
    echo "Files invalidated!\n";
}
echo "Done. Now try the scripts again.";
