<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Error</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?php echo URL_APP; ?>principal.php">Inicio</a></li>
                            <li class="breadcrumb-item active">Error</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="error-page">
                <h2 class="headline text-warning">Error! </h2>

                <div class="error-content">
                    <h3><i class="fas fa-exclamation-triangle text-warning"></i> Usted no tiene permisos.</h3>

                    <p>
                        No posee permisos para acceder a este módulo. !
                        Intente <a href="<?php echo URL_APP; ?>principal.php">volver al Inicio</a> ó seleccione otra opción del menú.
                    </p>

                </div>
                <!-- /.error-content -->
            </div>
            <!-- /.error-page -->
        </section>
        <!-- /.content -->
    </div>
<?php require_once("../footer.php"); ?>

