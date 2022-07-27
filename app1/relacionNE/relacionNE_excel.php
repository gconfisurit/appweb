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

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("relacionNE_modelo.php");

//INSTANCIAMOS EL MODELO
$notaentrega = new relacionNE();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$ruta = $_GET['ruta'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
foreach(range('A','F') as $columnID) {
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
$spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFont()->setSize(25);
$sheet->setCellValue('A1', 'Relacion de Notas de Entrega por EDV');
$sheet->setCellValue('A5', 'fecha tope:  '. date('d-m-Y'));

$spreadsheet->getActiveSheet()->mergeCells('A1:C1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('A7', Strings::titleFromJson('tipo_transaccion'))
    ->setCellValue('B7', Strings::titleFromJson('numerod'))
    ->setCellValue('C7', 'Número de Factura')
	->setCellValue('D7', 'Número de Devolución')
    ->setCellValue('E7', Strings::titleFromJson('codprov'))
    ->setCellValue('F7', Strings::titleFromJson('razon_social'))
    ->setCellValue('G7', Strings::titleFromJson('fecha_documento'))
    ->setCellValue('H7', Strings::titleFromJson('codvend'))
    ->setCellValue('I7', Strings::titleFromJson('total'))
    ->setCellValue('J7', 'Monto DEV')
	->setCellValue('K7', 'Abono')
	->setCellValue('L7', 'Saldo')
	->setCellValue('M7', 'Descuento')
	->setCellValue('N7', 'Estatus');

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:N7');


$query = $notaentrega->getdevolucionnotaentrega( $fechai, $fechaf,$ruta);
$row = 8;
foreach ($query as $i) {
    

    if($i["tipofac"]=='C'){
        $tipo='N/E';
    }

    $fecha_E = date('d/m/Y', strtotime($i["fechae"]));
    $subtotal = number_format($i["subtotal"], 2, ',', '.');
    $total = number_format($i["total"], 2, ',', '.');

       $estado='';
       $saldo = 0;
       $montototal =0;
        $montototaldv = $abono =0; 
        $montoDEV ='';

        $auxDEV =  $notaentrega->getmontoDEV( $i["numerodv"]);
        foreach ($auxDEV as $row1) 
            {
                $montoDEV = $row1["total"];
            }


        $descuentosanota = $notaentrega->get_descuentosanota( $i["numerod"]);
        $descuentosaitemnota = $notaentrega->get_descuentosaitemnota( $i["numerod"]);

         if ($descuentosaitemnota[0]["descuento"] > 0 &&  $descuentosanota[0]["descuento"] > 0) {
        $tdescuento = $descuentosaitemnota[0]["descuento"] +  $descuentosanota[0]["descuento"];
      } elseif ($descuentosaitemnota[0]["descuento"] > 0) {
        $tdescuento = $descuentosaitemnota[0]["descuento"];
      } elseif ($descuentosanota[0]["descuento"] > 0) {
        $tdescuento =  $descuentosanota[0]["descuento"];
      } else {
        $tdescuento = 0;
      }

      if ($i["estatus"] == 0) {
          $estado= "Pendiente";
        } elseif ($i["estatus"] == 1) {
           $estado= "Abono";
        } elseif ($i["estatus"] == 2) {
           $estado= "Facturada";
        } elseif ($i["estatus"] == 4) {
           $estado= "Devolucion";
        } elseif ($i["estatus"] == 3) {
           $estado= "Pagada";
        }elseif ($i["estatus"] == 5) {
            $estado= "PROCESADA";
        }

       $montototal = $i["total"];
        $abono =$i["abono"];
        $montototaldv = $montoDEV;

 if (count($auxDEV)!= 0 & $i["estatus"] == 0) {
                
                $saldo=($montototal - $abono) - $montototaldv ;

            } else {

                if (count($auxDEV)!= 0 & $i["estatus"] == 1) {
                
                $saldo=($montototal - $abono) - $montototaldv ;

                } else{
                   
                    if ($i["estatus"] == 0) {
                
                       $saldo=($montototal - $abono);

                    } else{
                        if ($i["estatus"] == 1) {
                
                           $saldo=($montototal - $abono);

                         } else{
                             if ($i["estatus"] ==3) {
                
                             $saldo=0;

                            }else{
                                $saldo=0;
                            }
                         }
                    }
                }
                
                
            }

        if ($i["numerodv"] == 0 or $i["numerodv"] =='' or $i["numerodv"] =='NULL') {
           $devolucion= "0";
        }else{
            $devolucion= $i["numerodv"];
        }



    
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A' . $row, $tipo);
    $sheet->setCellValue('B' . $row, $i['numerod']);
    $sheet->setCellValue('C' . $row, $i['numerof']);
    $sheet->setCellValue('D' . $row, $devolucion);
    $sheet->setCellValue('E' . $row, $i['rif']);
    $sheet->setCellValue('F' . $row, utf8_encode($i['rsocial']));
    $sheet->setCellValue('G' . $row, $fecha_E);
    $sheet->setCellValue('H' . $row, $i['codvend']);
    $sheet->setCellValue('I' . $row, $total);
    $sheet->setCellValue('J' . $row, $montoDEV);
    $sheet->setCellValue('K' . $row, number_format($i["abono"], 2, ',', '.'));
    $sheet->setCellValue('L' . $row, $saldo);
    $sheet->setCellValue('M' . $row, $tdescuento);
    $sheet->setCellValue('N' . $row, $estado);

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
    $spreadsheet->getActiveSheet()->getStyle('J'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('K'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('L'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('M'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('N'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Relacion_de_Notas_de_Entrega_por_EDV.xlsx"');
header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');

