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
        <img src="<?php echo URL_LIBRARY; ?>dist/img/logo_aj.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <!--<span class="brand-text font-weight-light">Logística y Despacho</span>-->
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

                <!-- CLIENTES -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>Clientes<i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo URL_APP; ?>relacionclientes/relacionclientes.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Relación de Clientes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo URL_APP; ?>relacionclientesinactivos/relacionclientesinactivos.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Clientes Inactivos</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- DESPACHOS -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon  fas fa-truck-loading"></i>
                        <p>Despachos<i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo URL_APP; ?>despachos/despachos.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Crear Despacho</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo URL_APP; ?>despachosrelacion/despachosrelacion.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Relación de Despachos</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ESTADISTICAS -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-poll"></i>
                        <p>Estadísticas<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <!-- SUB MENU ESTADISTICAS -->
                    <ul class="nav nav-treeview">
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Activaciones<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>clientescodnestle/clientescodnestle.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Clientes COD Nestle</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>activacionclientes/activacionclientes.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Activación de Clientes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>clientesbloqueados/clientesbloqueados.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Clientes Bloqueados</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>clientessintr/clientessintr.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Clientes Sin TR</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>clientesnuevos/clientesnuevos.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Clientes Nuevos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>clientesnoactivos/clientesnoactivos.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Clientes no Activos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- ALMACEN -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Almacén<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <!-- SUB MENU ALMACEN -->
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>indicadoresdespacho/indicadoresdespacho.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Indicadores Gestión Desp</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>facturassindespachar/facturassindespachar.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Facturas sin Despachar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- COMPRAS -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Compras<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <!-- SUB MENU COMPRAS -->
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>sellin/sellin.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Sell In</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>reportecompras/reportecompras.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Reporte de compras</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>historicocostos/historicocostos.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Historico Costos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>tasadolar/tasadolar.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Tasa Dolar</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- CUENTAS POR COBRAR -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cuentas por Cobrar<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <!-- SUB MENU CUENTAS POR COBRAR -->
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Resumen Cobranzas EDV</p>
                                    </a>
                                </li>
                                <!-- <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Reporte de Comisiones por Edv</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Estado de Cuentas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Facturas Pendientes por Cobrar en Divisas</p>
                                </a>
                            </li> -->
                            </ul>
                        </li>

                        <!-- INVENTARIO -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Inventario
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <!-- SUB MENU INVENTARIO -->
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>listadeprecio/listadeprecio.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Lista de Precios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>costodeinventario/costodeinventario.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Costos de Inventario</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>inventarioglobal/inventarioglobal.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Inventario Global</p>
                                    </a>
                                </li>
                                <!-- <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Sku Pendientes por Facturar</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Indicador de d�as de Inventario por SKU</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Inventario en Paquetes</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Disponible ALmacen</p>
                                </a>
                            </li> -->
                            </ul>
                        </li>

                        <!-- VENTAS -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ventas<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <!-- SUB MENU ALMACEN -->
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>kpi/kpi.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>KPI</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>kpimanager/kpimanager.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>KPI Manager's</p>
                                    </a>
                                </li>
                                <!--<li class="nav-item">
                                    <a href="<?php /*echo URL_APP; */?>facturassindespachar/facturassindespachar.php" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Facturas sin Despachar</p>
                                    </a>
                                </li>-->
                            </ul>
                        </li>
                    </ul>
                </li>

                <!-- CONFIGURACION -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">

                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Configuración<i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-address-book nav-icon"></i>
                                <p>Usuarios y Roles<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>usuario/usuario.php" class="nav-link">

                                        <i class="fas fa-users nav-icon"></i>
                                        <p>Getión de Usuarios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo URL_APP; ?>roles/roles.php" class="nav-link">
                                        <i class="fas fa-user-lock nav-icon"></i>
                                        <p>Roles</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo URL_APP; ?>chofer/chofer.php" class="nav-link">

                                <i class="fas fa-street-view nav-icon"></i>
                                <p>Choferes</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo URL_APP; ?>vehiculos/vehiculos.php" class="nav-link">

                                <i class="fas fa-truck nav-icon"></i>
                                <p>Vehículos</p>
                            </a>
                        </li>
                    </ul>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo URL_APP; ?>kpimarcas/kpimarca.php" class="nav-link">

                                <i class="fas fa-chart-line nav-icon"></i>
                                <p>Kpi Marcas</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo URL_APP; ?>gestionsistema/gestionsistema.php" class="nav-link">

                                <i class="fas fa-project-diagram nav-icon"></i>
                                <p>Gestión del sistema</p>
                            </a>
                        </li>
                    </ul>
                    <!--<ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">

                                <i class="fas fa-mail-bulk nav-icon"></i>
                                <p>Notificaciones</p>
                            </a>
                        </li>
                    </ul>-->
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