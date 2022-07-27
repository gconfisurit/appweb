<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("tasadolar_modelo.php");

//INSTANCIAMOS EL MODELO
$tasa = new TasaDolar();

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
        $this->Cell(40, 10, 'HISTORICO DE TASA DOLAR COMPRA', 0, 0, 'C');
        // Salto de línea
        $this->Ln(15);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(20), 6, Strings::titleFromJson('#'), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(45), 6, Strings::titleFromJson('fecha'), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(120), 6, Strings::titleFromJson('tasa'), 1, 1, 'C', true);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);

$pdf->SetWidths($width);

$datos = $tasa->get_tasadolar();
$num = count($datos);

foreach ($datos as $key=>$i) {

    $pdf->Row(
        array(
            $key+1,
            date(FORMAT_DATE, strtotime($i["fechae"])),
            Strings::rdecimal($i["tasa"], 2)
        )
    );
}
$pdf->Output();

?>