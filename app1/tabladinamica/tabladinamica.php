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
                        <h2>Tabla Dinámica</h2>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                            <li class="breadcrumb-item active">Tabla Dinámica</li>
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
                            <div class="form-group col-sm-3">
                                <label for="fechai"><?=Strings::titleFromJson('fecha_i')?></label>
                                <input type="date" class="form-control" id="fechai" name="fechai" required>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="fechaf"><?=Strings::titleFromJson('fecha_f')?></label>
                                <input type="date" class="form-control" id="fechaf" name="fechaf" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-group col-sm-3">
                                <label for="edv"><?=Strings::titleFromJson('descrip_vend')?></label>
                                <select class="form-control custom-select" name="vendedor" id="vendedor" style="width: 100%;" required>
                                    <!-- la lista de tipo se carga por ajax -->
                                </select>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="marca"><?=Strings::titleFromJson('marca_prod')?></label>
                                <select class="form-control custom-select" name="marca" id="marca" style="width: 100%;" required>
                                    <!-- la lista de tipo se carga por ajax -->
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">

                            </div>
                            <div class="col-sm-3">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="tipo_fact" name="tipo" value="f" checked>
                                    <label for="tipo_fact" class="custom-control-label"><?=Strings::titleFromJson('factura')?></label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="tipo_not" name="tipo" value="n">
                                    <label for="tipo_not" class="custom-control-label"><?=Strings::titleFromJson('nota_de_entrega')?></label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- BOX BOTON DE PROCESO -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-success" id="btn_consultar">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <?=Strings::titleFromJson('boton_consultar')?>
                    </button>
                </div>
            </div>
        </section>
    </div>
<?php require_once("../footer.php");?>
    <script type="text/javascript" src="tabladinamica.js"></script><?php
}
?>
</body>
</html>
