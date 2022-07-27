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
                            <button class="btn btn-primary" id="add_button" onclick="mostrar()" data-toggle="modal" data-target="#usuarioModal"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Usuario</button>
                            <hr>
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Usuarios Registrados</h3><!-- overflow:scroll; -->
                                </div>
                                <div class="card-body" style="width:auto;">
                                    <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="usuario_data">
                                        <thead style="background-color: #17A2B8;color: white;">
                                            <tr>
                                                <td class="text-center" title="<?=Strings::DescriptionFromJson('dni')?>"><?=Strings::titleFromJson('dni')?></td>
                                                <td class="text-center" title="<?=Strings::DescriptionFromJson('usuario')?>"><?=Strings::titleFromJson('usuario')?></td>
                                                <td class="text-center" title="<?=Strings::DescriptionFromJson('nombre_apellido')?>"><?=Strings::titleFromJson('nombre_apellido')?></td>
                                                <td class="text-center" title="<?=Strings::DescriptionFromJson('correo_electronico')?>"><?=Strings::titleFromJson('correo_electronico')?></td>
                                                <td class="text-center" title="<?=Strings::DescriptionFromJson('rol')?>"><?=Strings::titleFromJson('rol')?></td>
                                                <td class="text-center" title="<?=Strings::DescriptionFromJson('fecha_registro')?>"><?=Strings::titleFromJson('fecha_registro')?></td>
                                                <td class="text-center" title="<?=Strings::DescriptionFromJson('fecha_ultimo_ingreso')?>"><?=Strings::titleFromJson('fecha_ultimo_ingreso')?></td>
                                                <td class="text-center" title="<?=Strings::DescriptionFromJson('botones_accion')?>"><?=Strings::titleFromJson('botones_accion')?></td>
                                            </tr>
                                        </thead>
                                        <tfoot style="background-color: #ccc;color: white;">
                                            <tr>
                                                <td class="text-center"><?=Strings::titleFromJson('dni')?></td>
                                                <td class="text-center"><?=Strings::titleFromJson('usuario')?></td>
                                                <td class="text-center"><?=Strings::titleFromJson('nombre_apellido')?></td>
                                                <td class="text-center"><?=Strings::titleFromJson('correo_electronico')?></td>
                                                <td class="text-center"><?=Strings::titleFromJson('rol')?></td>
                                                <td class="text-center"><?=Strings::titleFromJson('fecha_registro')?></td>
                                                <td class="text-center"><?=Strings::titleFromJson('fecha_ultimo_ingreso')?></td>
                                                <td class="text-center"><?=Strings::titleFromJson('botones_accion')?></td>
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

            <!-- MODAL CREAR O EDITAR USUARIO -->
            <?php include 'modales/crear_o_editar_usuario.html' ?>
        </div>
        <!-- /.content-wrapper -->
        <?php require_once("../footer.php");?>
        <script type="text/javascript" src="usuario.js"></script><?php
    }
    ?>
</body>
</html>
