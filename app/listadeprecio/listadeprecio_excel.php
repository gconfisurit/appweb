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
require_once("listadeprecio_modelo.php");

//INSTANCIAMOS EL MODELO
$precios = new Listadeprecio();

$i = 0;
//funcion recursiva creada para reporte Excel que evalua los numeros > 0
// y asigna la letra desde la A....hasta la Z y AA, AB, AC.....AZ
function getExcelCol($num, $letra_temp = false) {
    $numero = $num % 26;
    $letra = chr(65 + $numero);
    $num2 = intval($num / 26);
    if(!$letra_temp)
        $GLOBALS['i'] = $GLOBALS['i'] +1;

    if ($num2 > 0) {
        return getExcelCol($num2 - 1, true) . $letra;
    } else {
        return $letra;
    }
}

$depos = $_GET['depos'];
$marcas = $_GET['marcas'];
$orden = $_GET['orden'];
$exis = $_GET['exis'];
$iva = $_GET['iva'];
$cubi = $_GET['cubi'];
$p1 = str_replace("1","1",$_GET['p1']);
$p2 = str_replace("1","2",$_GET['p2']);
$p3 = str_replace("1","3",$_GET['p3']);
$sumap = $_GET['p1'] + $_GET['p2'] + $_GET['p3'];
$sumap2 = $p1 + $p2 + $p3;
$pAux = '';


//creamos la cabecera de la tabla
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
$objDrawing->setCoordinates('G1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', 'REPORTE DE LISTADO DE PRECIOS E INVENTARIO');
//$sheet->setCellValue('A3', 'del: '. date(FORMAT_DATE, strtotime($fechai)));
//$sheet->setCellValue('A5', 'al:  '. date(FORMAT_DATE, strtotime($fechaf)));


$spreadsheet->getActiveSheet()->mergeCells('A1:E1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('codigo_prod'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('descrip_prod'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('marca_prod'));
//BULTOS
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('bultos'));
switch ($sumap) {
    case 1:
        $sheet->setCellValue(getExcelCol($i).'7', 'Precio '.$sumap2.' Bulto');
        break;
    case 2:
        if($p1 == 1){ $pAux = $p1; }else{ $pAux = $p2;}
        $sheet->setCellValue(getExcelCol($i).'7', 'Precio '.$pAux.' Bulto');
        if ($p3 == 3){ $pAux = $p3; }else{ $pAux = $p2;}
        $sheet->setCellValue(getExcelCol($i).'7', 'Precio '.$pAux.' Bulto');
        $pAux = '';
        break;
    default: /** 0 || 3**/
        $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('precio1_bulto'));
        $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('precio2_bulto'));
        $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('precio3_bulto'));
}
//PAQUETES
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('paquetes'));
switch ($sumap) {
    case 1:
        $sheet->setCellValue(getExcelCol($i).'7', 'Precio '.$sumap2.' Paquete');
        break;
    case 2:
        if($p1 == 1){ $pAux = $p1; }else{ $pAux = $p2;}
        $sheet->setCellValue(getExcelCol($i).'7', 'Precio '.$pAux.' Paquete');
        if ($p3 == 3){ $pAux = $p3; }else{ $pAux = $p2;}
        $sheet->setCellValue(getExcelCol($i).'7', 'Precio '.$pAux.' Paquete');
        $pAux = '';
        break;
    default: /** 0 || 3**/
        $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('precio1_paquete'));
        $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('precio2_paquete'));
        $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('precio3_paquete'));
}
if ($cubi == 1) {
    $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('cubicaje'));
}
//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++){ $spreadsheet->getActiveSheet()->getColumnDimension(getExcelCol($n))->setAutoSize(true); }


$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:'.getExcelCol($aux).'7');


