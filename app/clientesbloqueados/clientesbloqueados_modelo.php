
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Clientesbloqueados extends Conectar{

	public function ClientesBloqueadosPorVendedor($vendedor){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();

        //QUERY
		$sql= "SELECT cli.codclie as codclie, cli.descrip as descrip, cli.id3 as id3, cli.Direc1 AS direc1, cli.Direc2 AS direc2, cli.EsCredito AS escredito, cli.Observa AS observa, cli01.DiasVisita AS diasvisita  FROM SACLIE AS CLI INNER JOIN saclie_01 AS CLI01 ON CLI.codclie = CLI01.codclie WHERE CLI.codclie IN
		(SELECT DISTINCT(SAFACT.CodClie) AS CODCLIE FROM SAFACT WHERE SAFACT.CodVend = ? AND TipoFac = 'A' AND SAFACT.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie
		WHERE ACTIVO = 1 AND (SACLIE.CodVend = ? OR SACLIE_01.Ruta_Alternativa = ? OR SACLIE_01.Ruta_Alternativa_2 = ?)) AND NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS INT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B')AS INT)))
		AND CLI.activo = 1 AND (CLI.CodVend = ? OR CLI01.Ruta_Alternativa = ? OR CLI01.Ruta_Alternativa_2 = ?) AND escredito=0 ORDER BY cli.Descrip";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->bindValue(1,$vendedor);
		$sql->bindValue(2,$vendedor);
		$sql->bindValue(3,$vendedor);
		$sql->bindValue(4,$vendedor);
		$sql->bindValue(5,$vendedor);
		$sql->bindValue(6,$vendedor);
		$sql->bindValue(7,$vendedor);
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

	}

	public function getTotalClientesPorCodigo($codvend){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();

        //QUERY
		$sql= "SELECT count(codclie) AS cuenta FROM saclie WHERE codvend = ? AND activo = 1";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->bindValue(1,$codvend);
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

	}
}
