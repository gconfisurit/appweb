<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO
require_once("kpi_modelo.php");
require_once("../kpimanager/kpimanager_modelo.php");

//INSTANCIAMOS EL MODELO
$kpi = new Kpi();
$kpiManager = new KpiManager();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$d_habiles = $_GET['d_habiles'];
$d_trans = $_GET['d_trans'];

$i = 0;
$j = 0;
$documentsize = 'Legal';
$width = array();
$info = array();

function addWidthInArray($num){
    $GLOBALS['width'][$GLOBALS['i']] = $num;
    $GLOBALS['i'] = $GLOBALS['i'] + 1;
    return $num;
}

function addInfoInArray($info){
    $GLOBALS['info'][$GLOBALS['j']] = $info;
    $GLOBALS['j'] = $GLOBALS['j'] + 1;
}

class PDF extends FPDF
{
    var $widths;
    var $aligns;

    // Cabecera de página
    function Header()
    {
        $lista_marcaskpi = array_map(function ($arr) { return $arr['descripcion']; }, KpiMarcas::todos('DESC'));

        /*calculo del ancho adicional para mantener el orden de las celdas de acuerdo a su seleccion segun las siguientes premisas:
                * para el ancho de las celdas que son dinamicas son p1=25, p2=25, p3=25 y cubi=24
                * si solo aparece visualmente un precio, se suma el ancho de las otras dos mas el cubi
                * si aparece visualmente dos precios, se suma el ancho de una mas el cubi
                * si aparece los 3 precios, solo se suba el cubi
                * si aparecen los 3 precios y el cubi, no se suma nada.

         la suma de ancho adicional se distribuira de la siguiente forma:
                * codigo  = 20%
                * descripcion = 40%
                * marca   = 20%
                * bulto   = 10%
                * paquete = 10%
            TOTAL 100%
        */
        $anchoAdicional = 0;
        /*switch ($GLOBALS['sumap']) {
            case 1:
                $anchoAdicional += (50*2);// +25+25
                break;
            case 2:
                $anchoAdicional += (25*2);// +25
                break;
            default:
                $anchoAdicional += 0;// +0
        }
        if ($GLOBALS['cubi'] == 0) {
            $anchoAdicional += 24;
        }*/

        // Logo
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 18);
        // Movernos a la derecha
        $this->Cell(140);
        // Título
        $this->Cell(40, 12, 'REPORTE KPI (Key Performance Indicator)', 0, 1, 'C');
        $this->Ln(1);

        $this->Cell(75);
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(230,230,230);
        $this->Cell(10, 8, utf8_decode('Desde:'), 0, 0, 'R');
        $this->Cell(28, 7, $GLOBALS['fechai'], 'B', 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('Hasta:'), 0, 0, 'R');
        $this->Cell(28, 7, $GLOBALS['fechaf'], 'B', 0, 'C', true);
        $this->Cell(35, 8, utf8_decode('Días Habiles:'), 0, 0, 'R');
        $this->Cell(12, 7, $GLOBALS['d_habiles'], 'B', 0, 'C', true);
        $this->Cell(32, 8, utf8_decode('Días Transc:'), 0, 0, 'R');
        $this->Cell(12, 7, $GLOBALS['d_trans'], 'B', 0, 'C', true);



        //linea
        $this->Line(8, 32, 345, 32);
        $this->Ln(5);

