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
                            <div class="card card-info"  id="tabla">
                                <div class="card-header">
                                    <h3 class="card-title">Relación de Clientes Inactivos</h3><!-- overflow:scroll; -->
                                </div>

                                <div class="card-body" style="width:auto;">
                                    <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="cliente_data">
                                        <thead style="background-color: #17A2B8;color: white;">
                                        <tr>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('codclie')?>"><?=Strings::titleFromJson('codclie')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('razon_social')?>"><?=Strings::titleFromJson('razon_social')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('rif')?>"><?=Strings::titleFromJson('rif')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('botones_accion')?>"><?=Strings::titleFromJson('botones_accion')?></th>
                                        </tr>
                                        </thead>
                                        <tfoot style="background-color: #ccc;color: white;">
                                        <tr>
                                            <th class="text-center"><?=Strings::titleFromJson('codclie')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('razon_social')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('rif')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('botones_accion')?></th>
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

        </div>
        <!-- /.content-wrapper -->
        <?php require_once("../footer.php");?>
        <script type="text/javascript" src="relacionclientesinactivos.js"></script><?php
    }
    ?>
</body>
</html>
