
<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class ClientesSintr extends Conectar
{

	public function getclientessintr($fechai, $fechaf, $vendedor)
	{

		//LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
		//CUANDO ES APPWEB ES CONEXION.
		$conectar = parent::conexion2();
		parent::set_names();

		//QUERY
		if ($vendedor != "") {

		$sql =   "SELECT DISTINCT saclie.codclie, saclie.descrip, saclie.codvend, (SELECT COALESCE(SUM(saldo), 0) FROM saacxc WHERE codclie= saclie.CodClie AND tipocxc='10' AND saldo > 0) AS debe
		FROM saclie INNER JOIN  safact on safact.CodVend= saclie.CodVend
		WHERE activo = 1  and DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) between '$fechai' and '$fechaf' and saclie.CodVend = '$vendedor' ORDER BY codvend";
		
	}else {

			$sql =   "SELECT DISTINCT saclie.codclie, saclie.descrip, saclie.codvend, (SELECT COALESCE(SUM(saldo), 0) FROM saacxc WHERE codclie= saclie.CodClie AND tipocxc='10' AND saldo > 0) AS debe
			FROM saclie INNER JOIN  safact on safact.CodVend= saclie.CodVend
			WHERE activo = 1  and DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) between '$fechai' and '$fechaf' ORDER BY codvend";
		}
		//PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->bindValue(1, $fechai);
		$sql->bindValue(2, $fechaf);
		$sql->bindValue(3, $vendedor);

		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
	}
}
