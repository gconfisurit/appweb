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
                        <h1 class="m-0 text-dark">Reporte de compras</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Inicio</li>
                            <li class="breadcrumb-item">Reporte de compras</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->

                <div class="row mb-2">
                    <div class="col-sm-1 mt-4 form-check-inline">
                    </div><!-- /.col -->
                    <div class="col-sm-3 mt-4 form-check-inline">
                       <!-- <dt class="col-sm-3 text-gray"><?=Strings::titleFromJson('fecha_i')?>:</dt>-->
                       <!-- <input type="text" class="form-control-sm col-8 text-center" id="fechai" value="<?php echo $_GET['fechai']; ?>" readonly>-->
                    </div><!-- /.col -->
                    <div class="col-sm-3 mt-4 form-check-inline">
                        <dt class="col-sm-3 text-gray"><?=Strings::titleFromJson('fecha')?>:</dt>
                        <input type="text" class="form-control-sm col-8 text-center" id="fechaf" value="<?php echo $_GET['fechaf']; ?>" readonly>
                    </div><!-- /.col -->
                    <div class="col-sm-3 mt-4 form-check-inline">
                        <dt class="col-sm-4 text-gray"><?=Strings::titleFromJson('marca_prod')?>:</dt>
                        <input type="text" class="form-control-sm col-sm-8 text-center" id="marca1" value="<?php echo hash_equals('-', $_GET['marca']) ? 'TODAS' : $_GET['marca']; ?>" readonly>

                        <input type="hidden" id="marca" value="<?php echo $_GET['marca']; ?>">
                    </div><!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <!--            <div class="container">-->
            <form id="form_reportecompras" method="post">
                <table id="tabla" class="table table-sm text-center table-condensed table-bordered table-striped table-primary" style="width:100%;">
                    <thead style="color: white; font-weight: bold">
                    <tr id="cells">
                        <th style="width: 10px" rowspan="2"><?=Strings::titleFromJson('#')?></th>
                        <th rowspan="2"><?=Strings::titleFromJson('codigo_prod')?></th>
                        <th rowspan="2"><?=Strings::titleFromJson('descrip_prod')?></th>
                        <th rowspan="2"><?=Strings::titleFromJson('display_por_bulto')?></th>
                        <th colspan="2"><?=Strings::titleFromJson('ultimo_precio_compra')?></th>
                        <th rowspan="2"><?=Strings::titleFromJson('porcentaje_rentabilidad')?></th>
                        <th colspan="2"><?=Strings::titleFromJson('fecha_penultima_compra')?></th>
                        <th colspan="2"><?=Strings::titleFromJson('fecha_ultima_compra')?></th>
                        <th colspan="4"><?=Strings::titleFromJson('ventas_mes_anterior')?></th>
                        <th rowspan="2"><?=Strings::titleFromJson('ventas_total_ult_mes')?></th>
                        <th rowspan="2"><?=Strings::titleFromJson('existencia_actual_bultos')?></th>
                        <th rowspan="2"><?=Strings::titleFromJson('prod_no_vendidos')?></th>
                        <th rowspan="2"><?=Strings::titleFromJson('dias_inventario')?></th>
                        <th rowspan="2"><?=Strings::titleFromJson('sugerido')?></th>
                        <th rowspan="2"><?=Strings::titleFromJson('pedido')?></th>
                    </tr>
                    <tr>
                        <th><?=Strings::titleFromJson('display')?></th>
                        <th><?=Strings::titleFromJson('bulto')?></th>
                        <th><?=Strings::titleFromJson('fecha')?></th>
                        <th><?=Strings::titleFromJson('bultos')?></th>
                        <th><?=Strings::titleFromJson('fecha')?></th>
                        <th><?=Strings::titleFromJson('bultos')?></th>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                    </tr>
                    </thead>
                    <tbody style="background-color: aliceblue">
                    <!-- TD TABLA LLEGAN POR AJAX -->
                    </tbody>
                </table>
            </form>

            <hr>
            <!--</div>-->
            <!-- /.container-fluid -->
            <div class="container">
                <a href="#" class="card-link" id="btn_excel">
                    <?=Strings::titleFromJson('boton_excel')?>
                </a>

                 <a href="#" class="card-link" id="btn_pdf">
                    <?=Strings::titleFromJson('boton_pdf')?>
                </a>
            </div>
            <br>
        </div>

        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <input id="id" type="hidden" value="<?php echo $_SESSION['cedula']; ?>"/>
    <!-- Main Footer -->
    <?php require_once("../footer.php"); ?>
    <script src="<?php echo URL_HELPERS_JS; ?>Number.js" type="text/javascript"></script>

    <script type="text/javascript" src="reportecompras_tabla.js"></script>
</div>
<!-- ./wrapper -->

</body>
</html>


