<?php


class Kpimarca
{
    private $arr;

    /**
     * Kpimarcas constructor.
     * @param $arr
     */
    public function __construct($marcasKpi)
    {
        foreach ($marcasKpi as $marca)
            $this->arr[] = array(
                'marca' => $marca,
                'valor' => 0,
            );

        return $this->arr;
    }


    public function get_totalKpiMarcas ()
    {
        return $this->arr;
    }

    public function set_acumKpiMarcas ($marcasArr)
    {
        $arr_temp = array_map(function ($arr) { return $arr['marca']; }, $this->get_totalKpiMarcas());

        foreach ($marcasArr as $idx => $marca) {
            $pos = array_search($marca['marca'], $arr_temp);
            $this->arr[$pos]['valor'] += $marca['valor'];
        }
    }

}