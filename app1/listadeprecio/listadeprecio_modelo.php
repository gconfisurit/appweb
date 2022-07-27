
<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Listadeprecio extends Conectar{

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
        	$sql = "SELECT saprod.CodProd AS codprod, marca, Descrip AS descrip,  EsExento AS esexento, Cubicaje AS cubicaje, SUM(saexis.Existen) AS existen, Precio1 AS precio1, Precio2 AS precio2, Precio3 AS precio3, SUM(saexis.ExUnidad) AS exunidad, PrecioU AS preciou1, PrecioU2 AS preciou2, PrecioU3 AS preciou3 
                        FROM saexis 
                            INNER JOIN saprod ON saexis.codprod = saprod.codprod 
                            LEFT JOIN saprod_01 ON saprod.codprod = saprod_01.codprod 
                    WHERE (saexis.codubic = '01' OR saexis.codubic = '20' OR saexis.codubic = '30') $condicion 
                    GROUP BY SAPROD.CodProd, Precio1, Precio2, Precio3, PrecioU, PrecioU2, PrecioU3, marca, Descrip 
                    ORDER BY saprod.$orden";
        	break;
        	case 2:
        	$sql = "SELECT Saexis.CodProd AS codprod, Saexis.Existen AS existen, EsExento AS esexento, Descrip AS descrip, Marca AS marca, Saexis.ExUnidad AS exunidad, Precio1 AS precio1, Precio2 AS precio2, Precio3 AS precio3, PrecioU2 AS preciou2, PrecioU3 AS preciou3, preciou AS preciou1, Cubicaje AS cubicaje 
                        FROM saexis 
                            INNER JOIN saprod ON saexis.codprod = saprod.codprod 
                            LEFT JOIN saprod_01 ON saprod.codprod = saprod_01.codprod 
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

