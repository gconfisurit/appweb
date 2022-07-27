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
require_once("kpi_modelo.php");
require_once("../kpimanager/kpimanager_modelo.php");

//INSTANCIAMOS EL MODELO
$kpi = new Kpi();
$kpiManager = new KpiManager();

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

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$d_habiles = $_GET['d_habiles'];
$d_trans = $_GET['d_trans'];
$fechai2 = str_replace('/','-',$fechai); $fechai2 = date('Y-m-d', strtotime($fechai2));
$fechaf2 = str_replace('/','-',$fechaf); $fechaf2 = date('Y-m-d', strtotime($fechaf2));


# obtencion de las marcas para el KPI
$lista_marcaskpi = array_map(function ($arr) { return $arr['descripcion']; }, KpiMarcas::todos());


# Aqui se establece el ancho que ocupan las celdas
$ancho_rutas = 1;
$ancho_activacion = 4 + count($lista_marcaskpi);
$ancho_efectividad = 7;
$ancho_ventas = 15;
$ancho_total = $ancho_rutas + $ancho_activacion + $ancho_efectividad + $ancho_ventas;


# array con las celdas de porcentajes
# (num_celda + cantidad_marcas)
$arr_porc = [
    3 + count($lista_marcaskpi),  // %Act. Alcanzada
    11 + count($lista_marcaskpi), // % Efectividad Alcanzada a la Fecha
    14 + count($lista_marcaskpi), // %Alcanzado (Bulto)
    17 + count($lista_marcaskpi), // %Alcanzado (Kg)
    21 + count($lista_marcaskpi), // %Alcanzado ($)
    23 + count($lista_marcaskpi), // % Venta PEPSICO
    25 + count($lista_marcaskpi), // % Venta Complementaria
];


# creamos la cabecera de la tabla
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

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
$objDrawing->setCoordinates('P1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', Empresa::getName());
$spreadsheet->getActiveSheet()->mergeCells('A1:N1');
$spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray(array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$spreadsheet->getActiveSheet()->getStyle('A2:F2')->getFont()->setSize(18);
$sheet->setCellValue('A2', 'REPORTE KPI (Key Performance Indicator)');
$spreadsheet->getActiveSheet()->mergeCells('A2:K2');

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);



$sheet->setCellValue('C4', 'Desde:');
$sheet->setCellValue('D4', date(FORMAT_DATE, strtotime($fechai)));
$spreadsheet->getActiveSheet()->mergeCells('D4:E4');
$spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('D4:E4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$sheet->setCellValue('G4', 'Hasta:');
$sheet->setCellValue('H4', date(FORMAT_DATE, strtotime($fechaf)));
$spreadsheet->getActiveSheet()->mergeCells('H4:I4');
$spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('H4:I4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$sheet->setCellValue('L4', 'D. Habiles:');
$sheet->setCellValue('M4', $d_habiles);
$spreadsheet->getActiveSheet()->getStyle('L4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('M4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$sheet->setCellValue('O4', 'D. Transc:');
$sheet->setCellValue('P4', $d_trans);
$spreadsheet->getActiveSheet()->getStyle('O4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('P4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));






$row = 7;
$i = 1;
$sheet->setCellValue('A'.$row, 'Rutas');
$sheet->setCellValue('B'.$row, 'Activación');
$sheet->setCellValue(getExcelCol($i+=$ancho_activacion).$row, 'Efectividad');
$sheet->setCellValue(getExcelCol($i+=$ancho_efectividad-1).$row, 'Ventas');
$spreadsheet->getActiveSheet()->getStyle('A'.$row.':'.getExcelCol($ancho_total-1, true).$row)->getFont()->setSize(14);
$i = 1;
$spreadsheet->getActiveSheet()->mergeCells(getExcelCol($i).$row.':'.getExcelCol($i+=$ancho_activacion-2).$row);
$spreadsheet->getActiveSheet()->mergeCells(getExcelCol($i).$row.':'.getExcelCol($i+=$ancho_efectividad-2).$row);
$spreadsheet->getActiveSheet()->mergeCells(getExcelCol($i).$row.':'.getExcelCol($i+=$ancho_ventas-2).$row);
$i = 1;
$spreadsheet->getActiveSheet()->getStyle( 'A'.$row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '7abaff'],), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle( getExcelCol($i).$row.':'.getExcelCol($i+=$ancho_activacion-2).$row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '7abaff'],), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle( getExcelCol($i).$row.':'.getExcelCol($i+=$ancho_efectividad-2).$row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '7abaff'],), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle( getExcelCol($i).$row.':'.getExcelCol($i+=$ancho_ventas-2).$row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '7abaff'],), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));




$row = 8;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, 'Rutas');
$sheet->setCellValue(getExcelCol($i).$row, 'Maestro');
$sheet->setCellValue(getExcelCol($i).$row, 'Clie Activados');
#listado dinamico de las marcas
foreach ($lista_marcaskpi as $marcakpi)
    $sheet->setCellValue(getExcelCol($i).$row, $marcakpi);
