<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("clientesbloqueados_modelo.php");

//INSTANCIAMOS EL MODELO
$bloclientes = new Clientesbloqueados();

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
        $this->Cell(40, 10, 'REPORTE DE CLIENTES BLOQUEADOS', 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(18), 6, utf8_decode(Strings::titleFromJson('codclie')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(60), 6, utf8_decode(Strings::titleFromJson('razon_social')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(18), 6, utf8_decode(Strings::titleFromJson('rif')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(50), 6, utf8_decode(Strings::titleFromJson('direccion')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(21), 6, utf8_decode(Strings::titleFromJson('estatus')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(24), 6, utf8_decode(Strings::titleFromJson('dia_visita')), 1, 1, 'C', true);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);

$pdf->SetWidths($width);

$total = $bloclientes->getTotalClientesPorCodigo($codvend);
$query = $bloclientes->ClientesBloqueadosPorVendedor($codvend);
$num = count($query);

foreach ($query as $i) {

    $escredito = "";
    if ($i['escredito'] == 1) {
        $escredito = "SOLVENTE";
    } else {
        $escredito = "BLOQUEADO: " . utf8_encode($i['observa']);
    }

    $pdf->Row(
        array(
            $i['codclie'],
            utf8_decode($i['descrip']),
            $i['id3'],
            utf8_encode($i['direc1']) . " " . utf8_encode($i['direc2']),
            $escredito,
            $i['diasvisita']
        )
    );
}
$pdf->Ln(10);
$pdf->Cell(190, 10, 'Total de Clientes BLOQUEADOS:  '.$num.'  de  '.$total[0]['cuenta'].' Clientes.', 0, 1, 'C');
$pdf->Output();

?>