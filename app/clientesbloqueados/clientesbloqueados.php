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
						<h2>Clientes Bloqueados</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">Clientes Bloqueados</li>
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
					<h3 class="card-title">Seleccione un Vendedor</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
						<button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove"><i class="fas fa-times"></i></button>
					</div>
				</div>
				<!-- BOX CARD QUE CONTIENE EL FORMULARIO QUE SE CIERRA -->
				<div  class="card-body" id="minimizar">
					<form class="form-horizontal" >
						<div class="form-check form-check-inline">
							<select class="form-control custom-select" name="vendedor" id="vendedor" style="width: 100%;" required>
								<!-- la lista de vendedores se carga por ajax -->
                            </select>
                        </div>
                    </form>
					</div>
					<!-- BOX BOTON DE PROCESO -->
					<div class="card-footer">
						<button type="submit" class="btn btn-success" id="btn_clientesbloqueados"><i class="fa fa-search" aria-hidden="true"></i><?=Strings::titleFromJson('boton_consultar')?></button>
					</div>
				</div>

				<!-- BOX TABLA -->
				<div class="card card-info" id="tabla">
					<div class="card-header">
						<h3 class="card-title">Clientes Bloqueados</h3>
					</div>
					<div class="card-body" style="width:auto;">
						<table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="clientesbloqueados_data">
							<thead style="background-color: #17A2B8;color: white;">
								<tr>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('codclie')?>"><?=Strings::titleFromJson('codclie')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('razon_social')?>"><?=Strings::titleFromJson('razon_social')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('rif')?>"><?=Strings::titleFromJson('rif')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('direccion')?>"><?=Strings::titleFromJson('direccion')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('estatus')?>"><?=Strings::titleFromJson('estatus')?></th>
									<th class="text-center" title="<?=Strings::DescriptionFromJson('dia_visita')?>"><?=Strings::titleFromJson('dia_visita')?></th>
								</tr>
							</thead>
							<tfoot style="background-color: #ccc;color: white;">
								<tr>
									<th class="text-center"><?=Strings::titleFromJson('codclie')?></th>
									<th class="text-center"><?=Strings::titleFromJson('razon_social')?></th>
									<th class="text-center"><?=Strings::titleFromJson('rif')?></th>
									<th class="text-center"><?=Strings::titleFromJson('direccion')?></th>
									<th class="text-center"><?=Strings::titleFromJson('estatus')?></th>
									<th class="text-center"><?=Strings::titleFromJson('dia_visita')?></th>
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
							<button type="button" class="btn btn-info" id="btn_excel"><?=Strings::titleFromJson('boton_excel')?></button>
							<button type="button" class="btn btn-info" id="btn_pdf"><?=Strings::titleFromJson('boton_pdf')?></button>
						</div>
					</div>
				</div>
			</section>
		</div>
		<?php require_once("../footer.php");?>
		<script type="text/javascript" src="clientesbloqueados.js"></script><?php
    }
    ?>
</body>
</html>