#fin de listado dinamico de las marcas
$sheet->setCellValue(getExcelCol($i).$row, '%Act. Alcanzada');
$sheet->setCellValue(getExcelCol($i).$row, 'Pendientes');
$sheet->setCellValue(getExcelCol($i).$row, 'Visita');
$sheet->setCellValue(getExcelCol($i).$row, 'Obj  Facturas mas notas Mensual');
$sheet->setCellValue(getExcelCol($i).$row, 'Total Facturas Realizadas');
$sheet->setCellValue(getExcelCol($i).$row, 'Total Notas Realizadas');
$sheet->setCellValue(getExcelCol($i).$row, 'Devoluciones Realizadas (nt + fac)');
$sheet->setCellValue(getExcelCol($i).$row, 'Total Devoluciones Realizadas ($)');
$sheet->setCellValue(getExcelCol($i).$row, '% Efectividad Alcanzada a la Fecha');
$sheet->setCellValue(getExcelCol($i).$row, 'Objetivo (Bulto)');
$sheet->setCellValue(getExcelCol($i).$row, 'Logro (Bulto)');
$sheet->setCellValue(getExcelCol($i).$row, '%Alcanzado (Bulto)');
$sheet->setCellValue(getExcelCol($i).$row, 'Objetivo (Kg)');
$sheet->setCellValue(getExcelCol($i).$row, 'Logro (Kg)');
$sheet->setCellValue(getExcelCol($i).$row, '%Alcanzado (Kg)');
$sheet->setCellValue(getExcelCol($i).$row, 'Real Drop Size ($)');
$sheet->setCellValue(getExcelCol($i).$row, 'Objetivo Total Ventas ($)');
$sheet->setCellValue(getExcelCol($i).$row, 'Total Logro Ventas en ($)');
$sheet->setCellValue(getExcelCol($i).$row, '%Alcanzado ($)');
$sheet->setCellValue(getExcelCol($i).$row, 'Ventas PEPSICO ($)');
$sheet->setCellValue(getExcelCol($i).$row, '% Venta PEPSICO');
$sheet->setCellValue(getExcelCol($i).$row, 'Ventas Complementaria ($)');
$sheet->setCellValue(getExcelCol($i).$row, '% Venta Complementaria');
$sheet->setCellValue(getExcelCol($i).$row, 'Cobranza Rebajadas (Bs)');

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++) {
    if ($n >= 3 and $n < count($lista_marcaskpi)+3) {
        $spreadsheet->getActiveSheet()->getColumnDimension(getExcelCol($n, true))->setWidth('6');
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->getAlignment()->setTextRotation(90);
    } else {
        $spreadsheet->getActiveSheet()->getColumnDimension(getExcelCol($n, true))->setWidth('13');
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->getAlignment()->setWrapText(true);
        }
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE),'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
}
$spreadsheet->getActiveSheet()->getStyle( 'A'.$row.':'.getExcelCol($aux, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'c8dcff'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),));


//DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
$data = Array();
$total_general = array();

