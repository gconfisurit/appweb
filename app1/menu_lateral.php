<!-- MENU SUPERIOR TOP -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?php echo URL_APP; ?>principal.php" class="nav-link">Home</a>
        </li>
    </ul>
</nav>
<!-- BOX COMPLETO MENU LATERAL -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- LOGO SUPERIOR MENU -->
    <a href="<?php echo URL_APP; ?>principal.php" class="brand-link">
    <center>
	  <img src="<?php echo URL_LIBRARY; ?>dist/img/logo_empresa.png" class="center-block" alt="Logo de Empresa" width="200" height="90" style="opacity: .99">
    </center>
       
    </a>
    <!-- PERFIL DE USUARIO -->
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo URL_LIBRARY; ?>dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo $_SESSION["nomper"]; ?></a>
                <input id="id" type="hidden" value="<?php echo $_SESSION['cedula']; ?>"/>
            </div>
        </div>
        <!-- MENU LATERAL -->
        <nav id="content_menu" class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- INICIO -->
                <li class="nav-item">
                    <a href="<?php echo URL_APP; ?>principal.php" class="nav-link">
                        <i class="fas fa-home nav-icon"></i>
                        <p>Inicio</p>
                    </a>
                </li>

                <!-- CERRAR SESION -->
                <li class="nav-item">
                    <a href="<?php echo URL_APP; ?>destruir.php" class="nav-link">
                        <i class="fas fa-power-off"></i>
                        <p> Cerrar sesión</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>