        // Salto de línea
        $this->Ln(10);
        $this->SetFillColor(200,220,255);
        $this->SetFont('Arial', 'B', 7);
        // titulo de columnas
        $this->Cell(addWidthInArray(18 + ($anchoAdicional*0.20)), 6, 'Rutas', 1, 'C', true);
        $this->Cell(addWidthInArray(18 + ($anchoAdicional*0.40)), 6, 'Maestro', 1, 'C', true);
        $aux = 0;
        foreach ($lista_marcaskpi as $i => $marcaKpi) {
            if ($i>0) {
                $this->Ln(-4);
                $this->Cell(36+$aux);
            }
            $this->MultiCell(addWidthInArray(5 + ($anchoAdicional*0.20)), 6, $marcaKpi, 1, 'C', true);
            $aux+=5;
        }
        $this->Cell(addWidthInArray(24 + ($anchoAdicional*0.20)), 6, 'Clientes Activados', 1, 'C', true);
        $this->Cell(addWidthInArray(18  + ($anchoAdicional*0.10)), 6, utf8_decode('% Activación'), 1, 'C', true);
        $this->Cell(addWidthInArray(18  + ($anchoAdicional*0.10)), 6, 'Pendientes', 1, 'C', true);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation, $GLOBALS['documentsize']);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L', $documentsize);
$pdf->SetFont('Arial', '', 8);

/*$pdf->SetWidths($width);

$query = $precios->getListadeprecios($marcas, $depos, $exis, $orden);
$num = count($query);

foreach ($query as $x) {
    $j = 0;
    if ($x['esexento']) {
        $precio1 = $x['precio1'] * $iva;
        $precio2 = $x['precio2'] * $iva;
        $precio3 = $x['precio3'] * $iva;
        $preciou1 = $x['preciou1'] * $iva;
        $preciou2 = $x['preciou2'] * $iva;
        $preciou3 = $x['preciou3'] * $iva;
    } else {
        $precio1 = $x['precio1'];
        $precio2 = $x['precio2'];
        $precio3 = $x['precio3'];
        $preciou1 = $x['preciou1'];
        $preciou2 = $x['preciou2'];
        $preciou3 = $x['preciou3'];
    }

    addInfoInArray($x['codprod']);
    addInfoInArray(utf8_decode($x['descrip']));
    addInfoInArray($x['marca']);
    //BULTOS
    addInfoInArray(round($x['existen']));
    switch ($sumap) {
        case 1:
            if ($x['esexento'] == 0) { addInfoInArray( Strings::rdecimal($x['precio'. $sumap2 ]* $iva, 2) ); } else { addInfoInArray( Strings::rdecimal($x['precio'. $sumap2 ], 2) ); }
            break;
        case 2:
            if ($p1 == 1) { addInfoInArray( Strings::rdecimal($precio1, 2) ); } else { addInfoInArray( Strings::rdecimal($precio2, 2) ); }
            if ($p3 == 3) { addInfoInArray( Strings::rdecimal($precio3, 2) ); } else { addInfoInArray( Strings::rdecimal($precio2, 2) ); }
            break;
        default:
            addInfoInArray(Strings::rdecimal($precio1, 2));
            addInfoInArray(Strings::rdecimal($precio2, 2));
            addInfoInArray(Strings::rdecimal($precio3, 2));
    }
    addInfoInArray(round($x['exunidad']));
    switch ($sumap) {
        case 1:
            if ($x['esexento'] == 0) { addInfoInArray( Strings::rdecimal($x['preciou'. $sumap2 ]* $iva, 2) ); } else { addInfoInArray( Strings::rdecimal($x['preciou'. $sumap2 ], 2 ) ); }
            break;
        case 2:
            if ($p1 == 1) { addInfoInArray( Strings::rdecimal($preciou1, 2) ); } else { addInfoInArray( Strings::rdecimal($preciou2, 2) ); }
            if ($p3 == 3) { addInfoInArray( Strings::rdecimal($preciou3, 2) ); } else { addInfoInArray( Strings::rdecimal($preciou2, 2) ); }
            break;
        default:
            addInfoInArray(Strings::rdecimal($preciou1, 2));
            addInfoInArray(Strings::rdecimal($preciou2, 2));
            addInfoInArray(Strings::rdecimal($preciou3, 2));
    }
    if ($cubi == 1) {
        addInfoInArray($x['cubicaje']);
    }
    $pdf->Row($info);
}*/

$pdf->Output();
