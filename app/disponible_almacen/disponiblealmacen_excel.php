<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
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

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("disponiblealmacen_modelo.php");

//INSTANCIAMOS EL MODELO
$disponible = new disponiblealmacen();

$marcas = $_GET['marcas'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
foreach(range('A','G') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}

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
$objDrawing->setCoordinates('E1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setSize(25);


 $sheet->setCellValue('A1', 'Disponibilidad en Almacenes');


$sheet->setCellValue('A5', 'fecha tope:  '. date('d-m-Y'));

$spreadsheet->getActiveSheet()->mergeCells('A1:C1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('A7', utf8_decode(Strings::titleFromJson('codigo_prod')))
    ->setCellValue('B7', Strings::titleFromJson('descrip_prod'))
    ->setCellValue('C7', Strings::titleFromJson('marca_prod'))
    ->setCellValue('D7', Strings::titleFromJson('bulto_01'))
    ->setCellValue('E7', Strings::titleFromJson('paquete_01'))
    ->setCellValue('F7', Strings::titleFromJson('bulto_03'))
    ->setCellValue('G7', Strings::titleFromJson('paquete_03'))
    ->setCellValue('H7', Strings::titleFromJson('bulto_13'))
    ->setCellValue('I7', Strings::titleFromJson('paquete_13'));

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);


//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:I7');

$query = $disponible->getdisponiblealmacen( $marcas);

$row = 8;
foreach ($query as $i) {

    $sheet = $spreadsheet->getActiveSheet();

    if( $i["CodUbic"]==='01'){

        $sheet->setCellValue('A' . $row, $i['codprod']);
        $sheet->setCellValue('B' . $row, $i['Descrip']);
        $sheet->setCellValue('C' . $row, $i['marca']);
        $sheet->setCellValue('D' . $row, number_format($i["Bultos"], 2, ',', '.'));
        $sheet->setCellValue('E' . $row, number_format($i["Paquetes"], 2, ',', '.'));
        $sheet->setCellValue('F' . $row, number_format(0, 2, ',', '.'));
        $sheet->setCellValue('G' . $row, number_format(0, 2, ',', '.'));
        $sheet->setCellValue('H' . $row, number_format(0, 2, ',', '.'));
        $sheet->setCellValue('I' . $row, number_format(0, 2, ',', '.'));

    }else{
        if($row["CodUbic"]==='03'){

            $sheet->setCellValue('A' . $row, $i['codprod']);
            $sheet->setCellValue('B' . $row, $i['Descrip']);
            $sheet->setCellValue('C' . $row, $i['marca']);
            $sheet->setCellValue('D' . $row, number_format(0, 2, ',', '.'));
            $sheet->setCellValue('E' . $row, number_format(0, 2, ',', '.'));
            $sheet->setCellValue('F' . $row, number_format($i["Bultos"], 2, ',', '.'));
            $sheet->setCellValue('G' . $row, number_format($i["Paquetes"], 2, ',', '.'));
            $sheet->setCellValue('H' . $row, number_format(0, 2, ',', '.'));
            $sheet->setCellValue('I' . $row, number_format(0, 2, ',', '.'));

        }else{

            $sheet->setCellValue('A' . $row, $i['codprod']);
            $sheet->setCellValue('B' . $row, $i['Descrip']);
            $sheet->setCellValue('C' . $row, $i['marca']);
            $sheet->setCellValue('D' . $row, number_format(0, 2, ',', '.'));
            $sheet->setCellValue('E' . $row, number_format(0, 2, ',', '.'));
            $sheet->setCellValue('F' . $row, number_format(0, 2, ',', '.'));
            $sheet->setCellValue('G' . $row, number_format(0, 2, ',', '.'));
            $sheet->setCellValue('H' . $row, number_format($i["Bultos"], 2, ',', '.'));
            $sheet->setCellValue('I' . $row, number_format($i["Paquetes"], 2, ',', '.'));
        }
    }

    /** centrar las celdas **/
    $spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('F'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('G'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('H'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('I'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header('Content-Disposition: attachment;filename="Disponibilidad en Almacenes del '.$fechai.' hasta '.$fechaf.'.xlsx"');

header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');
