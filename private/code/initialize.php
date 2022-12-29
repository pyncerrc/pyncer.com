<?php
use Pyncer\Docs\App;

chdir(dirname(dirname(__DIR__)));

date_default_timezone_set('UTC');

$composerAutoloader = getcwd() .
    DIRECTORY_SEPARATOR . 'vendor' .
    DIRECTORY_SEPARATOR . 'autoload.php';

if (file_exists($composerAutoloader)) {
    require_once $composerAutoloader;
}

$composerAutoloader = null;

// Group permissions
define('Pyncer\IO\MODE_FILE', 0664);
define('Pyncer\IO\MODE_DIR', 0775);

return new App();
