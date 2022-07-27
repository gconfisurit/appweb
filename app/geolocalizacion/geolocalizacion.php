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
						<h2>Ubicaciones de Sucursales del Grupo Confisur</h2>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
							<li class="breadcrumb-item active">Grupo Confisur</li>
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
                                	<label for="sucursal">Sucursal</label>
									<select class="form-control custom-select" name="sucursal" id="sucursal" style="width: 100%;" required>
                                    <option name="" value="1">DISTRIBUCIONES AJ, C.A</option>
                                    <option name="" value="2">DISTRIBUCIONES CONFISUR, C.A</option>
                                    <option name="" value="3">COMERCIALIZADORA CONFISUR, C.A</option>
                                    <option name="" value="4">COMERCIALIZADORA CONFIGUAYANA, C.A PZO</option>
                                    <option name="" value="5">TRANSPORTE CONFISUR, C.A.</option>
                                    <option name="" value="6">COMERCIALIZADORA CONFIGUAYANA, C.A CBL</option>
                                    <option name="" value="7">CONFISUR SUCRE, C.A</option>
                                    <option name="" value="8">COMERCIALIZADORA CONFISUR PORLAMAR, C.A</option>
                                    <option name="" value="9">INNOVACION TECNOLOGICA INTEC, C.A</option>
                                    <option name="" value="10">TIENDAS A-1 C.A LOS OLIVOS</option>
                                    </select>

							</div>

							</div>
						</form>
					</div>
					<!-- BOX BOTON DE PROCESO -->
					<div class="card-footer">
						<button type="submit" class="btn btn-success" id="btn_consultar" name="btn_consultar"><i class="fa fa-search" aria-hidden="true"></i><?=Strings::titleFromJson('boton_consultar')?></button>
					</div>
				</div>
					</div>
                    <div class='row'>
                         <div class='col-md-12'>
                            <div id='mapa' style='width:100%; height: 1000px;'></div>
                        </div>
                    </div>
				</section>
			</div>
        <?php require_once("../footer.php");?>
        <script>

$( "#btn_consultar").click(function () {

var opc = $("#sucursal").val();
if(opc == '1' | opc == '2' | opc == '3' | opc == '4' | opc == '5' | opc == '9'){

    iniciarMapa(8.24753, -62.81411);            

}else{
    if(opc == '6'){
        
        iniciarMapa(8.131995697158724, -63.548524673765584);

    }else{
        if(opc == '7'){
            
            iniciarMapa(10.651115768186337, -63.257617860255706);

        }else{
            if(opc == '8'){
            
                iniciarMapa(10.961497193223934, -63.9198278148505);

            }else{
                if(opc == '10'){
            
                    iniciarMapa(8.284952348291622, -62.713326717947595);

                }
            }
        }
    }
}

});

            function iniciarMapa(latitud, longitud){

                coordenadas={
                    lng: longitud,
                    lat: latitud
                }

                generarMapa(coordenadas);

            }

            function generarMapa(coordenadas){
                var mapa = new google.maps.Map(document.getElementById('mapa'),{
                   
                    zoom: 17,
                    center: new google.maps.LatLng(coordenadas.lat , coordenadas.lng)
                });

                
               
                    marcador = new google.maps.Marker({
                    map: mapa,
                    draggable: false,
                    position: new google.maps.LatLng(coordenadas.lat , coordenadas.lng)
                    });
                

            }
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=&callback=iniciarMapa"></script>
        <?php
    }
    ?>
</body>
</html>
