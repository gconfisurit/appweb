<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("clientessintr_modelo.php");

//INSTANCIAMOS EL MODELO
$clientessintr = new ClientesSintr();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$codvend = $_GET['vendedor'];

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
        // Logo
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', '', 11);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(40, 10, 'REPORTE DE CLIENTES SIN REALIZAR TRASACCIONES', 0, 1, 'C');
        $this->Cell(80);
        $this->Cell(50, 10, 'DEL ' . date(FORMAT_DATE, strtotime($GLOBALS['fechai'])) . ' AL ' . date(FORMAT_DATE, strtotime($GLOBALS['fechaf'])), 0, 0, 'C');

        // Salto de línea
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(19), 6, utf8_decode(Strings::titleFromJson('ruta')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(25), 6, utf8_decode(Strings::titleFromJson('codclie')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(100), 6, utf8_decode(Strings::titleFromJson('razon_social')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(45), 6, utf8_decode(Strings::titleFromJson('saldo')), 1, 1, 'C', true);
    }

}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);

$pdf->SetWidths($width);

$query = $clientessintr->getclientessintr($fechai, $fechaf, $codvend);
$num = count($query);

foreach ($query as $i) {

    $pdf->Row(
        array(
            $i['codvend'],
            $i['codclie'],
            $i['descrip'],
            Strings::rdecimal($i['debe'],2)
        )
    );
}
$pdf->Ln(10);
$pdf->Cell(190, 10, 'Total de Clientes:  '.$num, 0, 1, 'C');
$pdf->Output();
