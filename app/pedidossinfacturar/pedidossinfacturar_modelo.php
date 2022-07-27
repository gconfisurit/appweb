
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Pedidossinfacturar extends Conectar {

    public function getPedidos($data)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $marca = (!hash_equals('-', $data['marca'])) ? " AND c.marca = ?" : "";

        //QUERY
        $sql = "SELECT c.Marca AS marca, a.CodItem AS coditem, a.Descrip1 AS producto, a.Cantidad AS cantidad, a.TotalItem AS total, a.fechae AS fechae, a.CodVend AS ruta, b.Descrip AS cliente, a.EsUnid AS unidad
                FROM SAITEMFAC AS a
                    INNER JOIN SAFACT AS b ON a.numerod = b.NumeroD
                    INNER JOIN saprod AS c ON a.coditem = c.CodProd
                WHERE a.TipoFac='e' AND a.OTipo ='f' AND DATEADD(dd, 0, DATEDIFF(dd, 0, a.FechaE))
                    BETWEEN ? AND ? $marca ORDER BY a.FechaE, a.coditem DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);
        if(!hash_equals("-", $data['marca']))
            $sql->bindValue($i+=1,$data['marca']);

        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }
}

