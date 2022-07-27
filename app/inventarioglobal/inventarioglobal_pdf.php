<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("inventarioglobal_modelo.php");

//INSTANCIAMOS EL MODELO
$invglobal = new InventarioGlobal();

//verificamos si existe al menos 1 deposito selecionado
//y se crea el array.
$depos = $_GET['depo'] ?? array();

$fechaf = date('Y-m-d');
$dato = explode("-", $fechaf); //Hasta
$aniod = $dato[0]; //año
$mesd = $dato[1]; //mes
$diad = "01"; //dia
$fechai = $aniod . "-01-01";

$coditem = $cantidad = $tipo = array();
$t = 0;

//array of space in cells
$j = 0;
$width = array();
$documentsize = 'Legal';
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
        // Logo
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', '', 11);
        // Movernos a la derecha
        $this->Cell(100);
        // Título
        $this->Cell(40, 10, 'REPORTE DE INVENTARIO GLOBAL DEL ' . date(FORMAT_DATE, strtotime($GLOBALS['fechai'])) . ' AL ' . date(FORMAT_DATE, strtotime($GLOBALS['fechaf'])), 0, 0, 'C');
        // Salto de línea
        $this->Ln(15);
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(18), 6, utf8_decode(('Codigo')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(50), 6, utf8_decode(('Descripcion')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(22), 6, utf8_decode(('B. x Despachar')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(22), 6, utf8_decode(('P. x Despachar')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(18), 6, utf8_decode(('B. Sistema')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(18), 6, utf8_decode(('P. Sistema')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(12), 6, utf8_decode(('Total B.')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(12), 6, utf8_decode(('Total P.')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(13), 6, 'B. Fisico', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(13), 6, 'P. Fisico', 1, 1, 'C', true);
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
$pdf->AddPage('P', $documentsize);
$pdf->SetFont('Arial', '', 8);

$pdf->SetWidths($width);

$devolucionesDeFactura = Factura::getInvoiceReturns($fechai, $fechaf, $depos);
if(count($devolucionesDeFactura) > 0) {
    foreach ($devolucionesDeFactura as $devol) {
        $coditem[] = $devol['coditem'];
        $cantidad[] = $devol['cantidad'];
        $tipo[] = $devol['esunid'];
        $t += 1;
    }
}

$relacion_inventarioglobal = $invglobal->getInventarioGlobal($fechai, $fechaf, $depos);
$tbulto = $tpaq = $tbultoinv = $tpaqinv = $tbultsaint = $tpaqsaint = 0;
$cant_paq = 0;
$cant_bul = 0;

foreach ($relacion_inventarioglobal as $i) {
    if($t > 0) {
        for($e = 0; $e < $t; $e++)
        {
            if($coditem[$e] == $i['CodProd']) {
                switch ($tipo[$e]) {
                    case '0':
                        $cant_bul = $i['bultosxdesp'] - $cantidad[$e];
                        break;
                    case '1':
                        $cant_paq = $i['paqxdesp'] - $cantidad[$e];
                        break;
                }
//                        $e = $t + 2;
                break;
            }else{
                $cant_bul = $i['bultosxdesp'];
                $cant_paq = $i['paqxdesp'];
            }
        }
    } else {
        $cant_bul = $i['bultosxdesp'];
        $cant_paq = $i['paqxdesp'];
    }
    ////conversión de bultos a paquetes
    $cantemp = $i['CantEmpaq'];
    $invbut  = $i['exis'];
    $invpaq  = $i['exunid'];

    if($cant_paq >= $cantemp){
        $conv = floor($cant_paq / $cantemp);
        $cant_paq -= ($conv * $cantemp);
        $cant_bul += $conv;
    }
    if($invpaq >= $cantemp){
        $conv = floor($invpaq / $cantemp);
        $invpaq -= ($conv * $cantemp);
        $invbut += $conv;
    }
    $tinvbult = $invbut + $cant_bul;
    $tinvpaq = $invpaq + $cant_paq;

    if($tinvpaq >= $cantemp){
        $conv1 = floor($tinvpaq / $cantemp);
        $tinvpaq -= ($conv1 * $cantemp);
        $tinvbult += $conv1;
    }

    $pdf->Row(
        array(
            $i['CodProd'],
            utf8_decode($i['Descrip']),
            Strings::rdecimal($cant_bul,0),
            Strings::rdecimal($cant_paq,0),
            Strings::rdecimal($invbut,0),
            Strings::rdecimal($invpaq,0),
            Strings::rdecimal($tinvbult,0),
            Strings::rdecimal($tinvpaq,0),
            utf8_decode(''),
            utf8_decode('')
        )
    );

    //ACUMULAMOS LOS TOTALES
    $tbulto     += $cant_bul;
    $tpaq       += $cant_paq;
    $tbultoinv  += $tinvbult;
    $tpaqinv    += $tinvpaq;
    $tbultsaint += $invbut;
    $tpaqsaint  += $invpaq;
}
$pdf->SetFont('Arial', '', 9);
$pdf->Row(
    array(
        '', 'TOTALES: ',
        Strings::rdecimal($tbulto,0),
        Strings::rdecimal($tpaq,0),
        Strings::rdecimal($tbultsaint,0),
        Strings::rdecimal($tpaqsaint,0),
        Strings::rdecimal($tbultoinv,0),
        Strings::rdecimal($tpaqinv,0),
        utf8_decode(''),
        utf8_decode('')
    )
);
$pdf->Ln(10);

$pdf->Cell(190, 10, 'Facturas sin despachar:  '. count($devolucionesDeFactura), 0, 1, 'C');
$pdf->Output();

?>