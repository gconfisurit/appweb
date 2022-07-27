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
//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("libroventas_modelo.php");

//INSTANCIAMOS EL MODELO
$libroventa = new LibroVenta();

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

$fechai = Dates::normalize_date($_GET['fechai']).' 00:00:00';
$fechaf = Dates::normalize_date($_GET['fechaf']).' 23:59:59';

# creamos la cabecera de la tabla
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
foreach(range('B','R') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}

# Logo
$gdImage = imagecreatefrompng(PATH_LIBRARY.'build/images/logo.png');
$objDrawing = new MemoryDrawing();
$objDrawing->setName('Sample image');
$objDrawing->setDescription('TEST');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight(108);
$objDrawing->setWidth(128);
$objDrawing->setCoordinates('H1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', Empresa::getName());
$spreadsheet->getActiveSheet()->mergeCells('A1:G1');
$spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray(array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$spreadsheet->getActiveSheet()->getStyle('A2:F2')->getFont()->setSize(18);
$sheet->setCellValue('A2', 'Libro de Ventas');
$spreadsheet->getActiveSheet()->mergeCells('A2:K2');

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);



$sheet->setCellValue('C4', 'Desde:');
$sheet->setCellValue('D4', date(FORMAT_DATE, strtotime($_GET['fechai'])));
$spreadsheet->getActiveSheet()->mergeCells('D4:E4');
$spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('D4:E4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$sheet->setCellValue('G4', 'Hasta:');
$sheet->setCellValue('H4', date(FORMAT_DATE, strtotime($_GET['fechaf'])));
$spreadsheet->getActiveSheet()->mergeCells('H4:I4');
$spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('H4:I4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$row = 8;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numero_operacion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('fecha_documento'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('rif'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('razon_social'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('tipo_documento'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numerod'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numero_control'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('tipo_transaccion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numerod_afectado'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numero_comprobante_retencion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('total_ventas'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('ventas_exentas'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('base_imponible'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('porcentaje_alicuota'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('monto_iva'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('iva_retenido'));

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++)
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE),'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
$spreadsheet->getActiveSheet()->getStyle( 'A'.$row.':'.getExcelCol($aux, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'c8dcff'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),));




$datos = $libroventa->getLibroPorFecha($fechai, $fechaf);
$retenciones_otros_periodos = $libroventa->getRetencionesOtrosPeriodos($fechai, $fechaf);

//DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
$data = $totales_libro = $resumen = $otros_periodos = $totales_otros_periodos = Array();

$tvii = $ve = $magbi16c = $mag16c = $ivare = $ivape = 0;
$ivare2 = $ivape2 = 0;

if (is_array($datos)==true and count($datos)>0)
{
    foreach ($datos as $key => $row)
    {
        $retencion_dato = $libroventa->getRetencionItem($fechai, $fechaf, $row['numerodoc']);

        //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        $base_imponible = $row["totalventas"] - $row['mtoexento'];
        $totalventasconiva = $base_imponible + $row['mtoexento'] + $row['montoiva_contribuyeiva'];

        $tvii += $totalventasconiva;
        $ve += $row['mtoexento'];
        $magbi16c += $base_imponible;
        $mag16c += $row['montoiva_contribuyeiva'];
        $ivare += count($retencion_dato)>0 ? Numbers::avoidNull($retencion_dato[0]['retencioniva']) : 0;

        $sub_array['num']  = $key+1;
        $sub_array['fechaemision']  = date(FORMAT_DATE, strtotime($row["fechaemision"]));
        $sub_array['rifcliente']    = $row["rifcliente"];
        $sub_array['nombre']        = utf8_encode($row["nombre"]);
        $sub_array['tipodoc']       = $row["tipodoc"];
        $sub_array['numerodoc']     = $row["numerodoc"];
        $sub_array['nroctrol']      = Strings::avoidNull($row["nroctrol"]);
        $sub_array['tiporeg']       = $row["tiporeg"];
        $sub_array['factafectada']  = Strings::avoidNull($row["factafectada"]);
        $sub_array['nroretencion']  = count($retencion_dato)>0 ? Strings::avoidNull($retencion_dato[0]["nroretencion"]) : '';
        $sub_array['totalventasconiva'] = Strings::rdecimal($totalventasconiva, 2);
        $sub_array['mtoexento']      = Strings::rdecimal($row["mtoexento"], 2);
        $sub_array['base_imponible'] = Strings::rdecimal($base_imponible, 2);
        $sub_array['alicuota_contribuyeiva'] = Strings::rdecimal($row["alicuota_contribuyeiva"], 0);
        $sub_array['montoiva_contribuyeiva'] = Strings::rdecimal($row["montoiva_contribuyeiva"], 2);
        $sub_array['retencioniva']  = count($retencion_dato)>0 ? Strings::avoidNull($retencion_dato[0]["retencioniva"]) : '';

        $data[] = $sub_array;
    }
}

