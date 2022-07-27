<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Activacionclientes extends Conectar{

	public function lista_busca_activacionclientes($fecha_final){

		 //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
		 //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();
		parent::set_names();

 		//QUERY
		$sql= "SELECT saclie.FechaUV AS fechauv, saclie.CodClie AS codclie, saclie.Descrip AS descrip, saclie.ID3 AS id3, saclie.CodVend AS codvend, 
                        (SELECT COALESCE(SUM(saldo), 0) FROM saacxc WHERE codclie= saclie.CodClie AND tipocxc='10' AND saldo>0) AS total 
                FROM saclie WHERE fechauv < ? AND activo > 0 ORDER BY fechauv desc";

		 //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->bindValue(1,$fecha_final);
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

	}
}
