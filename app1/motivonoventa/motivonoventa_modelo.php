
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class MotivoNoVenta extends Conectar{

    public function getMotivoNoVenta($data)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $cond_edv = (!hash_equals("-", $data['edv']))
            ? ' AND A.edv = ? '
            : '';

        //QUERY
        $sql= "SELECT a.fecha, a.edv, a.codclie, b.descrip, a.motivo
                FROM visitas_edv AS a
                    LEFT JOIN SACLIE AS b ON a.CodClie = b.CodClie
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, a.Fecha))
                    BETWEEN ? AND ? $cond_edv
                ORDER BY a.fecha DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $data['fechai']);
        $sql->bindValue($i+=1, $data['fechaf']);
        if (!hash_equals("-", $data['edv']))
            $sql->bindValue($i+=1, $data['edv']);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }
}

