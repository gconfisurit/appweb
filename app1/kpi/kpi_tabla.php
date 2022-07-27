<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once("../header.php"); ?>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
        <div class="container">
            <a href="#" class="navbar-brand">
                <img src="<?php echo URL_LIBRARY; ?>dist/img/AdminLTELogo.png " alt="AdminLTE Logo"
                     class="brand-image img-circle elevation-3"
                     style="opacity: .8">
                <span class="brand-text font-weight-light"><?=Strings::titleFromJson('nombre_app')?></span>
            </a>

            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Right navbar links -->
            <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                <li class="nav-item">
                    <input type="button" class="btn btn-primary" value="Cerrar Ventana" onClick="self.close();" onKeyPress="self.close();" />
                </li>
            </ul>
        </div>
    </nav>
    <!-- /.navbar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark"> KPI <small>(Key Performance Indicator)</small></h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Inicio</li>
                            <li class="breadcrumb-item">KPI</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->

                <div class="row mb-2">
                    <div class="col-sm-3 mt-4 form-check-inline">
                        <dt class="col-sm-3 text-gray">Desde:</dt>
                        <input type="text" class="form-control-sm col-8 text-center" id="fechai" value="<?php echo $_GET['fechai']; ?>" readonly>
                    </div><!-- /.col -->
                    <div class="col-sm-3 mt-4 form-check-inline">
                        <dt class="col-sm-4 text-gray">Hasta:</dt>
                        <input type="text" class="form-control-sm col-sm-8 text-center" id="fechaf" value="<?php echo $_GET['fechaf']; ?>" readonly>
                    </div><!-- /.col -->
                    <div class="col-sm-2 mt-4 form-check-inline">
                        <dt class="col-sm-7 text-gray">Días Habiles:</dt>
                        <input type="text" class="form-control-sm col-sm-5  text-center" id="d_habiles" value="<?php echo $_GET['d_habiles']; ?>" readonly>
                    </div><!-- /.col -->
                    <div class="col-sm-2 mt-4 form-check-inline">
                        <dt class="col-sm-7 text-gray">Días Transc:</dt>
                        <input type="text" class="form-control-sm col-sm-5  text-center" id="d_trans" value="<?php echo $_GET['d_trans']; ?>" readonly>
                    </div><!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <!--            <div class="container">-->
            <table id="tabla" class="table table-sm text-center table-condensed table-bordered table-striped table-responsive table-primary" style="width:100%;">
                <thead style="color: white;">
                <tr style="background-color: teal">
                    <th class="small align-middle" colspan="1"  id="cabecera_rutas">Rutas</th>
                    <th class="small align-middle" colspan="4"  id="cabecera_activacion">Activaci&oacute;n</th>
                    <th class="small align-middle" colspan="7"  id="cabecera_efectividad">Efectividad</th>
                    <th class="small align-middle" colspan="15" id="cabecera_ventas">Ventas</th>
                </tr>
                <tr id="cells">
                    <th class="small align-middle">Rutas</th>
                    <th class="small align-middle">Maestro de Clientes</th>
                    <th class="small align-middle">Clientes Activados</th>
                    <th class="small align-middle">% Activación Clientes Alcanzado</th>
                    <th class="small align-middle">Clientes Pendientes</th>
                    <th class="small align-middle">Frecuencia de Visita</th>
                    <th class="small align-middle">Objetivo Facturas más Notas Mensual</th>
                    <th class="small align-middle">Total Facturas Realizadas</th>
                    <th class="small align-middle">Total Notas Realizadas</th>
                    <th class="small align-middle">Devoluciones Realizadas (nt + fac)</th>
                    <th class="small align-middle">Total Devoluciones Realizadas ($)</th>
                    <th class="small align-middle">% Efectividad Alcanzada a la Fecha</th>
                    <th class="small align-middle">Objetivo (Bulto)</th>
                    <th class="small align-middle">Logro (Bulto)</th>
                    <th class="small align-middle">%Alcanzado (Bulto)</th>
                    <th class="small align-middle">Objetivo (Kg)</th>
                    <th class="small align-middle">Logro (Kg)</th>
                    <th class="small align-middle">%Alcanzado (Kg)</th>
                    <th class="small align-middle">Real Drop Size ($)</th>
                    <th class="small align-middle">Objetivo Total Ventas ($)</th>
                    <th class="small align-middle">Total Logro Ventas en ($)</th>
                    <th class="small align-middle">%Alcanzado ($)</th>
                    <th class="small align-middle">Ventas PEPSICO ($)</th>
                    <th class="small align-middle">% Venta PEPSICO</th>
                    <th class="small align-middle">Ventas Complementaria ($)</th>
                    <th class="small align-middle">% Venta Complementaria</th>
                    <th class="small align-middle">Cobranza Rebajadas (Bs)</th>
                </tr>
                </thead>
                <tbody style="background-color: aliceblue">
                <!-- TD TABLA LLEGAN POR AJAX -->
                </tbody>
            </table>

            <div class="row text-center">
                <div class="col-sm-1">
                    <div class="bg-danger color-palette"><span>ROJO: 0 - 50% </span></div>
                </div>
                <div class="col-sm-1">
                    <div class="bg-warning color-palette"><span>AMARILLO: 51 - 80%</span></div>
                </div>
                <div class="col-sm-1">
                    <div class="bg-success color-palette"><span>VERDE: 81 - 100% </span></div>
                </div>
            </div>

            <hr>
            <!--</div>-->
            <!-- /.container-fluid -->
            <div class="container">
                <a href="kpi_excel.php?&fechai=<?php echo $_GET['fechai']; ?> &fechaf=<?php echo $_GET['fechaf']; ?>&d_habiles=<?php echo $_GET['d_habiles']; ?>&d_trans=<?php echo $_GET['d_trans']; ?>" class="card-link" id="btn_excel">
                    <?=Strings::titleFromJson('boton_excel')?>
                </a>

               <!-- <a href="kpi_pdf.php?&fechai=<?php /*echo $_GET['fechai']; */?>&fechaf=<?php /*echo $_GET['fechaf']; */?>&d_habiles=<?php /*echo $_GET['d_habiles']; */?>&d_trans=<?php /*echo $_GET['d_trans']; */?>" class="card-link" id="btn_pdf" target="_blank">
                    <?=Strings::titleFromJson('boton_pdf')?>
                </a>-->
            </div>
        </div>

        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- MODAL  DETALLE DE EDV -->
    <?php include 'modales/detalle_edv.html' ?>

    <!-- MODAL  LISTA DE CLIENTES SEGUN SU OPCION A MOSTRAR (MAESTRO, CLIENTES ACTIVADOS, CLIENTES PENDIENTES) -->
    <?php include 'modales/lista_clientes.html' ?>

    <!-- MODAL  LISTA DETALLE DE CLIENTES Y PRODUCTOS POR EDV Y MARCA -->
    <?php include 'modales/detalle_activacion_por_marca.html' ?>

    <!-- MODAL  LISTA DETALLE DE DOCUMENTOS SEGUN OPCION A MOSTRAR (FACT REALIZADAS, NOTAS REALIZADAS, DEVOLUCIONES, COBRANZAS REBAJADAS) -->
    <?php include 'modales/lista_documentos.html' ?>

    <input id="id" type="hidden" value="<?php echo $_SESSION['cedula']; ?>"/>
    <!-- Main Footer -->
    <?php require_once("../footer.php"); ?>
    <script src="<?php echo URL_HELPERS_JS; ?>Number.js" type="text/javascript"></script>

    <script type="text/javascript" src="kpi_tabla.js"></script>
</div>
<!-- ./wrapper -->

</body>
</html>


