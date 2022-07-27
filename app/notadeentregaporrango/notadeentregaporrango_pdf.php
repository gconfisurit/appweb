<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO
require_once("notadeentregaporrango_modelo.php");
require_once("../notadeentrega/notadeentrega_modelo.php");

//INSTANCIAMOS EL MODELO
$notaporrango = new NotaDeEntregaPorRango();
$nota = new NotaDeEntrega();
$documentoi = $_GET['documentoi'];
$documentof = $_GET['documentof'];

//array of space in cells
$s = 0;
$j = 0;
$width = array();
$info = array();

function addWidthInArray($num){
    $GLOBALS['width'][$GLOBALS['s']] = $num;
    $GLOBALS['s'] = $GLOBALS['s'] + 1;
    return $num;
}

function addInfoInArray($info){
    $GLOBALS['info'][$GLOBALS['j']] = $info;
    $GLOBALS['j'] = $GLOBALS['j'] + 1;
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
        $this->Cell(75);
        // Título
        $empresa = Empresa::getInfo();
        $this->Cell(20);
        $this->Cell(30,4, $empresa["descrip"],0,1,'L');
        $this->SetFont('');
        $this->Cell(38);
        $this->Cell(68,4, $empresa["rif"],0,1,'L');
        $this->Cell(38);
        $this->Cell(68,4, $empresa["direc1"],0,1,'L');
        $this->Cell(38);
        $this->Cell(68,5, $empresa["telef"],0,1,'L');

        //linea
        $this->Line(8, 30, 200, 30);
        $this->Ln(3);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
//        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();


$documentos = $notaporrango->get_lista_numerod_del_rango($documentoi, $documentof);
if (ArraysHelpers::validate($documentos))
{
    foreach ($documentos as $documento)
    {
        $s = 0;
        $width = array();
        $info = array();

        $numerod = $documento['numerod'];

        $pdf->AddPage();
        $pdf->SetFont('Arial','',8);


        $cabecera = NotasDeEntrega::getHeaderById($numerod);
        $descuentoitem  = Numbers::avoidNull( $nota->get_descuento($numerod, 'C')['descuento'] );

        $observacion = Strings::avoidNull($cabecera['notas1']);
        $Represent = Strings::avoidNull($cabecera['Represent']);
        $subtotal = Strings::rdecimal($cabecera['subtotal'],2);
        $descuentototal = Strings::rdecimal($cabecera['descuento'],2);
        $totalnota = Strings::rdecimal($cabecera['total'],2);


        //datos del cliente
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(19,8, "Cod. Cliente: ",0,0,'L');
        $pdf->SetFont('');
        $pdf->Cell(40,8, $cabecera["codclie"],0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(6,8, "Rif: ",0,0,'L');
        $pdf->SetFont('');
        $pdf->Cell(40,8, $cabecera["rif"],0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(15,8, "Vendedor: ",0,0,'L');
        $pdf->SetFont('');
        $pdf->Cell(10,8, $cabecera["codvend"],0,1,'L');

       $pdf->SetFont('Arial','B',8);
        $pdf->Cell(20,8, "Razon Social: ",0,0,'L');
        $pdf->SetFont('');
        $pdf->Cell(70,8, $cabecera["rsocial"],0,0,'L');

        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(22,8, "Representante: ",0,0,'L');
        $pdf->SetFont('');
        $pdf->Cell(35,8, $cabecera["Represent"],0,0,'L');

        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(14,8, "Telefono: ",0,0,'L');
        $pdf->SetFont('');
        $pdf->Cell(22,8, $cabecera["telefono"],0,1,'L');

        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(23,8, "Direccion Fiscal: ",0,0,'L');
        $pdf->SetFont('');
        $pdf->Cell(80,8, $cabecera["direccion"],0,1,'L');
        $pdf->Cell(140,8, $cabecera["direccion2"],0,0,'L');
        
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(10,8, "Fecha: ",0,0,'L');
        $pdf->SetFont('');
        $pdf->Cell(20,8, Date(FORMAT_DATE, strtotime($cabecera['fechae'])),0,1,'L');

        //Nota de entrega
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(77);
        $pdf->Cell(30,10,'NOTA DE ENTREGA',0,1,'C');

        //numero de documento
        $pdf->SetFont('');
        $pdf->SetTextColor(255 , 0, 0);
        $pdf->Cell(185,8,'# '.$numerod,0,1,'R');
        $pdf->SetTextColor(0 , 0, 0);

        //linea
        $pdf->Line(8, 83, 200, 83);
        $pdf->Ln(3);

        $anchoAdicional = 0;
        if ($descuentoitem > 0) {
            $anchoAdicional += 0;// +0
        } else {
            $anchoAdicional += (20+18);// +38
        }

        // titulo de la tabla
        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetFillColor(200,220,255);
        // titulo de columnas
        $pdf->Cell(addWidthInArray(15 + ($anchoAdicional*0.20)),6,'Codigo',1,0,'C',true);
        $pdf->Cell(addWidthInArray(60 + ($anchoAdicional*0.40)),6,utf8_decode('Descripción'),1,0,'C',true);
        $pdf->Cell(addWidthInArray(16 + ($anchoAdicional*0.16)),6,'Cantidad',1,0,'C',true);
        $pdf->Cell(addWidthInArray(15 + ($anchoAdicional*0.12)),6,'Unidad',1,0,'C',true);
        $pdf->Cell(addWidthInArray(26),6,'Precio Unitario',1,0,'C',true);
        if ($descuentoitem > 0) {
            $pdf->Cell(addWidthInArray(20),6,'Sub Total',1,0,'C',true);
            $pdf->Cell(addWidthInArray(18),6,'Descuento',1,0,'C',true);
        }
        $pdf->Cell(addWidthInArray(18 + ($anchoAdicional*0.12)),6,'Total',1,1,'C',true);

        $pdf->SetWidths($width);

        $detalle = NotasDeEntrega::getDetailById($numerod);
        $pdf->SetFont('Arial','',7);
        $multiplicador_linea = 0;
        foreach ($detalle as $i) {
            $j = 0;
            $multiplicador_linea += 5;

            addInfoInArray($i['coditem']);
            addInfoInArray(utf8_decode($i['descripcion']));
            addInfoInArray(number_format($i['cantidad']));
            addInfoInArray(($i['esunidad'] == '1') ? "PAQ" : "BUL");
            addInfoInArray(Strings::rdecimal($i['precio'], 2));
            if($descuentoitem > 0)
            {
                addInfoInArray(Strings::rdecimal($i['totalitem'], 2));
                addInfoInArray(Strings::rdecimal($i['descuento'], 2));
            }
            addInfoInArray(Strings::rdecimal($i['total'], 2));

            $pdf->Row($info);
        }
        //linea
        $pdf->Line(8, $multiplicador_linea + 98, 200, $multiplicador_linea + 98);
        $pdf->Ln(10);


        $pdf->SetFont('Arial','B',8);
        if($descuentototal >= 0) {
            $pdf->Cell(155,8, "",0,0,'L');
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(15,8, "Sub Total: ",0,0,'L');
            $pdf->SetFont('');
            $pdf->Cell(33,8, $subtotal,0,1,'L');
            $pdf->Cell(153,8, "",0,0,'L');
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(16,8, "Descuento: ",0,0,'L');
            $pdf->SetFont('');
            $pdf->Cell(33,8, $descuentototal,0,1,'L');
        }

        $pdf->Cell(23,8, "Observaciones: ",0,0,'L');
        $pdf->SetFont('');
        $pdf->Cell(130,8, $observacion,0,0,'L');
        $pdf->Cell(8,8, "",0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(9,8, "Total: ",0,0,'L');
        $pdf->SetFont('');
        $pdf->Cell(33,8, $totalnota,0,1,'L');
        $pdf->Ln(5);
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(77);
 $pdf->Cell(30,10,'SIN DERECHO A CREDITO FISCAL',0,1,'C');
$pdf->Ln(-5);
$pdf->Cell(0,10,'VERIFIQUE SU MERCANCIA, NO SE ACEPTAN RECLAMOS DESPUES DE HABER FIRMADO',0,1,'C');
$pdf->Ln(-5);
$pdf->Cell(0,10,'Y SELLADO ESTA NOTA DE ENTREGA.',0,0,'C');
$pdf->Ln();
$pdf->Cell(0,10,'ESTIMADO CLIENTE CONFIRME SU PAGO REALIZADO A NUESTRA EMPRESA',0,1,'C');
$pdf->Ln(-5);
$pdf->Cell(0,10,'CON UN MENSAJE AL WHATSAPP +58 424-9466495',0,1,'C');
$pdf->SetFont('Arial','B',8); 
$pdf->Cell(0,5,'"GRACIAS POR PREFERIRNOS".',0,1,'C');
        $pdf->Ln(25);

        // lineas de firma
        $pdf->Line(40, $multiplicador_linea + 163, 70, $multiplicador_linea + 163);
        $pdf->Line(135, $multiplicador_linea + 163, 165, $multiplicador_linea + 163);

        // texto de firmas
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(33);
        $pdf->Cell(23,1, "Depachado por",0,0,'C');
        $pdf->Cell(73);
        $pdf->Cell(23,1, "Recibido por",0,0,'C');
    }
}

$pdf->Output();

?>