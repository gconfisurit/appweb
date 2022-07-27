<?php
include_once (PATH_HELPERS_PHP . "php/index.php");

class Documents
{
    public static function getInvoice($numerod, $type = 'A')
    {
        # array de salida
        $output = array();

        #variables para control de cantidad
        $paquetes = $bultos = 0;

        # banderas de control
        $esExcento = $desctoAlTotal = $desctoAlItem = false;
        $existeData = false;


        $head = Factura::getHeaderById($numerod);
        if (is_array($head) == true and count($head) > 0) {
            $output['cabecera'] = array(
                'descrip' => $head[0]['descrip'],
                'codusua' => $head[0]['codusua'],
                'fechae' => date('d/m/Y', strtotime($head[0]['fechae'])),
                'codvend' => $head[0]['codvend'],
            );

            $esExcento = (bool) (floatval($head[0]['excento']) > 0);
            $desctoAlTotal = (bool) (floatval($head[0]['descuento']) > 0);

            $content = Factura::getDetailById($numerod, $type);
            if (is_array($content) == true and count($content) > 0) {

                if (floatval($content[0]['descuento']) > 0) {
                    $desctoAlItem = true;
                }

                # debido a que el detalle de una factura es de multiples items(productos),
                # se procede a crear un array que almacene cada registro
                $array = Array();
                foreach ($content as $item) {

                    switch ($item['esunid']) {
                    case 1:
                        $paquetes += intval($item['cantidad']);
                        break;
                    case 0:
                        $bultos += intval($item['cantidad']);
                        break;
                    }

                    # asignamos al array, un array asociativo con las columnas
                    $array[] = array(
                        'coditem'   => $item['coditem'],
                        'descrip'   => $item['descrip'],
                        'cantidad'  => Strings::rdecimal($item['cantidad'], 0),
                        'tipounid'  => $item['tipo'],
                        'precio'    => Strings::rdecimal($item['precio'], 2),
                        'descuento' => Strings::rdecimal($item['descuento'], 2),
                        'totalitem' => Strings::rdecimal($item['total'], 2),
                    );
                }

                # una vez culminado las iteraciones, el array de registros, se asigna a una variable de salida
                $output["detalle"] = $array;
            }

            $output['totales'] = array(
                'subtotal'  => Strings::rdecimal($head[0]['subtotal'],2),
                'descuento' => Strings::rdecimal($head[0]['descuento'],2),
                'exento'    => Strings::rdecimal($head[0]['excento'],2),
                'base'      => Strings::rdecimal($head[0]['base_imponible'],2),
                'iva'       => Strings::rdecimal($head[0]['iva'], 2),
                'impuesto'  => Strings::rdecimal($head[0]['impuesto'],2),
                'total'     => Strings::rdecimal($head[0]['total'],2),
            );
        }
        # y devolvemos tambien los paquetes y bultos totales
        $output['info'] = array(
            'paquetes'      => $paquetes,
            'bultos'        => $bultos,
            'existeData'    => (bool) (count($head)>0),
            'esExcento'     => $esExcento,
            'desctoAlTotal' => $desctoAlTotal,
            'desctoAlItem'  => $desctoAlItem,
        );

        return $output;
    }



    public static function getdetallefacturasporcobrar($fechai, $fechaf , $data)
    {
        # array de salida
        $output = array();


        $head = Factura::getHeaderById($numerod);
        if (is_array($head) == true and count($head) > 0) {
            $output['cabecera'] = array(
                'descrip' => $head[0]['descrip'],
                'codusua' => $head[0]['codusua'],
                'fechae' => date('d/m/Y', strtotime($head[0]['fechae'])),
                'codvend' => $head[0]['codvend'],
            );

            $esExcento = (bool) (floatval($head[0]['excento']) > 0);
            $desctoAlTotal = (bool) (floatval($head[0]['descuento']) > 0);

            $content = Factura::getDetailById($numerod, $type);
            if (is_array($content) == true and count($content) > 0) {

                if (floatval($content[0]['descuento']) > 0) {
                    $desctoAlItem = true;
                }

                # debido a que el detalle de una factura es de multiples items(productos),
                # se procede a crear un array que almacene cada registro
                $array = Array();
                foreach ($content as $item) {

                    switch ($item['esunid']) {
                    case 1:
                        $paquetes += intval($item['cantidad']);
                        break;
                    case 0:
                        $bultos += intval($item['cantidad']);
                        break;
                    }

                    # asignamos al array, un array asociativo con las columnas
                    $array[] = array(
                        'coditem'   => $item['coditem'],
                        'descrip'   => $item['descrip'],
                        'cantidad'  => Strings::rdecimal($item['cantidad'], 0),
                        'tipounid'  => $item['tipo'],
                        'precio'    => Strings::rdecimal($item['precio'], 2),
                        'descuento' => Strings::rdecimal($item['descuento'], 2),
                        'totalitem' => Strings::rdecimal($item['total'], 2),
                    );
                }

                # una vez culminado las iteraciones, el array de registros, se asigna a una variable de salida
                $output["detalle"] = $array;
            }

            $output['totales'] = array(
                'subtotal'  => Strings::rdecimal($head[0]['subtotal'],2),
                'descuento' => Strings::rdecimal($head[0]['descuento'],2),
                'exento'    => Strings::rdecimal($head[0]['excento'],2),
                'base'      => Strings::rdecimal($head[0]['base_imponible'],2),
                'iva'       => Strings::rdecimal($head[0]['iva'], 2),
                'impuesto'  => Strings::rdecimal($head[0]['impuesto'],2),
                'total'     => Strings::rdecimal($head[0]['total'],2),
            );
        }
        # y devolvemos tambien los paquetes y bultos totales
        $output['info'] = array(
            'paquetes'      => $paquetes,
            'bultos'        => $bultos,
            'existeData'    => (bool) (count($head)>0),
            'esExcento'     => $esExcento,
            'desctoAlTotal' => $desctoAlTotal,
            'desctoAlItem'  => $desctoAlItem,
        );

        return $output;
    }




    public static function getNote($numerod, $type)
    {
        // TODO: Implement getNote() method.
    }
}