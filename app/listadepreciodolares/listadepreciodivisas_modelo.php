<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Listadepreciodivisas extends Conectar{

	public function getListadeprecios($marca, $depos, $exis, $orden){

		 //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
		 //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();
		parent::set_names();

 		//QUERY
		 if(hash_equals("-", $marca)) { //todas las marcas
		 	if(hash_equals("-", $depos)) {
		 		$opc = 1;
		 		if(hash_equals("0", $exis)) {
		 			$condicion = " ";
		 		} else {
		 			$condicion = " AND (saexis.existen > 0 OR saexis.exunidad > 0) ";
		 		}
		 	} else {
		 		$opc = 2;
		 		if(hash_equals("0", $exis)) {
		 			$condicion = " ";
		 		} else {
		 			$condicion = " AND (saexis.existen > 0 OR saexis.exunidad > 0) ";
		 		}
		 	}
        } else { //sino, por marca
        	if(hash_equals("-", $depos)) {
        		$opc = 1;
        		if(hash_equals("0", $exis)) {
        			$condicion = " AND marca = '$marca' ";
        		} else {
        			$condicion = " AND (saexis.existen > 0 OR saexis.exunidad > 0) AND marca = '$marca' ";
        		}
        	} else {
        		$opc = 2;
        		if(hash_equals("0", $exis)) {
        			$condicion = " AND marca = '$marca' ";
        		} else {
        			$condicion = " AND (saexis.existen > 0 OR saexis.exunidad > 0) AND marca = '$marca' ";
        		}
        	}
        }

        switch ($opc) {
        	case 1:
        	$sql = "SELECT saprod.CodProd AS codprod, marca, Descrip AS descrip, SUM(saexis.Existen) AS existen, Precio1_P AS precio1, Precio2_P AS precio2, Precio3_P AS precio3, SUM(saexis.ExUnidad) AS exunidad, Precio1_B AS preciou1, Precio2_B AS preciou2, Precio3_B AS preciou3 
                        FROM saexis 
                            INNER JOIN saprod ON saexis.codprod = saprod.codprod 
                            LEFT JOIN saprod_02 ON saprod.codprod = saprod_02.codprod 
                    WHERE (saexis.codubic = '01' OR saexis.codubic = '20' OR saexis.codubic = '30') $condicion 
                    GROUP BY SAPROD.CodProd, Precio1_P, Precio2_P, Precio3_P, Precio1_B, Precio2_B, Precio3_B, marca, Descrip 
                    ORDER BY saprod.$orden";
        	break;
        	case 2:
        	$sql = "SELECT Saexis.CodProd AS codprod, Saexis.Existen AS existen, EsExento AS esexento, Descrip AS descrip, Marca AS marca, Saexis.ExUnidad AS exunidad, Precio1_P AS precio1, Precio2_P AS precio2, Precio3_P AS precio3, Precio2_B AS preciou2, Precio3_B AS preciou3, precio1_B AS preciou1
                        FROM saexis 
                            INNER JOIN saprod ON saexis.codprod = saprod.codprod 
                            LEFT JOIN saprod_02 ON saprod.codprod = saprod_02.codprod 
                    WHERE (saexis.codubic = '$depos') $condicion 
                    ORDER BY saprod.$orden";
        	break;
        }

		 //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}

