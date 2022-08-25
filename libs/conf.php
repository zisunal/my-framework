<?php
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
defined('INCLUDES_PATH') ? null : define('INCLUDES_PATH', DS . 'includes');
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    define('PROTOCOL', 'https://');
} else {
    define('PROTOCOL', 'http://');
}
require_once __DIR__ . "/../vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.zisunal');
$dotenv->load();
define("SUBDIR", $_ENV["SUBDIR"]);
define("TM_ZONE", $_ENV["TM_ZONE"]);
define("DEBUG", $_ENV["DEBUG"]);
define("FAVICON", $_ENV["FAVICON"]);
if (!empty(SUBDIR)) {
    defined('SITE_ROOT') ? null : define('SITE_ROOT', $_SERVER['HTTP_HOST'] . "/" . SUBDIR);
} else {
    defined('SITE_ROOT') ? null : define('SITE_ROOT', $_SERVER['HTTP_HOST']);
}

//Script defaults
const GO_FONTS = '<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">';
const FA = '<!--Fontawesome--><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />';
const ANIM = '<!--Animate CSS--><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />';
const BT_CSS = '<!--Bootstrap CSS--><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css';
const ION_ICONS = '<!--ION Icons--><link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">';
const MMENU_CSS = '<!--Mmenu--><link rel="stylesheet" href="' . PROTOCOL . SITE_ROOT . '/assets/libraries/mmenu/mmenu.css">';
const SWIP_CSS = '<!--Swiper--><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">';
const OWL_THEME_DEF = '<!--Owl Carousel--><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">';
const NICE_SELECT = '<!--Nice Select 2--><link rel="stylesheet" href="' . PROTOCOL . SITE_ROOT . '/assets/libraries/nice-select2/css/nice-select2.css">';
const FILE_POND_CSS = '<!--File Pond--><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/filepond/4.30.4/filepond.min.css">';
const MAT_CSS = '<!--Mat CSS--><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">';
const MAT_ICONS = '<!--Mat CSS--><link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">';
const INTRO_CSS = '<!--Intro CSS--><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@8.3.1/dist/css/shepherd.css">';
const MAIN_CSS = '<!--Custom CSS--><link rel="stylesheet" href="' . PROTOCOL . SITE_ROOT . '/assets/css/style.css{{VERSION}}">';
const BT_JS = '<!--Bootstrap JS--><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>';
const MAIN_JS = '<!--Custom JS--><script src="' . PROTOCOL . SITE_ROOT . '/assets/js/script.js{{VERSION}}"></script>';
const SWIP_JS = '<!--Owl Carousel--><script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>';
const NICE_SEL_JS = '<!--Nice Select 2 JS--><script src="' . PROTOCOL . SITE_ROOT . '/assets/libraries/nice-select2/js/nice-select2.js"></script>';
const MMENU_JS = '<!--Mobile Menu--><script src="' . PROTOCOL . SITE_ROOT . '/assets/libraries/mmenu/mmenu.js"></script>';
const WOW = '<!--Mobile Menu--><script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>';
const SWAL_JS = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
const FILE_POND_JS = '<script src="https://cdnjs.cloudflare.com/ajax/libs/filepond/4.30.4/filepond.min.js"></script>';
const MAT_JS = '<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>';
const POLY_JS = '<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>';
const GO_MAP_JS = '<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBqXyBrtMrelhQXuC4O3A4xGQkxUKhi21M&callback=initMap&v=weekly" defer ></script>';
const INTRO_JS = '<script src="https://cdn.jsdelivr.net/npm/shepherd.js@8.3.1/dist/js/shepherd.min.js"></script>';