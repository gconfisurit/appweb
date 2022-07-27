<?php if (strlen(session_id()) < 1)

    session_start(); ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Logistica y Despacho</title>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<!--    <link rel="stylesheet" href="--><?php //echo URL_LIBRARY; ?><!--plugins/datatables-responsive/css/responsive.bootstrap4.min.css">-->
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>plugins/datatables/jquery.columntoggle.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>plugins/daterangepicker/daterangepicker.css">
    <!-- sweetalert2 -->
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>plugins/sweetalert2/sweetalert2.css">
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>style_loader.css">
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>color-palette.css">
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>build/css/form_despacho.css">
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <!-- OrgChart Plugin -->
    <link rel="stylesheet" href="<?php echo URL_LIBRARY;?>plugins/jquery-orgchart/css/jquery.orgchart.css">
    <link rel="stylesheet" href="<?php echo URL_LIBRARY;?>plugins/jquery-orgchart/css/style.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo URL_LIBRARY; ?>dist/css/adminlte.min.css">
</head>