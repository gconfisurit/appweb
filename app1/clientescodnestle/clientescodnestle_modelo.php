
<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class ClientesCodNestle extends Conectar{

	public function getClientes_cnestle($opc,$vendedor){

		 //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
		 //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();
		parent::set_names();

 		//QUERY
		$sql = "";

		if(hash_equals("-", $vendedor)){
			switch ($opc){
				case "1":
				$sql = "SELECT a.CodVend as codvend, a.CodClie as codclie, a.Descrip as descrip, a.FechaE as fecha, a.ID3 as rif, b.DiasVisita as dvisita, b.CodNestle as codnestle, b.Clasificacion as clasificacion FROM saclie as a INNER JOIN saclie_01 as b ON a.codclie = b.codclie ORDER BY a.codvend, a.codclie";
				break;
				case "2":
				$sql = "SELECT a.CodVend as codvend, a.CodClie as codclie, a.Descrip as descrip, a.FechaE as fecha, a.ID3 as rif, b.DiasVisita as dvisita, b.CodNestle as codnestle, b.Clasificacion as clasificacion FROM saclie as a INNER JOIN saclie_01 as b ON a.codclie = b.codclie WHERE (LEN(b.codnestle) > 3) AND codnestle IS NOT NULL ORDER BY a.codvend, a.codclie";
				break;
				case "3":
				$sql = "SELECT a.CodVend as codvend, a.CodClie as codclie, a.Descrip as descrip, a.FechaE as fecha, a.ID3 as rif, b.DiasVisita as dvisita, b.CodNestle as codnestle, b.Clasificacion as clasificacion FROM saclie as a INNER JOIN saclie_01 as b ON a.codclie = b.codclie WHERE (LEN(b.codnestle) <= 3) ORDER BY a.codvend, a.codclie";
				break;
				default:
				echo "error";
			}
		} else {
			switch ($opc){
				case "1":
				$sql = "SELECT a.CodVend as codvend, a.CodClie as codclie, a.Descrip as descrip, a.FechaE as fecha, a.ID3 as rif, b.DiasVisita as dvisita, b.CodNestle as codnestle, b.Clasificacion as clasificacion FROM saclie as a INNER JOIN saclie_01 as b ON a.codclie = b.codclie WHERE a.codvend = ? ORDER BY a.codvend, a.codclie";
				break;
				case "2":
				$sql = "SELECT a.CodVend as codvend, a.CodClie as codclie, a.Descrip as descrip, a.FechaE as fecha, a.ID3 as rif, b.DiasVisita as dvisita, b.CodNestle as codnestle, b.Clasificacion as clasificacion FROM saclie as a INNER JOIN saclie_01 as b ON a.codclie = b.codclie WHERE (LEN(b.codnestle) > 3) AND b.codnestle IS NOT NULL AND a.codvend = ? ORDER BY a.codvend, a.codclie";
				break;
				case "3":
				$sql = "SELECT a.CodVend as codvend, a.CodClie as codclie, a.Descrip as descrip, a.FechaE as fecha, a.ID3 as rif, b.DiasVisita as dvisita, b.CodNestle as codnestle, b.Clasificacion as clasificacion FROM saclie as a INNER JOIN saclie_01 as b ON a.codclie = b.codclie WHERE (LEN(b.codnestle) <= 3)  AND a.codvend = ? ORDER BY a.codvend, a.codclie";
				break;
				default:
				echo "error";
			}
		}

		 //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->bindValue(1,$vendedor);
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

	}

}

