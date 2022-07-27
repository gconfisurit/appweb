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
                            <h2>Lista de Precio</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Lista de Precio</li>
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
                        <h3 class="card-title">Seleccione las Siguientes Opciones</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
                    <div class="card-body" id="minimizar">
                        <form class="form-horizontal">
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="form-check form-check-inline">
                                        <select class="custom-select" name="depo" id="depo" required>
                                            <!-- la lista de depositos se carga por ajax -->
                                        </select>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <select class="custom-select" name="marca" id="marca" style="width: 100%;" required>
                                            <!-- la lista de marcas se carga por ajax -->
                                        </select>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <select class="custom-select" name="orden" id="orden" style="width: 100%;" required>
                                            <option value="">Ordenar por:</option>
                                            <option value="codprod">Código</option>
                                            <option value="descrip">Descripción</option>
                                            <option value="marca">Marca</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group row">
                                <div class="custom-control custom-checkbox col-sm-1">
                                    <input class="custom-control-input" type="checkbox" id="p1" value="checkbox" name="checkbox">
                                    <label for="p1" class="custom-control-label"><?=Strings::titleFromJson('precio_1')?></label>
                                </div>
                                <div class="custom-control custom-checkbox col-sm-1">
                                    <input class="custom-control-input" type="checkbox" id="p2" value="checkbox" name="checkbox">
                                    <label for="p2" class="custom-control-label"><?=Strings::titleFromJson('precio_2')?></label>
                                </div>
                                <div class="custom-control custom-checkbox col-sm-1">
                                    <input class="custom-control-input" type="checkbox" id="p3" value="checkbox" name="checkbox">
                                    <label for="p3" class="custom-control-label"><?=Strings::titleFromJson('precio_3')?></label>
                                </div>
                                <div class="custom-control custom-checkbox col-sm-1">
                                    <input class="custom-control-input" type="checkbox" id="iva" value="checkbox" name="checkbox" checked="checked">
                                    <label for="iva" class="custom-control-label"><?=Strings::titleFromJson('iva')?></label>
                                </div>
                                <div class="custom-control custom-checkbox col-sm-1">
                                    <input class="custom-control-input" type="checkbox" id="cubi" value="checkbox" name="checkbox">
                                    <label for="cubi" class="custom-control-label"><?=Strings::titleFromJson('cubicaje')?></label>
                                </div>
                                <div class="custom-control custom-checkbox col-sm-1">
                                    <input class="custom-control-input" type="checkbox" id="exis" value="checkbox" name="checkbox" checked="checked">
                                    <label for="exis" class="custom-control-label">Con Existencia</label>
                                </div>
                                <div class="custom-control custom-checkbox col-sm-1">
                                    <input class="custom-control-input" type="checkbox" id="bulto" value="checkbox" name="checkbox" checked="checked">
                                    <label for="bulto" class="custom-control-label">Bulto</label>
                                </div>
                                <div class="custom-control custom-checkbox col-sm-1">
                                    <input class="custom-control-input" type="checkbox" id="paquete" value="checkbox" name="checkbox" checked="checked">
                                    <label for="paquete" class="custom-control-label">Paquete</label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- BOX BOTON DE PROCESO -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success" id="btn_listadeprecio"><i class="fa fa-search" aria-hidden="true"></i><?=Strings::titleFromJson('boton_consultar')?></button>
                    </div>
                </div>

                <!-- BOX TABLA -->
                <div class="card card-info" id="tabla">
                    <div class="card-header">
                        <h3 class="card-title">Clientes</h3>
                    </div>
                    <div class="card-body table-responsive mt-2 p-0" style="width:100%; height:400px;">
                        <table class="table table-sm table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="tablaprecios">
                            <thead style="background-color: #17A2B8;color: white;">
                                <tr>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('codigo_prod')?>"><?=Strings::titleFromJson('codigo_prod')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('descrip_prod')?>"><?=Strings::titleFromJson('descrip_prod')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('marca_prod')?>"><?=Strings::titleFromJson('marca_prod')?></th>

                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('bultos')?>"><?=Strings::titleFromJson('bultos')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('precio1_bulto')?>"><?=Strings::titleFromJson('precio1_bulto')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('precio2_bulto')?>"><?=Strings::titleFromJson('precio2_bulto')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('precio3_bulto')?>"><?=Strings::titleFromJson('precio3_bulto')?></th>

                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('paquetes')?>"><?=Strings::titleFromJson('paquetes')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('precio1_paquete')?>"><?=Strings::titleFromJson('precio1_paquete')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('precio2_paquete')?>"><?=Strings::titleFromJson('precio2_paquete')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('precio3_paquete')?>"><?=Strings::titleFromJson('precio3_paquete')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('cubicaje')?>"><?=Strings::titleFromJson('cubicaje')?></th>
                                </tr>
                            </thead>
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
                </div>

                </div>
        <?php require_once("../footer.php"); ?>
        <script type="text/javascript" src="listadeprecio.js"></script><?php
    }
    ?>
</body>
</html>
