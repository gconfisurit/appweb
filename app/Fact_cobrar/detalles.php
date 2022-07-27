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
						<h2>Facturas Pendientes por Cobrar</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">Facturas por Cobrar</li>
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
						<h3 class="card-title">Relación de Facturas por Cobrar</h3>
					</div>
					<div class="card-body table-responsive mt-2 p-0" style="width:100%; height:400px;">
						<table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="cobrar_data_detalle">
							<thead style="background-color: #17A2B8;color: white;">
								<tr>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('tipo_transaccion')?>"><?=Strings::titleFromJson('tipo_transaccion')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('numerod')?>"><?=Strings::titleFromJson('numerod')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('codclie')?>"><?=Strings::titleFromJson('codclie')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('cliente')?>"><?=Strings::titleFromJson('cliente')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('fecha_emision')?>"><?=Strings::titleFromJson('fecha_emision')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('fecha_despacho')?>"><?=Strings::titleFromJson('fecha_despacho')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('dias_transcurridos')?>"><?=Strings::titleFromJson('dias_transcurridos')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('dias_transcurridos_hoy')?>"><?=Strings::titleFromJson('dias_transcurridos_hoy')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('saldo_pendiente')?>"><?=Strings::titleFromJson('saldo_pendiente')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('saldo_pendiente_$')?>"><?=Strings::titleFromJson('saldo_pendiente_$')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('ruta')?>"><?=Strings::titleFromJson('ruta')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('supervisor')?>"><?=Strings::titleFromJson('supervisor')?></th>
								</tr>
							</thead>
							<tfoot style="background-color: #ccc;color: white;">
								<tr>
									
								    <th class="text-center"><?=Strings::titleFromJson('tipo_transaccion')?></th>	
                                    <th class="text-center"><?=Strings::titleFromJson('numerod')?></th>
									<th class="text-center"><?=Strings::titleFromJson('codclie')?></th>
                                    <th class="text-center"><?=Strings::titleFromJson('cliente')?></th>
									<th class="text-center"><?=Strings::titleFromJson('fecha_emision')?></th>
                                    <th class="text-center"><?=Strings::titleFromJson('fecha_despacho')?></th>
                                    <th class="text-center"><?=Strings::titleFromJson('dias_transcurridos')?></th>
                                    <th class="text-center"><?=Strings::titleFromJson('dias_transcurridos_hoy')?></th>
									<th class="text-center"><?=Strings::titleFromJson('saldo_pendiente')?></th>
									<th class="text-center"><?=Strings::titleFromJson('saldo_pendiente_$')?></th>
									<th class="text-center"><?=Strings::titleFromJson('ruta')?></th>
                                    <th class="text-center"><?=Strings::titleFromJson('supervisor')?></th>
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
        <script type="text/javascript" src="detalles.js"></script>
        <?php
    }
    ?>
</body>
</html>
