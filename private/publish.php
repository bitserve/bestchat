#!/usr/bin/php
<?php

// Helper script used to push a new copy to your server
// using rsync. It only uploads files that have changed

$dir = realpath(__DIR__.'/../');
$cfg = (include __DIR__.'/config.php');
$cfg = (object)$cfg;

// -c use content matching  not timestamps and -z for compress
passthru("cd '$dir' && rsync -z -c -r --progress . '{$cfg->ssh}'");