$coordinadores = $kpi->get_coordinadores();
if (is_array($coordinadores) == true and count($coordinadores) > 0)
{
    $marcasKpi = array_map(function ($arr) { return $arr['descripcion']; }, KpiMarcas::todos());
    $ttl_marcas = new Kpimarca($marcasKpi);

    # inicializacion variables ttl
    $ttl_clientes           = 0;
    $ttl_clientes_activos   = 0;
    $ttl_clientes_noactivos = 0;
    $ttl_activacionBultos   = array();
    $ttl_porc_activacion    = 0;
    $ttl_obj_documentos_mensual  = 0;
    $ttl_facturas_realizadas     = 0;
    $ttl_notas_realizadas        = 0;
    $ttl_devoluciones_realizadas = 0;
    $ttl_montoendivisa_devoluciones = 0;
    $ttl_efec_alcanzada_fecha       = 0;
    $ttl_objetivo_bulto             = 0;
    $ttl_logro_bulto                = 0;
    $ttl_porc_alcanzado_bulto       = 0;
    $ttl_objetivo_kg                = 0;
    $ttl_logro_kg                   = 0;
    $ttl_porc_alcanzado_kg          = 0;
    $ttl_objetivo_ventas_divisas    = 0;
    $ttl_logro_ventas_divisas       = 0;
    $ttl_porc_ventas_divisas        = 0;
    $ttl_real_dz_dolares            = 0;
    $ttl_logro_ventas_divisas_pepsico        = 0;
    $ttl_porcentaje_ventas_divisas_pepsico   = 0;
    $ttl_logro_ventas_divisas_complementaria = 0;
    $ttl_porcentaje_ventas_divisas_complementaria = 0;
    $ttl_cobranzasRebajadas = 0;

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();
    foreach ($coordinadores as $coord)
    {
        //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();
        $sub_array['coordinador'] = $coord["coordinador"];
        $subttl_marcas = new Kpimarca($marcasKpi);

        # inicializacion variables subttl
        $subttl_clientes           = 0;
        $subttl_clientes_activos   = 0;
        $subttl_clientes_noactivos = 0;
        $subttl_activacionBultos   = array();
        $subttl_porc_activacion    = 0;
        $subttl_obj_documentos_mensual  = 0;
        $subttl_facturas_realizadas     = 0;
        $subttl_notas_realizadas        = 0;
        $subttl_devoluciones_realizadas = 0;
        $subttl_montoendivisa_devoluciones = 0;
        $subttl_efec_alcanzada_fecha       = 0;
        $subttl_objetivo_bulto             = 0;
        $subttl_logro_bulto                = 0;
        $subttl_porc_alcanzado_bulto       = 0;
        $subttl_objetivo_kg                = 0;
        $subttl_logro_kg                   = 0;
        $subttl_porc_alcanzado_kg          = 0;
        $subttl_objetivo_ventas_divisas    = 0;
        $subttl_logro_ventas_divisas       = 0;
        $subttl_porc_ventas_divisas        = 0;
        $subttl_real_dz_dolares            = 0;
        $subttl_logro_ventas_divisas_pepsico        = 0;
        $subttl_porcentaje_ventas_divisas_pepsico   = 0;
        $subttl_logro_ventas_divisas_complementaria = 0;
        $subttl_porcentaje_ventas_divisas_complementaria = 0;
        $subttl_cobranzasRebajadas = 0;

        $vendedores = $kpi->get_rutasPorCoordinador($coord["coordinador"]);
        if (is_array($vendedores) == true and count($vendedores) > 0) {
            foreach ($vendedores as $vend)
            {
                $ruta = $vend["ID3"];
                $clientes           = count($kpi->get_MaestroClientesPorRuta($ruta));
                $clientes_activos   = count($kpi->get_ClientesActivosPorRuta($ruta, $fechai2, $fechaf2));
                $clientes_noactivos = $clientes - $clientes_activos;
                $activacionBultos   = KpiHelpers::activacionBultosPorMarcasKpi($ruta, $marcasKpi, $fechai2, $fechaf2);
                $porc_activacion    = ($clientes!=0) ? ($clientes_activos/$clientes) * 100 : 0;
                $frecuencia         = $kpi->get_frecuenciaVisita($ruta)[0];
                $frecuenciaVisita   = KpiHelpers::frecuenciaVisita($frecuencia);
                $obj_documentos_mensual = KpiHelpers::objetivoFacturasMasNotasMensual($clientes, $d_habiles, $frecuencia);
                $facturas_realizadas    = count($kpi->get_ventasFactura($ruta, $fechai2, $fechaf2));
                $notas_realizadas = count($kpi->get_ventasNotas($ruta, $fechai2, $fechaf2));
                $devolucionesFact = $kpi->get_devolucionesFactura($ruta, $fechai2, $fechaf2);
                $devolucionesNota = $kpi->get_devolucionesNotas($ruta, $fechai2, $fechaf2);
                $devoluciones_realizadas = count($devolucionesFact) + count($devolucionesNota);
                $montoendivisa_devoluciones_fact = floatval($kpi->get_montoDivisasDevolucionesFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"]);
                $montoendivisa_devoluciones_nt   = floatval($kpi->get_montoDivisasDevolucionesNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"]);
                $montoendivisa_devoluciones = $montoendivisa_devoluciones_fact + $montoendivisa_devoluciones_nt;
                $efec_alcanzada_fecha = KpiHelpers::efectividadAlcanzadaAlaFecha($d_trans, $d_habiles, $obj_documentos_mensual, $facturas_realizadas, $notas_realizadas);
                $objetivo_bulto       = KpiHelpers::obtenerObjetivo($frecuencia, 'ObjVentasBu');
                $logro_bulto          = KpiHelpers::logroPorTipo($ruta, $fechai2, $fechaf2, 'BUL');
                $porc_alcanzado_bulto = (($objetivo_bulto!=0) ? ($logro_bulto/$objetivo_bulto)*100 : 0) ?? 0;
                $objetivo_kg          = KpiHelpers::obtenerObjetivo($frecuencia, 'ObjVentasKG');
                $logro_kg             = KpiHelpers::logroPorTipo($ruta, $fechai2, $fechaf2, 'KG');
                $porc_alcanzado_kg    = (($objetivo_kg!=0) ? ($logro_kg/$objetivo_kg)*100 : 0) ?? 0;
                $objetivo_ventas_divisas = KpiHelpers::obtenerObjetivo($frecuencia, 'ObjVentasBs'); # realmente objetivo $$
                $ventas_divisas_fact  = $kpi->get_ventasDivisasFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                $ventas_divisas_nt    = $kpi->get_ventasDivisasNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                $logro_ventas_divisas = floatval($ventas_divisas_fact) + floatval($ventas_divisas_nt);
                $porc_ventas_divisas  = (($objetivo_ventas_divisas!=0) ? ($logro_ventas_divisas/$objetivo_ventas_divisas)*100 : 0) ?? 0;
                $real_dz_dolares      = (($facturas_realizadas+$notas_realizadas) > 0) ? $logro_ventas_divisas/($facturas_realizadas+$notas_realizadas) : 0;
                $ventas_divisas_pepsico_fact         = $kpi->get_ventasDivisasPepsicoFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                $ventas_divisas_pepsico_nt           = $kpi->get_ventasDivisasPepsicoNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                $logro_ventas_divisas_pepsico        = floatval($ventas_divisas_pepsico_fact) + floatval($ventas_divisas_pepsico_nt);
                $porcentaje_ventas_divisas_pepsico   = ($logro_ventas_divisas > 0) ? ($logro_ventas_divisas_pepsico / $logro_ventas_divisas) * 100 : 0;
                $ventas_divisas_complementaria_fact  = $kpi->get_ventasDivisasComplementariaFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                $ventas_divisas_complementaria_nt    = $kpi->get_ventasDivisasComplementariaNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                $logro_ventas_divisas_complementaria = floatval($ventas_divisas_complementaria_fact) + floatval($ventas_divisas_complementaria_nt);
                $porcentaje_ventas_divisas_complementaria = ($logro_ventas_divisas > 0) ? ($logro_ventas_divisas_complementaria / $logro_ventas_divisas) * 100 : 0;
                $cobranzasRebajadas = KpiHelpers::totalCobranzasRebajadas($ruta, $fechai2, $fechaf2);

                #llenado de los subtotals
                $subttl_marcas->set_acumKpiMarcas($activacionBultos);
                $subttl_clientes                                 += $clientes;
                $subttl_clientes_activos                         += $clientes_activos;
                $subttl_activacionBultos                         = $subttl_marcas->get_totalKpiMarcas();
                $subttl_clientes_noactivos                       += $clientes_noactivos;
                $subttl_porc_activacion                          = ($subttl_clientes!=0) ? ($subttl_clientes_activos/$subttl_clientes) * 100 : 0;
                $subttl_obj_documentos_mensual                   += $obj_documentos_mensual;
                $subttl_facturas_realizadas                      += $facturas_realizadas;
                $subttl_notas_realizadas                         += $notas_realizadas;
                $subttl_devoluciones_realizadas                  += $devoluciones_realizadas;
                $subttl_montoendivisa_devoluciones               += $montoendivisa_devoluciones;
                $subttl_efec_alcanzada_fecha                     = KpiHelpers::efectividadAlcanzadaAlaFecha($d_trans, $d_habiles, $subttl_obj_documentos_mensual, $subttl_facturas_realizadas, $subttl_notas_realizadas);
                $subttl_objetivo_bulto                           += $objetivo_bulto;
                $subttl_logro_bulto                              += $logro_bulto;
                $subttl_porc_alcanzado_bulto                     = (($subttl_objetivo_bulto!=0) ? ($subttl_logro_bulto/$subttl_objetivo_bulto)*100 : 0) ?? 0;
                $subttl_objetivo_kg                              += $objetivo_kg;
                $subttl_logro_kg                                 += $logro_kg;
                $subttl_porc_alcanzado_kg                        = (($subttl_objetivo_kg!=0) ? ($subttl_logro_kg/$subttl_objetivo_kg)*100 : 0) ?? 0;
                $subttl_objetivo_ventas_divisas                  += $objetivo_ventas_divisas;
                $subttl_logro_ventas_divisas                     += $logro_ventas_divisas;
                $subttl_porc_ventas_divisas                      = (($subttl_objetivo_ventas_divisas!=0) ? ($subttl_logro_ventas_divisas/$subttl_objetivo_ventas_divisas)*100 : 0) ?? 0;
                $subttl_real_dz_dolares                          += $real_dz_dolares;
                $subttl_logro_ventas_divisas_pepsico             += $logro_ventas_divisas_pepsico;
                $subttl_porcentaje_ventas_divisas_pepsico        += $porcentaje_ventas_divisas_pepsico;
                $subttl_logro_ventas_divisas_complementaria      += $logro_ventas_divisas_complementaria;
                $subttl_porcentaje_ventas_divisas_complementaria += $porcentaje_ventas_divisas_complementaria;
                $subttl_cobranzasRebajadas                       += $cobranzasRebajadas;


                $sub_array1 = array(
                    'ruta'            => $ruta,
                    'maestro'         => $clientes,
                    'activos'         => $clientes_activos,
                    'marcas'          => $activacionBultos,
                    'porc_activacion' => Strings::rdecimal($porc_activacion),
                    'por_activar'     => $clientes_noactivos,
                    'visita'          => $frecuenciaVisita,
                    'obj_documentos_mensual'        => Strings::rdecimal($obj_documentos_mensual, 2),
                    'facturas_realizadas'           => $facturas_realizadas,
                    'notas_realizadas'              => $notas_realizadas,
                    'devoluciones_realizadas'       => $devoluciones_realizadas,
                    'montoendivisa_devoluciones'    => Strings::rdecimal($montoendivisa_devoluciones,2),
                    'efec_alcanzada_fecha'          => Strings::rdecimal($efec_alcanzada_fecha, 2),
                    'objetivo_bulto'                => Strings::rdecimal($objetivo_bulto, 2),
                    'logro_bulto'                   => Strings::rdecimal($logro_bulto, 2),
                    'porc_alcanzado_bulto'          => Strings::rdecimal($porc_alcanzado_bulto, 2),
                    'objetivo_kg'                   => Strings::rdecimal($objetivo_kg, 2),
                    'logro_kg'                      => Strings::rdecimal($logro_kg, 2),
                    'porc_alcanzado_kg'             => Strings::rdecimal($porc_alcanzado_kg, 2),
                    'drop_size_divisas'             => Strings::rdecimal($real_dz_dolares, 2),
                    'objetivo_ventas_divisas'       => Strings::rdecimal($objetivo_ventas_divisas, 2),
                    'logro_ventas_divisas'          => Strings::rdecimal($logro_ventas_divisas, 2),
                    'porc_alcanzado_ventas_divisas' => Strings::rdecimal($porc_ventas_divisas, 2),
                    'logro_ventas_divisas_pepsico'                 => Strings::rdecimal($logro_ventas_divisas_pepsico, 2),
                    'porc_alcanzado_ventas_divisas_pepsico'        => Strings::rdecimal($porcentaje_ventas_divisas_pepsico, 2),
                    'logro_ventas_divisas_complementaria'          => Strings::rdecimal($logro_ventas_divisas_complementaria, 2),
                    'porc_alcanzado_ventas_divisas_complementaria' => Strings::rdecimal($porcentaje_ventas_divisas_complementaria, 2),
                    'cobranzas_rebajadas'                          => Strings::rdecimal($cobranzasRebajadas, 2),
                );

                $sub_array['data'][] = $sub_array1;
            }

            #llenado del total general
            $ttl_marcas->set_acumKpiMarcas($subttl_marcas->get_totalKpiMarcas());
            $ttl_clientes                                 += $subttl_clientes;
            $ttl_clientes_activos                         += $subttl_clientes_activos;
            $ttl_activacionBultos                         = $ttl_marcas->get_totalKpiMarcas();
            $ttl_clientes_noactivos                       += $subttl_clientes_noactivos;
            $ttl_porc_activacion                          = ($ttl_clientes!=0) ? ($ttl_clientes_activos/$ttl_clientes) * 100 : 0;;
            $ttl_obj_documentos_mensual                   += $subttl_obj_documentos_mensual;
            $ttl_facturas_realizadas                      += $subttl_facturas_realizadas;
            $ttl_notas_realizadas                         += $subttl_notas_realizadas;
            $ttl_devoluciones_realizadas                  += $subttl_devoluciones_realizadas;
            $ttl_montoendivisa_devoluciones               += $subttl_montoendivisa_devoluciones;
            $ttl_efec_alcanzada_fecha                     = KpiHelpers::efectividadAlcanzadaAlaFecha($d_trans, $d_habiles, $ttl_obj_documentos_mensual, $ttl_facturas_realizadas, $ttl_notas_realizadas);
            $ttl_objetivo_bulto                           += $subttl_objetivo_bulto;
            $ttl_logro_bulto                              += $subttl_logro_bulto;
            $ttl_porc_alcanzado_bulto                     = (($ttl_objetivo_bulto!=0) ? ($ttl_logro_bulto/$ttl_objetivo_bulto)*100 : 0) ?? 0;
            $ttl_objetivo_kg                              += $subttl_objetivo_kg;
            $ttl_logro_kg                                 += $subttl_logro_kg;
            $ttl_porc_alcanzado_kg                        = (($ttl_objetivo_kg!=0) ? ($ttl_logro_kg/$ttl_objetivo_kg)*100 : 0) ?? 0;
            $ttl_objetivo_ventas_divisas                  += $subttl_objetivo_ventas_divisas;
            $ttl_logro_ventas_divisas                     += $subttl_logro_ventas_divisas;
            $ttl_porc_ventas_divisas                      = (($ttl_objetivo_ventas_divisas!=0) ? ($ttl_logro_ventas_divisas/$ttl_objetivo_ventas_divisas)*100 : 0) ?? 0;
            $ttl_real_dz_dolares                          += $subttl_real_dz_dolares;
            $ttl_logro_ventas_divisas_pepsico             += $subttl_logro_ventas_divisas_pepsico;
            $ttl_porcentaje_ventas_divisas_pepsico        += $subttl_porcentaje_ventas_divisas_pepsico/count($vendedores);
            $ttl_logro_ventas_divisas_complementaria      += $subttl_logro_ventas_divisas_complementaria;
            $ttl_porcentaje_ventas_divisas_complementaria += $subttl_porcentaje_ventas_divisas_complementaria/count($vendedores);
            $ttl_cobranzasRebajadas                       += $subttl_cobranzasRebajadas;

            $subtotal = array(
                'ruta'            => "SUBTOTAL",
                'maestro'         => $subttl_clientes,
                'activos'         => $subttl_clientes_activos,
                'marcas'          => $subttl_activacionBultos,
                'porc_activacion' => Strings::rdecimal($subttl_porc_activacion),
                'por_activar'     => $subttl_clientes_noactivos,
                'visita'          => "",
                'obj_documentos_mensual'        => Strings::rdecimal($subttl_obj_documentos_mensual, 2),
                'facturas_realizadas'           => $subttl_facturas_realizadas,
                'notas_realizadas'              => $subttl_notas_realizadas,
                'devoluciones_realizadas'       => $subttl_devoluciones_realizadas,
                'montoendivisa_devoluciones'    => Strings::rdecimal($subttl_montoendivisa_devoluciones,2),
                'efec_alcanzada_fecha'          => Strings::rdecimal($subttl_efec_alcanzada_fecha, 2),
                'objetivo_bulto'                => Strings::rdecimal($subttl_objetivo_bulto, 2),
                'logro_bulto'                   => Strings::rdecimal($subttl_logro_bulto, 2),
                'porc_alcanzado_bulto'          => Strings::rdecimal($subttl_porc_alcanzado_bulto, 2),
                'objetivo_kg'                   => Strings::rdecimal($subttl_objetivo_kg, 2),
                'logro_kg'                      => Strings::rdecimal($subttl_logro_kg, 2),
                'porc_alcanzado_kg'             => Strings::rdecimal($subttl_porc_alcanzado_kg, 2),
                'drop_size_divisas'             => Strings::rdecimal($subttl_real_dz_dolares, 2),
                'objetivo_ventas_divisas'       => Strings::rdecimal($subttl_objetivo_ventas_divisas, 2),
                'logro_ventas_divisas'          => Strings::rdecimal($subttl_logro_ventas_divisas, 2),
                'porc_alcanzado_ventas_divisas' => Strings::rdecimal($subttl_porc_alcanzado_kg, 2),
                'logro_ventas_divisas_pepsico'                 => Strings::rdecimal($subttl_logro_ventas_divisas_pepsico, 2),
                'porc_alcanzado_ventas_divisas_pepsico'        => Strings::rdecimal($subttl_porcentaje_ventas_divisas_pepsico/count($vendedores), 2),
                'logro_ventas_divisas_complementaria'          => Strings::rdecimal($subttl_logro_ventas_divisas_complementaria, 2),
                'porc_alcanzado_ventas_divisas_complementaria' => Strings::rdecimal($subttl_porcentaje_ventas_divisas_complementaria/count($vendedores), 2),
                'cobranzas_rebajadas'                          => Strings::rdecimal($subttl_cobranzasRebajadas, 2),
            );

            $sub_array['subtotal'] = $subtotal;
        }
        $data[] = $sub_array;
    }

    $total_general = array(
        'ruta'            => "TOTAL GENERAL",
        'maestro'         => $ttl_clientes,
        'activos'         => $ttl_clientes_activos,
        'marcas'          => $ttl_activacionBultos,
        'porc_activacion' => Strings::rdecimal($ttl_porc_activacion),
        'por_activar'     => $ttl_clientes_noactivos,
        'visita'          => "",
        'obj_documentos_mensual'        => Strings::rdecimal($ttl_obj_documentos_mensual, 2),
        'facturas_realizadas'           => $ttl_facturas_realizadas,
        'notas_realizadas'              => $ttl_notas_realizadas,
        'devoluciones_realizadas'       => $ttl_devoluciones_realizadas,
        'montoendivisa_devoluciones'    => Strings::rdecimal($ttl_montoendivisa_devoluciones,2),
        'efec_alcanzada_fecha'          => Strings::rdecimal($ttl_efec_alcanzada_fecha, 2),
        'objetivo_bulto'                => Strings::rdecimal($ttl_objetivo_bulto, 2),
        'logro_bulto'                   => Strings::rdecimal($ttl_logro_bulto, 2),
        'porc_alcanzado_bulto'          => Strings::rdecimal($ttl_porc_alcanzado_bulto, 2),
        'objetivo_kg'                   => Strings::rdecimal($ttl_objetivo_kg, 2),
        'logro_kg'                      => Strings::rdecimal($ttl_logro_kg, 2),
        'porc_alcanzado_kg'             => Strings::rdecimal($ttl_porc_alcanzado_kg, 2),
        'drop_size_divisas'             => Strings::rdecimal($ttl_real_dz_dolares, 2),
        'objetivo_ventas_divisas'       => Strings::rdecimal($ttl_objetivo_ventas_divisas, 2),
        'logro_ventas_divisas'          => Strings::rdecimal($ttl_logro_ventas_divisas, 2),
        'porc_alcanzado_ventas_divisas' => Strings::rdecimal($ttl_porc_alcanzado_kg, 2),
        'logro_ventas_divisas_pepsico'                 => Strings::rdecimal($ttl_logro_ventas_divisas_pepsico, 2),
        'porc_alcanzado_ventas_divisas_pepsico'        => Strings::rdecimal($ttl_porcentaje_ventas_divisas_pepsico/count($coordinadores), 2),
        'logro_ventas_divisas_complementaria'          => Strings::rdecimal($ttl_logro_ventas_divisas_complementaria, 2),
        'porc_alcanzado_ventas_divisas_complementaria' => Strings::rdecimal($ttl_porcentaje_ventas_divisas_complementaria/count($coordinadores), 2),
        'cobranzas_rebajadas'                          => Strings::rdecimal($ttl_cobranzasRebajadas, 2),
    );
}

$row = 9;
if (is_array($data)==true and count($data)>0) {
    foreach ($data as $x) {
        $i = 0;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue(getExcelCol($i, true) . $row, 'Coordinador:   ' . strtoupper($x['coordinador']));
        $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(25);
        $spreadsheet->getActiveSheet()->mergeCells(getExcelCol($i, true).$row.':'.getExcelCol($ancho_total-1, true).$row);
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row.':'.getExcelCol($ancho_total-1).$row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));

        $row++;
        foreach ($x['data'] as $item) {
            $i = 0;
            $sheet->setCellValue(getExcelCol($i).$row, $item['ruta']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['maestro']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['activos']);
            foreach ($item['marcas'] as $marca) {
                $sheet->setCellValue(getExcelCol($i).$row, $marca['valor']);
            }
            $sheet->setCellValue(getExcelCol($i).$row, $item['porc_activacion'].' %');
            $sheet->setCellValue(getExcelCol($i).$row, $item['por_activar']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['visita']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['obj_documentos_mensual']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['facturas_realizadas']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['notas_realizadas']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['devoluciones_realizadas']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['montoendivisa_devoluciones']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['efec_alcanzada_fecha'].' %');
            $sheet->setCellValue(getExcelCol($i).$row, $item['objetivo_bulto']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['logro_bulto']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['porc_alcanzado_bulto'].' %');
            $sheet->setCellValue(getExcelCol($i).$row, $item['objetivo_kg']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['logro_kg']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['porc_alcanzado_kg'].' %');
            $sheet->setCellValue(getExcelCol($i).$row, $item['drop_size_divisas']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['objetivo_ventas_divisas']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['logro_ventas_divisas']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['porc_alcanzado_ventas_divisas'].' %');
            $sheet->setCellValue(getExcelCol($i).$row, $item['logro_ventas_divisas_pepsico']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['porc_alcanzado_ventas_divisas_pepsico'].' %');
            $sheet->setCellValue(getExcelCol($i).$row, $item['logro_ventas_divisas_complementaria']);
            $sheet->setCellValue(getExcelCol($i).$row, $item['porc_alcanzado_ventas_divisas_complementaria'].' %');
            $sheet->setCellValue(getExcelCol($i).$row, $item['cobranzas_rebajadas']);


            for ($n=0; $n<$ancho_total; $n++) {
                if (in_array($n, $arr_porc)==true) {
                    $value = intval(str_replace(".", ",",  $item[array_keys($item)[$n-count($lista_marcaskpi)+1]]));
                    switch(true) {
                        case $value > 80:
                            //pinta la celda en verde
                            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'],), 'font' => array('name' => 'Arial', 'bold'  => false, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                            break;
                        case $value > 50 and $value <= 80:
                            //pinta la celda en amarillo
                            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'],), 'font' => array('name' => 'Arial', 'bold'  => false, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                            break;
                        case $value <= 50:
                            //pinta la celda en rojo
                            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'],), 'font' => array('name' => 'Arial', 'bold'  => false, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                            break;
                    }
                } else {
                    //solo lo centra
                    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => false, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                }
            }
            $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(21);

            $row++;
        }

        $i = 0;
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['ruta']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['maestro']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['activos']);
        foreach ($x['subtotal']['marcas'] as $marca) {
            $sheet->setCellValue(getExcelCol($i).$row, $marca['valor']);
        }
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['porc_activacion'].' %');
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['por_activar']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['visita']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['obj_documentos_mensual']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['facturas_realizadas']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['notas_realizadas']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['devoluciones_realizadas']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['montoendivisa_devoluciones']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['efec_alcanzada_fecha'].' %');
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['objetivo_bulto']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['logro_bulto']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['porc_alcanzado_bulto'].' %');
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['objetivo_kg']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['logro_kg']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['porc_alcanzado_kg'].' %');
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['drop_size_divisas']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['objetivo_ventas_divisas']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['logro_ventas_divisas']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['porc_alcanzado_ventas_divisas'].' %');
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['logro_ventas_divisas_pepsico']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['porc_alcanzado_ventas_divisas_pepsico'].' %');
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['logro_ventas_divisas_complementaria']);
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['porc_alcanzado_ventas_divisas_complementaria'].' %');
        $sheet->setCellValue(getExcelCol($i).$row, $x['subtotal']['cobranzas_rebajadas']);

        for ($n=0; $n<$ancho_total; $n++) {
            if (in_array($n, $arr_porc)==true) {
                $value = intval(str_replace(".", ",",  $x['subtotal'][array_keys($item)[$n-count($lista_marcaskpi)+1]]));
                switch(true) {
                    case $value > 80:
                        //pinta la celda en verde
                        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                        break;
                    case $value > 50 and $value <= 80:
                        //pinta la celda en amarillo
                        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                        break;
                    case $value <= 50:
                        //pinta la celda en rojo
                        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                        break;
                }
            } else {
                //centra y pinta de gris claro
                $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80E6E6FA'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
            }
        }
        $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(25);

        $row++;
    }
}

