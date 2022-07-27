<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("proveedor_modelo.php");

//INSTANCIAMOS EL MODELO
$proveedores = new listarProveedores();

$orden = $_GET['orden'];

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

class PDF extends FPDF
{
    var $widths;
    var $aligns;

    // Cabecera de página
    function Header()
    {
       
        /*calculo del ancho adicional para mantener el orden de las celdas de acuerdo a su seleccion segun las siguientes premisas:
                * para el ancho de las celdas que son dinamicas son p1=25, p2=25, p3=25 y cubi=24
                * si solo aparece visualmente un precio, se suma el ancho de las otras dos mas el cubi
                * si aparece visualmente dos precios, se suma el ancho de una mas el cubi
                * si aparece los 3 precios, solo se suba el cubi
                * si aparecen los 3 precios y el cubi, no se suma nada.

         la suma de ancho adicional se distribuira de la siguiente forma:
                * codigo  = 20%
                * descripcion = 40%
                * marca   = 20%
                * bulto   = 10%
                * paquete = 10%
            TOTAL 100%
        */
        $anchoAdicional = 0;
        switch ($GLOBALS['sumap']) {
            case 1:
                $anchoAdicional += (50*2);// +25+25
                break;
            case 2:
                $anchoAdicional += (25*2);// +25
                break;
            default: /** 0 || 3**/
                $anchoAdicional += 0;// +0
        }
        

        // Logo
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', '', 12);
        // Movernos a la derecha
        $this->Cell(140);
        // Título
        if($orden=="Todos"){
            $this->Cell(40, 10, 'REPORTE DE TODOS LOS PROVEEDORES', 0, 0, 'C');
            
        }elseif($orden=="Activos"){
            $this->Cell(40, 10, 'REPORTE DE LOS PROVEEDORES ACTIVOS', 0, 0, 'C');
            
        }elseif($orden=="Inactivos"){
            $this->Cell(40, 10, 'REPORTE DE LOS PROVEEDORES INACTIVOS', 0, 0, 'C');
            
        }
        // Salto de línea
        $this->Ln(20);
        $this->SetFont('Arial', 'B', 8.5);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(20 + ($anchoAdicional*0.20)), 6, utf8_decode(Strings::titleFromJson('codprov')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(65 + ($anchoAdicional*0.40)), 6, utf8_decode(Strings::titleFromJson('razon_social')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(30 + ($anchoAdicional*0.20)), 6, utf8_decode(Strings::titleFromJson('rif')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(18 + ($anchoAdicional*0.20)), 6, utf8_decode(Strings::titleFromJson('activo')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(18 + ($anchoAdicional*0.20)), 6, utf8_decode(Strings::titleFromJson('direc1')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(12 + ($anchoAdicional*0.20)), 6, utf8_decode(Strings::titleFromJson('direc2')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(9 + ($anchoAdicional*0.20)), 6, utf8_decode(Strings::titleFromJson('estado')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(6 + ($anchoAdicional*0.20)), 6, utf8_decode(Strings::titleFromJson('tlf')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(6 + ($anchoAdicional*0.20)), 6, utf8_decode(Strings::titleFromJson('tlf_movil')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(3 + ($anchoAdicional*0.20)), 6, utf8_decode(Strings::titleFromJson('correo_electronico')), 1, 0, 'C', true);
       
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
$pdf->AddPage('L', $documentsize);
$pdf->SetFont('Arial', '', 8);

$pdf->SetWidths($width);

$query = $proveedores->getlistaproveedores($orden);
$num = count($query);

foreach ($query as $i) {

    if ($i['Activo'] == 1) {
        $estado = "ACTIVO";
    } else {
        if ($i['Activo'] == 0) {
            $estado = "INACTIVO";
        }
    }

    $CodProv = $i['CodProv'];
    $Descrip = utf8_encode($i['Descrip']);
    $ID3 = $i['ID3'];
    $estatus = $estado;
    $Direc1 = $i['Direc1'];
    $Direc2 = $i['Direc2'];
    $geoestado = utf8_encode($i['Descrip']);
    $Telef = $i['Telef'];
    $Movil = $i['Movil'];
    $Email = utf8_encode($i['Email']);

    addInfoInArray($CodProv);
    addInfoInArray($Descrip);
    addInfoInArray($ID3);
    addInfoInArray($Direc1);
    addInfoInArray($Direc2);  
    addInfoInArray($geoestado);
    addInfoInArray($Telef);
    addInfoInArray( $Movil);
    addInfoInArray( $Email);

    $pdf->Row($info);
}
$pdf->Ln(10);
$pdf->Cell(335, 10, 'Total de Proveedores:  '.$num, 0, 1, 'C');

$pdf->Output();
