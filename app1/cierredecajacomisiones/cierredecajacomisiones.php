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
						<h2>CIERRE DE CAJA EDV'S</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">CIERRE DE CAJA - COMSIONES</li>
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
							<div class="col-sm-12">
								<div class="form-check form-check-inline">
									<label for="vutil" class="col-form-label col-sm-4"><?=Strings::titleFromJson('fecha_i')?></label>
									<input type="date" class="form-control col-sm-9"  id="fechai" name="fechai" required>
								</div>&nbsp;&nbsp;&nbsp;&nbsp;
								<div class="form-check form-check-inline">
									<label for="vutil" class="col-form-label col-sm-4"><?=Strings::titleFromJson('fecha_f')?></label>
									<input type="date" class="form-control col-sm-9"  id="fechaf" name="fechaf" required>
								</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
								<div class="form-check form-check-inline">
                                <label for="orden">Vendedores</label>
									<select class="form-control custom-select" name="ruta" id="ruta" style="width: 100%;" required>
										<!-- la lista de rutas se carga por ajax -->
                                    </select>
									</div>
                                    <div class="form-check form-check-inline">
                                	<label for="orden">Tipo de Transacción</label>
									<select class="form-control custom-select" name="tipo" id="tipo" style="width: 100%;" required>
                                    <option name="" value="B">Facturas</option>
                                    <option name="" value="D">Notas de Entrega</option>
                                    </select>
									</div>
								</div>
							</div>
						</form>
					</div>
					<!-- BOX BOTON DE PROCESO -->
					<div class="card-footer">
						<button type="submit" class="btn btn-success" id="btn_consultar" name="btn_consultar"><i class="fa fa-search" aria-hidden="true"></i><?=Strings::titleFromJson('boton_consultar')?></button>
					</div>
				</div>

				<!-- BOX TABLA -->
				<div class="card card-info" id="tabla">
					<div class="card-header">
						<h3 class="card-title">RELACION CIERRE DE CAJA EDV'S</h3>
					</div>
					<div class="card-body" style="width:auto;">
						<table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="comisiones_data">
							<thead style="background-color: #17A2B8;color: white;">
								<tr>
								
									<th class="text-center" title="<?=Strings::DescriptionFromJson('codvend')?>"><?=Strings::titleFromJson('codvend')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('numero_unico')?>"><?=Strings::titleFromJson('numero_unico')?></th>	
									<th class="text-center" title="<?=Strings::DescriptionFromJson('cod_operacion')?>"><?=Strings::titleFromJson('cod_operacion')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('factura')?>"><?=Strings::titleFromJson('factura')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('fecha_emision')?>"><?=Strings::titleFromJson('fecha_emision')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('fecha_pago')?>"><?=Strings::titleFromJson('fecha_pago')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('dias_transcurridos')?>"><?=Strings::titleFromJson('dias_transcurridos')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('codclie')?>"><?=Strings::titleFromJson('codclie')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('cliente')?>"><?=Strings::titleFromJson('cliente')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('monto')?>"><?=Strings::titleFromJson('monto')?></th>
								</tr>
							</thead>
							<tfoot style="background-color: #ccc;color: white;">
								<tr>
									<th class="text-center"><?=Strings::titleFromJson('codvend')?></th>
									<th class="text-center"><?=Strings::titleFromJson('numero_unico')?></th>
									<th class="text-center"><?=Strings::titleFromJson('cod_operacion')?></th>
									<th class="text-center"><?=Strings::titleFromJson('factura')?></th>
									<th class="text-center"><?=Strings::titleFromJson('fecha_emision')?></th>
									<th class="text-center"><?=Strings::titleFromJson('fecha_pago')?></th>
									<th class="text-center"><?=Strings::titleFromJson('dias_transcurridos')?></th>
                                    <th class="text-center"><?=Strings::titleFromJson('codclie')?></th>
                                    <th class="text-center"><?=Strings::titleFromJson('cliente')?></th>
                                    <th class="text-center"><?=Strings::titleFromJson('monto')?></th>
								</tr>
							</tfoot>
							<tbody>
								<!-- TD TABLA LLEGAN POR AJAX -->
							</tbody>
						</table>
						<div align="center">
							<br>
							<p id="cuenta"></p>
							<br>
						</div>
						<!-- BOX BOTONES DE REPORTES-->
						<div align="center">
							<br<p><span id="total_registros"></span></p><br>
							<button type="button" class="btn btn-info" id="btn_excel"><?=Strings::titleFromJson('boton_excel2')?></button>
							<!--<button type="button" class="btn btn-info" id="btn_pdf"><?=Strings::titleFromJson('boton_pdf2')?></button>-->
						</div>
					</div>
				</section>
			</div>
        <?php require_once("../footer.php");?>
		<!-- InputMask -->
        <script src="<?php echo URL_LIBRARY; ?>plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
        <script type="text/javascript" src="cierredecajacomisiones.js"></script><?php
    }
    ?>
</body>
</html>
