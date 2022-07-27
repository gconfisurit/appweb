<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("despachos_modelo.php");

//INSTANCIAMOS EL MODELO
$despachos  = new Despachos();

$correlativo = $_GET['correlativo'];

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
        $despachos  = new Despachos();

       $modelo= $capacidad =$descripcion= $cedula_chofer=  $placa=  $fechad =  $nota = '';


        $cabeceraDespacho = Choferes::getCabeceraDespacho($_GET['correlativo']);

         foreach ($cabeceraDespacho as $row) {

            $cedula_chofer=$row['cedula_chofer'];
            $placa=$row['placa'];
            $fechad = $row['fechad'];
            $nota = $row['nota'];
      
         }

        $chofer = Choferes::getByDni( $cedula_chofer);

        foreach ($chofer as $row2) {
            $descripcion = $row2['descripcion'];
         }



        $vehiculo = Vehiculo::getById($placa);

        foreach ($vehiculo as $row1) {

            $modelo = $row1['modelo'];
            $capacidad = $row1['capacidad'];
      
         }
        // Logo
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 11);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(30, 10, Empresa::getName(), 0, 0, 'C');
        $this->Ln();

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(90,7,'Nro de Despacho: '.str_pad($GLOBALS["correlativo"], 8, 0, STR_PAD_LEFT),0,0,'C');
        $this->Ln();
        $this->SetFont ('Arial','',7);
        $this->Cell(90,7,'Fecha Despacho: '.date(FORMAT_DATE, strtotime($fechad)),0,0,'L');
        $this->Cell(90,7,'Vehiculo de Carga : '.$placa.'  '.$modelo.'  '.$capacidad.'Kg',0,0,'L');
        $this->Ln();

        $this->Cell(150,7,'Destino : '.$nota." - ".$descripcion,0,0,'L');
        $this->Ln();

        $this->Cell(62,7,'Listado de Productos a Despachar',0,0,'C');
        $this->Ln();
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->SetFont ('Arial','B',8);
        $this->Cell(25,7,utf8_decode(Strings::titleFromJson('codigo_prod')),1,0,'C',true);
        $this->Cell(70,7, utf8_decode(Strings::titleFromJson('descrip_prod')),1,0,'C',true);
        $this->Cell(30,7,utf8_decode(Strings::titleFromJson('cantidad_bultos')),1,0,'C',true);
        $this->Cell(32,7,utf8_decode(Strings::titleFromJson('cantidad_paquetes')),1,0,'C',true);
        $this->Cell(30,7,utf8_decode(Strings::titleFromJson('peso')),1,1,'C',true);
    }

}


$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);

$pdf->SetWidths(array(25,70,30,32,30));
$lote = "";

//facturas por correlativo
$documentos = $despachos->getDocumentosPorCorrelativo($correlativo);
$num = count($documentos);
foreach ($documentos AS $item) {
    $tipodoc = ($item['TipoFac']=='A') ? "FACT" : "N/E";
    $lote .= " ".$item['numeros']." (".$tipodoc."),";
}

//le quitamos 1 caracter para quitarle la ultima coma
$lote = substr($lote, 0, -1);


//obtener los productos por despacho creado
$productosDespacho = Array();
$datos_f = $despachos->getProductosDespachoCreadoEnFacturas($correlativo);
$datos_n = $despachos->getProductosDespachoCreadoEnNotaDeEntrega($correlativo);

foreach (array($datos_f, $datos_n) as $dato) {
    foreach ($dato as $row) {
        $arr = array_map(function ($arr) { return $arr['coditem']; }, $productosDespacho);

        if (!in_array($row['coditem'], $arr)) {
            #no existe en el array
            $productosDespacho[] = $row;
        } else {
            # si existe en el array
            $pos = array_search($row['coditem'], $arr);
            $productosDespacho[$pos]['bultos'] += intval($row['bultos']);
            $productosDespacho[$pos]['paquetes'] += intval($row['paquetes']);
        }
    }
}

$total_bultos = 0;
$total_paq = 0;
$total_peso = 0;

$total_peso_azucar = 0;
$total_peso_galleta = 0;
$total_peso_chocolote = 0;

foreach ($productosDespacho as $i) {

    $bultos = 0;
    $paq = 0;

    if ($i["bultos"] > 0){
        $bultos = $i["bultos"];
    }
    if ($i["paquetes"] > 0){
        $paq = $i["paquetes"];
    }

    if ($i["esempaque"] != 0){
        if ($i["paquetes"] >= $i["cantempaq"]){

            if ($i["cantempaq"] != 0) {
                $bultos_total = $i["paquetes"] / $i["cantempaq"];
            }else{
                $bultos_total = 0;
            }
            $decimales = explode(".",$bultos_total);
            $bultos_deci = $bultos_total - $decimales[0];
            $paq = $bultos_deci * $i["cantempaq"];
            $bultos = $decimales[0] + $bultos;
        }
    }
    $peso = $bultos * $i['tara'];
    if($i["cantempaq"] != 0) {
        $peso += ($i['tara'] * $paq) / $i['cantempaq'];
    }

    switch ($i["codinst"]){
        case "80":
            $total_peso_chocolote = $total_peso_chocolote + $peso;
            break;
        case "81":
            $total_peso_azucar = $total_peso_azucar + $peso;
            break;
        case "82":
            $total_peso_galleta = $total_peso_galleta + $peso;
            break;
    }

    $total_peso += $peso;
    $total_bultos += $bultos;
    $total_paq += $paq;

    $pdf->Row(
        array(
            $i["coditem"],
            strtoupper($i["descrip"]),
            round($bultos),
            round($paq),
            Strings::rdecimal($peso)
        )
    );

}
$pdf->SetFillColor(200,220,255);
$pdf->SetFont ('Arial','B',8);
$pdf->Cell(95,7,'Total = ',1,0,'C');
$pdf->Cell(30,7,$total_bultos.' Bult',1,0,'C',true);
$pdf->Cell(32,7,$total_paq.' Paq',1,0,'C',true);
$pdf->Cell(30,7,Strings::rdecimal($total_peso).'Kg'.' - '.Strings::rdecimal($total_peso/1000).'TN',1,0,'C',true);
$pdf->Ln();
$pdf->Cell(62,7,'DOCUMENTOS DESPACHADOS '.$num,0,0,'C');
$pdf->Ln();
$pdf->Cell(20,7,' ',0,0,'C');
//$lote = str_replace(";"," ",$lote);
$pdf->MultiCell(140,5,$lote);
$pdf->Ln();
$pdf->Cell(120,7,'Total Azucar (TN): '.($total_peso_azucar/1000).' Total Chocolate (TN): '.($total_peso_chocolote/100).' Total Galleta (TN): '.($total_peso_galleta/1000),0,0,'C');
$pdf->Ln();

$pdf->Output();

?>
