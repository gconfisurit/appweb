<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("ventaskgedvporcategoria_modelo.php");

//INSTANCIAMOS EL MODELO
$ventaskg = new VentasKgEdvPorCategoria();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$vendedor = $_GET['vendedor'];
$inst = $_GET['instancia'];

$vende = ($vendedor=='-') ? 'TODOS' : $vendedor;
$instan = ($inst=='-') ? 'TODAS' : $inst;

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
        $this->Cell(40, 12, 'VENTAS EN KG DE EJECUTIVO EN VENTAS (X CATEGORIA)', 0, 1, 'C');
        $this->Ln(1);

        $this->Cell(5);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(230,230,230);
        $this->Cell(10, 7, utf8_decode('Desde:'), 0, 0, 'R');
        $this->Cell(28, 6, $GLOBALS['fechai'], 'B', 0, 'C', true);
        $this->Cell(20, 7, utf8_decode('Hasta:'), 0, 0, 'R');
        $this->Cell(28, 6, $GLOBALS['fechaf'], 'B', 0, 'C', true);
        $this->Cell(28, 7, utf8_decode('Vendedor:'), 0, 0, 'R');
        $this->Cell(12, 6, $GLOBALS['vende'], 'B', 0, 'C', true);
        $this->Cell(28, 7, utf8_decode('Instancia:'), 0, 0, 'R');
        $this->Cell(12, 6, $GLOBALS['instan'], 'B', 0, 'C', true);

        // Salto de línea
        $this->Ln(20);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(85), 6, utf8_decode(Strings::titleFromJson('categoria')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(35), 6, utf8_decode(Strings::titleFromJson('und_bultos')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(35), 6, utf8_decode(Strings::titleFromJson('und_kg')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(35), 6, utf8_decode(Strings::titleFromJson('monto_bs')), 1, 1, 'C', true);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);

$pdf->SetWidths($width);


$datos = array(
    'fechai'    => $fechai,
    'fechaf'    => $fechaf,
    'vendedor'  => $vendedor,
    'instancia' => $inst,
);

$total_monto = $total_peso = $total_cant = 0;
$instancias_data = $ventaskg->getinstancias($datos);

foreach ($instancias_data as $key => $instancia) {

    $peso = $cant = $monto = 0;
    $notas_debitos = $ventaskg->getNotaDebitos($datos, $instancia["codinst"]);
    if (ArraysHelpers::validate($notas_debitos)) {
        foreach ($notas_debitos as $i) {
            if ($i['unidad'] == 0) {
                if ($i['tipo'] == 'A') {
                    $monto += $i["monto"];
                    $peso  += $i["peso"];
                    $cant  += $i["cantidad"];
                } else {
                    $monto -= $i["monto"];
                    $peso  -= $i["peso"];
                    $cant  -= $i["cantidad"];
                }
            } else {
                if ($i['tipo'] == 'A') {
                    $monto += $i["monto"];
                    $peso  += (($i["peso"]/$i["paquetes"]) * $i["cantidad"]);
                    $cant  += ($i["cantidad"] / $i["paquetes"]);
                } else {
                    $monto -= $i["monto"];
                    $peso  -= (($i["peso"]/$i["paquetes"]) * $i["cantidad"]);
                    $cant  -= ($i["cantidad"] / $i["paquetes"]);
                }
            }
        }
    }

    $descuento = Functions::find_discount($datos['fechai'], $datos['fechaf'], $instancia["codinst"]);
    $monto -= $descuento;
    $total_cant  += $cant;
    $total_peso  += $peso;
    $total_monto += $monto;

    $pdf->Row(
        array(
            strtoupper($instancia["descrip"]),
            number_format($cant, 2, ",", "."),
            number_format($peso, 2, ",", "."),
            number_format($monto, 2, ",", "."),
        )
    );
}
$pdf->SetFont('Arial', 'B', 7);
$pdf->Row(
    array(
        'TOTAL: ',
        number_format($total_cant, 2, ",", ".").' Und',
        number_format($total_peso, 2, ",", ".").' Kg',
        number_format($total_monto, 2, ",", ".").' (Bs, SIN/IVA)',
    )
);

$pdf->Output();

?>