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
                        <h2>Devoluciones sin motivo</h2>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                            <li class="breadcrumb-item active">Devoluciones sin motivo</li>
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
                                <div class="form-check form-check-inline pr-3">
                                    <select class="form-control custom-select" name="tipodespacho" id="tipodespacho" style="width: 100%;" required>
                                        <!-- la lista se carga por ajax -->
                                    </select>
                                </div>
                                <div class="form-check form-check-inline">
                                    <select class="form-control custom-select" name="tipodoc" id="tipodoc" style="width: 100%;" required>
                                        <!-- la lista se carga por ajax -->
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
                    <h3 class="card-title">Relación de Devoluciones sin motivo</h3>
                </div>
                <div class="card-body" style="width:auto;">
                    <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="devolucionessinmotivo_data">
                        <thead style="background-color: #17A2B8;color: white;">
                        <tr>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('#')?>"><?=Strings::titleFromJson('#')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('descrip_vend')?>"><?=Strings::titleFromJson('descrip_vend')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('numero_devolucion')?>"><?=Strings::titleFromJson('numero_devolucion')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('numerod')?>"><?=Strings::titleFromJson('numerod')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('fecha_devolucion')?>"><?=Strings::titleFromJson('fecha_devolucion')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('codclie')?>"><?=Strings::titleFromJson('codclie')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('razon_social')?>"><?=Strings::titleFromJson('razon_social')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('monto')?>"><?=Strings::titleFromJson('monto')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('motivo_devolucion')?>">Seleccione <?=Strings::titleFromJson('motivo_devolucion')?></th>
                        </tr>
                        </thead>
                        <tfoot style="background-color: #ccc;color: white;">
                        <tr>
                            <th class="text-center"><?=Strings::titleFromJson('#')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('descrip_vend')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('numero_devolucion')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('numerod')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('fecha_devolucion')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('codclie')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('razon_social')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('monto')?></th>
                            <th class="text-center"><?=Strings::titleFromJson('motivo_devolucion')?></th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <!-- TD TABLA LLEGAN POR AJAX -->
                        </tbody>
                    </table>
                    <!-- BOX BOTONES DE REPORTES-->
                    <div align="center">
                        <button type="button" class="btn btn-info" id="btn_excel"><?=Strings::titleFromJson('boton_excel')?></button>
                        <button type="button" class="btn btn-info" id="btn_pdf"><?=Strings::titleFromJson('boton_pdf')?></button>
                    </div>
                </div>
        </section>
    </div>
<?php require_once("../footer.php");?>
    <script type="text/javascript" src="devolucionessinmotivo.js"></script><?php
}
?>
</body>
</html>
