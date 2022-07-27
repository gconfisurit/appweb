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
                            <h2>Creación de Despachos</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Creación de Despacho</li>
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
                        <h3 class="card-title">Formulario para la Creación del Despacho</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
                    <div class="card-body" id="minimizar">
                        <div class="stepwizard">
                            <div class="stepwizard-row setup-panel">
                                <div class="stepwizard-step">
                                    <a href="#step-1" type="button" class="btn btn-primary btn-circle" id="step1">1</a>
                                    <p>Paso 1</p>
                                </div>
                                <div class="stepwizard-step">
                                    <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                    <p>Paso 2</p>
                                </div>

                            </div>
                        </div>
                        <form role="form">
                            <div class="row setup-content" id="step-1">

                                <div class="col-md-12">
                                    <h3> Datos de Translado</h3>
                                    <label><?=Strings::titleFromJson('fecha_despacho')?></label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" >
                                    <label><?=Strings::titleFromJson('chofer')?></label>
                                    <select class="form-control custom-select" id="chofer" name="chofer" style="width: 100%;" >
                                        <!-- los choferes se cargan por ajax -->
                                    </select>
                                    <label><?=Strings::titleFromJson('vehiculo')?></label>
                                    <select class="form-control custom-select" id="vehiculo" name="vehiculo" style="width: 100%;" >
                                        <!-- los vehiculos se cargan por ajax -->
                                    </select>
                                    <label><?=Strings::titleFromJson('destino')?></label>
                                    <input type="text" class="form-control input-sm" maxlength="120" id="destino" name="destino" >
                                    <br />

                                    <!-- <button id="buscarxfact_button"
                                            class="btn btn-primary pull-left verFactura"
                                            onclick="limpiar_campo_documento_modal()"
                                            data-toggle="modal"
                                            data-target="#buscarxfacturaModal"
                                            type="button"><?=Strings::titleFromJson('boton_buscardocumento')?>
                                    </button>

                                    <button id="buscarmercanciapordespachar_button"
                                            class="btn btn-primary porDespachar"
                                            onclick="limpiar_modal_mercancia()"
                                            data-toggle="modal"
                                            data-target="#buscarpordespacharModal"
                                            type="button"><?=Strings::titleFromJson('boton_pordespachar')?>
                                    </button>-->

                                    <button class="btn btn-success nextBtn  float-right" type="button"><?=Strings::titleFromJson('boton_siguiente')?></button>
                                </div>

                            </div>
                            <div class="row setup-content" id="step-2">
                                <div class="col-md-12">
                                    <h3> Inclusión de Documentos</h3>
                                    <div class="form-group">
                                        <label for="factura" class="control-label">Ingrese Número de Documento a Despachar</label>
                                        <!-- <input maxlength="10" type="text" class="form-control" placeholder="Numero de Factura" id="numero_d" name="numero_d" />-->
                                        <select class="form-control custom-select" name="numero_d" id="numero_d" style="width: 100%;" required>
										<!-- la lista de Documentos se carga por ajax -->
                                       </select>
                                    </div>
                                    <!-- <div class="form-group">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="tipo_fact" name="tipo" value="f" checked>
                                            <label for="tipo_fact" class="custom-control-label"><?=Strings::titleFromJson('factura')?></label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="tipo_not" name="tipo" value="n">
                                            <label for="tipo_not" class="custom-control-label"><?=Strings::titleFromJson('nota_de_entrega')?></label>
                                        </div>
                                    </div>-->
                                </div>
                                <div class="col-3">
                                    <button class="btn btn-primary pull-left" id='anadir' type="button"><?=Strings::titleFromJson('boton_anadir')?></button>
                                </div>
                                <div class="col-6">
                                    <div class="container text-center  justify-content-center align-items-center" id="containerProgress">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5><span class="badge bg-success" id="textoBarraProgreso">0 / 0</span></h5>
                                            </div>
                                            <div class="col-6">
                                                <h5><span class="badge bg-info" id="textoBarraProgresoCubicaje">0 / 0</span></h5>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-success" id="barraProgreso" style="width: 0"></div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-info" id="barraProgresoCubicaje" style="width: 0"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <button class="btn btn-success float-right generar" type="button"><?=Strings::titleFromJson('boton_generar')?></button>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>
                <div class="card card-info" id="tabla_documentos_por_despachar">
                    <div class="card-header">
                        <h3 class="card-title">Relación de Facturas a Despachar</h3>
                    </div>
                    <div class="card-body" style="width:auto;">
                        <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="docs_por_despachar_data">
                            <thead style="background-color: #17A2B8;color: white;">
                            <tr>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('numerod')?>"><?=Strings::titleFromJson('numerod')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('fecha')?>"><?=Strings::titleFromJson('fecha')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('razon_social')?>"><?=Strings::titleFromJson('razon_social')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('zona')?>"><?=Strings::titleFromJson('zona')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('ruta')?>"><?=Strings::titleFromJson('ruta')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('monto')?>"><?=Strings::titleFromJson('monto')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('peso')?>"><?=Strings::titleFromJson('peso')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('volumen')?>"><?=Strings::titleFromJson('volumen')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('botones_accion')?>"><?=Strings::titleFromJson('botones_accion')?></th>
                            </tr>
                            </thead>
                            <tfoot style="background-color: #ccc;color: white;">
                            <tr>
                                <th class="text-center"><?=Strings::titleFromJson('numerod')?></th>
                                <th class="text-center"><?=Strings::titleFromJson('fecha')?></th>
                                <th class="text-center"><?=Strings::titleFromJson('razon_social')?></th>
                                <th class="text-center"><?=Strings::titleFromJson('zona')?></th>
                                <th class="text-center"><?=Strings::titleFromJson('ruta')?></th>
                                <th class="text-center"><?=Strings::titleFromJson('monto')?></th>
                                <th class="text-center"><?=Strings::titleFromJson('peso')?></th>
                                <th class="text-center"><?=Strings::titleFromJson('volumen')?></th>
                                <th class="text-center"><?=Strings::titleFromJson('botones_accion')?></th>
                            </tr>
                            </tfoot>
                            <tbody>
                            <!-- TD TABLA LLEGAN POR AJAX -->
                            </tbody>
                        </table>

                    </div>
                </div>
                <input name="ci_usuario" id="ci_usuario" value="<?php echo $_SESSION["cedula"]?>" type="hidden" />
                <div class="card card-info" id="tabla_detalle_despacho" style="display:none;">
                    <div class="card-header">
                        <h3 class="card-title">Detalle del Despacho</h3>
                    </div>
                    <div class="card-body" style="width:auto;">
                        <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="despacho_general_data">
                            <thead style="background-color: #17A2B8;color: white;">
                            <tr>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('codigo_prod')?>"><?=Strings::titleFromJson('codigo_prod')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('descrip_prod')?>"><?=Strings::titleFromJson('descrip_prod')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('cantidad_bultos')?>"><?=Strings::titleFromJson('cantidad_bultos')?></th>
                                <th class="text-center" title="<?=Strings::DescriptionFromJson('cantidad_paquetes')?>"><?=Strings::titleFromJson('cantidad_paquetes')?></th>
                            </tr>
                            </thead>
                            <tfoot style="background-color: #aaa;color: white;">
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center">TOTAL = </th>
                                <th id="cantBul_tfoot" class="text-center"><?=Strings::titleFromJson('cantidad_bultos')?></th>
                                <th id="cantPaq_tfoot" class="text-center"><?=Strings::titleFromJson('cantidad_paquetes')?></th>
                            </tr>
                            </tfoot>
                            <tbody>
                            <!-- TD TABLA LLEGAN POR AJAX -->
                            </tbody>
                        </table>
                        <!-- BOX BOTONES DE REPORTES-->
                        <div align="center">
                            <br>
                            <p id="cuenta"></p>
                            <br>
                        </div>
                        <div align="center">
                            <button type="button" class="btn btn-info" id="btn_newdespacho">Crear Otro Despacho</button>
                            <button type="button" class="btn btn-info" id="btn_pdf"><?=Strings::titleFromJson('boton_pdf')?></button>
                        </div>
                    </div>
            </section>

            <!-- Modal bucar factura -->
            <?php include 'modales/buscar_documento_modal.html' ?>

            <!-- Modal bucar mercancia por despachar -->
            <?php include 'modales/mercancia_por_despachar.html' ?>

        </div>
        <?php require_once("../footer.php"); ?>
        <script type="text/javascript" src="<?php echo URL_HELPERS_JS ?>Number.js"></script>
        <script type="text/javascript" src="mercancia_por_despachar.js"></script>
        <script type="text/javascript" src="despachos_funciones.js"></script>
        <script type="text/javascript" src="despachos.js"></script><?php
    }
    ?>
</body>
</html>
