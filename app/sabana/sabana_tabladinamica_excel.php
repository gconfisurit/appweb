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
require_once("sabana_tabladinamica_modelo.php");

//INSTANCIAMOS EL MODELO
$tabladinamica = new Tabladinamica();

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

$data = array(
    'fechai' => $_GET['fechai'],
    'fechaf' => $_GET['fechaf'],
    'marca'  => $_GET['marca'],
    'edv'    => $_GET['vendedor'],
);

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

$temp='';
switch ($_GET['t']) {
    case 'f': $temp = Strings::titleFromJson('factura'); break;
    case 'n': $temp = Strings::titleFromJson('nota_de_entrega'); break;
}
$spreadsheet->getActiveSheet()->getStyle('A2:F2')->getFont()->setSize(18);
$sheet->setCellValue('A2', 'Sabana Tabla dinámica ('.$temp.')');
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

$sheet->setCellValue('L4', 'EDV:');
$sheet->setCellValue('M4', (!hash_equals('-', $_GET['vendedor'])) ? $_GET['vendedor'] : 'Todos');
$spreadsheet->getActiveSheet()->getStyle('L4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('M4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$sheet->setCellValue('O4', 'Marca:');
$sheet->setCellValue('P4', (!hash_equals('-', $_GET['marca'])) ? $_GET['marca'] : 'Todas');
$spreadsheet->getActiveSheet()->getStyle('O4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('P4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));


foreach(range('A','Y') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}


