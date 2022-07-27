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
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("reportecompras_modelo.php");

//INSTANCIAMOS EL MODELO
$reporte = new ReporteCompras();

$fechai = $_GET['fechaf'];
$fechaf = $_GET['fechaf'];
$marca = $_GET['marca'];
$n = $_GET['n'];
$v = $_GET['v'];

$separa = explode("-", $fechai);
$dia = $separa[2];
$mes = $separa[1];
$anio = $separa[0];

$fechaiA = date(FORMAT_DATE_TO_EVALUATE, mktime(0,0,0,($mes)-1,1, $anio));
$fechafA = date(FORMAT_DATE_TO_EVALUATE, mktime(0,0,0,$mes,1, $anio)-1);


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
//$spreadsheet->getActiveSheet()->getStyle('A7:T7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
//$spreadsheet->getActiveSheet()->getStyle('A8:T8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');

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
$objDrawing->setCoordinates('O1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$spreadsheet->getActiveSheet()->getStyle('A3:F3')->getFont()->setSize(18);
$spreadsheet->getActiveSheet()->getStyle('A5:F5')->getFont()->setSize(18);
$sheet->setCellValue('A1', 'REPORTE DE COMPRAS');
$sheet->setCellValue('A3', "Del: " . date(FORMAT_DATE, strtotime($_GET['fechaf'])));
$sheet->setCellValue('A5', 'Proveedor: '. $marca);

/** TITULO DE LA TABLA **/
$spreadsheet->getActiveSheet()
    ->mergeCells('A1:H1')->mergeCells('A3:D3')->mergeCells('A5:G5')
    ->mergeCells('A7:A8')->mergeCells('B7:B8')->mergeCells('C7:C8')
    ->mergeCells('D7:D8')->mergeCells('E7:F7')->mergeCells('G7:G8')
    ->mergeCells('H7:I7')->mergeCells('J7:K7')->mergeCells('L7:O7')
    ->mergeCells('P7:P8')->mergeCells('Q7:Q8')->mergeCells('R7:R8')
    ->mergeCells('S7:S8')->mergeCells('T7:T8')->mergeCells('T7:T8')
    ->mergeCells('U7:U8');

$sheet->setCellValue('A7', Strings::titleFromJson('#'))
    ->setCellValue('B7', Strings::titleFromJson('codigo_prod'))
    ->setCellValue('C7', Strings::titleFromJson('descrip_prod'))
    ->setCellValue('D7', Strings::titleFromJson('display_por_bulto'))
    ->setCellValue('E7', Strings::titleFromJson('ultimo_precio_compra'))
    ->setCellValue('E8', Strings::titleFromJson('display'))
    ->setCellValue('F8', Strings::titleFromJson('bulto'))
    ->setCellValue('G7', Strings::titleFromJson('porcentaje_rentabilidad'))
    ->setCellValue('H7', Strings::titleFromJson('fecha_penultima_compra'))
    ->setCellValue('H8', Strings::titleFromJson('fecha'))
    ->setCellValue('I8', Strings::titleFromJson('bultos'))
    ->setCellValue('J7', Strings::titleFromJson('fecha_ultima_compra'))
    ->setCellValue('J8', Strings::titleFromJson('fecha'))
    ->setCellValue('K8', Strings::titleFromJson('bultos'))
    ->setCellValue('L7', Strings::titleFromJson('ventas_mes_anterior'))
    ->setCellValue('L8', '1')
    ->setCellValue('M8', '2')
    ->setCellValue('N8', '3')
    ->setCellValue('O8', '4')
    ->setCellValue('P7', Strings::titleFromJson('ventas_total_ult_mes'))
    ->setCellValue('Q7', Strings::titleFromJson('existencia_actual_bultos'))
    ->setCellValue('R7', Strings::titleFromJson('prod_no_vendidos'))
    ->setCellValue('S7', Strings::titleFromJson('dias_inventario'))
    ->setCellValue('T7', Strings::titleFromJson('sugerido'))
    ->setCellValue('U7', Strings::titleFromJson('pedido'));


$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:U8');

$codidos_producto = $reporte->get_codprod_por_marca(ALMACEN_PRINCIPAL, $marca);
$i = 9;
$num=0;
foreach ($codidos_producto as $key => $coditem) {

    #Obtencion de datos
    $producto    = $reporte->get_datos_producto($coditem["codprod"]);
    $costos      = $reporte->get_costos($coditem["codprod"]);
    $ult_compras = $reporte->get_ultimas_compras($coditem["codprod"]);
    $ventas      = $reporte->get_ventas_mes_anterior($coditem["codprod"], $fechaiA, $fechafA);
    $bultosExis  = $reporte->get_bultos_existentes(ALMACEN_PRINCIPAL, $coditem["codprod"]);
    $no_vendidos = $reporte->get_productos_no_vendidos($coditem["codprod"], $fechai, $fechaf);

    #Calculos
    $rentabilidad = ReporteComprasHelpers::rentabilidad($producto[0]["precio1"], $producto[0]["costoactual"]);
    $fechapenultimacompra  = (count($ult_compras) > 1) ? date(FORMAT_DATE, strtotime($ult_compras[1]["fechae"])) : '------------';
    $bultospenultimacompra = (count($ult_compras) > 1) ? Strings::rdecimal($ult_compras[1]["cantBult"], 0) : 0;
    $fechaultimacompra   = (count($ult_compras) > 0) ? date(FORMAT_DATE,strtotime($ult_compras[0]["fechae"])) : '------------';
    $bultosultimacompra  = (count($ult_compras) > 0) ? Strings::rdecimal($ult_compras[0]["cantBult"], 0) : 0;
    $ventas_mes_anterior = ReporteComprasHelpers::ventasMesAnterior($ventas, $mes, $anio);
    $totalventasmesanterior = $ventas_mes_anterior["semana1"] + $ventas_mes_anterior["semana2"] + $ventas_mes_anterior["semana3"] + $ventas_mes_anterior["semana4"];
    $diasinventario = ($totalventasmesanterior > 0) ? ($bultosExis[0]["bultosexis"]/$totalventasmesanterior) : 0;
    $sugerido = ($totalventasmesanterior*1.2) - $bultosExis[0]["bultosexis"];
    $sugerido = ($sugerido > 0) ? $sugerido : 0;

    /** cargado de las filas **/
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A' . $i, $key+1);
    $sheet->setCellValue('B' . $i, $producto[0]["codprod"]);
    $sheet->setCellValue('C' . $i, $producto[0]["descrip"]);
    $sheet->setCellValue('D' . $i, Strings::rdecimal($producto[0]["displaybultos"], 0));
    $sheet->setCellValue('E' . $i, Strings::rdecimal((count($costos) > 0) ? (floatval($costos[0]["costodisplay"])) : 0, 2));
    $sheet->setCellValue('F' . $i, Strings::rdecimal((count($costos) > 0) ? (floatval($costos[0]["costobultos"])) : 0, 2));
    $sheet->setCellValue('G' . $i, Strings::rdecimal($rentabilidad, 2) . "%");
    $sheet->setCellValue('H' . $i, $fechapenultimacompra);
    $sheet->setCellValue('I' . $i, $bultospenultimacompra);
    $sheet->setCellValue('J' . $i, $fechaultimacompra);
    $sheet->setCellValue('K' . $i, $bultosultimacompra);
    $sheet->setCellValue('L' . $i, Strings::rdecimal($ventas_mes_anterior["semana1"], 2));
    $sheet->setCellValue('M' . $i, Strings::rdecimal($ventas_mes_anterior["semana2"], 2));
    $sheet->setCellValue('N' . $i, Strings::rdecimal($ventas_mes_anterior["semana3"], 2));
    $sheet->setCellValue('O' . $i, Strings::rdecimal($ventas_mes_anterior["semana4"], 2));
    $sheet->setCellValue('P' . $i, Strings::rdecimal($totalventasmesanterior, 2));
    $sheet->setCellValue('Q' . $i, Strings::rdecimal(floatval($bultosExis[0]["bultosexis"]), 2));
    $sheet->setCellValue('R' . $i, Strings::rdecimal(floatval($no_vendidos[0]["cantidadBult"]), 2));
    $sheet->setCellValue('S' . $i, Strings::rdecimal($diasinventario, 2));
    $sheet->setCellValue('T' . $i, Strings::rdecimal($sugerido, 2));
    $sheet->setCellValue('U' . $i, array_key_exists($key, $n) ? $n[$key] : '');


    /** centrarlas las celdas **/
    $spreadsheet->getActiveSheet()->getStyle('A'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('B'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('C'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('D'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('E'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('F'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    if($rentabilidad > 30){
        //pinta la celda en rojo
        $spreadsheet->getActiveSheet()->getStyle('G'.$i)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    }else {//solo lo centra
        $spreadsheet->getActiveSheet()->getStyle('G'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    }
    $spreadsheet->getActiveSheet()->getStyle('H'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('I'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('J'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('K'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('L'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('M'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('N'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('O'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('P'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('Q'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('R'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('S'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('T'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));
    $spreadsheet->getActiveSheet()->getStyle('U'.$i)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_THIN],) ));

    # Formatos numero para excel
    $spreadsheet->getActiveSheet()->getStyle('A'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
    $spreadsheet->getActiveSheet()->getStyle('D'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
    $spreadsheet->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $spreadsheet->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $spreadsheet->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
    $spreadsheet->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
    $spreadsheet->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
    $spreadsheet->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $spreadsheet->getActiveSheet()->getStyle('M'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $spreadsheet->getActiveSheet()->getStyle('N'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $spreadsheet->getActiveSheet()->getStyle('O'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $spreadsheet->getActiveSheet()->getStyle('P'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $spreadsheet->getActiveSheet()->getStyle('Q'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $spreadsheet->getActiveSheet()->getStyle('R'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $spreadsheet->getActiveSheet()->getStyle('S'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $spreadsheet->getActiveSheet()->getStyle('T'.$i)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

    $i++;
    $num++;
}

$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(65);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="REPORTE_COMPRAS_'.date(FORMAT_DATE, strtotime($_GET['fechai']))."_AL_".date(FORMAT_DATE, strtotime($_GET['fechaf'])).'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');

