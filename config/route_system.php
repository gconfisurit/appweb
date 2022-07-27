<?php

# URL  (principalmente para cargar librerias por url)
const URL_APP         = ("http://". SERVER ."/appweb/app/");
const URL_LIBRARY     = ("http://". SERVER ."/appweb/public/");
const URL_LANDINGPAGE = ("http://". SERVER ."/appweb/public/landingpage/");
const URL_HELPERS_JS  = ("http://". SERVER ."/appweb/helpers/js/");


# PATH  (los archivos php se cargan por ruta absoluta y no por url)
define("PATH_APP_PHP",     $_SERVER['DOCUMENT_ROOT'] ."/appweb/app/");
define("PATH_HELPERS_PHP", $_SERVER['DOCUMENT_ROOT'] ."/appweb/helpers/");
define("PATH_SERVICE_PHP", $_SERVER['DOCUMENT_ROOT'] ."/appweb/services/");
define("PATH_LIBRARY",     $_SERVER['DOCUMENT_ROOT'] ."/appweb/public/");
define("PATH_VENDOR",      $_SERVER['DOCUMENT_ROOT'] ."/appweb/vendor/");
define("PATH_CONFIG",      $_SERVER['DOCUMENT_ROOT'] ."/appweb/config/");


# Constantes de fecha
const FORMAT_DATE             = "d/m/Y";
const FORMAT_DATE_TO_EVALUATE = "Y-m-d";
const FORMAT_DATETIME         = "d/m/Y h:i:s";
const FORMAT_DATETIME2        = "d/m/Y h:i A";
const FORMAT_DATETIME_FOR_INSERT = "Y-m-d h:i:s";