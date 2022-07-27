<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once ("despachosrelacion_modelo.php");
require_once("../despachos/despachos_modelo.php");

//INSTANCIAMOS EL MODELO
$relacion  = new DespachosRelacion();
$despachos  = new Despachos();

$correlativo = $_GET['correlativo'];

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Logo
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 11);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(30, 10, Empresa::getName(), 0, 0, 'C');
        $this->Ln();

    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb = 0;
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
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw =& $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}


$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);

    /*******************************************/
    /*              CABECERA                   */
    /*******************************************/

    $despa =$_GET['correlativo'];
    
  $modelo= $capacidad =$descripcion= $cedula_chofer=  $placa=  $fechad =  $nota = '';


        $cabeceraDespacho =  Choferes::getCabeceraDespacho($despa);

         foreach ($cabeceraDespacho as $row_cabe) {

            $cedula_chofer=$row_cabe['cedula_chofer'];
            $placa=$row_cabe['placa'];
            $fechad = date('d/m/Y', strtotime($row_cabe["fechad"]));
            $nota = $row_cabe['nota'];

      
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



$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(90,7,'Nro de Despacho: '.str_pad($correlativo, 8, 0, STR_PAD_LEFT),0,0,'C');
$pdf->Ln();
$pdf->SetFont ('Arial','',7);
$pdf->Cell(90,7,'Fecha Despacho: '.$fechad,0,0,'L');
$pdf->Cell(90,7,'Vehiculo de Carga: : '.$placa.'  '.$modelo.'  '.$capacidad.'Kg',0,0,'L');
$pdf->Ln();

$pdf->Cell(150,7,'Destino : '.$nota." - ".$descripcion,0,0,'L');
$pdf->Ln();

    /*****************************************************************/
    /*               TABLA DE FACTURAS DEL DESPACHO                  */
    /*****************************************************************/
$pdf->SetFont ('Arial','',8);
$pdf->Cell(62,7,'Listado de Documentos Seleccionadas',0,0,'C');
$pdf->Ln();
$pdf->SetFillColor(200,220,255);
// titulo de columnas
$pdf->SetFont ('Arial','B',8);
$pdf->Cell(24,7, utf8_decode(Strings::titleFromJson('numerod')),1,0,'C',true);
$pdf->Cell(23,7, utf8_decode(Strings::titleFromJson('fecha_emision')),1,0,'C',true);
$pdf->Cell(15,7, utf8_decode(Strings::titleFromJson('ruta')),1,0,'C',true);
$pdf->Cell(30,7, utf8_decode(Strings::titleFromJson('codclie')),1,0,'C',true);
$pdf->Cell(73,7, utf8_decode(Strings::titleFromJson('razon_social')),1,0,'C',true);
$pdf->Cell(20,7, utf8_decode(Strings::titleFromJson('peso')),1,0,'C',true);
$pdf->SetFont ('Arial','',7);
$pdf->Ln();

$documentos_en_despacho = Array();
$documentos = $despachos->getDocumentosPorCorrelativo($correlativo);
foreach ($documentos AS $item) {
    switch ($item['TipoFac']) {
        case 'A':
            $peso_cubicaje_nota = $peso_cubicaje =0;
            $factura =$despachos->getFactura($item['numeros']);

            foreach ($factura as $fact) {

             $numerod = $fact['numerod'];
             $fechae = $fact['fechae'];
             $codvend = $fact['codvend'];
             $codclie = $fact['codclie'];
             $descrip = $fact['descrip'];
      
            }

             $peso = $despachos->getCubicajeYPesoTotalporFactura($item['numeros']);
             foreach ($peso as $p) {

             $peso_cubicaje += $p['tara'];
      
            }
            
            $documentos_en_despacho[] = array(
                "Tipofac" => 'FAC',
                "NumeroD" => $numerod,
                "FechaE"  => $fechae,
                "CodVend" => $codvend,
                "CodClie" => $codclie,
                "Descrip" => $descrip,
                "Peso"    => $peso_cubicaje);
            break;
        case 'C':
            $peso_cubicaje_nota = $peso_cubicaje =0;
            $notadeentrega = $despachos->getNotaDeEntrega($item['numeros']);


            foreach ($notadeentrega as $notas) {

             $numerod = $notas['numerod'];
             $fechae = $notas['fechae'];
             $codvend = $notas['codvend'];
             $codclie = $notas['codclie'];
             $descrip = $notas['descrip'];
      
            }


            $peso_nota = $despachos->getCubicajeYPesoTotalporNotaDeEntrega($item['numeros']);
             foreach ($peso_nota as $pnota) {

             $peso_cubicaje_nota += $pnota['tara'];
      
            }

            $documentos_en_despacho[] = array(
                "Tipofac" => 'N/E',
                "NumeroD" => $numerod,
                "FechaE"  => $fechae,
                "CodVend" => $codvend,
                "CodClie" => $codclie,
                "Descrip" => $descrip,
                "Peso"    => $peso_cubicaje_nota);
            break;
    }
}

$pdf->SetWidths(array(24,23,15,30,73,20));
foreach ($documentos_en_despacho AS $item){
    $pdf->Row(
        array(
            $item["NumeroD"] . " (".$item["Tipofac"].")",
            date(FORMAT_DATE, strtotime($item["FechaE"])),
            $item["CodVend"],
            $item["CodClie"],
            $item["Descrip"],
            Strings::rdecimal($item["Peso"], 2)
        )
    );
}
$pdf->Cell(62,7,'Total de Documentos Emitidos: '.count($documentos_en_despacho),0,0,'C');
$pdf->Ln();
$pdf->Ln();



    /*****************************************************************/
    /*              TABLA DE PRODUCTOS DEL DESPACHO                  */
    /*****************************************************************/
$lote = "";


//facturas por correlativo
$documentos = $despachos->getDocumentosPorCorrelativo($correlativo);
$num = count($documentos);
foreach ($documentos AS $item) {
    $tipodoc = ($item['TipoFac']=='A') ? "FAC" : "N/E";
    $lote .= " ".$item['numeros']." ($tipodoc),";
}
//le quitamos 1 caracter para quitarle la ultima coma
$lote = substr($lote, 0, -1);

$total_bultos = 0;
$total_paq = 0;
$total_peso = 0;
$total_peso_azucar = 0;
$total_peso_galleta = 0;
$total_peso_chocolote = 0;

$pdf->Cell(62,7,'Listado de Productos a Despachar',0,0,'C');
$pdf->Ln();
$pdf->SetFillColor(200,220,255);
// titulo de columnas
$pdf->SetFont ('Arial','B',8);
$pdf->Cell(25,7, utf8_decode(Strings::titleFromJson('codigo_prod')),1,0,'C',true);
$pdf->Cell(70,7, utf8_decode(Strings::titleFromJson('descrip_prod')),1,0,'C',true);
$pdf->Cell(30,7, utf8_decode(Strings::titleFromJson('cantidad_bultos')),1,0,'C',true);
$pdf->Cell(32,7, utf8_decode(Strings::titleFromJson('cantidad_paquetes')),1,0,'C',true);
$pdf->Cell(28,7, utf8_decode(Strings::titleFromJson('peso')),1,1,'C',true);
$pdf->SetFont ('Arial','',8);


//obtenemos los registros de los productos en dichos documentos
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

$pdf->SetWidths(array(25,70,30,32,28));
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
            Strings::rdecimal($peso, 2)
        )
    );

}
$pdf->SetFillColor(200,220,255);
$pdf->SetFont ('Arial','B',8);
$pdf->Cell(95,7,'Total = ',1,0,'C');
$pdf->Cell(30,7,$total_bultos.' Bult',1,0,'C',true);
$pdf->Cell(32,7,$total_paq.' Paq',1,0,'C',true);
$pdf->Cell(28,7,Strings::rdecimal($total_peso,2).'Kg'.' - '.Strings::rdecimal($total_peso/1000, 2).'TN',1,0,'C',true);
$pdf->Ln();
$pdf->Cell(62,7,'FACTURAS DESPACHADAS '.$num,0,0,'C');
$pdf->Ln();
$pdf->Cell(20,7,' ',0,0,'C');
$pdf->MultiCell(140,5,$lote);
$pdf->Ln();
$pdf->Cell(120,7,'Total Azucar (TN): '.($total_peso_azucar/1000).' Total Chocolate (TN): '.($total_peso_chocolote/100).' Total Galleta (TN): '.($total_peso_galleta/1000),0,0,'C');
$pdf->Ln();




    /***************************************************************************/
    /*              TABLA DE PRODUCTOS DEVUELTOS DEL DESPACHO                  */
    /***************************************************************************/
