
<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Clientestodos extends Conectar{


	public function getClientesTODOS($edv)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
		$conectar= parent::conexion2();
		parent::set_names();

 		//QUERY

			$sql=" SELECT (select count(CodClie) FROM SACLIE INNER JOIN savend on savend.CodVend = SACLIE.CodVend WHERE SACLIE.activo = '1' AND savend.CodVend = '$edv') as cliente_activo , 
			(select count(CodClie) FROM SACLIE INNER JOIN savend on .savend.CodVend = SACLIE.CodVend WHERE SACLIE.activo = '0' AND savend.CodVend = '$edv') as cliente_inactivo FROM savend WHERE savend.CodVend = '$edv' ORDER BY CodVend ASC ";

		
		 //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
		$sql = $conectar->prepare($sql);
		$sql->execute();
		return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

	}


}

