<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("clientescodnestle_modelo.php");

//INSTANCIAMOS EL MODELO
$clientescodnestle  = new ClientesCodNestle();

$opc = $_GET['opc'];
$ruta = $_GET['vendedor'];

$documentsize = 'Legal';

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
		$this->Image(PATH_LIBRARY.'build/images/logo.png',10,8,33);
		// Arial bold 15
		$this->SetFont('Arial','',14);
		// Movernos a la derecha
		$this->Cell(80);
		// Título
		$this->Cell(30,10,'REPORTE DE CLIENTES CON COD NESTLE',0,0,'C');
		// Salto de línea
		$this->Ln(20);
		$this->SetFillColor(200,220,255);
		// titulo de columnas
		$this->SetFont ('Arial','B',14);
		$this->Cell(addWidthInArray(30),7,utf8_decode(Strings::titleFromJson('ruta')),1,0,'C',true);
		$this->Cell(addWidthInArray(45),7, utf8_decode(Strings::titleFromJson('codclie')),1,0,'C',true);
		$this->Cell(addWidthInArray(90),7,utf8_decode(Strings::titleFromJson('razon_social')),1,0,'C',true);
		$this->Cell(addWidthInArray(35),7,utf8_decode(Strings::titleFromJson('rif')),1,0,'C',true);
		$this->Cell(addWidthInArray(45),7,utf8_decode(Strings::titleFromJson('fecha_apertura')),1,0,'C',true);
		$this->Cell(addWidthInArray(40),7,utf8_decode(Strings::titleFromJson('dia_visita')),1,0,'C',true);
		$this->Cell(addWidthInArray(46),7,utf8_decode(Strings::titleFromJson('clasificacion')),1,1,'C',true);
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
$pdf->SetFont('Arial','',10);

$pdf->SetWidths($width);

$query = $clientescodnestle ->getClientes_cnestle($opc, $ruta);
foreach ($query as $i) {

	$pdf->Row(
		array(
			$i['codvend'],
			$i['codclie'],
			$i['descrip'],
			$i['rif'],
			date('d/m/Y',strtotime($i['fecha'])),
			$i['dvisita'],
			$i['clasificacion'],
		)
	);

}
$pdf->Output();

	?>
