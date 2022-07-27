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
                        <h2>Imprimir Devolución de Notas de Entrega</h2>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                            <li class="breadcrumb-item active">Devolución Notas de Entrega</li>
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
                    <h3 class="card-title">Ingrese la Siguiente Opción</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
                <div  class="card-body" id="minimizar">
                    <form class="form-horizontal" >
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="nrodocumento"><?=Strings::titleFromJson('num_documento')?></label>
                                    <input type="text" class="form-control input-sm" maxlength="20" id="nrodocumento" name="nrodocumento" placeholder="Ingrese numero de la devolución Nota de entrega" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- BOX BOTON DE PROCESO -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-success" id="btn_consultar"><i class="fa fa-search" aria-hidden="true"></i><?=Strings::titleFromJson('boton_consultar')?></button>
                </div>
            </div>

            <!-- BOX TABLA -->
            <div class="card card-info" id="tabla">
                <div class="card-header">
                    <h3 class="card-title">Devolución de Nota de Entrega</h3>
                </div>
                <div class="card-body" style="width:auto;">

                    <table class="table text-center" style="width:100%;">
                        <thead>
                            <tr align="center">
                                <br><br>
                                <div class="row">
                                    <div class="col-5">
                                        <img src="<?=URL_LIBRARY?>build/images/logo.png" alt="" width="142" height="70" border="0" />
                                    </div>
                                    <div class="col-7">
                                        <strong id="descrip_empresa"></strong>
                                        <br>
                                        <span id="rif_empresa"></span>
                                        <br>
                                        <span id="direccion_empresa"></span>
                                        <br>
                                        <span id="telefono_empresa"></span>
                                    </div>
                                </div>
                            </tr>
                            <tr>
                                <td colspan="75">
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                                <td width="45%" ><strong>Cod. Cliente: </strong><span id="cabecera_codclie"></span></td>
                                <td width="25%"><strong>Rif: </strong> <span id="cabecera_rif"></span></td>
                                <td width="30%"><strong>Vendedor: </strong><span id="cabecera_codvend"></span></td>
                            </tr>
                            <tr>
                                <td width="45%" ><strong>Raz&oacute;n Social: </strong><span id="cabecera_rsocial"></span></td>
                                <td width="25%"><strong>Representante: </strong><span id="cabecera_representante"></span></td>
                                <td width="30%"><strong>Tel&eacute;fono: </strong><span id="cabecera_telefono"></span></td>
                                <td><strong>Fecha: </strong><span id="cabecera_fechae"></span></td>
                            </tr>
                            <tr>
                                <td ><strong>Direcci&oacute;n Fiscal: </strong><span id="cabecera_direccion"></span></td>
                                <td ><span id="cabecera_direccion2"></span></td>
                            </tr>
                            <tr>
                                <td height="21" colspan="75" style="text-align: center;"><strong>
                                        <h1>DEVOLUCIÓN NOTA DE ENTREGA</h1>
                                    </strong></td>
                            </tr>
                            <tr>
                                <td height="21" colspan="75" style="text-align: right; color: red;"><strong>
                                        <h1># <span id="numeront"></span></h1>
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="25">
                                    <hr>
                                </td>
                            </tr>
                        </thead>
                    </table>

                    <table class="table text-center" style="width:100%;"  id="notadeentrega_data">
                        <thead style="background-color: #17A2B8;color: white;">
                        <tr>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('codigo_prod')?>"><?=Strings::titleFromJson('codigo_prod')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('descrip_prod')?>"><?=Strings::titleFromJson('descrip_prod')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('cantidad')?>"><?=Strings::titleFromJson('cantidad')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('unidad')?>"><?=Strings::titleFromJson('unidad')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('precio_unitario')?>"><?=Strings::titleFromJson('precio_unitario')?></th>
                            <th id="header_subtotal" class="text-center" title="<?=Strings::DescriptionFromJson('subtotal')?>"><?=Strings::titleFromJson('subtotal')?></th>
                            <th id="header_descuento" class="text-center" title="<?=Strings::DescriptionFromJson('descuento')?>"><?=Strings::titleFromJson('descuento')?></th>
                            <th class="text-center" title="<?=Strings::DescriptionFromJson('total')?>"><?=Strings::titleFromJson('total')?></th>
                        </tr>
                        </thead>
                        <tfoot>
                            <tr id="footer_subtotal">
                                <th height="1" colspan="4"></th>
                                <th height="21" colspan="2" style="text-align: right;"><strong>Sub Total: </strong> <span id="tfoot_subtotal"></span></th>
                            </tr>
                            <tr id="footer_descuentototal">
                                <th height="1" colspan="4"></th>
                                <th height="21" colspan="2" style="text-align: right;"><strong>Descuento: </strong> <span id="tfoot_descuentototal"></span></th>
                            </tr>
                            <tr>
                                <th id="tfoot_observacion" height="1" colspan="4" class="text-left">
                                    <strong>Observaciones: </strong> <span id="tfoot_observacion_value"></span>
                                </th>
                                <th height="21" colspan="2" style="text-align: right;"><strong>Total: </strong> <span id="tfoot_totalnota"></span></th>
                            </tr>
                            <tr>
                                <th id="tfoot_sinderecho" height="21" colspan="6" style="text-align: center;">SIN DERECHO A CR&Eacute;DITO FISCAL. <br>VERIFIQUE SU MERCANCIA, NO SE ACEPTAN RECLAMOS DESPUES DE HABER FIRMADO Y SELLADO ESTA NOTA DE ENTREGA.</th>
                            </tr>
                        </tfoot>

                        <tbody>
                        <!-- TD TABLA LLEGAN POR AJAX -->
                        </tbody>
                    </table>
                    <br><br><br><br>
                    <!-- BOX BOTONES DE REPORTES-->
                    <div align="center">
                        <button type="button" class="btn btn-info" id="btn_excel"><?=Strings::titleFromJson('boton_excel')?></button>
                        <button type="button" class="btn btn-info" id="btn_pdf"><?=Strings::titleFromJson('boton_pdf')?></button>
                    </div>
                </div>
        </section>
    </div>
<?php require_once("../footer.php");?>
    <script type="text/javascript" src="<?php echo URL_HELPERS_JS ?>Number.js"></script>
    <script type="text/javascript" src="notadeentrega.js"></script><?php
}
?>
</body>
</html>
