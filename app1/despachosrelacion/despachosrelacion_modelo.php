<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class DespachosRelacion extends Conectar{

    public function getRelacionDespachos() {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql= "select TOP 200 * from appfacturas inner join appusuarios on appfacturas.id_usu = appusuarios.id_usu order by correl desc";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarfactura($corr) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql= "SELECT count(numeros) as contador from appfacturas_det where correl = '$corr'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getchofer($cedula) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql= "SELECT descripcion from appChofer where cedula = '$cedula'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

      public function getVehiculo($placa) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql= "select * from appVehiculo where placa = '$placa'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_despacho_por_correlativo($correlativo){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql= "SELECT * from appfacturas inner join appusuarios on appfacturas.id_usu = appusuarios.id_usu where correl = ? order by correl";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $correlativo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }

    public function get_detalle_despacho_por_correlativo($correlativo) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT * from appfacturas where correl = '$correlativo' ";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    public function get_documentos_por_correlativo($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT * FROM appfacturas_det where correl = '$correlativo'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    public function get_detalle_clientes_por_correlativo($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT * FROM SACLIE inner join safact on safact.CodClie=SACLIE.CodClie where NumeroD = '$correlativo'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


        public function fecha_despacho($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT * FROM appfacturas where correl = '$correlativo'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_productos_devueltos_de_un_despacho($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT DISTINCT CodItem, Descrip,

                    COALESCE((SELECT SUM(Cantidad) FROM SAITEMFAC WHERE CodItem = SAPROD.CodProd AND
                    EsUnid = 0 AND TipoFac = 'B' AND OTipo = 'A' AND (ONumero IN ( SELECT Numerod FROM [APPWEBAJ].dbo.Despachos_Det WHERE ID_Correlativo = ? ))), 0)
                    AS BULTOS,
                    COALESCE((SELECT SUM(Cantidad) FROM SAITEMFAC WHERE CodItem = SAPROD.CodProd AND
                    EsUnid = 1 AND TipoFac = 'B' AND OTipo = 'A' AND (ONumero IN ( SELECT Numerod FROM [APPWEBAJ].dbo.Despachos_Det WHERE ID_Correlativo = ? ))), 0)
                    AS PAQUETES,
                    CantEmpaq,
                    EsEmpaque,
                    saprod.Tara AS tara,
                    CodInst
                    
                    FROM SAITEMFAC INNER JOIN SAPROD ON SAITEMFAC.CodItem = SAPROD.CodProd WHERE
                    TipoFac = 'B' AND OTipo = 'A' AND (ONumero IN ( SELECT Numerod FROM [APPWEBAJ].dbo.Despachos_Det WHERE ID_Correlativo = ? )) ORDER BY SAITEMFAC.CodItem";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->bindValue(2,$correlativo);
        $sql->bindValue(3,$correlativo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_facturas_devueltas_de_un_despacho($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT DISTINCT ONumero
				FROM SAITEMFAC INNER JOIN SAPROD ON SAITEMFAC.CodItem = SAPROD.CodProd 
				WHERE TipoFac = 'B' AND OTipo = 'A'  AND (ONumero IN ( SELECT Numerod FROM [APPWEBAJ].dbo.Despachos_Det WHERE ID_Correlativo = ? )) 
				ORDER BY SAITEMFAC.ONumero";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}