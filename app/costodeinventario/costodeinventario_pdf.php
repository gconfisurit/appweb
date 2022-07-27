<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO
require_once("costodeinventario_modelo.php");

//INSTANCIAMOS EL MODELO
$costo = new CostodeInventario();

//obtenemos la marca seleccionada enviada por get
$marca = $_GET['marca'];

//verificamos si existe al menos 1 deposito selecionado
//y se crea el array.
if(isset($_GET['depo'])){
    $numero = $_GET['depo'];
} else {
    $numero = array();
}

//se contruye un string para listar los depositvos seleccionados
//en caso que no haya ninguno, sera vacio
$edv = "";
if(count($numero)>0) {
    foreach ($numero AS $i) {
        $edv .= "'" . $i . "',";
    }
}

        $factor=0;
        $costosd = 0;
        $costos_pd = 0;
        $costos = 0;
        $costos_p = 0;
        $precios = 0;
        $bultos = 0;
        $paquetes = 0;
        $total_costo_bultos = 0;
        $total_costo_paquetes = 0;
        $total_costo_bultosd = 0;
        $total_costo_paquetesd = 0;
        $total_tara = 0;

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
        $this->Cell(80);
        // Título
        $this->Cell(40, 10, 'REPORTE DE COSTOS E INVENTARIO', 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(31), 6, utf8_decode(Strings::titleFromJson('codigo_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(57), 6, utf8_decode(Strings::titleFromJson('descrip_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(22), 6, utf8_decode(Strings::titleFromJson('marca_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(29), 6, utf8_decode(Strings::titleFromJson('costo_bultos')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(29), 6, utf8_decode(Strings::titleFromJson('costo_paquete')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(29), 6, utf8_decode(Strings::titleFromJson('costo_bultosd')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(29), 6, utf8_decode(Strings::titleFromJson('costo_paqueted')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(29), 6, utf8_decode(Strings::titleFromJson('precio')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(19), 6, utf8_decode(Strings::titleFromJson('bultos')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(19), 6, utf8_decode(Strings::titleFromJson('paquetes')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(37), 6, utf8_decode(Strings::titleFromJson('totalcosto_bultos')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(45), 6, utf8_decode(Strings::titleFromJson('totalcosto_paquetes')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(37), 6, utf8_decode(Strings::titleFromJson('totalcosto_bultosd')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(45), 6, utf8_decode(Strings::titleFromJson('totalcosto_paquetesd')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(18), 6, utf8_decode(Strings::titleFromJson('tara')), 1, 1, 'C', true);

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
$pdf->AddPage('L',array(200,490));
$pdf->SetFont('Arial', '', 8);

$pdf->SetWidths($width);

//realiza la consulta con marca y almacenes
$query = $costo->getCostosdEinventario($edv, $marca);

foreach ($query as $i) {

    if ($i['display'] == 0) {
        $cdisplay = 0;
    } else {
        $cdisplay = $i['costo'] / $i['display'];
    }

    $factor=$i['factor'];

    $pdf->Row(
        array(
            $i['codprod'],
            utf8_decode($i['descrip']),
            $i['marca'],
            Strings::rdecimal($i['costo'],2),
            Strings::rdecimal($cdisplay,2),
            Strings::rdecimal($i['costo']/$factor,2),
            Strings::rdecimal($cdisplay/$factor,2),
            Strings::rdecimal($i['precio'],2),
            Strings::rdecimal($i['bultos'],2),
            Strings::rdecimal($i['paquetes'],2),
            Strings::rdecimal($i['costo'] * $i['bultos'],2),
            Strings::rdecimal($cdisplay * $i['paquetes'],2),
            Strings::rdecimal(($i['costo'] /$factor )* $i['bultos'],2),
            Strings::rdecimal(($cdisplay /$factor)* $i['paquetes'],2),
            Strings::rdecimal($i['tara'],2)
        )
    );

    $costos += $i['costo'];
    $costos_p += $cdisplay;
    $costosd += $i['costo']/$factor;
    $costos_pd += $cdisplay/$factor;
    $precios += $i['precio'];
    $bultos += $i['bultos'];
    $paquetes += $i['paquetes'];
    $total_costo_bultos += ($i['costo'] * $i['bultos']);
    $total_costo_paquetes += ($cdisplay * $i['paquetes']);
    $total_costo_bultosd += (($i['costo'] /$factor )* $i['bultos']);
    $total_costo_paquetesd += (($cdisplay /$factor)* $i['paquetes']);
    $total_tara += $i['tara'];
}
$pdf->SetFont('Arial', '', 9);
$pdf->Row(
    array(
        '', '', 'TOTALES: ',
        Strings::rdecimal($costos,2),
        Strings::rdecimal($costos_p,2),
        Strings::rdecimal($costosd,2),
        Strings::rdecimal($costos_pd,2),
        Strings::rdecimal($precios,2),
        Strings::rdecimal($bultos,2),
        Strings::rdecimal($paquetes,2),
        Strings::rdecimal($total_costo_bultos,2),
        Strings::rdecimal($total_costo_paquetes,2),
        Strings::rdecimal($total_costo_bultosd,2),
        Strings::rdecimal($total_costo_paquetesd,2),
        Strings::rdecimal($total_tara,2)
    )
);

$pdf->Output();

?>