$totales_libro = array(
    "tvii"     => Strings::rdecimal($tvii, 2),
    "ve"       => Strings::rdecimal($ve, 2),
    "magbi16c" => Strings::rdecimal($magbi16c, 2),
    "mag16c"   => Strings::rdecimal($mag16c, 2),
    "ivare"    => Strings::rdecimal($ivare, 2)
);

if (is_array($retenciones_otros_periodos)==true and count($retenciones_otros_periodos)>0)
{
    foreach ($retenciones_otros_periodos as $key => $row)
    {
        //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        $ivare2 += Numbers::avoidNull($row['retencioniva']);

        $sub_array['num']  = $key+1;
        $sub_array['fechaemision']  = date(FORMAT_DATE, strtotime($row["fechaemision"]));
        $sub_array['rifcliente']    = $row["rifcliente"];
        $sub_array['nombre']        = utf8_encode($row["nombre"]);
        $sub_array['tipodoc']       = $row["tipodoc"];
        $sub_array['numerodoc']     = $row["numerodoc"];
        $sub_array['tiporeg']       = $row["tiporeg"];
        $sub_array['factafectada']  = Strings::avoidNull($row["factafectada"]);
        $sub_array['fecharetencion'] = !is_null($row["fecharetencion"]) ? date(FORMAT_DATE, strtotime($row["fecharetencion"])) : '';
        $sub_array['totalgravable_contribuye'] = Strings::rdecimal($row["totalgravable_contribuye"], 2);
        $sub_array['totalivacontribuye'] = Strings::rdecimal($row["totalivacontribuye"], 2);
        $sub_array['retencioniva']  = Strings::rdecimal($row["retencioniva"], 2);

        $otros_periodos[] = $sub_array;
    }
}

$totales_otros_periodos = array(
    "ivare" => Strings::rdecimal($ivare2, 2)
);

