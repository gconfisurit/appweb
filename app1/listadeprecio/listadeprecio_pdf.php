<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("listadeprecio_modelo.php");

//INSTANCIAMOS EL MODELO
$precios = new Listadeprecio();

$depos = $_GET['depos'];
$marcas = $_GET['marcas'];
$orden = $_GET['orden'];
$exis = $_GET['exis'];
$iva = $_GET['iva'];
$cubi = $_GET['cubi'];
$p1 = str_replace("1","1",$_GET['p1']);
$p2 = str_replace("1","2",$_GET['p2']);
$p3 = str_replace("1","3",$_GET['p3']);
$sumap = $_GET['p1'] + $_GET['p2'] + $_GET['p3'];
$sumap2 = $p1 + $p2 + $p3;
$pAux = '';
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
        switch ($GLOBALS['cubi']){
            case 0:
                $aux = 1;
                break;
            case 1:
                $aux = 0;
                break;
            default:
                $aux = 0;
        }

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
        switch ($GLOBALS['sumap']) {
            case 1:
                $anchoAdicional += (50*2);// +25+25
                break;
            case 2:
                $anchoAdicional += (25*2);// +25
                break;
            default: /** 0 || 3**/
                $anchoAdicional += 0;// +0
        }
        if ($GLOBALS['cubi'] == 0) {
            $anchoAdicional += 24;
        }

        // Logo
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', '', 12);
        // Movernos a la derecha
        $this->Cell(140);
        // Título
        $this->Cell(40, 10, 'REPORTE DE LISTADO DE PRECIOS E INVENTARIO', 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->SetFont('Arial', 'B', 8.5);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(20 + ($anchoAdicional*0.20)), 6, utf8_decode(Strings::titleFromJson('codigo_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(65 + ($anchoAdicional*0.40)), 6, utf8_decode(Strings::titleFromJson('descrip_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(30 + ($anchoAdicional*0.20)), 6, utf8_decode(Strings::titleFromJson('marca_prod')), 1, 0, 'C', true);
        //BULTOS
        $this->Cell(addWidthInArray(18  + ($anchoAdicional*0.10)), 6, utf8_decode(Strings::titleFromJson('bultos')), 1, 0, 'C', true);
        switch ($GLOBALS['sumap']) {
            case 1:
                $this->Cell(addWidthInArray(25), 6, 'Pre '.$GLOBALS['sumap2'].' Bul', 1, 0, 'C', true);
                break;
            case 2:
                if($GLOBALS['p1'] == 1){ $pAux = $GLOBALS['p1']; }else{ $pAux = $GLOBALS['p2'];}
                $this->Cell(addWidthInArray(25), 6, 'Pre '.$pAux.' Bul', 1, 0, 'C', true);
                if ($GLOBALS['p3'] == 3){ $pAux = $GLOBALS['p3']; }else{ $pAux = $GLOBALS['p2'];}
                $this->Cell(addWidthInArray(25), 6, 'Pre '.$pAux.' Bul', 1, 0, 'C', true);
                break;
            default: /** 0 || 3**/
                $this->Cell(addWidthInArray(25), 6, utf8_decode(Strings::titleFromJson('precio1_bulto')), 1, 0, 'C', true);
                $this->Cell(addWidthInArray(25), 6, utf8_decode(Strings::titleFromJson('precio2_bulto')), 1, 0, 'C', true);
                $this->Cell(addWidthInArray(25), 6, utf8_decode(Strings::titleFromJson('precio3_bulto')), 1, 0, 'C', true);
        }
        //PAQUETES
        $this->Cell(addWidthInArray(18  + ($anchoAdicional*0.10)), 6, utf8_decode(Strings::titleFromJson('paquetes')), 1, 0, 'C', true);
        switch ($GLOBALS['sumap']) {
            case 1:
                $this->Cell(addWidthInArray(25), 6, 'Pre '.$GLOBALS['sumap2'].' Paq', 1, $aux, 'C', true);
                break;
            case 2:
                if($GLOBALS['p1'] == 1){ $pAux = $GLOBALS['p1']; }else{ $pAux = $GLOBALS['p2'];}
                $this->Cell(addWidthInArray(25), 6, 'Pre '.$pAux.' Paq', 1, 0, 'C', true);
                if ($GLOBALS['p3'] == 3){ $pAux = $GLOBALS['p3']; }else{ $pAux = $GLOBALS['p2'];}
                $this->Cell(addWidthInArray(25), 6, 'Pre '.$pAux.' Paq', 1, $aux, 'C', true);
                break;
            default: /** 0 || 3**/
                $this->Cell(addWidthInArray(25), 6, utf8_decode(Strings::titleFromJson('precio1_paquete')), 1, 0, 'C', true);
                $this->Cell(addWidthInArray(25), 6, utf8_decode(Strings::titleFromJson('precio2_paquete')), 1, 0, 'C', true);
                $this->Cell(addWidthInArray(25), 6, utf8_decode(Strings::titleFromJson('precio3_paquete')), 1, $aux, 'C', true);
        }
        if ($GLOBALS['cubi'] == 1) {
            $this->Cell(addWidthInArray(24), 6, utf8_decode(Strings::titleFromJson('cubicaje')), 1, 1, 'C', true);
        }
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

$pdf->SetWidths($width);

$query = $precios->getListadeprecios($marcas, $depos, $exis, $orden);
$num = count($query);

foreach ($query as $x) {
    $j = 0;
    if (!$x['esexento']) {
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
        default: /** 0 || 3**/
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
        default: /** 0 || 3**/
            addInfoInArray(Strings::rdecimal($preciou1, 2));
            addInfoInArray(Strings::rdecimal($preciou2, 2));
            addInfoInArray(Strings::rdecimal($preciou3, 2));
    }
    if ($cubi == 1) {
        addInfoInArray($x['cubicaje']);
    }
    $pdf->Row($info);
}
$pdf->Ln(10);
$pdf->Cell(335, 10, 'Total de Productos:  '.$num, 0, 1, 'C');

$pdf->Output();
