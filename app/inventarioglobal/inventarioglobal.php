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
                            <h2>Inventario Global</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Inventario Global</li>
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
                        <h3 class="card-title">Seleccione los Almacenes</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
                    <div class="card-body" id="minimizar">
                        <form id="frminventario" class="form-horizontal">
                            <div class="form-group col-sm-4 select2-blue">
                                <label><?=Strings::titleFromJson('almacen')?></label>
                                <select class="select2 depo" name="depo[]" id="depo[]" multiple="multiple" data-placeholder="Seleccione Almacen" data-dropdown-css-class="select2-blue" style="width: 100%;" required>
                                    <!-- la lista de almacenes se carga por ajax -->
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <input type="checkbox" id="checkbox"> Seleccionar Todos
                            </div>
                        </form>

                    </div>
                    <!-- BOX BOTON DE PROCESO -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success" id="btn_inventarioglobal"><i class="fa fa-search" aria-hidden="true"></i><?=Strings::titleFromJson('boton_consultar')?></button>
                    </div>
                </div>
                <!-- BOX TABLA -->
                <div class="card card-info" id="tabla">
                    <div class="card-header">
                        <h3 class="card-title">Inventario Global</h3>
                    </div>
                    <div class="card-body">
                        <div class="row table-responsive"  style="height: 300px;">
                            <table class="table table-hover table-condensed table-bordered table-striped table-sm table-head-fixed text-nowrap text-center" style="width:100%;" id="inventarioglobal_data">
                                <thead >
                                <tr>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('codigo_prod')?>"><?=Strings::titleFromJson('codigo_prod')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('descrip_prod')?>"><?=Strings::titleFromJson('descrip_prod')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('cantidad_bultos_despachar')?>"><?=Strings::titleFromJson('cantidad_bultos_despachar')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('cantidad_paquetes_despachar')?>"><?=Strings::titleFromJson('cantidad_paquetes_despachar')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('cantidad_bultos_sistema')?>"><?=Strings::titleFromJson('cantidad_bultos_sistema')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('cantidad_paquetes_sistema')?>"><?=Strings::titleFromJson('cantidad_paquetes_sistema')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('total_inv_bultos')?>"><?=Strings::titleFromJson('total_inv_bultos')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('total_inv_paquetes')?>"><?=Strings::titleFromJson('total_inv_paquetes')?></th>
                                </tr>
                                </thead>
                                <tfoot style="background-color: #ccc;color: white;">
                                <tr>
                                    <th colspan="2" class="text-right">Total=</th>
                                    <th class="text-center" id="tfoot_cantbul_x_des"><?=Strings::titleFromJson('cantidad_bultos_despachar')?></th>
                                    <th class="text-center" id="tfoot_cantpaq_x_des"><?=Strings::titleFromJson('cantidad_paquetes_despachar')?></th>
                                    <th class="text-center" id="tfoot_cantbul_sistema"><?=Strings::titleFromJson('cantidad_bultos_sistema')?></th>
                                    <th class="text-center" id="tfoot_cantpaq_sistema"><?=Strings::titleFromJson('cantidad_paquetes_sistema')?></th>
                                    <th class="text-center" id="tfoot_totalbul_inv"><?=Strings::titleFromJson('total_inv_bultos')?></th>
                                    <th class="text-center" id="tfoot_totalpaq_inv"><?=Strings::titleFromJson('total_inv_paquetes')?></th>
                                </tr>
                                </tfoot>
                                <tbody>
                                <!-- TD TABLA LLEGAN POR AJAX -->
                                </tbody>
                            </table>
                        </div>
                         <!-- BOX BOTONES DE REPORTES-->
                        <div align="center">
                            <br>
                            <p id="cuenta"></p>
                            <br>
                        </div>
                        <div align="center">
                            <button type="button" class="btn btn-info" id="btn_excel"><?=Strings::titleFromJson('boton_excel')?></button>
                            <button type="button" class="btn btn-info" id="btn_pdf"><?=Strings::titleFromJson('boton_pdf')?></button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php require_once("../footer.php"); ?>
        <script type="text/javascript" src="inventarioglobal.js"></script><?php
    }
    ?>
</body>
</html>