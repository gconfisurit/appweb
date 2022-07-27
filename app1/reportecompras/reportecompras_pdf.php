<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("reportecompras_modelo.php");

//INSTANCIAMOS EL MODELO
$reporte = new ReporteCompras();

$fechai = $_GET['fechaf'];
$fechaf = $_GET['fechaf'];
$marca = $_GET['marca'];
$n = $_GET['n'];
$v = $_GET['v'];

$hoy = date(FORMAT_DATE);

$i = 0;
$j = 0;
$documentsize = 'Legal';
$width = array();
$info = array();

function addWidthInArray($num){
    $GLOBALS['width'][$GLOBALS['i']] = $num;
    $GLOBALS['i'] = $GLOBALS['i'] + 1;
    return $num;
}

function addInfoInArray($info){
    $GLOBALS['info'][$GLOBALS['j']] = $info;
    $GLOBALS['j'] = $GLOBALS['j'] + 1;
}

$separa = explode("-", $fechai);
$dia = $separa[2];
$mes = $separa[1];
$anio = $separa[0];

$fechaiA = date(FORMAT_DATE_TO_EVALUATE, mktime(0,0,0,($mes)-1,1, $anio));
$fechafA = date(FORMAT_DATE_TO_EVALUATE, mktime(0,0,0,$mes,1, $anio)-1);


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
        $this->SetFont('Arial', 'B', 12);
        // Movernos a la derecha
        $this->Cell(140);
        // Título
        $this->Cell(40, 10, 'REPORTE DE COMPRAS DEL' . date(FORMAT_DATE, strtotime($_GET['fechaf'])), 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(200,220,255);
        $acumulador_espaciado = 0;
        // titulo de columnas
        $this->Cell(6, 18, utf8_decode(Strings::titleFromJson('#')), 1, 0, 'C', true);
        $this->Cell(20, 18, substr(utf8_decode(Strings::titleFromJson('codigo_prod')), 0, 7), 1, 0, 'C', true);
        $this->Cell(30, 18, utf8_decode(Strings::titleFromJson('descrip_prod')), 1, 0, 'C', true);
        $this->MultiCell2(16, 9, utf8_decode(Strings::titleFromJson('display_por_bulto')), 1, 0, 'C', true);
        $this->Ln(-9);
        $this->Cell($acumulador_espaciado += 72);
        $this->MultiCell2(44, 12, utf8_decode(Strings::titleFromJson('ultimo_precio_compra')), 1, 0, 'C', true);
        /*$this->Ln(-6);*/
        $this->Cell($acumulador_espaciado += 44);
        $this->Cell(13, 18, substr(utf8_decode(Strings::titleFromJson('porcentaje_rentabilidad')),0,5), 1, 0, 'C', true);
        $this->MultiCell2(31, 6, utf8_decode(Strings::titleFromJson('fecha_penultima_compra')), 1, 0, 'C', true);
        $this->Ln(-6);
        $this->Cell($acumulador_espaciado += 44);
        $this->MultiCell2(31, 6, utf8_decode(Strings::titleFromJson('fecha_ultima_compra')), 1, 0, 'C', true);
        $this->Ln(-6);
        $this->Cell($acumulador_espaciado += 31);
        $this->MultiCell2(30, 6, utf8_decode(Strings::titleFromJson('ventas_mes_anterior')), 1, 0, 'C', true);
        $this->Ln(-6);
        $this->Cell($acumulador_espaciado += 30);
        $this->MultiCell2(13, 4.5, utf8_decode(Strings::titleFromJson('ventas_total_ult_mes')), 1, 0, 'C', true);
        $this->Ln(-13.5);
        $this->Cell($acumulador_espaciado += 13);
        $this->MultiCell2(20, 6, utf8_decode(Strings::titleFromJson('existencia_actual_bultos')), 1, 0, 'C', true);
        $this->Ln(-12);
        $this->Cell($acumulador_espaciado += 20);
        $this->MultiCell2(20, 9, utf8_decode(Strings::titleFromJson('dias_inventario')), 1, 0, 'C', true);
        $this->Ln(-9);
        $this->Cell($acumulador_espaciado += 20);
        $this->MultiCell2(24, 9, utf8_decode(Strings::titleFromJson('prod_no_vendidos')), 1, 0, 'C', true);
        $this->Ln(-9);
        $this->Cell($acumulador_espaciado += 24);
        $this->Cell(20, 18, utf8_decode(Strings::titleFromJson('sugerido')), 1, 0, 'C', true);
        $this->Cell(14, 18, utf8_decode(Strings::titleFromJson('pedido')), 1, 1, 'C', true);

        $this->Ln(-6);
        $this->Cell(72);
        $this->Cell(22, 6, utf8_decode(Strings::titleFromJson('display')), 1, 0, 'C', true);
        $this->Cell(22, 6, utf8_decode(Strings::titleFromJson('bulto')), 1, 0, 'C', true);
        $this->Cell(13);
        $this->Cell(19, 6, utf8_decode(Strings::titleFromJson('fecha')), 1, 0, 'C', true);
        $this->Cell(12, 6, utf8_decode(Strings::titleFromJson('bultos')), 1, 0, 'C', true);
        $this->Cell(19, 6, utf8_decode(Strings::titleFromJson('fecha')), 1, 0, 'C', true);
        $this->Cell(12, 6, utf8_decode(Strings::titleFromJson('bultos')), 1, 0, 'C', true);
        $this->Cell(7.5, 6, '1', 1, 0, 'C', true);
        $this->Cell(7.5, 6, '2', 1, 0, 'C', true);
        $this->Cell(7.5, 6, '3', 1, 0, 'C', true);
        $this->Cell(7.5, 6, '4', 1, 1, 'C', true);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation, $GLOBALS['documentsize']);
    }

    function Row($data, $numberColumn = [], $fill = false)
    {
        //Calculate the height of the row
        $nb = 0;
        $j = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            if($j < count($numberColumn) and $i == $numberColumn[$j]){
                $this->SetFillColor(255,57,57);
                $this->MultiCell($w, 5, $data[$i], 0, $a, $fill);
                $j++;
            } else {
                $this->MultiCell($w, 5, $data[$i], 0, $a);
            }

            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L', $documentsize);
$pdf->SetFont('Arial', '', 8);

$pdf->SetWidths(array(6, 20, 30, 16, 22, 22, 13, 19, 12, 19, 12, 7.5, 7.5, 7.5, 7.5, 13, 20, 20, 24, 20, 14));

$codidos_producto = $reporte->get_codprod_por_marca(ALMACEN_PRINCIPAL, $marca);
$num=0;
foreach ($codidos_producto as $key => $coditem)
{
    #Obtencion de datos
    $producto    = $reporte->get_datos_producto($coditem["codprod"]);
    $costos      = $reporte->get_costos($coditem["codprod"]);
    $ult_compras = $reporte->get_ultimas_compras($coditem["codprod"]);
    $ventas      = $reporte->get_ventas_mes_anterior($coditem["codprod"], $fechaiA, $fechafA);
    $bultosExis  = $reporte->get_bultos_existentes(ALMACEN_PRINCIPAL, $coditem["codprod"]);
    $no_vendidos = $reporte->get_productos_no_vendidos($coditem["codprod"], $fechai, $fechaf);

    #Calculos
    $rentabilidad = ReporteComprasHelpers::rentabilidad($producto[0]["precio1"], $producto[0]["costoactual"]);
    $fechapenultimacompra  = (count($ult_compras) > 1) ? date(FORMAT_DATE, strtotime($ult_compras[1]["fechae"])) : '-----';
    $bultospenultimacompra = (count($ult_compras) > 1) ? Strings::rdecimal($ult_compras[1]["cantBult"], 0) : 0;
    $fechaultimacompra   = (count($ult_compras) > 0) ? date(FORMAT_DATE,strtotime($ult_compras[0]["fechae"])) : '-----';
    $bultosultimacompra  = (count($ult_compras) > 0) ? Strings::rdecimal($ult_compras[0]["cantBult"], 0) : 0;
    $ventas_mes_anterior = ReporteComprasHelpers::ventasMesAnterior($ventas, $mes, $anio);
    $totalventasmesanterior = $ventas_mes_anterior["semana1"] + $ventas_mes_anterior["semana2"] + $ventas_mes_anterior["semana3"] + $ventas_mes_anterior["semana4"];
    $diasinventario = ($totalventasmesanterior > 0) ? ($bultosExis[0]["bultosexis"]/$totalventasmesanterior) : 0;
    $sugerido = ($totalventasmesanterior*1.2) - $bultosExis[0]["bultosexis"];
    $sugerido = ($sugerido > 0) ? $sugerido : 0;

    /** cargado de las filas **/
    $pdf->Row(
        array(
            $key+1,
            $producto[0]["codprod"],
            $producto[0]["descrip"],
            Strings::rdecimal($producto[0]["displaybultos"], 0),
            Strings::rdecimal((count($costos) > 0) ? (floatval($costos[0]["costodisplay"])) : 0, 2),
            Strings::rdecimal((count($costos) > 0) ? (floatval($costos[0]["costobultos"])) : 0, 2),
            Strings::rdecimal($rentabilidad, 2) . "%",
            $fechapenultimacompra,
            $bultospenultimacompra,
            $fechaultimacompra,
            $bultosultimacompra,
            Strings::rdecimal($ventas_mes_anterior["semana1"], 2),
            Strings::rdecimal($ventas_mes_anterior["semana2"], 2),
            Strings::rdecimal($ventas_mes_anterior["semana3"], 2),
            Strings::rdecimal($ventas_mes_anterior["semana4"], 2),
            Strings::rdecimal($totalventasmesanterior, 2),
            Strings::rdecimal(floatval($bultosExis[0]["bultosexis"]), 2),
            Strings::rdecimal(floatval($no_vendidos[0]["cantidadBult"]), 2),
            Strings::rdecimal($diasinventario, 2),
            Strings::rdecimal($sugerido, 2),
            array_key_exists($key, $n) ? $n[$key] : ''
        ),
        [6],
        ($rentabilidad > 30) ? true : false
    );
    $num++;
}
$pdf->Output();
