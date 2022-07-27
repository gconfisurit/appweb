<?php


class Functions {

    public static function listDirectory($directorio)
    {
        $output = array();

        $listado = scandir($directorio);
        unset($listado[array_search('.', $listado, true)]);
        unset($listado[array_search('..', $listado, true)]);
        if (count($listado) < 1) {
            return [];
        }
        foreach($listado as $elemento)
        {
            # PARA LISTAR ARCHIVOS
            /* if(!is_dir($directorio.'/'.$elemento)) {
                $output[] = "$elemento";
            }*/

            # PARA LISTAR CARPETAS
            if(is_dir($directorio.'/'.$elemento)) {
                $output[] = "$elemento";
                Functions::listDirectory($directorio.'/'.$elemento);
            }
        }

        return $output;
    }

    public static function searchQuantityDocumentsByDates($array, $fieldSearch, $search, $format)
    {
        $indexI = $indexF = 0;
        $flag = true;

        for ($i=0; $i<count($array)-1&&$flag==true; $i++) {
        $indexI=$i;
        if (date_format(date_create($search), $format) == date_format(date_create($array[$i][$fieldSearch]), $format)) {
        for($j=$i+1; $j<count($array) && date_format(date_create($search), $format)==date_format(date_create($array[$j-1][$fieldSearch]), $format); $j++) {
        $indexF=$j;
        }
        $flag=false;
        }
        }
        return $indexF - $indexI;
    }

    public static function selectListMenus($id, $selectChangeToNone = false, $id_menu_except = -1)
    {
        $output = '';
        $datos = Menu::todos();
        $seleccionado = Menu::getById($id);
        $haySeleccionado = (is_array($seleccionado) == true and count($seleccionado) > 0);


        $output .= '<option value="-1">' . ( $selectChangeToNone ? 'Ninguno' : '--Seleccione--' ). '</option>';
        if (is_array($datos) == true and count($datos) > 0)
        {
            foreach ($datos as $key => $row)
            {
                if($id_menu_except != $row['id'])
                {
                    if ($haySeleccionado and ($row['id']==$seleccionado[0]['id']) )
                        $output .= '<option value="' . $row['id'] . '" selected>' . $row['nombre'] . '</option>';
                    else
                        $output .= '<option value="' . $row['id'] .'">'. $row['nombre'] .'</option>';
                }
            }
        }

        return $output;
    }

    public static function selectListCausasRechazos()
    {
        $output = '';
        $datos = CausasRechazos::todos();

        $output .= '<option value="">Seleccione</option>';
        if (is_array($datos) == true and count($datos) > 0)
        {
            foreach ($datos as $key => $row)
                $output .= '<option value="' . $row['descripcion'] .'">'. ucwords($row['descripcion']) .'</option>';
        }

        return $output;
    }

    public static function find_discount($datei, $datef, $code){
        $aux = 0;
        $consulta = VentasKg::getNumerodOfDiscounts($datei, $datef, $code);
        foreach ($consulta as $row) {
            $consul_facturas = Factura::getById($row['numerod']);
            if ($consul_facturas[0]["tipofac"] == "A") {
                $aux += $consul_facturas[0]["descuento"];
            } else {
                $aux -= $consul_facturas[0]["descuento"];
            }
        }
        return $aux;
    }

    public static function listModulesAvailable($id_modulo)
    {
        $output = array();
        $directorios_en_db  = array_map(function ($arr) { return $arr['ruta']; }, Modulos::todos());
        $directorios_en_app = Functions::listDirectory(PATH_APP_PHP);
        $directorio_seleccionado = Modulos::getById($id_modulo);

        if (is_array($directorio_seleccionado) == true and count($directorio_seleccionado) > 0)
            $output[] = $directorio_seleccionado[0]['ruta'];

        foreach ($directorios_en_app as $directorio)
            if (!in_array($directorio, $directorios_en_db))
                $output[] = $directorio;

        return $output;
    }

    public static function listModulesAvailableJson($name_modulo)
    {
        $output = $directorios_en_json = array();
        $directorios_en_db  = array_map(function ($arr) { return $arr['nombre']; }, Modulos::todosActivos());
        $config_json = ConfigJson::get();
        $directorio_seleccionado = Modulos::getByName($name_modulo);

        if (is_array($directorio_seleccionado) == true and count($directorio_seleccionado) > 0)
            $output[] = $directorio_seleccionado[0]['nombre'];

        foreach ($config_json as $key => $value)
            $directorios_en_json[] = $key;

        foreach ($directorios_en_db as $directorio_db)
            if (!in_array($directorio_db,  $directorios_en_json))
                $output[] = $directorio_db;

        return $output;
    }

