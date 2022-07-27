<?php

require (PATH_VENDOR.'autoload.php');
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Excel
{
    private int $i = 0;

    public function getCol($num, $letra_temp = false) {
        $numero = $num % 26;
        $letra = chr(65 + $numero);
        $num2 = intval($num / 26);
        if(!$letra_temp)
            $this->i = $this->i +1;

        if ($num2 > 0) {
            return getExcelCol($num2 - 1, true) . $letra;
        } else {
            return $letra;
        }
    }

    public function reset() {
        $this->i = 0;
    }

    public static function styleHeadTable()
    {
        return array(
            'font' => array(
                'name' => 'Arial',
                'bold'  => true,
                'color' => array('rgb' => '000000')
            ),
            'fill' => array(
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'c8dcff'],
            ),
            'borders' => array(
                'top' => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'right' => ['borderStyle' => Border::BORDER_MEDIUM],
            ),
            'alignment' => array(
                'horizontal'=> Alignment::HORIZONTAL_CENTER,
                'vertical'  => Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            )
        );
    }

}