$devoluciones = $relacion->get_productos_devueltos_de_un_despacho($correlativo);

if (count($devoluciones) != 0){
    $fact_devueltas = $relacion->get_facturas_devueltas_de_un_despacho($correlativo);

    $pdf->SetFont ('Arial','',7);
    $pdf->Cell(62,7,'PRODUCTOS DEVUELTOS',0,0,'C');
    $pdf->Ln();
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFillColor(255,0,0);
    $pdf->Cell(20,7,'Cod Prod',1,0,'C',true);
    $pdf->Cell(75,7,'Descripcion',1,0,'C',true);
    $pdf->Cell(30,7,'Cant Bultos',1,0,'C',true);
    $pdf->Cell(30,7,'Cant Paquetes',1,0,'C',true);
    $pdf->Cell(30,7,'Peso',1,0,'C',true);
    $pdf->Ln();
    $pdf->SetTextColor(0,0,0);
    $total_bultos = 0;
    $total_paq = 0;
    $total_peso = 0;

    $pdf->SetWidths(array(20,75,30,30,30));
    foreach ($devoluciones AS $i){

        $bultos = 0;
        $paq = 0;

        if ($i["BULTOS"] > 0){
            $bultos = $i["BULTOS"];
        }
        if ($i["PAQUETES"] > 0){
            $paq = $i["PAQUETES"];
        }

        if ($i["EsEmpaque"] != 0){
            if ($i["PAQUETES"] >= $i["CantEmpaq"]){
                $bultos_total = $i["PAQUETES"] / $i["CantEmpaq"];
                $decimales = explode(".",$bultos_total);
                $bultos_deci = $bultos_total - $decimales[0];
                $paq = $bultos_deci * $i["CantEmpaq"];
                $bultos = $decimales[0] + $bultos;
            }
        }
        $peso = $bultos * $i['tara'];
        $peso += ($i['tara'] * $paq) / $i['CantEmpaq'];

        $total_peso += $peso;
        $total_bultos += $bultos;
        $total_paq += $paq;

        $pdf->Row(
            array(
                $i["CodItem"],
                strtoupper($i["Descrip"]),
                round($bultos),
                round($paq),
                Strings::rdecimal($peso,2)
            )
        );
    }

    $pdf->SetFont ('Arial','B',8);
    $pdf->Cell(95,7,'Total Devuelto = ',1,0,'C');
    $pdf->SetTextColor(255,255,255);
    $pdf->Cell(30,7,$total_bultos.' Bult',1,0,'C',true);
    $pdf->Cell(30,7,$total_paq.' Paq',1,0,'C',true);
    $pdf->Cell(30,7,Strings::rdecimal($total_peso, 2).'Kg'.' - '.Strings::rdecimal($total_peso/1000, 2).'TN',1,0,'C',true);
    $pdf->Ln();
    $pdf->SetTextColor(0,0,0);
    $pdf->Cell(62,7,'FACTURAS AFECTADAS ('.count($fact_devueltas).') :',0,0,'C');
    $pdf->Ln();
    $notas = "";
    foreach ($fact_devueltas AS $item)
        $notas .= " ".$item['ONumero'].",";
    $notas = substr($notas, 0, -1);
    $pdf->Cell(20,7,' ',0,0,'C');
    $pdf->MultiCell(140,5,$notas);

}else{
    $pdf->Cell(20,7,' ',0,0,'C');
    $pdf->Cell(62,7,'NO SE LE REALIZARON DEVOLUCIONES A ESTE DESPACHO ',0,0,'C');
}

$pdf->Output();

?>
