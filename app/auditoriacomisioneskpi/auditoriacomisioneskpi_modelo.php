
<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Auditoriacomisioneskpi extends Conectar{

	public function getauditoriacomisioneskpi($fechai,$fechaf,$vendedor) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion();
		parent::set_names();
		
		//QUERY
		$sql = "";

        if ($vendedor !== ""){
            $sql = "SELECT A.campo, A.antes, A.despu, B.usuario, B.fechah, C.descrip 
						FROM auaj.dbo.cambio_hist_kpi AS A
							INNER JOIN auaj.dbo.hist_cambio_kpi AS B ON codigo = codig
							INNER JOIN aj.dbo.appusuarios AS C ON C.id_usu = B.usuario
						WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, B.fechah)) between '$fechai' and '$fechaf' AND A.antes != A.despu AND ruta = '$vendedor'
						ORDER BY fechah";
        }else{
          $sql = "SELECT A.campo, A.antes, A.despu, B.usuario, B.fechah, C.descrip 
					FROM auaj.dbo.cambio_hist_kpi AS A
						INNER JOIN auaj.dbo.hist_cambio_kpi AS B ON codigo = codig
						INNER JOIN aj.dbo.appusuarios AS C ON C.id_usu = B.usuario
					WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, B.fechah)) between '$fechai' and '$fechaf' and A.antes != A.despu
					ORDER BY fechah";
        }

        /*
		if (!hash_equals("-", $vendedor)) {
			$sql = "SELECT A.campo, A.antes, A.despu, B.usuario, B.fechah, E.nomper 
					FROM auaj.dbo.cambio_hist_comisiones AS A
						INNER JOIN auaj.dbo.hist_cambio_comisiones AS B ON codigo = codig
						INNER JOIN coaj.dbo.periodo AS C ON B.cod_per = C.cod_per
						INNER JOIN coaj.dbo.datos_edv AS D ON C.ci = D.ci
						INNER JOIN APPWEBAJ.dbo.Usuarios AS E ON CAST(E.cedula AS BIGINT) = B.usuario
					WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, B.fechah)) BETWEEN ? AND ? AND A.antes != A.despu AND ruta = ?";
		} else {
			$sql = "SELECT A.campo, A.antes, A.despu, B.usuario, B.fechah, E.nomper 
					FROM auaj.dbo.cambio_hist_comisiones AS A
						INNER JOIN auaj.dbo.hist_cambio_comisiones AS B ON codigo = codig
						INNER JOIN coaj.dbo.periodo AS C ON B.cod_per = C.cod_per
						INNER JOIN coaj.dbo.datos_edv AS D ON C.ci = D.ci
						INNER JOIN APPWEBAJ.dbo.Usuarios AS E ON CAST(E.cedula AS BIGINT) = B.usuario
					WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, B.fechah)) BETWEEN ? AND ? AND A.antes != A.despu";
		}
*/
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->bindValue(1,$fechai);
		$sql->bindValue(2,$fechaf);
		if (!hash_equals("-", $vendedor)) {
			$sql->bindValue(3,$vendedor);
		}
		$sql->execute();
		return $sql->fetchAll(PDO::FETCH_ASSOC);

	}
}
