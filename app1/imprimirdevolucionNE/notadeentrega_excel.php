<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require_once ( PATH_LIBRARY.'jpgraph4.3.4/src/jpgraph.php' );
require_once ( PATH_LIBRARY.'jpgraph4.3.4/src/jpgraph_bar.php' );
require_once ( PATH_LIBRARY.'jpgraph4.3.4/src/jpgraph_line.php' );

require (PATH_VENDOR.'autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Chart\Layout;

//LLAMAMOS AL MODELO
require_once("notadeentrega_modelo.php");

//INSTANCIAMOS EL MODELO
$nota = new NotaDeEntrega();
$numerod = $_GET['nrodocumento'];

$cabecera = NotasDeEntrega::getHeaderById2($numerod);
$descuentoitem  = Numbers::avoidNull( $nota->get_descuento($numerod, 'C')['descuento'] );

$observacion = Strings::avoidNull($cabecera['notas1']);
$subtotal = Strings::rdecimal($cabecera['subtotal']);
$descuentototal = Strings::rdecimal($cabecera['descuento']);
$totalnota = Strings::rdecimal($cabecera['total']);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

// Logo
$gdImage = imagecreatefrompng(PATH_LIBRARY.'build/images/logo.png');
$objDrawing = new MemoryDrawing();
$objDrawing->setName('Sample image');
$objDrawing->setDescription('TEST');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight(108);
$objDrawing->setWidth(128);
$objDrawing->setCoordinates('A2');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

// datos empresa
$empresa = Empresa::getInfo();
$sheet->setCellValue('I2', $empresa["descrip"])
    ->setCellValue('I3', $empresa["rif"])
    ->setCellValue('I4', $empresa["direc1"])
    ->setCellValue('I5', $empresa["telef"]);

//linea
$spreadsheet->getActiveSheet()->getStyle('A5:Q5')->applyFromArray(
    array("borders" => array("bottom" => array("style" => Border::BORDER_MEDIUM,)))
);

//datos del cliente
$spreadsheet->getActiveSheet()
    ->setCellValue('A7', "Cod. Cliente: ")
    ->setCellValue('C7', $cabecera["codclie"])
    ->setCellValue('J7', "Rif: ")
    ->setCellValue('k7', $cabecera["rif"])
    ->setCellValue('N7', "Vendedor: ")
    ->setCellValue('O7', $cabecera["codvend"])
    ->setCellValue('A9', "Razon Social: ")
    ->setCellValue('C9', $cabecera["rsocial"])
    ->setCellValue('J9', "Telefono: ")
    ->setCellValue('K9', $cabecera["telefono"])
    ->setCellValue('N9', "Fecha: ")
    ->setCellValue('O9', Date(FORMAT_DATE, strtotime($cabecera['fechae'])))
    ->setCellValue('A11', "Direccion Fiscal: ")
    ->setCellValue('C11', $cabecera["direccion"])
    ->setCellValue('C12', $cabecera["direccion2"])
    ->setCellValue('G13', " DEVOLUCION DE NOTA DE ENTREGA")
    ->setCellValue('O14', "# " . $numerod);
$spreadsheet->getActiveSheet()->getStyle('A7')->applyFromArray( array("font" => array("bold" => true)) );
$spreadsheet->getActiveSheet()->getStyle('J7')->applyFromArray( array("font" => array("bold" => true)) );
$spreadsheet->getActiveSheet()->getStyle('N7')->applyFromArray( array("font" => array("bold" => true)) );
$spreadsheet->getActiveSheet()->getStyle('A9')->applyFromArray( array("font" => array("bold" => true)) );
$spreadsheet->getActiveSheet()->getStyle('J9')->applyFromArray( array("font" => array("bold" => true)) );
$spreadsheet->getActiveSheet()->getStyle('N9')->applyFromArray( array("font" => array("bold" => true)) );
$spreadsheet->getActiveSheet()->getStyle('A11')->applyFromArray( array("font" => array("bold" => true)) );
$spreadsheet->getActiveSheet()->getStyle('G13')->applyFromArray( array("font" => array('bold' => true, 'size' =>20)) );
$spreadsheet->getActiveSheet()->getStyle('O14')->applyFromArray( array("font" => array("color" => array('rgb' => 'FF0000'), 'size' =>20)) );

//linea
$spreadsheet->getActiveSheet()->getStyle('A15:Q15')->applyFromArray(
    array("borders" => array("bottom" => array("style" => Border::BORDER_MEDIUM,)))
);
if ($descuentoitem > 0) {
    // titulo de la tabla
    $spreadsheet->getActiveSheet()
        ->mergeCells('C17:F17')
        ->mergeCells('I17:J17')
        ->mergeCells('K17:L17')
        ->mergeCells('M17:N17')
        ->mergeCells('O17:P17');
    $spreadsheet->getActiveSheet()
        ->setCellValue('B17', "Codigo")
        ->setCellValue('C17', "Descripcion")
        ->setCellValue('G17', "Cant")
        ->setCellValue('H17', "Und")
        ->setCellValue('I17', "Precio Unitario")
        ->setCellValue('K17', "Sub Total")
        ->setCellValue('M17', "Descuento")
        ->setCellValue('O17', "Total");
    $spreadsheet->getActiveSheet()->getStyle('B17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('C17:F17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('G17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('H17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('I17:J17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('K17:L17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('M17:N17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('O17:P17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
} else {
    // titulo de la tabla
    $spreadsheet->getActiveSheet()
        ->mergeCells('B17:C17')
        ->mergeCells('D17:H17')
        ->mergeCells('I17:J17')
        ->mergeCells('K17:L17')
        ->mergeCells('M17:N17')
        ->mergeCells('O17:P17');
    $spreadsheet->getActiveSheet()
        ->setCellValue('B17', "Codigo")
        ->setCellValue('D17', "Descripcion")
        ->setCellValue('I17', "Cantidad")
        ->setCellValue('K17', "Unidad")
        ->setCellValue('M17', "Precio Unitario")
        ->setCellValue('O17', "Total");
    $spreadsheet->getActiveSheet()->getStyle('B17:C17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('D17:H17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('I17:J17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('K17:L17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('M17:N17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('O17:P17')->applyFromArray( array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'borders' => array('top' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'bottom' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'left' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860')), 'right' => array('style' => Border::BORDER_MEDIUM , 'color' => array('rgb' => '143860'))), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
}



// contenido de tabla
$l = 18;
$detalle = NotasDeEntrega::getDetailById2($numerod);
foreach ($detalle as $i) {

    ($i['esunidad'] == '1') ? $esunidad = "PAQ" : $esunidad = "BUL";

    if ($descuentoitem > 0) {
        $spreadsheet->getActiveSheet()
            ->mergeCells('C'.$l.':F'.$l)
            ->mergeCells('I'.$l.':J'.$l)
            ->mergeCells('K'.$l.':L'.$l)
            ->mergeCells('M'.$l.':N'.$l)
            ->mergeCells('O'.$l.':P'.$l);
        $spreadsheet->getActiveSheet()
            ->setCellValue('B'.$l, $i['coditem'])
            ->setCellValue('C'.$l, utf8_decode($i['descripcion']))
            ->setCellValue('G'.$l, number_format($i['cantidad']))
            ->setCellValue('H'.$l, $esunidad)
            ->setCellValue('I'.$l, Strings::rdecimal($i['precio'], 2))
            ->setCellValue('K'.$l, Strings::rdecimal($i['totalitem'], 2))
            ->setCellValue('M'.$l, Strings::rdecimal($i['descuento'], 2))
            ->setCellValue('O'.$l, Strings::rdecimal($i['total'], 2));
        $spreadsheet->getActiveSheet()->getStyle('B'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
        $spreadsheet->getActiveSheet()->getStyle('C'.$l.':F'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
        $spreadsheet->getActiveSheet()->getStyle('G'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
        $spreadsheet->getActiveSheet()->getStyle('H'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
        $spreadsheet->getActiveSheet()->getStyle('I'.$l.':J'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
        $spreadsheet->getActiveSheet()->getStyle('K'.$l.':L'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
        $spreadsheet->getActiveSheet()->getStyle('M'.$l.':N'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
        $spreadsheet->getActiveSheet()->getStyle('O'.$l.':P'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    } else {
        $spreadsheet->getActiveSheet()
            ->mergeCells('B'.$l.':C'.$l)
            ->mergeCells('D'.$l.':H'.$l)
            ->mergeCells('I'.$l.':J'.$l)
            ->mergeCells('K'.$l.':L'.$l)
            ->mergeCells('M'.$l.':N'.$l)
            ->mergeCells('O'.$l.':P'.$l);
        $spreadsheet->getActiveSheet()
            ->setCellValue('B'.$l, $i['coditem'])
            ->setCellValue('D'.$l, utf8_decode($i['descripcion']))
            ->setCellValue('I'.$l, number_format($i['cantidad']))
            ->setCellValue('K'.$l, $esunidad)
            ->setCellValue('M'.$l, Strings::rdecimal($i['precio'], 2))
            ->setCellValue('O'.$l, Strings::rdecimal($i['total'], 2));
        $spreadsheet->getActiveSheet()->getStyle('B'.$l.':C'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
        $spreadsheet->getActiveSheet()->getStyle('D'.$l.':H'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
        $spreadsheet->getActiveSheet()->getStyle('I'.$l.':J'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
        $spreadsheet->getActiveSheet()->getStyle('K'.$l.':L'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
        $spreadsheet->getActiveSheet()->getStyle('M'.$l.':N'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
        $spreadsheet->getActiveSheet()->getStyle('O'.$l.':P'.$l)->applyFromArray( array('alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    }
    $l++;
}

//linea
$spreadsheet->getActiveSheet()->getStyle('A'.$l.':Q'.$l)->applyFromArray(
    array("borders" => array("bottom" => array("style" => Border::BORDER_MEDIUM,)))
);

$l += 2;

//observaciones y total
if($descuentototal > 0) {
    $spreadsheet->getActiveSheet()
        ->mergeCells('M'.$l.':N'.$l)
        ->mergeCells('M'.($l+1).':N'.($l+1))
        ->mergeCells('M'.($l+2).':N'.($l+2))
        ->mergeCells('G'.($l+4).':J'.($l+4));
    $spreadsheet->getActiveSheet()
        ->setCellValue('M'.$l, "Sub Total: ")
        ->setCellValue('O'.$l, $subtotal)
        ->setCellValue('M'.($l+1), "Descuento: ")
        ->setCellValue('O'.($l+1), $descuentototal)
        ->setCellValue('A'.($l+2), "Observaciones: ")
        ->setCellValue('C'.($l+2), $observacion)
        ->setCellValue('M'.($l+2), "Total: ")
        ->setCellValue('O'.($l+2), Strings::rdecimal($totalnota, 2))
        ->setCellValue('G'.($l+4), "SIN DERECHO A CREDITO FISCAL");
    $spreadsheet->getActiveSheet()->getStyle('M'.$l)->applyFromArray( array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('M'.($l+1).':N'.($l+1))->applyFromArray( array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $spreadsheet->getActiveSheet()->getStyle('M'.($l+2).':N'.($l+4))->applyFromArray( array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('type' => Fill::FILL_GRADIENT_LINEAR, 'rotation'   => 90, 'startcolor' => array('rgb' => 'c47cf2'), 'endcolor' => array('argb' => 'FF431a5d')), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );

//    $spreadsheet->getActiveSheet()->getStyle('M'.$l)->applyFromArray( array("font" => array("bold" => true)) );
//    $spreadsheet->getActiveSheet()->getStyle('M'.($l+1))->applyFromArray( array("font" => array("bold" => true)) );
    $spreadsheet->getActiveSheet()->getStyle('A'.($l+2))->applyFromArray( array("font" => array("bold" => true)) );
//    $spreadsheet->getActiveSheet()->getStyle('M'.($l+2))->applyFromArray( array("font" => array("bold" => true)) );
    $spreadsheet->getActiveSheet()->getStyle('G'.($l+4).':J'.($l+4))->applyFromArray( array('font' => array('size' => 13), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
    $l += 2;
} else {
    $spreadsheet->getActiveSheet()
        ->mergeCells('G'.($l+2).':J'.($l+2));
    $spreadsheet->getActiveSheet()
        ->setCellValue('A'.$l, "Observaciones: ")
        ->setCellValue('C'.$l, $observacion)
        ->setCellValue('M'.$l, "Total: ")
        ->setCellValue('N'.$l, Strings::rdecimal($totalnota, 2))
        ->setCellValue('G'.($l+2), "SIN DERECHO A CREDITO FISCAL");
    $spreadsheet->getActiveSheet()->getStyle('A'.$l)->applyFromArray( array("font" => array("bold" => true)) );
    $spreadsheet->getActiveSheet()->getStyle('M'.$l)->applyFromArray( array("font" => array("bold" => true)) );
    $spreadsheet->getActiveSheet()->getStyle('G'.($l+2).':J'.($l+2))->applyFromArray( array('font' => array('size' => 13), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)) );
}

$l += 2;

//firmas
$spreadsheet->getActiveSheet()
    ->mergeCells('C'.($l+5).':E'.($l+5))
    ->mergeCells('M'.($l+5).':O'.($l+5));
$spreadsheet->getActiveSheet()
    ->setCellValue('C'.($l+5), 'Depachado por')
    ->setCellValue('M'.($l+5), 'Recibido por');
$spreadsheet->getActiveSheet()->getStyle('C'.($l+5).':E'.($l+5))->applyFromArray( array('font' => array('name'  => 'Arial', 'bold'  => true), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap'      => TRUE)) );
$spreadsheet->getActiveSheet()->getStyle('M'.($l+5).':O'.($l+5))->applyFromArray( array('font' => array('name'  => 'Arial', 'bold'  => true), 'alignment' =>  array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap'      => TRUE)) );

// lineas de firma
$spreadsheet->getActiveSheet()->getStyle('C'.($l+4).':E'.($l+4))->applyFromArray(array("borders" => array("bottom" => array("style" => Border::BORDER_MEDIUM,))));
$spreadsheet->getActiveSheet()->getStyle('M'.($l+4).':O'.($l+4))->applyFromArray(array("borders" => array("bottom" => array("style" => Border::BORDER_MEDIUM,))));


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Devolucion_nota_entrega_'.$numerod.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');

