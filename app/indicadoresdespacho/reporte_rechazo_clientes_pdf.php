<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');
require_once ( PATH_LIBRARY.'jpgraph4.3.4/src/jpgraph.php' );
require_once ( PATH_LIBRARY.'jpgraph4.3.4/src/jpgraph_bar.php' );
require_once ( PATH_LIBRARY.'jpgraph4.3.4/src/jpgraph_line.php' );


//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("indicadoresdespacho_modelo.php");

//INSTANCIAMOS EL MODELO
$indicadores = new InidicadoresDespachos();

$tipoPeriodo = $_GET['tipoPeriodo'];
$periodo   = $_GET['periodo'];
$chofer_id = $_GET['chofer'];
$causa = $_GET['causa'];

switch($tipoPeriodo) {
    case "Anual":
        $fechai = $periodo."-01-01";
        $fechaf = $periodo."-12-31";
        break;
    case "Mensual":
        $fechai = $periodo."-01";
        $fechaf = date("Y-m-t", strtotime($periodo));
        break;
}

$documentsize = 'Legal';
$formato_fecha = $tipoPeriodo=="Anual" ? 'm-Y' : 'd-m-Y';
$cant_ordenes_despacho_max = 22;
$cant_fact_sinliquidar_max = 26;
$ancho_tabla_max = 19;
$row = 0;

$j = 0;
$width = array();
function addWidthInArray($num){
    $GLOBALS['width'][$GLOBALS['j']] = $num;
    $GLOBALS['j'] = $GLOBALS['j'] + 1;
    return $num;
}

class PDF extends FPDF
{
    var $widths;
    var $aligns;

    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial','B',12);
        /********************** */
        /** SE INSERTA EL LOGO **/
        /********************** */
        $this->Ln(-3);
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);

        /********************** */
        /** TITULO DEL REPORTE **/
        /********************** */
        $this->SetFont('Arial','BU',12);
        $this->Cell(330,13,utf8_decode('RECHAZO DE LOS CLIENTES'),'TLRB',0,'C');
        $this->SetFont('Arial','',8);
        $this->Cell(-13,4,utf8_decode('Codigo: FOR-TRA-09-R0'),'',1,'R');
        $this->Cell(305,10,utf8_decode('Fecha: 25/08/14'),'',1,'R');
        $this->SetAligns(array('L','L','R','L','R','L'));
    }

    // Pie de página
    function Footer()
    {
        $this->SetFont('Arial','B',6);
        $this->SetY(190);
        $this->SetX(60);
        $this->SetAligns(array('L','L','L','L','L','L','L'));
        $this->SetBorders(array('','LTBR','TLBR','TLBR','TLBR','TLBR','TRBL'));
        $this->SetWidths(array(40,20,80,15,25,15,25));
        $this->Row(array('','Aprobado por:', "",'C.I:',"", 'Firma:',""));
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation, $GLOBALS['documentsize']);
    }
}


/************************************* */
/** CONFIGURAMOS EL TIPO DE DOCUMENTO **/
/************************************* */
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L', $documentsize);
$pdf->SetFont('Arial', '', 7);


/************************************************************************************************ */
/** INICIO DE LOS PROCESOS DE OBTENCION DE DATOS Y PROCESAMIENTO PARA UTILIZARLOS POSTERIORMENTE **/
/************************************************************************************************ */
$query = $indicadores->get_causasrechazo_por_chofer($fechai, $fechaf, $chofer_id, $causa);

if($chofer_id!="-") {
    $chofer = Choferes::getByDni($chofer_id);
    $chofer = (count($chofer) > 0) ? $chofer[0]['cedula'].' - '.$chofer[0]['descripcion'] : "";
} else {
    $chofer = "Todos los Choferes";
}

