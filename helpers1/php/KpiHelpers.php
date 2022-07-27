<?php


class KpiHelpers
{
    public static function activacionBultosPorMarcasKpi($ruta, $marcasKpi, $fechai, $fechaf) {
        $temp = array();
        foreach ($marcasKpi as $key => $marca)
            $temp[] = array(
                'marca' => $marca,
                'valor' => count(KpiMarcas::bultosActivadosPorMarca($ruta, $marca, $fechai, $fechaf))
            );

        return $temp;
    }

    public static function frecuenciaVisita($frecuencia) {
        $frecu_ot = (isset($frecuencia['Frecuencia']) and !empty($frecuencia['Frecuencia'])) ? $frecuencia['Frecuencia'] : 2;

        switch ($frecu_ot) {
            case 1:
                $visita = "Mensual";
                break;
            case 2:
                $visita = "Quincenal";
                break;
            case 4:
                $visita = "Semanal";
                break;
            default:
                $visita = "Semanal";
        }

        return $visita;
    }

    public static function objetivoFacturasMasNotasMensual($clientes, $diasHabiles, $frecuencia) {
        $frecu = $diasHabiles / 5;
        $frecu_ot = isset($frecuencia) ? $frecuencia : 2;

        switch ($frecu_ot) {
            case 1:
                $frecu = $frecu * 0.25;
                break;
            case 2:
                $frecu = $frecu * 0.5;
                break;
            case 4:
                $frecu = $frecu * 1;
                break;
        }

        return ($frecu * $clientes);
    }

    public static function efectividadAlcanzadaAlaFecha($dias_trans, $dias_habiles, $obj_documentos_mensual, $facturas_realizadas, $notas_realizadas) {

        $tmp = ( ($dias_trans/$dias_habiles) * $obj_documentos_mensual );

        return ($tmp!=0) ? ( ($facturas_realizadas+$notas_realizadas) / $tmp )*100 : 0;
    }

    public static function obtenerObjetivo($frecuencia, $nombreCampo) {
        $objetivo = (isset($frecuencia[$nombreCampo]) and !empty($frecuencia[$nombreCampo])) ? $frecuencia[$nombreCampo] : 0;

        return $objetivo;
    }

    public static function logroPorTipo($ruta, $fechai, $fechaf, $tipo) {
        # considerar que:
        #       a = factura
        #       b = devolucion factura
        #       c = nota
        #       d = devolucion nota

        $logrado = 0;

        switch ($tipo) {
            case "KG":
                $logro_kg_a = ArraysHelpers::validateWithPosAndParameter(KpiLogro::Kg_fact($ruta, $fechai, $fechaf, 'A'),0,'kg');
                $logro_kg_b = ArraysHelpers::validateWithPosAndParameter(KpiLogro::Kg_fact($ruta, $fechai, $fechaf, 'B'),0,'kg') * (-1);
                $logro_kg_c = ArraysHelpers::validateWithPosAndParameter(KpiLogro::Kg_nota($ruta, $fechai, $fechaf, 'C'),0,'kg');
                $logro_kg_d = ArraysHelpers::validateWithPosAndParameter(KpiLogro::Kg_nota($ruta, $fechai, $fechaf, 'D'),0,'kg') * (-1);

                $logrado = $logro_kg_a + $logro_kg_b + $logro_kg_c + $logro_kg_d;
                break;
            case "UNI":
                $logro_unid_a = ArraysHelpers::validateWithPosAndParameter(KpiLogro::Unid_fact($ruta, $fechai, $fechaf, 'A'),0,'paq');
                $logro_unid_b = ArraysHelpers::validateWithPosAndParameter(KpiLogro::Unid_fact($ruta, $fechai, $fechaf, 'B'),0,'paq') * (-1);
                $logro_unid_c = ArraysHelpers::validateWithPosAndParameter(KpiLogro::Unid_nota($ruta, $fechai, $fechaf, 'C'),0,'paq');
                $logro_unid_d = ArraysHelpers::validateWithPosAndParameter(KpiLogro::Unid_nota($ruta, $fechai, $fechaf, 'D'),0,'paq') * (-1);

                $logrado = $logro_unid_a + $logro_unid_b + $logro_unid_c + $logro_unid_d;
                break;
            case "BUL":
                $logro_bul_a = ArraysHelpers::validateWithPosAndParameter(KpiLogro::Bul_fact($ruta, $fechai, $fechaf, 'A'),0,'bul');
                $logro_bul_b = ArraysHelpers::validateWithPosAndParameter(KpiLogro::Bul_fact($ruta, $fechai, $fechaf, 'B'),0,'bul') * (-1);
                $logro_bul_c = ArraysHelpers::validateWithPosAndParameter(KpiLogro::Bul_nota($ruta, $fechai, $fechaf, 'C'),0,'bul');
                $logro_bul_d = ArraysHelpers::validateWithPosAndParameter(KpiLogro::Bul_nota($ruta, $fechai, $fechaf, 'D'),0,'bul') * (-1);

                $logrado = $logro_bul_a + $logro_bul_b + $logro_bul_c + $logro_bul_d;
                break;
        }

        return ($logrado!=0) ? $logrado : 0;
    }

    public static function totalCobranzasRebajadas($ruta, $fechai, $fechaf) {
        $total = 0;
        $cobranzasRebajadas = Cobranzas::getCobranzasRebajadas($ruta, $fechai, $fechaf);

        if (is_array($cobranzasRebajadas) == true and count($cobranzasRebajadas) > 0)
        {
            foreach ($cobranzasRebajadas as $row) {
                $total += $row['MONTO'];
            }
        }

        return $total;
    }
}