<?php

require __DIR__ . '/../vendor/autoload.php';

// config.php derives INSTALLATION_PATH from the current working directory,
// exactly like index.php does when served by apache from the document root.
chdir(__DIR__ . '/../www');

require_once __DIR__ . '/../www/config.php';
require_once APP_PHP_PATH . '/index_app.php';