    public static function orgranigramaMenus($id = -1) {
        $output = array();

        $hijos = ($id==-1) ? Menu::withoutFather() : Menu::getChildren($id);
        if (is_array($hijos) == true and count($hijos) > 0)
        {
            foreach ($hijos as $key => $hijo)
            {
                $sub_array = array();

                $sub_array['title'] = $hijo['nombre'];
                $sub_array['name'] = $hijo['nombre'];

                $existenHijos = Menu::getChildren($hijo['id']);
                if (is_array($existenHijos) == true and count($existenHijos) > 0) {
                    $sub_array['children'] = Functions::orgranigramaMenus($hijo['id']);
                }

                $output[] = $sub_array;
            }
        }
        return $output;
    }

    public static function organigramaMenusWithModules($id, $type = -1, $type_id = -1, $itsForSideMenu = false, $isChildren = false, &$countModules = 0){
        $output = array();

        $hijos = ($id==-1) ? Menu::withoutFather() : Menu::getChildren($id);
        if (is_array($hijos) == true and count($hijos) > 0)
        {
            foreach ($hijos as $key => $hijo) {
                $sub_array = array();

                $sub_array['title'] = $hijo['nombre'];
                $sub_array['icon'] = $hijo['icono'];

                # verifica si existe hijos para aplicar recursion
                $existenHijos = Menu::getChildren($hijo['id']);
                $sub_array['children'] = (is_array($existenHijos) == true and count($existenHijos) > 0)
                    ? Functions::organigramaMenusWithModules($hijo['id'], $type, $type_id, $itsForSideMenu, true, $countModules)
                    : array();

                 # verificamos si tiene modulos
                $modulosMenu = array();
                $existenModulos = Modulos::getByMenuId($hijo['id'], $itsForSideMenu);
                if (is_array($existenModulos) == true and count($existenModulos) > 0) {
                    $arr_permissions_by_type = array();

                    # el parametro tipo:
                    #        0 el tipo es rol
                    #        1 el tipo es usuario
                    switch ($type) {
                        case 0: $arr_permissions_by_type = Permisos::getRolesGrupoPorRolID($type_id); break;
                        case 1: $arr_permissions_by_type = Permisos::getPermisosPorUsuarioID($type_id); break;
                    }

                    $modulosInDB = array_map(function ($arr) { return $arr['id_modulo']; }, $arr_permissions_by_type);
                    foreach ($existenModulos as $key1 => $modulo) {
                        $isSelected = in_array($modulo['id'], $modulosInDB);

                        if ($isSelected==true)
                            $countModules+=1;

                        $modulosMenu[] = array(
                            'id' 	    => $modulo['id'],
                            'name'      => $modulo['nombre'],
                            'route'     => $modulo['ruta'],
                            'icon'      => $modulo['icono'],
                            'selected'  => $isSelected
                        );
                    }
                }

                $sub_array['modules'] = $modulosMenu;


                # hacemos una condicion que:
                #   si es para el menu lateral, preguntamos si tiene al menos un modulo seleccionado, si no tiene, no muestra dicho menu
                #   si no es para menu lateral, es para el modulo de permisos de usuario, lista los seleccionados y no seleccionados
                if($itsForSideMenu==true)
                {
                    #si tiene al menos modulo seleccinado, agregamos al array
                    if ($countModules>0) {
                        $output[] = $sub_array;
                    }

                    # si no es hijo, reiniciamos el contador
                    if (!$isChildren) {
                        $countModules = 0;
                    }
                }
                else {
                    $output[] = $sub_array;
                }

            }
        }
        return $output;
    }

    public static function getNameDirectory()
    {
        $nombre_archivo = $_SERVER['PHP_SELF'];
        $nombre_archivo_a = explode('/appweb/app/', $nombre_archivo)[1];
        $archivo_nombre = explode("/",$nombre_archivo_a)[0];
        $archivo_nombre_a = explode('.', $archivo_nombre);
        $ar_nombre = $archivo_nombre_a[0];

        return $ar_nombre;
    }

}