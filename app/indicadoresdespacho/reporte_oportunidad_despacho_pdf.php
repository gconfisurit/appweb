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
        $this->Cell(330,13,utf8_decode('OPORTUNIDAD DE DESPACHO'),'TLRB',0,'C');
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
$query = $indicadores->get_oportunidaddespacho_por_chofer($fechai, $fechaf, $chofer_id);
$chofer = Choferes::getByDni($chofer_id);

//inicializamos la variables
$chofer = (count($chofer) > 0) ? $chofer[0]['cedula'].' - '.$chofer[0]['descripcion'] : "";
$ordenes_despacho_string = '';
$oportunidad_promedio = 0;
$total_ped = 0;
$objetivo = 80;
$totaldoc = 0;
$fechaaevaluar = "00/00/0000";
$fecha_desp = Array();
$cant_documentos = Array();
$oportunidad = Array();
$documentos = Array();
$nombre_mes = Array();

foreach ($query as $key => $item)
{
    $fecha_entrega = $item["fecha_entre"] ?? date('Y-m-d');
    $tiempo_entrega_estimado = intval($item['tiempo_estimado']);

    $tiempo = strtotime($fecha_entrega)-strtotime($item["fecha_desp"]);
    $tiempo_entrega = intval($tiempo/60/60/24);

    $cal_oportunidad = ($tiempo_entrega <= $tiempo_entrega_estimado) ? 100 : ($tiempo_entrega_estimado/$tiempo_entrega)*100;
    $oportunidad_promedio += $cal_oportunidad;

    /** oportunidad despachos **/
    if($item['fecha_desp'] != null) {
        //almacenamos el total de documentos para calcular la oportunidad posteriormente
        if (!Dates::check_in_range($fechaaevaluar, $fechaaevaluar, $item['fecha_desp'])) {
            $fechaaevaluar = $item['fecha_desp'];
            $totaldoc = Functions::searchQuantityDocumentsByDates($query, "fecha_desp", $fechaaevaluar, $formato_fecha);
        }

        //consultamos si la de la iteracion actual tiene fecha igual a la insertada en la interacion anterior
        if(count($fecha_desp)>0 and date_format(date_create($item['fecha_desp']), $formato_fecha) == $fecha_desp[count($fecha_desp)-1])
        {
            $cant_documentos[count($cant_documentos)-1] += 1;
            $oportunidad[count($oportunidad)-1] += floatval($cal_oportunidad/$totaldoc);
            $documentos[count($documentos)-1] .= (", " . $item['numerod']);
        }
        //si no es igual, solo inserta un nuevo registro al array
        else {
            $fecha_desp[] = date_format(date_create($item['fecha_desp']), $formato_fecha);
            $cant_documentos[] = 1;
            $oportunidad[] = floatval($totaldoc>1 ? $cal_oportunidad/$totaldoc : $oportunidad);
            $documentos[] = $item['numerod'];
            $nombre_mes[] = Dates::month_name(date_format(date_create($item['fecha_desp']), 'm'), true);
        }
    }
}

/** calculamos el porcentaje de oportunidad de despacho promedio **/
$oportunidad_promedio = count($oportunidad)>0 ? ($oportunidad_promedio/count($oportunidad)) : $oportunidad_promedio=0;


/** calcular la cantidad de documentos **/
foreach ($cant_documentos as $arr)
    $total_ped += intval($arr);