$ordenes_despacho_string = "";
$fact_sinliquidar_string = "";
$totaldespacho = Array();
$totalendespacho = 0;
$total_ped_devueltos = 0;
$total_ped_entregados = 0;
$total_ped_porliquidar = 0;
$promedio_diario_despacho = 0;
$fecha_entrega = Array();
$cant_documentos = Array();
$nombre_mes = Array();
$porc = Array();
$ordenes_despacho = Array();
$correlativo = Array();
$observacion = Array();

//almacenamos el total de despachos agrupado
foreach ($query as $item){
    if($item['fecha_entre']==null or Dates::check_in_range($fechai, $fechaf, $item['fecha_entre'])) {
        $fecha_entreg = $item['fecha_entre'] != null ? date_format(date_create($item['fecha_entre']), $formato_fecha) : "sin fecha de entrega";

        //consultamos si la fecha a consultar existe en el array totaldespacho
        if(count($totaldespacho)>0 and in_array($fecha_entreg, array_keys($totaldespacho)))
            $totaldespacho[$fecha_entreg] += intval($item['cant_documentos']);
        //si no existe, solo inserta un nuevo registro al array
        else $totaldespacho[$fecha_entreg] = intval($item['cant_documentos']);
    }
}

foreach ($query as $key => $item)
{
    /** obtener los despachos realizados para imprimirlos en un string ordenado al final **/
    if(count($ordenes_despacho)>0 and $item['correlativo'] == $ordenes_despacho[count($ordenes_despacho)-1]["correlativo"]) {
        $ordenes_despacho[count($ordenes_despacho)-1]['cant_documentos'] += intval($item['cant_documentos']);
    } else {
        $ordenes_despacho[] = array(
            "correlativo"     => $item['correlativo'],
            "cant_documentos" => $item['cant_documentos']
        );
    }

    /** causas de rechazo **/
    if(($item['tipo_pago'] =='N/C' or $item['tipo_pago'] =='N/C/P') and $key>=0 )
    {
        //consultamos si la de la iteracion actual tiene fecha igual a la insertada en la interacion anterior
        if(count($fecha_entrega)>0 and  ($item['fecha_entre'] != null
                and date_format(date_create($item['fecha_entre']), $formato_fecha) == $fecha_entrega[count($fecha_entrega)-1]) ||
            ($item['fecha_entre']==null and "sin fecha de entrega"==$fecha_entrega[count($fecha_entrega)-1])
        ) {
            $porcentaje = ($item['cant_documentos'] / $totaldespacho[$fecha_entrega[count($fecha_entrega)-1]]) * 100;
            $cant_documentos[count($cant_documentos)-1] += intval($item['cant_documentos']);
            $porc[count($porc)-1] += $porcentaje;
            $correlativo[count($correlativo)-1] .= (", " . $item['correlativo']);

            $arr = array_map(function ($val) { return $val['tipo']; }, $observacion[$fecha_entrega[count($fecha_entrega)-1]]);
            //verifica si existe la observacion
            if (!in_array(strtoupper($item['observacion']), $arr)) {
                # no existe, le agrega en una nueva posicion
                $observacion[$fecha_entrega[count($fecha_entrega)-1]][] = Array(
                    "tipo" => strtoupper($item['observacion']),
                    "cant" => intval($item['cant_documentos']),
                    "color" => Array("id" => $item['color_id'], "hex" => $item['color'])
                );
            } else {
                # si existe, le suma la cantidad de documentos
                $pos = array_search($item['observacion'], $arr);
                $observacion[$fecha_entrega[count($fecha_entrega)-1]][$pos]['cant'] += intval($item['cant_documentos']);
            }

        }
        //si no es igual, solo inserta un nuevo registro al array
        elseif($item['fecha_entre']==null or Dates::check_in_range($fechai, $fechaf, $item['fecha_entre'])){
            $fecha_ent = ($item['fecha_entre'] != null and strlen($item['fecha_entre'])>0)
                ? date_format(date_create($item['fecha_entre']), $formato_fecha) : "sin fecha de entrega";
            $nombreMes = ($item['fecha_entre'] != null and strlen($item['fecha_entre'])>0)
                ? Dates::month_name(date_format(date_create($item['fecha_entre']), 'm'), true) : "sin f. entreg.";
            $porcentaje = ($item['cant_documentos'] / $totaldespacho[$fecha_ent]) * 100;

            $fecha_entrega[] = $fecha_ent;
            $nombre_mes[] = $nombreMes;
            $cant_documentos[] = intval($item['cant_documentos']);
            $porc[] = $porcentaje;
            $correlativo[] = $item['correlativo'];
            $observacion[$fecha_ent][] = Array(
                "tipo"  => strtoupper($item['observacion']),
                "cant"  => intval($item['cant_documentos']),
                "color" => Array("id" => $item['color_id'], "hex" => $item['color'])
            );
        }
    }
}