$row = 8;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('#'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('codvend'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('descrip_vend'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('clase'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('tipo_transaccion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numero_operacion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('codclie'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('razon_social'));
$sheet->setCellValue(getExcelCol($i).$row, "Tipo de Cliente");
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('clasificacion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('codigo_prod'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('descrip_prod'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('marca_prod'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('cantidad'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('unidad'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('paquetes'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('bultos'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('peso'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('instancia'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('monto_dolars'));
$sheet->setCellValue(getExcelCol($i).$row, "Costo $");
$sheet->setCellValue(getExcelCol($i).$row, "TotalCosto $");
$sheet->setCellValue(getExcelCol($i).$row, "Rentabilidad bruta $");
$sheet->setCellValue(getExcelCol($i).$row, "Rentabilidad bruta %");
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('descuento'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('tasa'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('monto_bs'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('fecha'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('mes'));

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++)
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE),'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
$spreadsheet->getActiveSheet()->getStyle( 'A'.$row.':'.getExcelCol($aux, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'c8dcff'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),));




$datos = array();
switch ($_GET['t']) {
    case 'f': $datos = $tabladinamica->getTabladinamicaFactura($data); break;
    case 'n': $datos = $tabladinamica->getTabladinamicaNotaDeEntrega($data); break;
}

//DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
$arr_data = Array();

 $costo_total= $renta_bruta=$costod= $renta= $paqt = $bult = $kilo = $total = 0;

if (is_array($datos)==true and count($datos)>0)
{
    foreach ($datos as $key => $row)
    {
        //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

         $montod = $montobs = $descuento = 0;

        $multiplicador = in_array($row['tipo'], array('A','C'))
            ? 1
            : -1;

        switch ($_GET['t']) {
            case 'f':
                $montod = (is_numeric($row["factor"]) and $row["factor"]>0)
                    ? Numbers::avoidNull($row["montod"]) / $row["factor"]
                    : 0;

                $montobs = $row['montod'];

                $descuento = (is_numeric($row["factor"]) and $row["factor"]>0)
                    ? Numbers::avoidNull($row["descuento"]) / $row["factor"]
                    : 0;
                break;
            case 'n':
                $montod = Numbers::avoidNull($row["montod"]);

                $montobs = (is_numeric($row["factor"]) and $row["factor"]>0)
                    ? Numbers::avoidNull($row["montod"]) * $row["factor"]
                    : 0;

                $descuento = Numbers::avoidNull($row["descuento"]);
                break;
        }

         $data_costo = $tabladinamica->getCostosdEinventario($row["coditem"]);
                $costo= $factor=$bulto=$paquete=0;
                foreach ($data_costo as $pot){

                        if ($pot['display'] == 0) {
                             $cdisplay = 0;
                        } else {
                            $cdisplay = $pot['costo'] / $pot['display'];
                        }

                        $costo=$pot['costo'];
                        $factor=$pot['factor'];
                        $bulto=$pot['bultos'];
                        $paquete=$pot['paquetes'];

                }

        $sub_array['num']  = $key+1;
        $sub_array['codvend']       = $row["codvend"];
        $sub_array['vendedor']      = $row["vendedor"];
        $sub_array['clasevend']     = $row["clasevend"];
        $sub_array['tipo']          = $row["tipo"];
        $sub_array['numerod']       = $row["numerod"];
        $sub_array['codclie']       = $row["codclie"];
        $sub_array['cliente']       = utf8_encode($row["cliente"]);
        $sub_array['codnestle']     = Strings::avoidNull($row["codnestle"]);
        $sub_array['clasificacion'] = Strings::avoidNull($row["clasificacion"]);
        $sub_array['coditem']       = $row["coditem"];
        $sub_array['descripcion']   = utf8_encode($row["descripcion"]);
        $sub_array['marca']         = $row["marca"];
        $sub_array['cantidad']      = $row["cantidad"] * $multiplicador;
        $sub_array['unid']          = $row["unid"];
        $sub_array['paq']           = number_format($row["paq"] * $multiplicador, 1);
        $sub_array['bul']           = number_format($row["bul"] * $multiplicador, 1);
        $sub_array['kg']            = number_format($row["kg"] * $multiplicador, 1);
        $sub_array['instancia']     = $row["instancia"];
        $sub_array['montod']        =  number_format($montod  * $multiplicador, 2);


                if($row["unid"]=='BULT'){
                    if($factor == 0 or $row["tipo"]=='D'){
                            $sub_array['costod'] =0;
                            $sub_array['total_costod'] =0;
                            $sub_array['renta_bruta']  =  0;
                            $sub_array['rentabilidad']  =0;
                    }else{
                        $sub_array['costod'] = number_format(($costo /$factor ),2); 
                        $sub_array['total_costod'] = number_format(($costo /$factor)*$row["cantidad"],2);
                        $sub_array['renta_bruta']  =  number_format((($montod  * $multiplicador) - (($costo /$factor)*$row["cantidad"])),2); 
                        $sub_array['rentabilidad']  =   number_format(((($montod  * $multiplicador)-($descuento  * $multiplicador))/(($costo /$factor)*$row["cantidad"])-1)*100,2);
                    }
                        
                }else{
                    if($row["unid"]=='PAQ'){
                        if($factor == 0 or $row["tipo"]=='D'){
                            $sub_array['costod'] =0;
                            $sub_array['total_costod'] =0;
                            $sub_array['renta_bruta']  =  0;
                            $sub_array['rentabilidad']  =0;
                        }else{
                        $sub_array['costod'] =  number_format(($cdisplay /$factor),2);
                        $sub_array['total_costod'] =  number_format(($cdisplay /$factor)*$row["cantidad"],2);
                        $sub_array['renta_bruta']  = number_format((($montod  * $multiplicador) - (($cdisplay /$factor)*$row["cantidad"])),2); 
                        $sub_array['rentabilidad']  =   number_format(((($montod  * $multiplicador)-($descuento  * $multiplicador))/(($cdisplay /$factor)*$row["cantidad"])-1)*100,2);
                        }
                    }
                }

        $sub_array['descuento']     =  number_format($descuento  * $multiplicador, 2);
        $sub_array['factor']        =  number_format($row['factor'], 2);
        $sub_array['montobs']       =  number_format($montobs * $multiplicador, 2);
        $sub_array['fechae']        = date(FORMAT_DATE, strtotime($row["fechae"]));
        $sub_array['mes']           =  utf8_encode($row['MES']);

        $paqt  += $row["paq"] * $multiplicador;
        $bult  += $row["bul"] * $multiplicador;
        $kilo  += $row["kg"]  * $multiplicador;
        $total += $montod * $multiplicador;

        if($row["unid"]=='BULT'){
                    if($factor == 0 or $row["tipo"]=='D'){
                            $costod +=0;
                            $costo_total+=0;
                            $renta_bruta +=0;
                            $renta +=   0;
                        }else{
                        $costod += (($costo /$factor ));
                        $costo_total+=(($costo /$factor )*$row["cantidad"]); 
                        $renta_bruta +=(($montod  * $multiplicador) - (($costo /$factor)*$row["cantidad"]));
                        $renta += ((($montod  * $multiplicador)-($descuento  * $multiplicador))/(($costo /$factor)*$row["cantidad"])-1)*100 ; 
                        }
                }else{
                    if($row["unid"]=='PAQ' ){
                        if($factor == 0 or $row["tipo"]=='D'){
                            $costod +=0;
                            $costo_total+=0;
                            $renta_bruta +=0;
                            $renta +=   0;
                        }else{
                          $costod += (($cdisplay /$factor));
                          $costo_total+=(($cdisplay /$factor )*$row["cantidad"]); 
                          $renta_bruta +=(($montod  * $multiplicador) - (($cdisplay /$factor)*$row["cantidad"]));
                          $renta += ((($montod  * $multiplicador)-($descuento  * $multiplicador))/(($cdisplay /$factor)*$row["cantidad"])-1)*100 ;
                        }
                    }
                }

                $renta +=   0;

        $arr_data[] = $sub_array;
    }
}

$total = (hash_equals('n', $_GET['t']))
    ? Numbers::avoidNull($tabladinamica->getTotalNotaDeEntrega($data,'C')[0]['montod']) - Numbers::avoidNull($tabladinamica->getTotalNotaDeEntrega($data, 'D')[0]['montod'])
    : $total;

$totales_tabladinamica = array(
    "paqt"  => number_format($paqt, 2),
    "bult"  => number_format($bult, 2),
    "kilo"  => number_format($kilo, 2),
    "costodto"  => number_format($costod,2),
    "costod_total"  => number_format($costo_total,2),
    "renta_bruta"  => number_format($renta_bruta, 2),
    "renta"  => number_format($renta/count($datos), 2),
    "total" => number_format($total, 2),
);

switch ($_GET['t']) {
    case 'f': $resumen = $tabladinamica->getResumenFactura($data); break;
    case 'n': $resumen = $tabladinamica->getResumenNotaDeEntrega($data); break;
}

//DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
$arr_data1 = Array();

if (is_array($resumen)==true and count($resumen)>0)
{
    foreach ($resumen as $key => $row)
    {
        //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        $descuentototal = $descuentototalbs = 0;

        switch ($_GET['t']) {
            case 'f':
                $descuentototal =  ($row["descto1"] > 0 & $row["descto2"] > 0)
                    ? ($row["descto1"] + $row["descto2"]) / $row["tasa"]
                    :  $row["descto1"] / $row["tasa"];

                $descuentototalbs =  ($row["descto1"] > 0 & $row["descto2"] > 0)
                    ? $row["descto1"] + $row["descto2"]
                    : $row["descto1"];
                break;
            case 'n':
                $descuentototal   = $row["descuento"];

                $descuentototalbs = $row["descuento"] * $row["tasa"];
                break;
        }

        $sub_array['num']              = $key+1;
        $sub_array['codvend']          = $row["codvend"];
        $sub_array['codclie']          = $row["codclie"];
        $sub_array['descrip']          = $row["descrip"];
        $sub_array['descuentototal']   = number_format($descuentototal, 2);
        $sub_array['tasa']             = number_format($row["tasa"], 2);
        $sub_array['descuentototalbs'] = number_format($descuentototalbs, 2);
        $sub_array['numerod']          = utf8_decode($row["numerod"]);
        $sub_array['tipofac']          = $row["tipofac"];
        $sub_array['fechae']           = date(FORMAT_DATE, strtotime($row["fechae"]));

        $arr_data1[] = $sub_array;
    }
}




$row = 9;
if (is_array($arr_data)==true and count($arr_data)>0) {
    foreach ($arr_data as $x) {
        $i = 0;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue(getExcelCol($i) . $row, $x['num']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['codvend']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['vendedor']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['clasevend']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['tipo']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['numerod']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['codclie']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['cliente']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['codnestle']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['clasificacion']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['coditem']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['descripcion']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['marca']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['cantidad']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['unid']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['paq']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['bul']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['kg']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['instancia']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['montod']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['costod']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['total_costod']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['renta_bruta']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['rentabilidad']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['descuento']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['factor']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['montobs']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['fechae']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['mes']);

        $i = 0;
        /** centrarlas las celdas **/
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
         $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

        $row++;
    }
}

$i = 0;
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue(getExcelCol($i) . $row,'Totales');
$sheet->setCellValue(getExcelCol($i+=14) . $row, $totales_tabladinamica['paqt']);
$sheet->setCellValue(getExcelCol($i) . $row, $totales_tabladinamica['bult']);
$sheet->setCellValue(getExcelCol($i) . $row, $totales_tabladinamica['kilo']);
$sheet->setCellValue(getExcelCol($i) . $row, '');
$sheet->setCellValue(getExcelCol($i) . $row, $totales_tabladinamica['total']);
$sheet->setCellValue(getExcelCol($i) . $row, $totales_tabladinamica['costodto']);
$sheet->setCellValue(getExcelCol($i) . $row, $totales_tabladinamica['costod_total']);
$sheet->setCellValue(getExcelCol($i) . $row, $totales_tabladinamica['renta_bruta']);
$sheet->setCellValue(getExcelCol($i) . $row, $totales_tabladinamica['renta']);
$sheet->setCellValue(getExcelCol($i) . $row, '');
$sheet->setCellValue(getExcelCol($i) . $row, '');
$sheet->setCellValue(getExcelCol($i) . $row, '');
$sheet->setCellValue(getExcelCol($i) . $row, '');
$spreadsheet->getActiveSheet()->mergeCells('A'.$row.':O'.$row);
$i = 0;
/** centrarlas las celdas **/
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i+=14) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));





$row += 5;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('ruta'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('codclie'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('razon_social'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('descuento_dolars'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('tasa'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('monto_bs'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('descuento'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('tipo'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('fecha'));

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++)
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE),'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
$spreadsheet->getActiveSheet()->getStyle( 'A'.$row.':'.getExcelCol($aux, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'c8dcff'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),));

$row += 1;
if (is_array($arr_data1)==true and count($arr_data1)>0) {
    foreach ($arr_data1 as $x) {
        $i = 0;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue(getExcelCol($i) . $row, $x['codvend']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['codclie']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['descrip']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['descuentototal']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['tasa']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['descuentototalbs']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['numerod']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['tipofac']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['fechae']);

        $i = 0;
        /** centrarlas las celdas **/
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

        $row++;
    }
}

$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(80);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Sabana_tabladinamica_de_'.date(FORMAT_DATE, strtotime($_GET['fechai'])).'_al_'.date(FORMAT_DATE, strtotime($_GET['fechaf'])).'_'.$_GET['marca'].'_'.$_GET['vendedor'].'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');