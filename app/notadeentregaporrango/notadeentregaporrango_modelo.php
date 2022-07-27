
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class NotaDeEntregaPorRango extends Conectar {

    public function get_lista_numerod_del_rango($numerod_i, $numerod_f) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT numerod FROM sanota WHERE numerod BETWEEN ? AND ? ";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $numerod_i);
        $sql->bindValue(2, $numerod_f);
        $sql->execute();

        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}

