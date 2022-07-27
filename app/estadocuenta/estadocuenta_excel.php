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
require_once("estadocuenta_modelo.php");

//INSTANCIAMOS EL MODELO
$cuenta = new estadocuenta();

$fechai=$_GET["fechai"];
$fechaf=$_GET["fechaf"];
$cliente=$_GET["cliente"];
$tipo=$_GET["tipo"];

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
$spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFont()->setSize(25);

if($tipo=='B'){
    
    $sheet->setCellValue('A1', 'Estado de Cuenta de Factura');
}else{
    if($tipo=='D'){
        
    $sheet->setCellValue('A1', 'Estado de Cuenta de Notas de Entregas');
    }
}

$sheet->setCellValue('A3', 'fecha tope:  '. date('d-m-Y'));

$spreadsheet->getActiveSheet()->mergeCells('A1:C1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('A10', utf8_decode('Tipo de Transaccion'))
    ->setCellValue('B10', "Código")
    ->setCellValue('C10', "Cliente")
    ->setCellValue('D10', "Fecha Emision")
    ->setCellValue('E10', "Fecha Vencimiento")
    ->setCellValue('F10', "Documento")
    ->setCellValue('G10', "Documento Afectado")
    ->setCellValue('H10', "Descripción")
    ->setCellValue('I10', "Débitos")
    ->setCellValue('J10', "Créditos")
    ->setCellValue('K10', 'Saldo');

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);


//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A10:K10');


    $query = $cuenta->getestadocuenta( $fechai, $fechaf,$cliente,$tipo);



$tipoDoc='';
$row = 11;
foreach ($query as $i) {

     $sheet = $spreadsheet->getActiveSheet();

         $mtotald = number_format($i["Monto"], 2, ',', '.');
         $saldoact = number_format($i["SaldoAct"], 2, ',', '.');

    $sheet->setCellValue('A5', 'Cliente :'.$i["CodClie"]);
    $sheet->setCellValue('G5', 'Saldo Actual :'.$saldoact);

   


            $fechaE = date('d/m/Y', strtotime($i["FechaE"]));
			$fechaV = date('d/m/Y', strtotime($i["FechaV"]));

            if($tipo == 'B'){


                 switch ($i["TipoCxc"]) {
                    case "10":
                
                    $tipoDoc= "Factura";
                
                    break;
                    case "31":
                
                    $tipoDoc= "Nota de Crédito";
                
                    break;
                    case "41":
            
                    $tipoDoc= "PAG"; 
                    break;
                    case "81":
            
                    $tipo= "RET"; 
                    break;
                    default:
                
                    $tipoDoc= "No tiene tipo Fact";
            
                    }


            }else{

                   if($tipo == 'D'){


                   switch ($i["TipoCxc"]) {
                    case "10":
                
                    $tipoDoc= "Nota de Entrega";
                
                    break;
                    case "31":
                
                    $tipoDoc= "Nota de Crédito";
                
                    break;
                    case "41":
            
                    $tipoDoc= "PAG"; 
                    break;
                    case "81":
            
                    $tipo= "RET"; 
                    break;
                    default:
                
                    $tipoDoc= "No tiene tipo Fact";
            
                    }


                 }


            }


           

            $sheet->setCellValue('A' . $row, $tipoDoc);
            $sheet->setCellValue('B' . $row, $i["CodClie"]);
            $sheet->setCellValue('C' . $row, $i["Descrip"]);
            $sheet->setCellValue('D' . $row, $fechaE);
            $sheet->setCellValue('E' . $row, $fechaV);
            $sheet->setCellValue('F' . $row, $i['NumeroD']);

             if($tipoDoc=="PAG" or $tipoDoc=="Nota de Crédito" or $tipoDoc=="RET"){
                   $sheet->setCellValue('G' . $row, $i['NumeroN']);
            }else{
                    $sheet->setCellValue('G' . $row, "");
            }

            $sheet->setCellValue('H' . $row, $i["Document"]);

              if($tipoDoc=="Factura" or $tipoDoc=="Nota de Entrega"){

              $sheet->setCellValue('I' . $row, $mtotald);
              $sheet->setCellValue('J' . $row, "");

             }else{
              $sheet->setCellValue('I' . $row, "");
              $sheet->setCellValue('J' . $row, $mtotald);
              
            }

        
            $sheet->setCellValue('K' . $row, $saldoact);
   

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
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
if($tipo='B'){
    header('Content-Disposition: attachment;filename="Estado de Cuentas de Factura del '.$fechai.' hasta '.$fechaf.'.xlsx"');
}else{
    if($tipo='D'){
    header('Content-Disposition: attachment;filename="Estado de Cuentas de Notas de Entregas del '.$fechai.' hasta '.$fechaf.'.xlsx"');
    }
}


header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');
