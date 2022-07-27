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
						<h2>Estado de Cuentas</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">Estado de Cuentas</li>
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
                                <label for="orden">Código de Cliente</label>
									<input type="text" class="form-control input-sm" maxlength="20" id="cliente" name="cliente" placeholder="Ingrese RIF del Cliente" required>
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
						<h3 class="card-title">Estado de Cuentas</h3>
					</div>
					<div class="card-body table-responsive mt-2 p-0" style="width:100%; height:400px;">
						<table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="estadocuenta_data">
							<thead style="background-color: #17A2B8;color: white;">
								<tr>
									<th class="text-center" title="Tipo de Documento">Tipo de Documento</th>
									<th class="text-center" title="Código">Código</th>
									<th class="text-center" title="Cliente">Cliente</th>
									<th class="text-center" title="Fecha Emision">Fecha Emision</th>
									<th class="text-center" title="Fecha Vencimiento">Fecha Vencimiento</th>	
									<th class="text-center" title="Documento">Documento</th>
									<th class="text-center" title="Documento Afectado">Documento Afectado</th>
									<th class="text-center" title="Descripción">Descripción</th>
									<th class="text-center" title="Débitos">Débitos</th>
									<th class="text-center" title="Créditos">Créditos</th>
                                    <th class="text-center" title="">Saldo</th>
								</tr>
							</thead>
							<tfoot style="background-color: #ccc;color: white;">
								<tr>
									<th class="text-center">Tipo de Documento</th>
									<th class="text-center">Código</th>
									<th class="text-center">Cliente</th>
									<th class="text-center">Fecha Emision</th>
									<th class="text-center">Fecha Vencimiento</th>	
									<th class="text-center">Documento</th>
									<th class="text-center">Documento Afectado</th>
									<th class="text-center">Descripción</th>
									<th class="text-center">Débitos</th>
									<th class="text-center">Créditos</th>
									<th class="text-center">Saldo</th>
						
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
        <script type="text/javascript" src="estadoscuentas.js"></script><?php
    }
    ?>
</body>
</html>