//a partir de aqui comienza a llenarse la tabla
$datos = $precios->getListadeprecios($marcas, $depos, $exis, $orden);
$num = count($datos);
$row = 8;
foreach ($datos as $x) {
    $i = 0;
    if (!$x['esexento']) {
        $precio1 = $x['precio1'] * $iva;
        $precio2 = $x['precio2'] * $iva;
        $precio3 = $x['precio3'] * $iva;
        $preciou1 = $x['preciou1'] * $iva;
        $preciou2 = $x['preciou2'] * $iva;
        $preciou3 = $x['preciou3'] * $iva;
    } else {
        $precio1 = $x['precio1'];
        $precio2 = $x['precio2'];
        $precio3 = $x['precio3'];
        $preciou1 = $x['preciou1'];
        $preciou2 = $x['preciou2'];
        $preciou3 = $x['preciou3'];
    }
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue(getExcelCol($i, true) . $row, $x['codprod']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $row, $x['descrip']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $row, $x['marca']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    //BULTOS
    $sheet->setCellValue(getExcelCol($i, true) . $row, $x['existen']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    switch ($sumap) {
        case 1:
            if ($x['esexento'] == 0) {
                $pAux = Strings::rdecimal($x['precio' . $sumap2] * $iva, 2);
            } else {
                $pAux = Strings::rdecimal($x['precio' . $sumap2], 2);
            }
            $sheet->setCellValue(getExcelCol($i, true) . $row, $pAux);
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            break;
        case 2:
            if ($p1 == 1) {
                $pAux = Strings::rdecimal($precio1, 2);
            } else {
                $pAux = Strings::rdecimal($precio2, 2);
            }
            $sheet->setCellValue(getExcelCol($i, true) . $row, $pAux);
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            if ($p3 == 3) {
                $pAux = Strings::rdecimal($precio3, 2);
            } else {
                $pAux = Strings::rdecimal($precio2, 2);
            }
            $sheet->setCellValue(getExcelCol($i, true) . $row, $pAux);
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            break;
        default:
            /** 0 || 3**/
            $sheet->setCellValue(getExcelCol($i, true) . $row, Strings::rdecimal($precio1, 2));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

            $sheet->setCellValue(getExcelCol($i, true) . $row, Strings::rdecimal($precio2, 2));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

            $sheet->setCellValue(getExcelCol($i, true) . $row, Strings::rdecimal($precio3, 2));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    }
    $pAux = '';
    //PAQUETES
    $sheet->setCellValue(getExcelCol($i, true) . $row, round($x['exunidad']));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    switch ($sumap) {
        case 1:
            if ($x['esexento'] == 0) {
                $pAux = Strings::rdecimal($x['preciou' . $sumap2] * $iva, 2);
            } else {
                $pAux = Strings::rdecimal($x['preciou' . $sumap2], 2);
            }
            $sheet->setCellValue(getExcelCol($i, true) . $row, $pAux);
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            break;
        case 2:
            if ($p1 == 1) {
                $pAux = Strings::rdecimal($preciou1, 2);
            } else {
                $pAux = Strings::rdecimal($preciou2, 2);
            }
            $sheet->setCellValue(getExcelCol($i, true) . $row, $pAux);
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            if ($p3 == 3) {
                $pAux = Strings::rdecimal($preciou3, 2);
            } else {
                $pAux = Strings::rdecimal($preciou2, 2);
            }
            $sheet->setCellValue(getExcelCol($i, true) . $row, $pAux);
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            break;
        default:
            /** 0 || 3**/
            $sheet->setCellValue(getExcelCol($i, true) . $row, Strings::rdecimal($preciou1, 2));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

            $sheet->setCellValue(getExcelCol($i, true) . $row, Strings::rdecimal($preciou2, 2));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

            $sheet->setCellValue(getExcelCol($i, true) . $row, Strings::rdecimal($preciou3, 2));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    }
    $pAux = '';
    if ($cubi == 1) {
        $sheet->setCellValue(getExcelCol($i, true) . $row, $x['cubicaje']);
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    }
    $row++;
}
// por ultimo se escribe la cantidad de productos
$sheet->setCellValue('B' . ($row + 3), 'Total de Productos:  ' . $num);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Listado_de_precios_e_inventario_' . date('d/m/Y') . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');
