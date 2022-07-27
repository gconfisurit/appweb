
<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class ClientesNuevos extends Conectar{

	public function getClientesNuevos($fechai,$fechaf){

		 //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
		 //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();
		parent::set_names();

 		//QUERY
		$sql= "SELECT CodClie AS codclie, Descrip AS descrip, ID3 AS id3, FechaE AS fechae, CodVend AS codvend
		FROM saclie WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? ORDER BY fechae asc";

		 //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->bindValue(1,$fechai);
		$sql->bindValue(2,$fechaf);
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

	}

}

