<?php

require_once "./config.php";
require_once APP_PHP_PATH . "/index_app.php";

$objectOfRequest = $_POST;

$z3950settingsArray = Utils::getZ3950Settings($objectOfRequest);
$kohaSettingsArray = Utils::getKohaSettings($_POST);

if (Utils::areAllValuesEmpty($z3950settingsArray, Utils::getZ3950SupporterValues())) {
    $objectOfRequest = Utils::addDefaults($objectOfRequest, Utils::getZ3950SupporterValues(), getDefaultZ3950());
    $z3950settingsArray = Utils::getZ3950Settings($objectOfRequest);
}
if (Utils::areAllValuesEmpty($kohaSettingsArray, Utils::getKohaSupportedValues())) {
    $objectOfRequest = Utils::addDefaults($objectOfRequest, Utils::getKohaSupportedValues(), getDefaultKoha());
    $kohaSettingsArray = Utils::getKohaSettings($objectOfRequest);
}

$z3950settings = Z3950::fromRequest($z3950settingsArray);
$kohaSettings = Koha::fromRequest($kohaSettingsArray);

$response = getResponse($_POST);

$files = (mustHideObjects($response))
    ? new Files("", "", "")
    : Files::fromvalue($response->getValue());

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
    <link href="index.css" rel="stylesheet">

    <title>Unimarc to Marc21 through Z39/50 - DataScouting</title>
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    About
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="https://www.loc.gov/z3950/" target="_blank">
                        Z39.50
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item"
                       href="https://www.ifla.org/publications/unimarc-formats-and-related-documentation"
                       target="_blank">
                        Unimarc
                    </a>
                    <a class="dropdown-item" href="https://www.loc.gov/marc/bibliographic/" target="_blank">
                        Marc21
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="https://www.loc.gov/marc/unimarctomarc21.html" target="_blank">
                        Unimarc to Marc21
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="https://datascouting.com/" target="_blank">
                        DataScouting
                    </a>
                </div>
            </li>
        </ul>
    </div>
    <div class="mx-auto order-0">
        <a class="navbar-brand mx-auto" href="#">
            Unimarc to Marc21 through Z39/50
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".dual-collapse2">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="https://datascouting.com" target="_blank">
                    Developed by DataScouting
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-primary text-center">
                <?php echo $response->getMessage(); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="h2">
                    Configuration
                </div>
            </div>
            <div class="row">
                <hr/>
            </div>
            <div class="row">

                <div class="form-row">
                    <div class="col <?php echo getHideClassOrEmpty($response) ?>">
                        <form method="post">

                            <div class="form-group">
                                <div class="h5">
                                    Koha Intra Settings
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="koha_intra_host">Host</label>
                                <input type="text" class="form-control" id="koha_intra_host" name="koha_intra_host"
                                       placeholder="eg koha-intra.koha.gr"
                                       value="<?php echo $kohaSettings->getHost() ?>">
                            </div>
                            <div class="form-group">
                                <label for="koha_intra_port">Port</label>
                                <input type="text" class="form-control" id="koha_intra_port" name="koha_intra_port"
                                       placeholder="eg 1234"
                                       value="<?php echo $kohaSettings->getPort() ?>">
                            </div>
                            <div class="form-group">
                                <label for="koha_intra_basic_username">Basic Auth Username</label>
                                <input type="text" class="form-control" id="koha_intra_basic_username"
                                       name="koha_intra_basic_username"
                                       placeholder="eg basicuser"
                                       value="<?php echo $kohaSettings->getBasicAuthUsername() ?>">
                            </div>
                            <div class="form-group">
                                <label for="koha_intra_basic_password">Basic Auth Password</label>
                                <input type="password" class="form-control" id="koha_intra_basic_password"
                                       name="koha_intra_basic_password"
                                       placeholder="eg basicpass"
                                       value="<?php echo $kohaSettings->getBasicAuthPassword() ?>">
                            </div>
                            <div class="form-group">
                                <label for="koha_intra_username">Username</label>
                                <input type="text" class="form-control" id="koha_intra_username"
                                       name="koha_intra_username"
                                       placeholder="eg admin"
                                       value="<?php echo $kohaSettings->getUsername() ?>">
                            </div>

                            <div class="form-group">
                                <label for="koha_intra_password">Password</label>
                                <input type="password" class="form-control" id="koha_intra_password"
                                       name="koha_intra_password"
                                       placeholder="eg adminpass"
                                       value="<?php echo $kohaSettings->getPassword() ?>">
                            </div>

                            <div class="form-group">
                                <label for="koha_intra_value">Value</label>
                                <input type="text" class="form-control" id="koha_intra_value"
                                       name="koha_intra_value"
                                       placeholder="<?php echo $z3950settingsArray['z3950_value']; ?>"
                                       value="<?php echo $files->getValue(); ?>" readonly>
                            </div>

                            <div class="form-group margin-10">
                                <div class="btn-group w-full">
                                    <button type="submit" class="btn btn-primary">
                                        Import to Koha
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col">
                        <form method="post">

                            <div class="form-group">
                                <div class="h5">
                                    Z39/50 Settings
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="z3950_host">Z39/50 Host</label>
                                <input type="text" class="form-control" id="z3950_host" name="z3950_host"
                                       placeholder="eg z3950.nlg.gr"
                                       value="<?php echo $z3950settings->getHost() ?>">
                            </div>
                            <div class="form-group">
                                <label for="z3950_port">Z39/50 Port</label>
                                <input type="number" class="form-control" id="z3950_port" name="z3950_port"
                                       placeholder="eg 210"
                                       value="<?php echo $z3950settings->getPort() ?>">
                            </div>

                            <div class="form-group">
                                <label for="z3950_database">Z39/50 Database</label>
                                <input type="text" class="form-control" id="z3950_database" name="z3950_database"
                                       placeholder="eg biblios"
                                       value="<?php echo $z3950settings->getDatabase() ?>">
                            </div>

                            <div class="form-group">
                                <div class="h5">
                                    Value
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="z3950_value">Value to Find</label>
                                <input type="text" class="form-control" id="z3950_value" name="z3950_value"
                                       placeholder="eg 978-960-044-259-5"
                                       value="<?php echo $files->getValue(); ?>">
                            </div>

                            <div class="form-group">
                                <label for="z3950_type">Type of Value to Find</label>
                                <select class="form-control" id="z3950_type" name="z3950_type">
                                    <?php
                                    foreach (QueryType::getTypes() as $key => $value) {
                                        echo sprintf(
                                            "<option %s>%s</option>",
                                            ($z3950settings->getType() == $key) ? "selected" : "",
                                            $key
                                        );
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-row margin-10">
                                <div class="btn-group w-full">
                                    <button type="submit" class="btn btn-primary text-center">
                                        Search | Convert
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <div class="row <?php echo getHideClassOrEmpty($response); ?>" style="margin-left: -20px;">
                <div class="btn-group">
                    <a target="_blank"
                       class="btn btn-primary disabled">
                        <i class="fas fa-download"></i>
                        Download as:
                    </a>
                    <a target="_blank"
                       href="<?php echo Utils::getRelatedFilePath(INSTALLATION_PATH, $files->getXml()); ?>"
                       class="btn btn-success">
                        MARC21XML
                    </a>
                    <a target="_blank"
                       href="<?php echo Utils::getRelatedFilePath(INSTALLATION_PATH, $files->getMrc()); ?>"
                       class="btn btn-success">
                        MARC21MRC
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 <?php echo getHideClassOrEmpty($response); ?>">
            <div class="row">
                <div class="h2">
                    Preview MARC21 XML
                </div>
            </div>
            <div class="row">
                <label style="width: 100%">
                    <textarea style="width: 100%; height: 32em" readonly>
                        <?php echo $files->getXmlContents(); ?>
                    </textarea>
                </label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center">
            <a href="https://datascouting.com" target="_blank">
                <img src="storage/persistent/datascouting.png" alt="DataScouting" class="footer-logo"/>
            </a>
        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>
</html>