/** concatenar la cantidad de documentos **/
foreach ($documentos as $arr)
    $ordenes_despacho_string .= (", " . $arr);


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
    $pdf->SetFont('Arial','',8);
    $pdf->SetAligns(array('L','L','R','L','R','L'));
    $pdf->SetBorders(array('LT','T','T','T','T','TR'));
    $pdf->SetWidths(array(40,194,20,20,19,37));
    $pdf->RowWithHeight(array(
        'CHOFER:', "$chofer",
        'DESDE', date_format(date_create($fechai), "d-m-Y") ." ".count(explode(",", $ordenes_despacho_string)),
        'HASTA',date_format(date_create($fechaf), "d-m-Y")),
        5);
    $pdf->SetWidths(array(40,290));
    $pdf->SetBorders(array('L','R'));
    $pdf->RowWithHeight(array('',''), 2);
    $pdf->RowWithHeight(array('DOCUMENTOS',"$ordenes_despacho_string"), 4);
    $pdf->RowWithHeight(array('',''), 2);
    $pdf->Cell(0,8,"",0,1,'C');


    /************************************* */
    /** CONTENIDO DE LA TABLA DE LA TABLA **/
    /************************************* */
    $x = count($cant_documentos);

    $pdf->SetFont('Arial','',10);
    $pdf->SetFillColor(213, 213, 246);
    if($x>=21){
        $pdf->CellFitSpace((13*(22))+5,6,utf8_decode('Oportunidad de despachos'),'TLRB',1,'C',true);
    }else{
        $pdf->CellFitSpace((13*($x+1))+5,6,utf8_decode('Oportunidad de despachos'),'TLRB',1,'C',true);
    }
    $pdf->SetFont('Arial','',6.2);
    $pdf->SetFillColor(182, 182, 247);
    for($i=0;$i<$x;$i++){
        if($i==0){
            $pdf->Cell(18,5,'F. Despacho','TLBR',0,'C',true);
        }
        if($i==21){
            $pdf->Cell(18,5,'F. Despacho','TLBR',0,'C',true);
            $pdf->Cell(13,5, $tipoPeriodo!="Anual" ? $fecha_entrega[$i] : $nombre_mes[$i],'TLBR',0,'C', true);
        }else{
            $pdf->Cell(13,5, $tipoPeriodo!="Anual" ? $fecha_entrega[$i] : $nombre_mes[$i],'TLBR',0,'C', true);
        }
    }
    $pdf->Cell(0,5,"",'',1,'C');
    for($i=0;$i<$x;$i++){
        if($i==0){
            $pdf->Cell(18,5,'Cant Documentos','TLBR',0,'C');
        }
        if($i==21){
            $pdf->Cell(18,5,'Cant Documentos','TLBR',0,'C');
            $pdf->Cell(13,5,$cant_documentos[$i],'TLBR',0,'C');
        }else{
            $pdf->Cell(13,5,$cant_documentos[$i],'TLBR',0,'C');
        }
    }
    $pdf->Cell(0,5,"",'',1,'C');
    for($i=0;$i<$x;$i++){
        if($i==0){
            $pdf->Cell(18,5,'% Oportunidad','TLBR',0,'C');
        }
        if($i==21){
            $pdf->Cell(18,5,'% Oportunidad','TLBR',0,'C');
            $pdf->Cell(13,5,Strings::rdecimal($oportunidad[$j], 2)." %",'TLBR',0,'C');
        }else{
            $pdf->Cell(13,5,Strings::rdecimal($oportunidad[$j], 2)." %",'TLBR',0,'C');
        }
    }



    /************************************* */
    /**      TOTALES BAJO LA TABLA        **/
    /************************************* */
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(20,6,'','',1,'C');
    $pdf->Cell(50,4,utf8_decode("Total de Pedidos: ".$total_ped),'',0,'L');



    /************************************* */
    /**             GRAFICO               **/
    /************************************* */
    $pdf->AddPage('L', $documentsize);
    $y = $pdf->GetY();
    $x = $pdf->GetX();

    //el objetivo es una linea de objetivo de oportunidad
    $objetivo = array_map(function() { return 80; }, $oportunidad);

    //para este reporte el valor mas alto es 100% correspondiente a maximo porcentaje de oportunidad
    $valorMasAlto = 100;

    $valuesPar = $valuesImpar = Array();
    for($m=0; $m <= $valorMasAlto+5; $m+=5)
        if ($m%2==0){$valuesPar[] = $m;}
        else{$valuesImpar[] = $m;}

    // Create the graph. These two calls are always required
    $graph = new Graph ( 850 , 850 , 'auto' );
    $graph->SetScale("textlin");

    $graph->SetMargin(50 , 30 , 40 , 100);

    $graph->yaxis->SetTickPositions($valuesPar, $valuesImpar);
    $graph->yaxis->scale->SetGrace(100-$objetivo[0]);
    $graph->SetBox(false);
    $graph->yaxis->title->SetFont(FF_VERDANA, FS_NORMAL);
    $graph->xaxis->title->SetFont(FF_VERDANA, FS_NORMAL);
    $graph->xaxis->title->Set("Fecha","left");
    $graph->yaxis->title->Set("Porcentaje(%) de Oportunidad de Despachos","middle");
    $graph->xaxis->SetTitleMargin(25);
    $graph->yaxis->SetTitleMargin(35);


    $graph->ygrid->SetFill(false);
    $graph->yaxis->HideLine(false);
    $graph->yaxis->HideTicks(false , false);
    // Setup month as labels on the X-axis
    $graph->xaxis->SetTickLabels($tipoPeriodo!="Anual" ? $fecha_entrega : $nombre_mes);

    // Create the bar plots
    $b1plot = new BarPlot($oportunidad);
    $b1plot->value->show();
    $lplot = new LinePlot($objetivo);

    // ...and add it to the graPH
    $graph->Add($b1plot);
    $graph->Add($lplot);

    $b1plot->value->Show();
    $b1plot->value->SetColor("black","darkred");
    $b1plot->value->SetFormat('%1d%%');
    $b1plot->SetColor("white");
//    $b1plot->SetFillGradient("#000066" , "white" , GRAD_LEFT_REFLECTION);
    $b1plot->SetWidth(25);
    $b1plot->SetLegend("% de Oportunidad");

    $lplot->SetBarCenter();
    $lplot->SetColor("red");
    $lplot->SetLegend("Objetivo (".$objetivo[0]."%)");
    $lplot->mark->SetWidth(15);
    $lplot->mark->setColor("red");
    $lplot->mark->setFillColor("red");

    $graph->legend->SetFrameWeight(1);
    $graph->legend->SetColumns(6);
    $graph->legend->Pos(0.2, 0.03);
    $graph->legend->SetPos(0.5,0.95,'center','bottom');
    $graph->legend->SetColor('#4E4E4E' , '#00A78A');

    $graph->title->Set('');

    // Display the graph
    $x = 20;
    $ancho = 320;
    $altura = 170;

    // Display the graph
    $graph->Stroke("oportunidad_despacho.png");
    $pdf->Image("oportunidad_despacho.png",$x,$y,$ancho,$altura);
    unlink("oportunidad_despacho.png");


    $pdf->Output();
}

?>