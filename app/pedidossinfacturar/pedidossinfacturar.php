<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

if (!isset($_SESSION['cedula'])) {
    session_destroy(); Url::redirect(URL_APP);
}
?>
<!DOCTYPE html>
<html>
<?php require_once("../header.php");?>
<body class="hold-transition sidebar-mini layout-fixed">
<?php require_once("../menu_lateral.php");
if (!PermisosHelpers::verficarAcceso( Functions::getNameDirectory() )) {
    include ('../errorNoTienePermisos.php');
}
else { ?>
    <!-- BOX COMPLETO DE LA VISTA -->
    <div class="content-wrapper">
        <!-- BOX DE LA MIGA DE PAN -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h2>Pedidos sin Facturar</h2>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                            <li class="breadcrumb-item active">Pedidos sin Facturar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <!-- BOX DEL CONTENIDO DE LA VISTA FORMULARIO Y TABLA -->
        <section class="content">
            <!-- BOX FORMULARIO -->
            <div class="card card-info"  >
                <div class="card-header">
                    <h3 class="card-title">Seleccione las Siguientes Opciones</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
                <div  class="card-body" id="minimizar">
                    <form class="form-horizontal" >
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-check form-check-inline">
                                    <label for="vutil" class="col-form-label col-sm-4"><?=Strings::titleFromJson('fecha_i')?></label>
                                    <input type="date" class="form-control col-sm-9"  id="fechai" name="fechai" required>
                                </div>&nbsp;&nbsp;&nbsp;&nbsp;
                                <div class="form-check form-check-inline">
                                    <label for="vutil" class="col-form-label col-sm-4"><?=Strings::titleFromJson('fecha_f')?></label>
                                    <input type="date" class="form-control col-sm-9"  id="fechaf" name="fechaf" required>
                                </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                                <div class="form-check form-check-inline">
                                    <select class="form-control custom-select" name="marca" id="marca" style="width: 100%;" required>
                                        <!-- la lista de marcas se carga por ajax -->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- BOX BOTON DE PROCESO -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-success" id="btn_consultar"><i class="fa fa-search" aria-hidden="true"></i><?=Strings::titleFromJson('boton_consultar')?></button>
                </div>
            </div>

            <!-- BOX TABLA -->
            <div class="card card-info" id="tabla">
                <div class="card-header">
                    <h3 class="card-title">Relacion de Pedidos sin Facturar</h3>
                </div>
                <div class="card-body" style="width:auto;">
                    <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="pedidossinfacturar_data">
                        <thead style="background-color: #17A2B8;color: white;">
                        <tr>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('#')?>"><?=Strings::titleFromJson('#')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('fecha')?>"><?=Strings::titleFromJson('fecha')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('marca_prod')?>"><?=Strings::titleFromJson('marca_prod')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('codigo_prod')?>"><?=Strings::titleFromJson('codigo_prod')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('descrip_prod')?>"><?=Strings::titleFromJson('descrip_prod')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('cliente')?>"><?=Strings::titleFromJson('cliente')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('unidad')?>"><?=Strings::titleFromJson('unidad')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('cantidad')?>"><?=Strings::titleFromJson('cantidad')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('total')?>"><?=Strings::titleFromJson('total')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('ruta')?>"><?=Strings::titleFromJson('ruta')?></th>
                        </tr>
                        </thead>
                        <tfoot style="background-color: #ccc;color: white;">
                        <tr>
                            <th class="text-center"><?=Strings::titleFromJson('#')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('fecha')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('marca_prod')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('codigo_prod')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('descrip_prod')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('cliente')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('unidad')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('cantidad')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('total')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('ruta')?></th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <!-- TD TABLA LLEGAN POR AJAX -->
                        </tbody>
                    </table>
                    <div align="center">
                        <br>
                        <p id="cuenta"></p>
                        <br>
                    </div>
                    <!-- BOX BOTONES DE REPORTES-->
                    <div align="center">
                        <button type="button" class="btn btn-info" id="btn_excel"><?=Strings::titleFromJson('boton_excel')?></button>
                        <button type="button" class="btn btn-info" id="btn_pdf"><?=Strings::titleFromJson('boton_pdf')?></button>
                    </div>
                </div>
        </section>
    </div>
<?php require_once("../footer.php");?>
    <script type="text/javascript" src="pedidossinfacturar.js"></script><?php
}
?>
</body>
</html>

