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
                        <h1 class="m-0 text-dark">Tabla Dinámica
                            (<?php
                            switch ($_GET['t']) {
                                case 'f': echo Strings::titleFromJson('factura'); break;
                                case 'n': echo Strings::titleFromJson('nota_de_entrega'); break;
                            }
                            ?>)
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Inicio</li>
                            <li class="breadcrumb-item">Tabla Dinámica</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->

                <input type="hidden" id="fechai" value="<?php echo $_GET['fechai']; ?>">
                <input type="hidden" id="fechaf" value="<?php echo $_GET['fechaf']; ?>">
                <input type="hidden" id="vendedor" value="<?php echo $_GET['vendedor']; ?>">
                <input type="hidden" id="marca" value="<?php echo $_GET['marca']; ?>">
                <input type="hidden" id="tipo" value="<?php echo $_GET['t']; ?>">

                <div class="row mb-2">
                    <div class="col-sm-3 mt-4 form-check-inline">
                        <dt class="col-sm-3 text-gray">Desde:</dt>
                        <input type="text" class="form-control-sm col-8 text-center" id="fi" value="<?php echo date(FORMAT_DATE, strtotime($_GET['fechai'])); ?>" readonly>
                    </div><!-- /.col -->
                    <div class="col-sm-3 mt-4 form-check-inline">
                        <dt class="col-sm-4 text-gray">Hasta:</dt>
                        <input type="text" class="form-control-sm col-sm-8 text-center" id="ff" value="<?php echo date(FORMAT_DATE, strtotime($_GET['fechaf'])); ?>" readonly>
                    </div><!-- /.col -->
                    <div class="col-sm-2 mt-4 form-check-inline">
                        <dt class="col-sm-7 text-gray">EDV:</dt>
                        <input type="text" class="form-control-sm col-sm-5  text-center" id="edv" value="<?php echo (!hash_equals('-', $_GET['vendedor'])) ? $_GET['vendedor'] : 'Todos'; ?>" readonly>
                    </div><!-- /.col -->
                    <div class="col-sm-2 mt-4 form-check-inline">
                        <dt class="col-sm-7 text-gray">Marca:</dt>
                        <input type="text" class="form-control-sm col-sm-5  text-center" id="m" value="<?php echo (!hash_equals('-', $_GET['marca'])) ? $_GET['marca'] : 'Todas'; ?>" readonly>
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
                <thead style="color: white; font-weight: bold">
                <tr id="cells">
                    <th class="small align-middle"><?=Strings::titleFromJson('#')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('codvend')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('descrip_vend')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('clase')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('tipo_transaccion')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('numero_operacion')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('codclie')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('razon_social')?></th>
                    <th class="small align-middle">Tipo de Cliente</th>
                    <th class="small align-middle"><?=Strings::titleFromJson('clasificacion')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('codigo_prod')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('descrip_prod')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('marca_prod')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('cantidad')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('unidad')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('paquetes')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('bultos')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('peso')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('instancia')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('monto_dolars')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('descuento')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('tasa')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('monto_bs')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('fecha')?></th>
                    <th class="small align-middle"><?=Strings::titleFromJson('mes')?></th>
                </tr>
                </thead>
                <tbody style="background-color: aliceblue">
                <!-- TD TABLA LLEGAN POR AJAX -->
                </tbody>
            </table>

            <hr>

            <table id="tabla1" class="table table-sm text-center table-condensed table-bordered table-striped table-primary" style="width:50%;">
                <thead style="color: white; font-weight: bold">
                <tr id="cells">
                    <th class="small align-middle"><?=strtoupper( Strings::titleFromJson('ruta') )?></th>
                    <th class="small align-middle"><?=strtoupper( Strings::titleFromJson('codclie') )?></th>
                    <th class="small align-middle"><?=strtoupper( Strings::titleFromJson('razon_social') )?></th>
                    <th class="small align-middle"><?=strtoupper( Strings::titleFromJson('descuento_dolars') )?></th>
                    <th class="small align-middle"><?=strtoupper( Strings::titleFromJson('tasa') )?></th>
                    <th class="small align-middle"><?=strtoupper( Strings::titleFromJson('monto_bs') )?></th>
                    <th class="small align-middle"><?=strtoupper( Strings::titleFromJson('descuento') )?></th>
                    <th class="small align-middle"><?=strtoupper( Strings::titleFromJson('tipo') )?></th>
                    <th class="small align-middle"><?=strtoupper( Strings::titleFromJson('fecha') )?></th>
                </tr>
                </thead>
                <tbody style="background-color: aliceblue">
                <!-- TD TABLA LLEGAN POR AJAX -->
                </tbody>
            </table>

            <hr>
            <!--</div>-->
            <!-- /.container-fluid -->
            <div class="container">
                <a href="tabladinamica_excel.php?&fechai=<?php echo $_GET['fechai']; ?>&fechaf=<?php echo $_GET['fechaf']; ?>&vendedor=<?php echo $_GET['vendedor']; ?>&marca=<?php echo $_GET['marca']; ?>&t=<?php echo $_GET['t']; ?>" class="card-link" id="btn_excel">
                    <?=Strings::titleFromJson('boton_excel')?>
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

    <script type="text/javascript" src="tabladinamica_tabla.js"></script>
</div>
<!-- ./wrapper -->

</body>
</html>


