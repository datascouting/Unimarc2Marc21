<?php

define("INSTALLATION_PATH", getcwd());

define("LOAD_DEFAULTS_BY_DEFAULT", true);

require_once INSTALLATION_PATH . "/commons/AppPath.php";
require_once INSTALLATION_PATH . "/commons/Storage.php";
require_once INSTALLATION_PATH . "/commons/TransformTemplate.php";
require_once INSTALLATION_PATH . "/commons/Z3950.php";
require_once INSTALLATION_PATH . "/commons/Koha.php";
require_once INSTALLATION_PATH . "/commons/Defaults.php";
require_once INSTALLATION_PATH . "/commons/StatusCodes.php";