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
						<h2>Disponible en Almacen</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">Disponible en Almacen</li>
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
                                	<label for="marcas">Mascas</label>
									<select class="form-control custom-select" name="marcas" id="marcas" style="width: 100%;" required>
                                    
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
						<h3 class="card-title">Relacion de Disponibilidad en Almacenes</h3>
					</div>
					<div class="card-body" style="width:auto;">
						<table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="inventario_data">
							<thead style="background-color: #17A2B8;color: white;">
								<tr>
								
									<th class="text-center" title="<?=Strings::DescriptionFromJson('codigo_prod')?>"><?=Strings::titleFromJson('codigo_prod')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('descrip_prod')?>"><?=Strings::titleFromJson('descrip_prod')?></th>	
									<th class="text-center" title="<?=Strings::DescriptionFromJson('marca_prod')?>"><?=Strings::titleFromJson('marca_prod')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('bulto_01')?>"><?=Strings::titleFromJson('bulto_01')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('paquete_01')?>"><?=Strings::titleFromJson('paquete_01')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('bulto_03')?>"><?=Strings::titleFromJson('bulto_03')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('paquete_03')?>"><?=Strings::titleFromJson('paquete_03')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('bulto_13')?>"><?=Strings::titleFromJson('bulto_13')?></th>
                                    <th class="text-center" title="<?=Strings::DescriptionFromJson('paquete_13')?>"><?=Strings::titleFromJson('paquete_13')?></th>
								</tr>
							</thead>
							<tfoot style="background-color: #ccc;color: white;">
								<tr>
									<th class="text-center"><?=Strings::titleFromJson('codigo_prod')?></th>
									<th class="text-center"><?=Strings::titleFromJson('descrip_prod')?></th>
									<th class="text-center"><?=Strings::titleFromJson('marca_prod')?></th>
									<th class="text-center"><?=Strings::titleFromJson('bulto_01')?></th>
									<th class="text-center"><?=Strings::titleFromJson('paquete_01')?></th>
									<th class="text-center"><?=Strings::titleFromJson('bulto_03')?></th>
									<th class="text-center"><?=Strings::titleFromJson('paquete_03')?></th>
                                    <th class="text-center"><?=Strings::titleFromJson('bulto_13')?></th>
                                    <th class="text-center"><?=Strings::titleFromJson('paquete_13')?></th>
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
							<button type="button" class="btn btn-info" id="btn_excel"><?=Strings::titleFromJson('boton_excel')?></button>
						<!--	<button type="button" class="btn btn-info" id="btn_pdf"><?=Strings::titleFromJson('boton_pdf2')?></button>-->
						</div>
					</div>
				</section>
			</div>
        <?php require_once("../footer.php");?>
		<!-- InputMask -->
        <script src="<?php echo URL_LIBRARY; ?>plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
        <script type="text/javascript" src="disponiblealmacen.js"></script>
        <?php
    }
    ?>
</body>
</html>