/** los despachos realizados obtenidos se agregan a un string **/
foreach ($ordenes_despacho as $arr){
    $ordenes_despacho_string .= ($arr['correlativo'] . "(" . Strings::addCero($arr['cant_documentos']) . "), ");
}

/** calcular los pedidos devueltos **/
foreach ($cant_documentos as $arr){
    $total_ped_devueltos += intval($arr);
}

/** calcular el total despachos **/
foreach ($totaldespacho as $key => $arr){
    $totalendespacho += intval($arr);
}


/****************************************************************************** */
/** EVALUAMOS SI LA DATA PROCESADA ES INFERIOR A 42 PARA EVITAR DESBORDAMIENTO **/
/****************************************************************************** */
if(count(explode(",", $ordenes_despacho_string)) > 300) {

    echo "<script>
                alert('ERROR: Desbordamiento! la cantidad de datos excede el límite para este reporte.');
                window.close();
          </script>";

} else {


    /****************************** */
    /**      DATOS DEL REPORTE     **/
    /****************************** */
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetAligns(array('L', 'L', 'R', 'L', 'R', 'L'));
    $pdf->SetBorders(array('LT', 'T', 'T', 'T', 'T', 'TR'));
    $pdf->SetWidths(array(40, 194, 20, 20, 19, 37));
    $pdf->RowWithHeight(array(
        'CHOFER:', "$chofer",
        'DESDE', date_format(date_create($fechai), "d-m-Y"),
        'HASTA', date_format(date_create($fechaf), "d-m-Y")),
        5);
    $pdf->SetWidths(array(40, 290));
    $pdf->SetBorders(array('L', 'R'));
    $pdf->RowWithHeight(array('', ''), 2);
    $pdf->RowWithHeight(array('ORDENES DE DESPACHO', "$ordenes_despacho_string"), 4);
    $pdf->RowWithHeight(array('', ''), 2);
    $pdf->SetWidths(array(40, 290));
    $pdf->SetBorders(array('LB', 'RB'));
    $pdf->RowWithHeight(array('CAUSA DEL RECHAZO', "$causa"), 4);
    $pdf->Cell(0, 8, "", 0, 1, 'C');


    /************************************* */
    /** CONTENIDO DE LA TABLA DE LA TABLA **/
    /************************************* */
    $x = count($cant_documentos);

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetFillColor(213, 213, 246);
    if ($x >= 21) {
        $pdf->CellFitSpace((13 * (22)) + 5, 6, utf8_decode('Rechazos de los Clientes'), 'TLRB', 1, 'C', true);
    } else {
        $pdf->CellFitSpace((13 * ($x + 1)) + 5, 6, utf8_decode('Rechazos de los Clientes'), 'TLRB', 1, 'C', true);
    }
    $pdf->SetFont('Arial', '', 6.2);
    $pdf->SetFillColor(182, 182, 247);
    for ($i = 0; $i < $x; $i++) {
        if ($i == 0) {
            $pdf->Cell(18, 5, utf8_decode('F. Devolución'), 'TLBR', 0, 'C', true);
        }
        if ($i == 21) {
            $pdf->Cell(18, 5, utf8_decode('F. Devolución'), 'TLBR', 0, 'C', true);
            $pdf->Cell(13, 5, $tipoPeriodo != "Anual" ? $fecha_entrega[$i] : $nombre_mes[$i], 'TLBR', 0, 'C', true);
        } else {
            $pdf->Cell(13, 5, $tipoPeriodo != "Anual" ? $fecha_entrega[$i] : $nombre_mes[$i], 'TLBR', 0, 'C', true);
        }
    }
    $pdf->Cell(0, 5, "", '', 1, 'C');
    for ($i = 0; $i < $x; $i++) {
        if ($i == 0) {
            $pdf->Cell(18, 5, 'Devoluciones', 'TLBR', 0, 'C');
        }
        if ($i == 21) {
            $pdf->Cell(18, 5, 'Devoluciones', 'TLBR', 0, 'C');
            $pdf->Cell(13, 5, $cant_documentos[$i], 'TLBR', 0, 'C');
        } else {
            $pdf->Cell(13, 5, $cant_documentos[$i], 'TLBR', 0, 'C');
        }
    }
    $pdf->Cell(0, 5, "", '', 1, 'C');
    for ($i = 0; $i < $x; $i++) {
        if ($i == 0) {
            $pdf->Cell(18, 5, '% Rechazos', 'TLBR', 0, 'C');
        }
        if ($i == 21) {
            $pdf->Cell(18, 5, '% Rechazos', 'TLBR', 0, 'C');
            $pdf->Cell(13, 5, Strings::rdecimal($porc[$i], 2) . " %", 'TLBR', 0, 'C');
        } else {
            $pdf->Cell(13, 5, Strings::rdecimal($porc[$i], 2) . " %", 'TLBR', 0, 'C');
        }
    }
    if ($tipoPeriodo != "Anual") {
        $pdf->Cell(0, 5, "", '', 1, 'C');
        for ($i = 0; $i < $x; $i++) {
            if ($i == 0) {
                $pdf->Cell(18, 5, 'Orden(es) D', 'TLBR', 0, 'C');
            }
            if ($i == 21) {
                $pdf->Cell(18, 5, 'Orden(es) D', 'TLBR', 0, 'C');
                $pdf->CellFitSpace(13, 5, $correlativo[$i], 'TLBR', 0, 'C');
            } else {
                $pdf->CellFitSpace(13, 5, $correlativo[$i], 'TLBR', 0, 'C');
            }
        }
    }


    /************************************* */
    /**      TOTALES BAJO LA TABLA        **/
    /************************************* */
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(20, 6, '', '', 1, 'C');
    $pdf->Cell(50, 4, utf8_decode("Total de Pedidos en el camión: " . $totalendespacho), '', 0, 'L');
    $pdf->Cell(50, 4, utf8_decode("Total de Pedidos devueltos: " . $total_ped_devueltos), '', 1, 'L');


    /************************************* */
    /**             GRAFICO               **/
    /************************************* */
# ancho y alto de la imagen grafico
    $aWidth = 850;
    $aHeight = 850;
    $pdf->AddPage('L', $documentsize);
    $y = $pdf->GetY();
    $x = $pdf->GetX();

    $labels = $tipoPeriodo != "Anual" ? $fecha_entrega : $nombre_mes;
    $causas = CausasRechazos::todos();

# array inicializado con los valores en cero basado en la cantidad de registros de $labels
    $arr_temp = array_map(function () {
        return 0;
    }, $labels);

# creamos un array con los id de colores por causa de rechazo disponibles en el rango de fecha
    $index_color = [];
    foreach ($observacion as $obsr) {
        foreach ($obsr as $value) {
            array_push($index_color, $value['color']['id']);
        }
    }
    $index_color = array_unique($index_color);
    sort($index_color);

# creamos un array inicializado basandose en la dimension de index_color.
    $values = array();
    foreach ($index_color as $idx) {
        array_push($values, array(
            'id' => $idx,
            'tipo' => $causas[$idx - 1]['descripcion'],
            'values' => $arr_temp,
            'color' => $causas[$idx - 1]['color']
        ));
    }

# llenamos el array con la data necesaria para ser procesada en el grafico
    foreach ($values as $index => $val) {
        foreach ($observacion as $grupo_asocionacion => $obsr) {
            $tipos_observacion = array_map(function ($tipo_obs) {
                return $tipo_obs['tipo'];
            }, $obsr);
            if (in_array($val['tipo'], $tipos_observacion)) {
                $idx = array_search($grupo_asocionacion, array_keys($observacion));
                $values[$index]['values'][$idx] = $obsr[array_search($val['tipo'], $tipos_observacion)]['cant'];
            }
        }
    }


# obtencion del valor mas alto en base a $cant_documentos
    $valorMasAlto = 0;
    foreach ($cant_documentos as $item)
        if ($item > $valorMasAlto) {
            $valorMasAlto = $item;
        }

# llenado de unos array con las escalas para el grafico en base al valor mas alto
    $salto = $valorMasAlto < 10 ? 1 : 5;
    $valuesPar = $valuesImpar = array();
    for ($m = 0; $m <= $valorMasAlto + $salto; $m += $salto)
        if ($m % 2 == 0) {
            $valuesPar[] = $m;
        } else {
            $valuesImpar[] = $m;
        }


// Create the graph. These two calls are always required
    $graph = new Graph ($aWidth, $aHeight, 'auto');
    $graph->SetScale("textlin");

    $graph->SetMargin(50, 30, 40, 100);

    $graph->yaxis->SetTickPositions($valuesPar, $valuesImpar);
    $graph->SetBox(false);
    $graph->yaxis->title->SetFont(FF_VERDANA, FS_NORMAL);
    $graph->xaxis->title->SetFont(FF_VERDANA, FS_NORMAL);
    $graph->xaxis->title->Set("Fecha", "left");
    $graph->yaxis->title->Set("Cantidad Devoluciones", "middle");
    $graph->xaxis->SetTitleMargin(25);
    $graph->yaxis->SetTitleMargin(35);

    $graph->ygrid->SetFill(false);
    $graph->yaxis->HideLine(false);
    $graph->yaxis->HideTicks(false, false);
// Setup month as labels on the X-axis
    $graph->xaxis->SetTickLabels($tipoPeriodo != "Anual" ? $fecha_entrega : $nombre_mes);

// Create the bar plots desgloce de devoluciones
    $bplot = array();
    foreach ($values as $key => $value) {
        //creamos un nuevo Barplot
        $bplotTemp = new BarPlot($value['values']);
        $bplotTemp->value->show();
        $bplotTemp->value->SetColor("black", "darkred");
        $bplotTemp->value->SetFormat('%1d');

        //buscamos y signamos el color
        $bplotTemp->SetColor($value['color']);
        $bplotTemp->SetFillColor($value['color']);

        //asignamos el nombre de la leyenda
        $bplotTemp->SetWidth(25);
        $bplotTemp->SetLegend($value['tipo']);

        //por ultimo lo agregamos a un array para su posterior plot
        $bplot[] = $bplotTemp;
    }
// Create the grouped bar plot
    $gbbplot = new AccBarPlot($bplot);
// ...and add it to the graPH
    $graph->Add($gbbplot);

    $graph->legend->SetFrameWeight(1);
    $graph->legend->SetColumns(4);
    $graph->legend->Pos(0.2, 0.03);
    $graph->legend->SetPos(0.5, 0.99, 'center', 'bottom');
    $graph->legend->SetFont(FF_VERDANA, FS_NORMAL, 8);
    $graph->legend->SetColor('#4E4E4E', '#00A78A');

    $graph->title->Set('');

// Display the graph
    $x = 20;
    $ancho = 320;
    $altura = 160;

// Display the graph
    $graph->Stroke("rechazo_clientes.png");
    $pdf->Image("rechazo_clientes.png", $x, $y, $ancho, $altura);
    unlink("rechazo_clientes.png");


    $pdf->Output();
}
?>