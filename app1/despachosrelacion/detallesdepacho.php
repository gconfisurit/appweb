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
						<h2>Relación de Despachos por Facturas</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">Relación de Despachos</li>
						</ol>
					</div>
				</div>
			</div>
		</section>
		<!-- BOX DEL CONTENIDO DE LA VISTA FORMULARIO Y TABLA -->
		<section class="content">
			<!-- BOX FORMULARIO -->
				<!-- BOX TABLA -->
				<div class="card card-info" id="tabla_detalle">
					<div class="card-header">
						<h3 class="card-title">Relación de Despachos</h3>
					</div>
					<div class="card-body table-responsive mt-2 p-0" style="width:100%; height:400px;">
						<table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="cobrar_data_detalle">
							<thead style="background-color: #17A2B8;color: white;">
								<tr>
                                    <th class="text-center" title="Tipo de Documento">Tipo de Documento</th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('numerod')?>"><?=Strings::titleFromJson('numerod')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('codclie')?>"><?=Strings::titleFromJson('codclie')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('cliente')?>"><?=Strings::titleFromJson('cliente')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('fecha_emision')?>"><?=Strings::titleFromJson('fecha_emision')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('monto')?>"><?=Strings::titleFromJson('monto')?></th>
								</tr>
							</thead>
							<tfoot style="background-color: #ccc;color: white;">
								<tr>
									
                                    <th class="text-center">Tipo de Documento</th>
                                    <th class="text-center"><?=Strings::titleFromJson('numerod')?></th>
									<th class="text-center"><?=Strings::titleFromJson('codclie')?></th>
                                    <th class="text-center"><?=Strings::titleFromJson('cliente')?></th>
									<th class="text-center"><?=Strings::titleFromJson('fecha_emision')?></th>
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
							<button type="button" class="btn btn-info" id="btn_excel_detalle"><?=Strings::titleFromJson('boton_excel2')?></button>
						<!--	<button type="button" class="btn btn-info" id="btn_pdf"><?=Strings::titleFromJson('boton_pdf2')?></button>-->
						</div>
					</div>
				</section>
			</div>
        <?php require_once("../footer.php");?>
        <script type="text/javascript" src="detallesdespacho.js"></script>
        <?php
    }
    ?>
</body>
</html>
