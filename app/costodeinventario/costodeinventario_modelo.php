
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class CostodeInventario extends Conectar
{
	public function getCostosdEinventario($edv, $marca)
	{
		//LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
		//CUANDO ES APPWEB ES CONEXION.
		$conectar = parent::conexion2();
		parent::set_names();

		//armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
		$depo = "(" . substr($edv, 0, strlen($edv) - 1) . ")";
		if ($depo != "()") {
			$codubic = "AND saexis.codubic IN " . $depo;
		} else {
			$codubic = "";
		}
		//se considera si la marca seleccionada es todos, o si es una marca en especifico
		if ($marca != "-") {
			$q_marca = "AND marca LIKE ?";
		} else {
			$q_marca = "";
		}

		//QUERY
		$sql = "SELECT saexis.codprod AS codprod, CantEmpaq AS display, Descrip AS descrip, Tara AS tara, Marca AS marca, CostAct AS costo, precio1 AS precio, SUM(saexis.existen) AS bultos,SUM(saexis.exunidad) AS paquetes,
		(SELECT factorp from SACONF where CodSucu = 00000) as factor
                FROM saprod INNER JOIN saexis ON saprod.codprod = saexis.codprod
                WHERE (saexis.existen > 0 OR saexis.exunidad > 0) AND len(marca) > 0 $codubic $q_marca
                GROUP BY saexis.codprod, CantEmpaq, descrip, CostAct, precio1, Marca, tara";

		//PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
        if ($marca != "-") {
            $sql->bindValue(1, $marca);
        }
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
	}
}
