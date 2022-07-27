<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class KpiManager extends Conectar {

    public function get_datos_edv($edv)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $condicion = ($edv!='-') ? 'WHERE U.CodVend = ?' : '';

        //QUERY
        $sql = "SELECT S.Descrip, S.clase, S.Telef, S.activo, U.* FROM savend_02 AS U INNER JOIN savend AS S ON S.CodVend = U.CodVend $condicion";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        if ($edv!='-')
            $sql->bindValue(1, $edv);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_clases_edv()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT DISTINCT clase FROM savend WHERE Clase <> ''";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_objetivos_kpi()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "SELECT id, descripcion FROM Kpi_objetivos";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_clientesPorEdv($edv) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT saclie.codclie AS codclie FROM saclie INNER JOIN saclie_01 ON saclie.codclie = saclie_01.codclie 
                WHERE (codvend = ? OR SACLIE_01.ruta_alternativa = ? OR SACLIE_01.Ruta_Alternativa_2 = ?)  AND activo = '1' ORDER BY codclie";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $edv);
        $sql->bindValue(2, $edv);
        $sql->bindValue(3, $edv);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editar_estado_edv($id, $estado){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar=parent::conexion2();
        parent::set_names();

        $sql="UPDATE savend SET Activo=? WHERE codvend=?";

        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$estado);
        $sql->bindValue(2,$id);
        return $resultado = $sql->execute();
    }

    function get_datos_edv_antesactualizar($edv) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT OBJCAPTAR, OBJESPECIAL, OBJLOGESPEC, OBJVENTASBS, OBJVENTASUN, OBJVENTASKG, OBJVENTASBU FROM savend_02 WHERE codvend = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $edv);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizar_edv($values){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar=parent::conexion2();
        parent::set_names();

        $sql="UPDATE savend SET CLASE = ?, Descrip = ? WHERE codvend = ?";

        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $values['clase']);
        $sql->bindValue(2, $values['nombre']);
        $sql->bindValue(3, $values['ruta']);
        return $resultado = $sql->execute();
    }

    public function actualizar_edv_02($values) {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar=parent::conexion2();
        parent::set_names();

        $sql="UPDATE savend_02 SET 
                APENOM = ?, CEDULA = ?, coordinador = ?, FRECUENCIA = ?, AVA = ?, AVA_FOTOS = ?,
                OBJCAPTAR = ?, OBJESPECIAL = ?, OBJLOGESPEC = ?, OBJVENTASBS = ?, Tiempo_Estimado_Despacho = ?,
                UBICACION = ?, OBJVENTASUN = ?, OBJVENTASKG = ?, OBJVENTASBU = ?, Requerido_Bult_Und = ?
                WHERE codvend = ?";

        $sql=$conectar->prepare($sql);
        $sql->bindValue($i+=1, $values['nombre']);
        $sql->bindValue($i+=1, '');
        $sql->bindValue($i+=1, $values['supervisor']);
        $sql->bindValue($i+=1, $values['frecuencia']);
        $sql->bindValue($i+=1, $values['obj_ava']);
        $sql->bindValue($i+=1, $values['fotos_ava']);
        $sql->bindValue($i+=1, '');
        $sql->bindValue($i+=1, $values['obj_especial']);
        $sql->bindValue($i+=1, $values['logro_obj_especial']);
        $sql->bindValue($i+=1, $values['obj_ventas_divisas']);
        $sql->bindValue($i+=1, $values['tiempo_est_despacho']);
        $sql->bindValue($i+=1, $values['ubicacion']);
        $sql->bindValue($i+=1, $values['obj_ventas_und']);
        $sql->bindValue($i+=1, $values['obj_ventas_kg']);
        $sql->bindValue($i+=1, $values['obj_ventas_bul']);
        $sql->bindValue($i+=1, $values['objetivo_kpi']);
        $sql->bindValue($i+=1, $values['ruta']);
        return $resultado = $sql->execute();
    }

    public function insertar_historico_cambio_kpi($idusuario, $edv) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "INSERT INTO auaj.dbo.hist_cambio_kpi (usuario, fechah, ruta) VALUES (?, ?, ?)";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$idusuario);
        $sql->bindValue(2,date(FORMAT_DATETIME));
        $sql->bindValue(3,$edv);
        $resultado = $sql->execute();

        return $resultado ? $conectar->lastInsertId() : -1;
    }

    public function insertar_cambio_historico_kpi($codigo, $campo, $antes, $despues) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "INSERT INTO auaj.dbo.cambio_hist_kpi (codig, campo, antes, despu) VALUES (?, ?, ?, ?)";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$codigo);
        $sql->bindValue(2,$campo);
        $sql->bindValue(3,$antes);
        $sql->bindValue(4,$despues);
        return $sql->execute();
    }

}