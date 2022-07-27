<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("activacionclientes_modelo.php");

//INSTANCIAMOS EL MODELO
$actclientes = new Activacionclientes();

$fechaf = $_GET['fecha_final'];

$j = 0;
$width = array();
function addWidthInArray($num){
    $GLOBALS['width'][$GLOBALS['j']] = $num;
    $GLOBALS['j'] = $GLOBALS['j'] + 1;
    return $num;
}

class PDF extends FPDF
{
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
        $this->Cell(30, 10, 'REPORTE DE CLIENTES NO ACTIVOS', 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->SetFont('Arial', '', 9);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(32), 6, utf8_decode(Strings::titleFromJson('fecha_ult_venta')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(27), 6, utf8_decode(Strings::titleFromJson('codclie')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(66), 6, utf8_decode(Strings::titleFromJson('razon_social')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(20), 6, Strings::titleFromJson('rif'), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(19), 6, Strings::titleFromJson('ruta'), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(25), 6, Strings::titleFromJson('saldo_pendiente'), 1, 1, 'C', true);

    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);

$pdf->SetWidths($width);

$query = $actclientes->lista_busca_activacionclientes($fechaf);

foreach ($query as $i) {

    $pdf->Row(
        array(
            date(FORMAT_DATE, strtotime($i['fechauv'])),
            $i['codclie'],
            utf8_decode($i['descrip']),
            $i['id3'],
            $i['codvend'],
            Strings::rdecimal($i['total'], 2)
        )
    );

}
$pdf->Output();

?>
