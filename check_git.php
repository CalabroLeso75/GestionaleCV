<?php
$log = shell_exec('git log -n 1 2>&1');
$status = shell_exec('git status 2>&1');
file_put_contents('git_status_check.txt', "Log:\n$log\nStatus:\n$status");
