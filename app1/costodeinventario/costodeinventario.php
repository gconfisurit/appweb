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
                            <h2>Costos de Inventario</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Costos de Inventario</li>
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
                        <form class="form-horizontal" id="frmCostos" >
                            <div class="form-group row">
                                <div class="form-group col-sm-3">
                                    <label><?=Strings::titleFromJson('marca_prod')?></label>
                                    <select class="custom-select" name="marca" id="marca" style="width: 100%;" required>
                                        <!-- la lista de marcas se carga por ajax -->
                                    </select>
                                </div>
                                <div class="form-group col-sm-4 select2-blue">
                                    <label><?=Strings::titleFromJson('almacen')?></label>
                                    <select class="select2" name="depo[]" id="depo[]" multiple="multiple" data-placeholder="Seleccione Deposito" data-dropdown-css-class="select2-blue" style="width: 100%;" required>
                                        <!-- la lista de almacenes se carga por ajax -->
                                    </select>
                                </div>

                            </div>
                        </form>
                    </div>
                    <!-- BOX BOTON DE PROCESO -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success" id="btn_costodeinventario"><i class="fa fa-search" aria-hidden="true"></i><?=Strings::titleFromJson('boton_consultar')?></button>
                    </div>
                </div>

                <!-- BOX TABLA -->
                <div class="card card-info" id="tabla">
                    <div class="card-header">
                        <h3 class="card-title">Costos de Inventario</h3>
                    </div>
                    <div class="card-body table-responsive mt-2 p-0" style="width:100%; height:400px;">
                        <table class="table table-sm table-hover table-condensed table-bordered table-striped table-head-fixed text-nowrap text-center" id="costodeinventario_data">
                            <thead style="color: black;">
                            <tr>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('codigo_prod')?>"><?=Strings::titleFromJson('codigo_prod')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('descrip_prod')?>"><?=Strings::titleFromJson('descrip_prod')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('marca_prod')?>"><?=Strings::titleFromJson('marca_prod')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('costo_bultos')?>"><?=Strings::titleFromJson('costo_bultos')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('costo_paquete')?>"><?=Strings::titleFromJson('costo_paquete')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('costo_bultosd')?>"><?=Strings::titleFromJson('costo_bultosd')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('costo_paqueted')?>"><?=Strings::titleFromJson('costo_paqueted')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('precio')?>"><?=Strings::titleFromJson('precio')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('bultos')?>"><?=Strings::titleFromJson('bultos')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('paquetes')?>"><?=Strings::titleFromJson('paquetes')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('totalcosto_bultos')?>"><?=Strings::titleFromJson('totalcosto_bultos')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('totalcosto_paquetes')?>"><?=Strings::titleFromJson('totalcosto_paquetes')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('totalcosto_bultosd')?>"><?=Strings::titleFromJson('totalcosto_bultosd')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('totalcosto_paquetesd')?>"><?=Strings::titleFromJson('totalcosto_paquetesd')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('peso')?>"><?=Strings::titleFromJson('tara')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('costototalbs')?>"><?=Strings::titleFromJson('costototalbs')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('costototal')?>"><?=Strings::titleFromJson('costototal')?></th>
                            </tr>
                            </thead>
                            <tbody>
                                <!-- TD TABLA LLEGAN POR AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div align="center">
                        <br>
                        <br><p id="total_items">Total de Item:<code> <span id="total_registros"></span> </code></p><br>
                        <button type="button" class="btn btn-info" id="btn_excel"><?=Strings::titleFromJson('boton_excel')?></button>
                        <button type="button" class="btn btn-info" id="btn_pdf"><?=Strings::titleFromJson('boton_pdf')?></button>
                        <br>
                        <br>
                    </div>
                </div>
            </section>

        </div>
        <?php require_once("../footer.php");?>
        <script type="text/javascript" src="costodeinventario.js"></script><?php
    }
    ?>
</body>
</html>

