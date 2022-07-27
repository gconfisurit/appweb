<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY . 'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("skunovendidos_modelo.php");

//INSTANCIAMOS EL MODELO
$sku = new Skunovendidos();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];

$j = 0;
$documentsize = 'Legal';
$width = array();
function addWidthInArray($num)
{
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
        $this->Image(PATH_LIBRARY . 'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 12);
        // Movernos a la derecha
        $this->Cell(140);
        // Título
        $this->Cell(40, 10, 'REPORTE DE PRODUCTOS NO VENDIDOS DEL ' . date(FORMAT_DATE, strtotime($GLOBALS['fechai'])) . ' AL ' . date(FORMAT_DATE, strtotime($GLOBALS['fechaf'])), 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(200, 220, 255);
        // titulo de columnas
        $this->Cell(addWidthInArray(20), 6, utf8_decode(Strings::titleFromJson('numerod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(20), 6, utf8_decode(Strings::titleFromJson('codvend')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(20), 6, utf8_decode(Strings::titleFromJson('descrip_vend')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(23), 6, utf8_decode(Strings::titleFromJson('codclie')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(38), 6, utf8_decode(Strings::titleFromJson('razon_social')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(27), 6, utf8_decode(Strings::titleFromJson('codigo_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(35), 6, utf8_decode(Strings::titleFromJson('descrip_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(20), 6, utf8_decode(Strings::titleFromJson('marca_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(25), 6, utf8_decode(Strings::titleFromJson('tipo_empaque')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(18), 6, utf8_decode(Strings::titleFromJson('cantidad')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(20), 6, utf8_decode(Strings::titleFromJson('subtotal')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(21), 6, utf8_decode(Strings::titleFromJson('inv_bultos')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(21), 6, utf8_decode(Strings::titleFromJson('inv_paquetes')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(25), 6, utf8_decode(Strings::titleFromJson('fecha')), 1, 1, 'C', true);
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

$data = array(
    'fechai' => $fechai,
    'fechaf' => $fechaf,
);

$query = $sku->getnovendidos($data);

foreach ($query as $i) {

    $esunid = ($i["esunid"]=='1') ? 'PAQUETE' : 'BULTO';

    $pdf->Row(
        array(
            $i['numerod'],
            $i['codvend'],
            $i['vendedor'],
            $i['codclie'],
            $i['cliente'],
            $i['coditem'],
            $i['descrip1'],
            $i['marca'],
            $esunid,
            Strings::rdecimal($i['cantidad'],1),
            Strings::rdecimal($i['totalitem'], 2),
            Strings::rdecimal($i['bultos'], 2),
            Strings::rdecimal($i['paquetes'],2),
            date(FORMAT_DATE, strtotime($i['fechae']))
        )
    );
}
$pdf->Output();