$i = 0;
$spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(25);
$spreadsheet->getActiveSheet()->mergeCells(getExcelCol($i, true).$row.':'.getExcelCol($ancho_total-1, true).$row);
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row.':'.getExcelCol($ancho_total-1).$row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));

$row++;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, $total_general['ruta']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['maestro']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['activos']);
foreach ($total_general['marcas'] as $marca) {
    $sheet->setCellValue(getExcelCol($i).$row, $marca['valor']);
}
$sheet->setCellValue(getExcelCol($i).$row, $total_general['porc_activacion'].' %');
$sheet->setCellValue(getExcelCol($i).$row, $total_general['por_activar']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['visita']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['obj_documentos_mensual']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['facturas_realizadas']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['notas_realizadas']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['devoluciones_realizadas']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['montoendivisa_devoluciones']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['efec_alcanzada_fecha'].' %');
$sheet->setCellValue(getExcelCol($i).$row, $total_general['objetivo_bulto']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['logro_bulto']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['porc_alcanzado_bulto'].' %');
$sheet->setCellValue(getExcelCol($i).$row, $total_general['objetivo_kg']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['logro_kg']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['porc_alcanzado_kg'].' %');
$sheet->setCellValue(getExcelCol($i).$row, $total_general['drop_size_divisas']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['objetivo_ventas_divisas']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['logro_ventas_divisas']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['porc_alcanzado_ventas_divisas'].' %');
$sheet->setCellValue(getExcelCol($i).$row, $total_general['logro_ventas_divisas_pepsico']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['porc_alcanzado_ventas_divisas_pepsico'].' %');
$sheet->setCellValue(getExcelCol($i).$row, $total_general['logro_ventas_divisas_complementaria']);
$sheet->setCellValue(getExcelCol($i).$row, $total_general['porc_alcanzado_ventas_divisas_complementaria'].' %');
$sheet->setCellValue(getExcelCol($i).$row, $total_general['cobranzas_rebajadas']);

