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
<!-- head -->
<?php require_once("../header.php");?>
<body class="hold-transition sidebar-mini layout-fixed">
    <?php require_once("../menu_lateral.php");
    if (!PermisosHelpers::verficarAcceso( Functions::getNameDirectory() )) {
        include ('../errorNoTienePermisos.php');
    }
    else { ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- BOX DE LA MIGA DE PAN -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h2>Tasa Dolar</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../principal.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Tasa Dolar</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Content Header (Page header) -->
            <section class="content">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-body">
                             <div class="card card-info"  id="tabla">
                                <div class="card-header">
                                    <h3 class="card-title">Relacion</h3><!-- overflow:scroll; -->
                                </div>

                                <div class="card-body" style="width:auto;">
                                    <table class="table table-hover table-condensed table-bordered table-striped text-center" style="width:100%;" id="tasa_data">
                                        <thead style="background-color: #17A2B8;color: white;">
                                        <tr>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('#')?>"><?=Strings::titleFromJson('#')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('fecha')?>"><?=Strings::titleFromJson('fecha')?></th>
                                            <th class="text-center" title="<?=Strings::DescriptionFromJson('tasa')?>"><?=Strings::titleFromJson('tasa')?></th>
                                        </tr>
                                        </thead>
                                        <tfoot style="background-color: #ccc;color: white;">
                                        <tr>
                                            <th class="text-center"><?=Strings::titleFromJson('#')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('fecha')?></th>
                                            <th class="text-center"><?=Strings::titleFromJson('tasa')?></th>
                                        </tr>
                                        </tfoot>
                                        <tbody>
                                        <!-- TD de la tabla que se pasa por ajax -->
                                        </tbody>
                                    </table>
                                    <!-- BOX BOTONES DE REPORTES-->
                                    <div align="center">
                                        <button type="button" class="btn btn-info" id="btn_excel"><?=Strings::titleFromJson('boton_excel')?></button>
                                        <button type="button" class="btn btn-info" id="btn_pdf"><?=Strings::titleFromJson('boton_pdf')?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
        <!-- /.content-wrapper -->
        <?php require_once("../footer.php");?>
        <script type="text/javascript" src="tasadolar.js"></script><?php
    }
    ?>
</body>
</html>
