# Unimarc2Marc21

Web service that allows item fetching through Z39.50 server, transformation of Unimarc to Marc21 and upload to Koha repository through Koha-API 

## Getting Started

These instructions will get you a copy of the project up and running on your local machine or server.

### Prerequisites

* Linux-based OS or Windows with Subsystem for Linux
    * Tested with Ubuntu 20.04 LTS
* Git - [Install Guide](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* Docker - [Install Guide](https://docs.docker.com/engine/install/)
* Docker Compose - [Install Guide](https://docs.docker.com/compose/install/)

### Installing

* Open a terminal
* Clone the repository `git clone git@github.com:datascouting/Unimarc2Marc21.git Unimarc2Marc21`
* Open the repository `cd ./Unimarc2Marc21`
* Copy the example environment file `cp .env.example .env`
* Edit the environment file `./.env`
    * (IMPORTANT) Change the APP_UID and APP_GID with the host UID and host GID
        * Defaults: UID `1000` - GID `1000`
    * (OPTIONAL) Change the HOST_PORT (this port will be used for access to the application from browser)
        * Default: `80` (HTTP)
    * (OPTIONAL) Change the TIMEZONE
        * Default: `Europe/Athens`
* Edit the Defaults file `./www/commons/Defaults.php`
    * This file contains default Z39.50 server credentials/settings and default KOHA installation credentials  
        * Default Z39.50: `National Library of Greece`
        * Default KOHA: `None`
* Build the docker image `docker-compose build`
* Start the docker container `docker-compose up -d`

### Defaults File Fields
#### Default Z39.50
* `z3950_host` - The z39.50 server host `REQUIRED`
    * domain name or IP
* `z3950_port` - The z39.50 server port `REQUIRED`
* `z3950_database` - The z39.50 database name `REQUIRED`
* `z3950_type` - The default search type `REQUIRED`
    * Currently supported types: `ISBN (Preferred)`, `ISSN` and `Title`

#### Default Koha
* `koha_intra_host` - The Koha Intra host `REQUIRED`
    * domain name or IP
* `koha_intra_port` - The Koha Intra port `REQUIRED`
* `koha_intra_basic_username` - The Koha Intra basic auth username `OPTIONAL`
* `koha_intra_basic_password` - The Koha Intra basic auth password `OPTIONAL`
* `koha_intra_username` - The Koha Intra username `REQUIRED`
* `koha_intra_password` - The Koha Intra password`REQUIRED`

## Built With

* [Docker](https://www.docker.com/) - Development and Delivery Platform
* [YAZ](https://github.com/indexdata/yaz) - Z39.50 Client and Converter
* [xsltproc](http://xmlsoft.org/XSLT/index.html) - XSLT Transformer
* [Bootstrap](https://github.com/twbs/bootstrap) - Web UI Framework
* [FontAwesome](https://github.com/FortAwesome/Font-Awesome) - Web UI Library
* [jQuery](https://github.com/jquery/jquery) - Web Library
* [Popper.js](https://github.com/popperjs/popper-core) - Web Library

## Authors
* **Iordanis Kostelidis** - *Initial work as Platform* - [DataScouting](https://github.com/DataScouting)

See also the list of [contributors](https://github.com/datascouting/Unimarc2Marc21/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* [Anthony Serdyukov](https://github.com/edsd/biblio-metadata/) for Xslt File
* [Theodoros Theodoropoulos](https://bugs.koha-community.org/bugzilla3/show_bug.cgi?id=16488) for modified version of Anthony's Xslt File
* [Terry Reese](https://github.com/reeset/unimarc) for Xslt File from MarcEdit
    