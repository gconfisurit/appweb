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
                            <h2 id="title_permisos">Permisos</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
<!--                                <input id="btnGestion" type="button" class="btn btn-outline-primary mr-3" value="Gestión permisos" />-->
                                <input id="btnVolver"  type="button" class="btn btn-outline-secondary" value="Volver" />
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <!-- BOX DEL CONTENIDO DE LA VISTA FORMULARIO Y TABLA -->
            <section class="content">

                <!-- BOX TABLA -->
                <div class="card card-info" id="tabla">
                    <div class="card-header">
                        <h3 class="card-title">Permisos</h3>
                    </div>
                    <div class="card-body" style="width:auto;">
                        <form id="permisos_form">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group text-center">
                                        <h3 class="">Seleccione los permisos a habilitar</h3>
                                    </div>
                                </div>
                            </div>


                            <div id="permisos" class="mt-4">
                                <!--se cargan por ajax-->
                            </div>

                            <div class="text-left m-t-10">
                                <!--tipo 0 es roles, tipo 1 es usuarios-->
                                <input type="hidden" name="tipo" id="tipo" value="<?php echo $_GET['t'] ?>"/>
                                <input type="hidden" name="tipoid" id="tipoid" value="<?php echo $_GET['i'] ?>"/>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php require_once("../footer.php");?>
        <!--<script src="--><?php //echo URL_HELPERS_JS; ?><!--Permissions.js" type="text/javascript"></script>-->

        <script type="text/javascript" src="permiso.js"></script><?php
    }
    ?>
</body>
</html>
