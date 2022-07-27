<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("motivonoventa_modelo.php");

//INSTANCIAMOS EL MODELO
$motivonoventa = new MotivoNoVenta();

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
        $this->Cell(40, 10, 'REPORTE DE MOTIVO NO VENTA', 0, 1, 'C');

        $this->Cell(80);
        $this->Cell(40, 10, 'DE ' . date(FORMAT_DATE, strtotime($GLOBALS['fechai'])) . ' AL ' . date(FORMAT_DATE, strtotime($GLOBALS['fechaf'])) . ' EDV: ' . (!hash_equals('-', $GLOBALS['codvend']) ? $GLOBALS['codvend'] : 'Todos'), 0, 0, 'C');

        // Salto de línea
        $this->Ln(15);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(24), 6, utf8_decode(Strings::titleFromJson('fecha')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(25), 6, utf8_decode(Strings::titleFromJson('ruta')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(28), 6, utf8_decode(Strings::titleFromJson('codclie')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(70), 6, utf8_decode(Strings::titleFromJson('razon_social')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(41), 6, utf8_decode(Strings::titleFromJson('causa')), 1, 1, 'C', true);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);

$pdf->SetWidths($width);

$data = array(
    'edv'    => $codvend,
    'fechai' => $fechai,
    'fechaf' => $fechaf
);

$query = $motivonoventa->getMotivoNoVenta($data);

foreach ($query as $i) {


    $motivo = '';
    switch (intval($i["motivo"])) {
        case 1: $motivo = "Cliente Cerrado"; break;
        case 2: $motivo = "Cliente con Inventario"; break;
        case 3: $motivo = "Cliente a la espera de pedido anterior"; break;
        case 4: $motivo = "Cliente no visitado"; break;
        case 5: $motivo = "Cliente fuera de ruta"; break;
        case 6: $motivo = "Cliente con deuda y sin pago"; break;
        case 7: $motivo = "Cliente compra a la competencia"; break;
        case 8: $motivo = "Cliente considera altos los precios"; break;
    }

    $pdf->Row(
        array(
            date(FORMAT_DATE, strtotime($i['fecha'])),
            $i['edv'],
            $i['codclie'],
            utf8_decode($i['descrip']),
            $motivo,
        )
    );
}
$pdf->Ln(10);
$pdf->Cell(190, 10, 'total de Clientes: '. count($query), 0, 1, 'C');
$pdf->Output();

?>