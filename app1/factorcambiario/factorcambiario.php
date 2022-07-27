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
<?php require_once("../header.php"); ?>
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
                        <h2>Factor Cambiario</h2>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                            <li class="breadcrumb-item active">Factor Cambiario</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <!-- BOX DEL CONTENIDO DE LA VISTA FORMULARIO Y TABLA -->
        <section class="content">
            <!-- BOX FORMULARIO -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Inserte factor a actualizar</h3>
                </div>
                <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
                <div class="card-body" id="minimizar">
                    <div class="row">
                        <div class="col-4 pb-4">
                            <label for="factor_activo">Factor Activo</label>
                            <input id="factor_activo" type="text" class="form-control col-sm-9 text-right" value="0,00" disabled>
                        </div>
                    </div>
                    <form id="frm_factor" class="form-horizontal">
                        <div class="row">
                            <div class="col-4">
                                <label for="factor_nuevo">Nuevo Factor</label>
                                <input id="factor_nuevo" name="factor_nuevo" type="text" class="form-control col-sm-9 text-right" value="0,00" maxlength="60">
                            </div>
                        </div>
                    </form>
                </div>
                <!-- BOX BOTON DE PROCESO -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-success" id="btn_guardar"><i class="fa fa-check" aria-hidden="true"></i><?=Strings::titleFromJson('boton_actualizar')?></button>
                </div>
            </div>

    </div>
<?php require_once("../footer.php"); ?>

    <script type="text/javascript" src="factorcambiario.js"></script><?php
}
?>
</body>
</html>
