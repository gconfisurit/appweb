<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("clientesnuevos_modelo.php");

//INSTANCIAMOS EL MODELO
$clientesnuevos = new ClientesNuevos();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];

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
        $this->Cell(40, 10, 'REPORTE DE CLIENTES NUEVOS DE ' . date(FORMAT_DATE, strtotime($GLOBALS['fechai'])) . ' AL ' . date(FORMAT_DATE, strtotime($GLOBALS['fechaf'])), 0, 1, 'C');

        // Salto de línea
        $this->Ln(10);
        $this->SetFont('Arial', '', 9);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(25), 6, utf8_decode(Strings::titleFromJson('codclie')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(80), 6, utf8_decode(Strings::titleFromJson('razon_social')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(30), 6, utf8_decode(Strings::titleFromJson('rif')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(20), 6, utf8_decode(Strings::titleFromJson('fecha')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(30), 6, utf8_decode(Strings::titleFromJson('ruta')), 1, 1, 'C', true);

    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);

$pdf->SetWidths($width);

$query = $clientesnuevos->getClientesNuevos($fechai, $fechaf);
$num = count($query);

foreach ($query as $i) {

    $pdf->Row(
        array(
            $i['codclie'],
            $i['descrip'],
            $i['id3'],
            date(FORMAT_DATE, strtotime($i['fechae'])),
            $i['codvend']
        )
    );
}
$pdf->Ln(10);
$pdf->Cell(190, 10, 'Total de Clientes Nuevos:  '.$num, 0, 1, 'C');
$pdf->Output();


?>
