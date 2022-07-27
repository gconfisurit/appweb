<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("pedidossinfacturar_modelo.php");

//INSTANCIAMOS EL MODELO
$pedsinfacturar = new Pedidossinfacturar();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$marca = $_GET['marca'];

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
        $this->Cell(40, 10, 'RELACION DE PEDIDOS SIN FACTURAR DE ' . date(FORMAT_DATE, strtotime($GLOBALS['fechai'])) . ' AL ' . date(FORMAT_DATE, strtotime($GLOBALS['fechaf'])), 0, 0, 'C');
        // Salto de línea
        $this->Ln(15);
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(15), 6, utf8_decode(Strings::titleFromJson('fecha')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(20), 6, utf8_decode(Strings::titleFromJson('marca_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(24), 6, utf8_decode(Strings::titleFromJson('codigo_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(34), 6, utf8_decode(Strings::titleFromJson('descrip_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(32), 6, utf8_decode(Strings::titleFromJson('cliente')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(12), 6, utf8_decode(Strings::titleFromJson('unidad')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(15), 6, utf8_decode(Strings::titleFromJson('cantidad')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(25), 6, utf8_decode(Strings::titleFromJson('total')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(13), 6, utf8_decode(Strings::titleFromJson('ruta')), 1, 1, 'C', true);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);

$pdf->SetWidths($width);

$data = array(
    'fechai' => $fechai,
    'fechaf' => $fechaf,
    'marca'  => $marca
);

$query = $pedsinfacturar->getPedidos($data);

foreach ($query as $i) {

    $unidad = ($i['unidad'] == 1)
        ? Strings::titleFromJson('paquete')
        : Strings::titleFromJson('bulto');

    $pdf->Row(
        array(
            date(FORMAT_DATE, strtotime($i["fechae"])),
            $i["marca"],
            $i["coditem"],
            $i["producto"],
            $i["cliente"],
            $unidad,
            Strings::rdecimal($i["cantidad"],0),
            Strings::rdecimal($i["total"],2),
            $i["ruta"]
        )
    );
}
$pdf->Output();


?>