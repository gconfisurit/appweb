<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
include_once("../config/const.php");
include_once("../config/route_system.php");

if (isset($_SESSION['cedula'])) {
    Url::redirect(URL_APP . 'principal.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>APP Despacho y Logística</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
<!--    <link href="--><?php //echo URL_LANDINGPAGE; ?><!--img/favicon.png" rel="icon">-->
    <link href="<?php echo URL_LANDINGPAGE; ?>img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>plugins/fontawesome-free/css/all.min.css">
    <!-- Vendor CSS Files -->
    <link href="<?php echo URL_LANDINGPAGE; ?>vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo URL_LANDINGPAGE; ?>vendor/icofont/icofont.min.css" rel="stylesheet">
    <link href="<?php echo URL_LANDINGPAGE; ?>vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="<?php echo URL_LANDINGPAGE; ?>vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="<?php echo URL_LANDINGPAGE; ?>vendor/venobox/venobox.css" rel="stylesheet">
    <link href="<?php echo URL_LANDINGPAGE; ?>vendor/owl.carousel/owl.carousel.min.css" rel="stylesheet">
    <link href="<?php echo URL_LANDINGPAGE; ?>vendor/aos/aos.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="<?php echo URL_LANDINGPAGE; ?>css/style.css" rel="stylesheet">

    <!-- =======================================================
    * Template Name: Arsha - v2.3.1
    * Template URL: https://bootstrapmade.com/arsha-free-bootstrap-html-template-corporate/
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
</head>

<body>

<!-- ======= Header ======= -->
<header id="header" class="fixed-top ">
    <div class="container d-flex align-items-center">

        <h1 class="logo mr-auto"><a href="index.php">DISTRIBUCIONES AJ, C.A</a></h1>
        <!-- Uncomment below if you prefer to use an image logo -->
        <!-- <a href="index.html" class="logo mr-auto"><img src="img/logo.png" alt="" class="img-fluid"></a>-->

        <a id="btnLogin" data-toggle="modal" onclick="limpiar()" data-target="#loginModal" href="#" class="get-started-btn scrollto">Iniciar Sesión</a>

    </div>
</header><!-- End Header -->

<!-- ======= Hero Section ======= -->
<section id="hero" class="d-flex align-items-center">

    <div class="container">
        <div class="row">
            <div class="col-lg-6 d-flex flex-column justify-content-center pt-4 pt-lg-0 order-2 order-lg-1" data-aos="fade-up" data-aos-delay="200">
                <h1>Tecnología avanzada para tus necesidades.</h1>
                <h2> La innovación distingue a los líderes de los seguidores. </h2>
                <!-- <div class="d-lg-flex">
                    <a href="#" class="btn-get-started scrollto">Empezar</a>
                    <a href="https://www.youtube.com/watch?v=jDDaplaOz7Q" class="venobox btn-watch-video" data-vbtype="video" data-autoplay="true"> Watch Video <i class="icofont-play-alt-2"></i></a>
                </div> -->
            </div>
            <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-in" data-aos-delay="200">
                <img src="<?php echo URL_LIBRARY; ?>dist/img/logo_empresa.png" class="img-fluid animated" alt="">
            </div>
        </div>
    </div>

</section><!-- End Hero -->

<!-- MODAL LOGIN -->
<?php include 'auth/modales/login.html' ?>

<!-- MODAL RECUPERAR CONTRASEÑA -->
<?php include 'auth/modales/recuperar_contrasena.html' ?>

<a href="#" class="back-to-top"><i class="ri-arrow-up-line"></i></a>
<div id="preloader"></div>

<!-- Vendor JS Files -->
<!-- jQuery -->
<script src="<?php echo URL_LIBRARY; ?>plugins/jquery/jquery.min.js"></script>
<!--<script src="--><?php //echo URL_LANDINGPAGE; ?><!--vendor/jquery/jquery.min.js"></script>-->
<script src="<?php echo URL_LANDINGPAGE; ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo URL_LANDINGPAGE; ?>vendor/jquery.easing/jquery.easing.min.js"></script>
<!-- jquery-validation -->
<script src="<?php echo URL_LIBRARY; ?>plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="<?php echo URL_LIBRARY; ?>plugins/jquery-validation/additional-methods.min.js"></script>
<!-- sweetalert2 -->
<script src="<?php echo URL_LIBRARY; ?>plugins/sweetalert2/sweetalert2.all.min.js"></script>
<!-- InputMask -->
<script src="<?php echo URL_LIBRARY; ?>plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>

<script src="<?php echo URL_LANDINGPAGE; ?>vendor/waypoints/jquery.waypoints.min.js"></script>
<script src="<?php echo URL_LANDINGPAGE; ?>vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="<?php echo URL_LANDINGPAGE; ?>vendor/venobox/venobox.min.js"></script>
<script src="<?php echo URL_LANDINGPAGE; ?>vendor/owl.carousel/owl.carousel.min.js"></script>
<script src="<?php echo URL_LANDINGPAGE; ?>vendor/aos/aos.js"></script>

<!-- Template Main JS File -->
<script src="<?php echo URL_LANDINGPAGE; ?>js/main.js"></script>

<script src="<?php echo URL_APP; ?>auth/auth.js"></script>
<script src="<?php echo URL_APP; ?>auth/recuperar_contrasena.js"></script>
<script src="<?php echo URL_HELPERS_JS; ?>SweetAlerts.js" type="text/javascript"></script>


<!-- para el geolocalizador 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<link rel="stylesheet" href="style.css" />-->

</body>

</html>