
<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class ClientesNoActivos extends Conectar{

	public function getClientesNoactivos($edv, $fechai, $fechaf)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();
		parent::set_names();

 		//QUERY

			$sql= "SELECT cli.codclie AS codclie, cli.descrip AS descrip, cli.id3 AS id3, cli.Direc1 AS direc1, cli.Direc2 AS direc2, cli.EsCredito AS escredito, cli.Observa AS observa, cli01.DiasVisita AS diasvisita  from SACLIE AS CLI inner join saclie_01 AS CLI01 ON CLI.codclie = CLI01.codclie WHERE CLI.codclie NOT IN
                (SELECT DISTINCT (SAFACT.CodClie) AS CODCLIE FROM SAFACT WHERE SAFACT.CodVend = ? AND TipoFac in ('A') AND SAFACT.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie
                WHERE ACTIVO = 1 AND (SACLIE.CodVend = ? OR Ruta_Alternativa = ? OR Ruta_Alternativa_2 = ?)) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SAFACT.FechaE)) BETWEEN ? AND ? AND NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac IN ('A') AND x.NumeroR IS NOT NULL AND cast(X.Monto AS BIGINT) = cast((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac IN ('B')) AS BIGINT))
                UNION
                SELECT DISTINCT (SANOTA.CodClie) AS CODCLIE FROM SANOTA WHERE SANOTA.CodVend = ? AND TipoFac IN ('C') AND SANOTA.numerof = '0' AND SANOTA.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie
                WHERE ACTIVO = 1 AND (SACLIE.CodVend = ? OR Ruta_Alternativa = ? OR Ruta_Alternativa_2 = ?)) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SANOTA.FechaE)) BETWEEN ? AND ? AND NumeroD NOT IN (SELECT X.NumeroD FROM SANOTA AS X WHERE X.TipoFac IN ('C') AND x.numerof IS NOT NULL AND cast(X.subtotal AS BIGINT) = cast((SELECT Z.subtotal FROM SANOTA AS Z WHERE Z.NumeroD = x.numerof AND Z.TipoFac IN ('D')) AS BIGINT)))
                AND CLI.activo = 1 AND (CLI.CodVend = ? OR CLI01.Ruta_Alternativa = ? OR CLI01.Ruta_Alternativa_2 = ?) ORDER BY cli.Descrip";

	

		
		 //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->bindValue($i+=1,$edv);
		$sql->bindValue($i+=1,$edv);
		$sql->bindValue($i+=1,$edv);
		$sql->bindValue($i+=1,$edv);
		$sql->bindValue($i+=1,$fechai);
		$sql->bindValue($i+=1,$fechaf);

        $sql->bindValue($i+=1,$edv);
        $sql->bindValue($i+=1,$edv);
        $sql->bindValue($i+=1,$edv);
        $sql->bindValue($i+=1,$edv);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);

		$sql->bindValue($i+=1,$edv);
		$sql->bindValue($i+=1,$edv);
		$sql->bindValue($i+=1,$edv);
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

	}

	public function getClientesNoactivosTODOS($edv)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();
		parent::set_names();

 		//QUERY

			$sql=" SELECT (select count(CodClie) FROM SACLIE INNER JOIN savend on savend.CodVend = SACLIE.CodVend WHERE SACLIE.activo = '1' AND savend.CodVend = '$edv') as cliente_activo , 
			(select count(CodClie) FROM SACLIE INNER JOIN savend on .savend.CodVend = SACLIE.CodVend WHERE SACLIE.activo = '0' AND savend.CodVend = '$edv') as cliente_inactivo FROM savend WHERE activo = '1' ORDER BY CodVend ASC ";

		
		 //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

	}

	/*public function getTotalClientesnoActivos($fechai,$fechaf,$vendedor){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();
		parent::set_names();

        //QUERY
		$sql= "SELECT count(cli.codclie) AS cuenta from SACLIE AS CLI inner join saclie_01 AS CLI01 ON CLI.codclie = CLI01.codclie WHERE CLI.codclie not IN
		(SELECT distinct(SAFACT.CodClie) AS CODCLIE FROM SAFACT WHERE SAFACT.CodVend = ? AND TipoFac = 'A' AND SAFACT.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie
		WHERE ACTIVO = 1 AND (SACLIE.CodVend = ? or SACLIE_01.Ruta_Alternativa = ? OR SACLIE_01.Ruta_Alternativa_2 = ?)) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SAFACT.FechaE)) BETWEEN ? AND ? AND NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND cast(X.Monto AS int) = cast((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS int)))
		AND CLI.activo = 1 AND (CLI.CodVend = ? OR CLI01.Ruta_Alternativa = ? OR CLI01.Ruta_Alternativa_2 = ?)  ";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->bindValue(1,$vendedor);
		$sql->bindValue(2,$vendedor);
		$sql->bindValue(3,$vendedor);
		$sql->bindValue(4,$vendedor);
		$sql->bindValue(5,$fechai);
		$sql->bindValue(6,$fechaf);
		$sql->bindValue(7,$vendedor);
		$sql->bindValue(8,$vendedor);
		$sql->bindValue(9,$vendedor);
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

	}*/
}