$resumen = array(
    array(
        "descripcion"    => 'Total ventas internas no gravadas',
        "base_imponible" => Strings::rdecimal($ve, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Total ventas de Exportación',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Total ventas internas Gravadas por Alicuota General (16%)',
        "base_imponible" => Strings::rdecimal($magbi16c, 2),
        "credito_fiscal" => Strings::rdecimal($mag16c, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Total ventas internas Gravadas por Alicuota General',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Total ventas Gravadas por Alicuota reducida',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(

        "descripcion"    => 'Total Ventas y Débitos Fiscales para efectos de determinación',
        "base_imponible" => Strings::rdecimal(($ve+$magbi16c), 2),
        "credito_fiscal" => Strings::rdecimal($mag16c, 2),
        "isBold" => true, "isColored" => true,
    ),
    array(
        "descripcion"    => 'Iva Retenidos periodos anteriores',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal($ivare2, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Iva Retenidos en este periodo',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal($ivare, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Total IVA Retenido',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(($ivare2+$ivare), 2),
        "isBold" => true, "isColored" => true,
    ),
);




$row = 9;
if (is_array($data)==true and count($data)>0) {
    foreach ($data as $x) {
        $i = 0;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue(getExcelCol($i) . $row, $x['num']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['fechaemision']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['rifcliente']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['nombre']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['tipodoc']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['numerodoc']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['tiporeg']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['factafectada']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['nroretencion']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['totalventasconiva']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['mtoexento']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['base_imponible']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['alicuota_contribuyeiva'] . '%');
        $sheet->setCellValue(getExcelCol($i) . $row, $x['montoiva_contribuyeiva']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['retencioniva']);

        $i = 0;
        /** centrarlas las celdas **/
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

        $row++;
    }
}

$i = 0;
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue(getExcelCol($i) . $row,'Totales');
$sheet->setCellValue(getExcelCol($i+=8) . $row, $totales_libro['tvii']);
$sheet->setCellValue(getExcelCol($i) . $row, $totales_libro['ve']);
$sheet->setCellValue(getExcelCol($i) . $row, $totales_libro['magbi16c']);
$sheet->setCellValue(getExcelCol($i) . $row, '');
$sheet->setCellValue(getExcelCol($i) . $row, $totales_libro['mag16c']);
$sheet->setCellValue(getExcelCol($i) . $row, $totales_libro['ivare']);
$spreadsheet->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
$i = 0;
/** centrarlas las celdas **/
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i+=8) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));





$row += 5;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numero_operacion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('fecha_comprobante_retencion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('id_fiscal'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('razon_social'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('tipo_documento'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numero_comprobante_retencion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('tipo_transaccion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numerod_afectado'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('fecha_documento_afectado'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('base_retencion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('iva'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('iva_retenido'));

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++)
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE),'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
$spreadsheet->getActiveSheet()->getStyle( 'A'.$row.':'.getExcelCol($aux, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'c8dcff'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),));

$row += 1;
if (is_array($otros_periodos)==true and count($otros_periodos)>0) {
    foreach ($otros_periodos as $x) {
        $i = 0;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue(getExcelCol($i) . $row, $x['num']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['fechaemision']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['rifcliente']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['nombre']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['tipodoc']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['numerodoc']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['tiporeg']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['factafectada']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['fecharetencion']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['totalgravable_contribuye']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['totalivacontribuye'].'%');
        $sheet->setCellValue(getExcelCol($i) . $row, $x['retencioniva']);

        $i = 0;
        /** centrarlas las celdas **/
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

        $row++;
    }
}

$i = 0;
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue(getExcelCol($i) . $row,'Totales');
$sheet->setCellValue(getExcelCol($i+=10) . $row, $totales_otros_periodos['ivare']);
$spreadsheet->getActiveSheet()->mergeCells('A'.$row.':K'.$row);
$i = 0;
/** centrarlas las celdas **/
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i+=10) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));





$row += 5;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, strtoupper( Strings::titleFromJson('resumen_credito_fiscal') ));
$sheet->setCellValue(getExcelCol($i+=3).$row, strtoupper( Strings::titleFromJson('base_imponible') ));
$sheet->setCellValue(getExcelCol($i).$row, strtoupper( Strings::titleFromJson('credito_fiscal') ));
$spreadsheet->getActiveSheet()->mergeCells('A'.$row.':D'.$row);

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++)
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE),'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
$spreadsheet->getActiveSheet()->getStyle( 'A'.$row.':'.getExcelCol($aux, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'c8dcff'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),));

$row += 1;
if (is_array($resumen)==true and count($resumen)>0) {
    foreach ($resumen as $x) {
        $i = 0;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue(getExcelCol($i) . $row, $x['descripcion']);
        $sheet->setCellValue(getExcelCol($i+=3) . $row, $x['base_imponible']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['credito_fiscal']);
        $spreadsheet->getActiveSheet()->mergeCells('A'.$row.':D'.$row);

        $i = 0;
        /** centrarlas las celdas **/
        if ($x['isBold'] and $x['isColored']) {
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')),'fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i+=3) . $row)->applyFromArray(array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')),'fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')),'fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        } else {
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i+=3) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        }

        $row++;
    }
}



$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(80);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="libroventas_de_'.date(FORMAT_DATE, strtotime($_GET['fechai'])).'_al_'.date(FORMAT_DATE, strtotime($_GET['fechaf'])).'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');