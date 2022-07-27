<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
require_once("../../config/conexion.php");

if (!isset($_SESSION['cedula'])) {
    session_destroy(); Url::redirect(URL_APP);
}
?>
<!DOCTYPE html>
<html>
<!-- head -->
<?php require_once("../header.php");?>
<body class="hold-transition sidebar-mini layout-fixed">
    <?php require_once("../menu_lateral.php");
    if (!PermisosHelpers::verficarAcceso( Functions::getNameDirectory() )) {
        include ('../errorNoTienePermisos.php');
    }
    else { ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-body">
                            <button id="buscarxfact_button"
                                    class="btn btn-primary"
                                    onclick="limpiar_campo_documento_modal()"
                                    data-toggle="modal"
                                    data-target="#buscarxfacturaModal">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                <?=Strings::titleFromJson('boton_buscardocumento')?>
                            </button>

                            <hr>
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Relación de Despachos</h3><!-- overflow:scroll; -->
                                </div>
                                <div class="card-body" style="width:auto;">
                                    <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="relacion_data">
                                        <thead style="background-color: #17A2B8;color: white;">
                                        <tr>
                                            
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('despacho')?>"><?=Strings::titleFromJson('despacho')?></th>
                                             <th class="text-center" title="Fecha">Fecha</th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('usuario')?>"><?=Strings::titleFromJson('usuario')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('cantidad_numerod')?>"><?=Strings::titleFromJson('cantidad_numerod')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('destino')?>"><?=Strings::titleFromJson('destino')?></th>
                                            <th class="text-center" title="Acciones">Acciones</th>
                                           <!-- <th class="text-center" title="Borrar">Borrar</th>
                                            <th class="text-center" title="Ver">Ver</th>-->
                                            
                                             </tr>
                                        </thead>
                                        <tfoot style="background-color: #ccc;color: white;">
                                        <tr>
                                            <th class="text-center"><?=Strings::titleFromJson('despacho')?></th>
                                             <th class="text-center">Fecha</th>
                                            <th class="text-center"><?=Strings::titleFromJson('usuario')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('cantidad_numerod')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('destino')?></th>
                                            <th class="text-center" >Acciones</th>
                                           <!-- <th class="text-center" >Borrar</th>
                                            <th class="text-center" >Ver</th>-->
                                        </tr>
                                        </tfoot>
                                        <tbody>
                                        <!-- TD de la tabla que se pasa por ajax -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <!-- Modal bucar documento -->
            <?php include '../despachos/modales/buscar_documento_modal.html' ?>

            <!-- Modal principal editar despachos -->
            <?php include 'modales/editar_principal_despachos_modal.html' ?>

            <!-- Modal agregar documento despachos -->
            <?php include 'modales/agregar_documento_despacho.html' ?>

            <!-- Modal editar chofer, destino y vehiculo despachos -->
            <?php include 'modales/editar_chofer_destino_despacho.html' ?>

            <!-- Modal editar documento de un despachos -->
            <?php include 'modales/editar_documento_despacho.html' ?>

            <!-- Modal ver productos de un despcho -->
            <?php include 'modales/ver_productos_factura_modal.html' ?>

            <!-- Modal tipo de reporte -->
            <?php include 'modales/tipo_reporte.html' ?>

        </div>
        <!-- /.content-wrapper -->
        <?php require_once("../footer.php");?>
        <script type="text/javascript" src="<?php echo URL_HELPERS_JS ?>Number.js"></script>
        <script type="text/javascript" src="despachosrelacion.js"></script><?php
    }
    ?>
</body>
</html>
