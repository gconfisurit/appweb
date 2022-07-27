<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("sellin_modelo.php");

//INSTANCIAMOS EL MODELO
$sellin = new sellin();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$marca = $_GET['marca'];
$tipo = $_GET['tipo'];

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
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', '', 11);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(40, 10, 'REPORTE DE SELL IN COMPRAS DE ' . date(FORMAT_DATE, strtotime($GLOBALS['fechai'])) . ' AL ' . date(FORMAT_DATE, strtotime($GLOBALS['fechaf'])), 0, 0, 'C');
        // Salto de línea
        $this->Ln(15);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(32), 6, utf8_decode(Strings::titleFromJson('codigo_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(60), 6, utf8_decode(Strings::titleFromJson('descrip_prod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(30), 6, utf8_decode("Compra de Factura"), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(36), 6, utf8_decode("Devolución de Factura"), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(30), 6, utf8_decode("Compra de NE"), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(36), 6, utf8_decode("Devolución de NE"), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(23), 6, utf8_decode(Strings::titleFromJson('total')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(20), 6, utf8_decode(Strings::titleFromJson('marca_prod')), 1, 1, 'C', true);
    }
}

$pdf = new PDF('L');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);

$pdf->SetWidths($width);

$query =  $sellin->getsellin($fechai, $fechaf, $marca ,$tipo);

foreach ($query as $i) {


     if($tipo=='f'){

            $pdf->Row(
                array(
                    $i['coditem'],
                    utf8_encode($i['producto']),
                    number_format(0, 2),
                    number_format(0, 2),
                    number_format($i['compras'], 2),
                    number_format($i['devol'], 2),
                    number_format($i['total'],2),
                    $i['marca']
                )
            );


    }else{

             if($tipo=='n'){

                  $pdf->Row(
                        array(
                            $i['coditem'],
                            utf8_encode($i['producto']),
                            number_format($i['compras'], 2),
                            number_format($i['devol'], 2),
                            number_format(0, 2),
                            number_format(0, 2),
                            number_format($i['total'],2),
                            $i['marca']
                        )
                    );


            }else{

                 if($tipo=='Todos'){

                      $pdf->Row(
                            array(
                                $i['coditem'],
                                utf8_encode($i['producto']),
                                number_format($i['compras'], 2),
                                number_format($i['devol'], 2),
                                number_format($i['compras_notas'], 2),
                                number_format($i['devol_notas'], 2),
                                number_format($i['total'],2),
                                $i['marca']
                            )
                        );

                    
                    }

                }


            }
}
$pdf->Output();


?>