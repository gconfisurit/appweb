
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class NotaDeEntrega extends Conectar {

    public function get_datos_cliente($codclie)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT direc2, represent FROM saclie WHERE codclie = ? ";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codclie);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_descuento($numerod, $tipo)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT sum(descuento) AS descuento FROM saitemnota WHERE numerod = ? AND tipofac = ? ";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $numerod);
        $sql->bindValue(2, $tipo);
        $sql->execute();

        return $sql->fetch(PDO::FETCH_ASSOC);
    }

}