for ($n=0; $n<$ancho_total; $n++) {
    if (in_array($n, $arr_porc)==true) {
        $value = intval(str_replace(".", ",",  $total_general[array_keys($item)[$n-count($lista_marcaskpi)+1]]));
        switch(true) {
            case $value > 80:
                //pinta la celda en verde
                $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                break;
            case $value > 50 and $value <= 80:
                //pinta la celda en amarillo
                $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                break;
            case $value <= 50:
                //pinta la celda en rojo
                $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                break;
        }
    } else {
        //centra y pinta de gris claro
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80E6E6FA'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
    }
}
$spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(25);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);

$row+=6;
$sheet->setCellValue('B'.$row, 'ROJO: 0 - 50% ');
$sheet->setCellValue('E'.$row, 'AMARILLO: 51 - 80%');
$sheet->setCellValue('J'.$row,'VERDE: 81 - 100%');
$spreadsheet->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
$spreadsheet->getActiveSheet()->mergeCells('E'.$row.':H'.$row);
$spreadsheet->getActiveSheet()->mergeCells('J'.$row.':M'.$row);
//pinta la celda en verde
$spreadsheet->getActiveSheet()->getStyle('J'.$row.':M'.$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'],), 'font' => array('name' => 'Arial', 'bold'  => false, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
//pinta la celda en amarillo
$spreadsheet->getActiveSheet()->getStyle('E'.$row.':H'.$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'],), 'font' => array('name' => 'Arial', 'bold'  => false, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
//pinta la celda en rojo
$spreadsheet->getActiveSheet()->getStyle('B'.$row.':C'.$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'],), 'font' => array('name' => 'Arial', 'bold'  => false, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
$spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(28);



$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(80);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="kpi_de_'.$fechai.'_al_'.$fechaf.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');