
<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class ReporteCompras extends Conectar
{
    public function get_codprod_por_marca($almcen_principal, $marca) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $addQuery = (!hash_equals("-", $marca))
            ? "AND prod.marca = ?"
            : "";

        $sql = "SELECT prod.Codprod AS codprod 
                FROM SAPROD AS prod 
                    INNER JOIN saexis AS exis ON prod.CodProd = exis.CodProd
                WHERE (activo = '1' AND exis.codubic = ? $addQuery)
                GROUP BY prod.CodProd";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $almcen_principal);
        if (!hash_equals("-", $marca)) {
            $sql->bindValue($i+=1, $marca);
        }
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_datos_producto($codprod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT prod.codprod, prod.descrip, prod.CantEmpaq displaybultos, 
                       prod.fechauc, prod.precio1, prod.CostAct costoactual
                FROM SAPROD AS prod WHERE prod.CodProd = ?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codprod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_costos($codprod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT TOP(1) item.FechaE, prod2.costo, prod.CantEmpaq,
                     COALESCE(prod2.costo/NULLIF(CantEmpaq,0), 0) costodisplay, (prod2.costo) costobultos
                FROM SAPROD AS prod
                    INNER JOIN SAPROD_02 AS prod2 ON prod2.CodProd = prod.CodProd
                    INNER JOIN SAITEMCOM AS item ON prod.CodProd = item.CodItem
                WHERE item.coditem = ? AND item.TipoCom IN ('H','J') ORDER BY FechaE DESC";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codprod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ultimas_compras($codprod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT TOP(2) item.fechae, item.NumeroD, SUM(item.Cantidad) cantBult
                FROM SACOMP AS comp
                    INNER JOIN SAITEMCOM AS item ON comp.NumeroD = item.NumeroD
                    INNER JOIN SAPROD AS prod ON item.CodItem = prod.CodProd
                WHERE item.TipoCom IN ('H','J') AND prod.CodProd = ? AND comp.numeroN IS NULL
                GROUP BY item.NumeroD ,item.FechaE
                ORDER BY item.FechaE DESC";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codprod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ventas_mes_anterior($codprod, $fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT fechae, COALESCE((CASE WHEN EsUnid=1 THEN cantidad/CantEmpaq ELSE Cantidad END), 0) AS cantidadBult
                FROM SAITEMFAC INNER JOIN SAPROD prod ON prod.CodProd = saitemfac.CodItem
                WHERE CodItem = ? AND DATEADD(dd, 0, DATEDIFF(dd, 0, fechaE)) between ? and ? AND TipoFac = 'A'

                UNION

                SELECT fechae, COALESCE((CASE WHEN esunidad=1 THEN cantidad/CantEmpaq ELSE Cantidad END), 0) AS cantidadBult
                FROM SAITEMNOTA INNER JOIN SAPROD prod ON prod.CodProd = saitemnota.CodItem
                WHERE CodItem = ? AND DATEADD(dd, 0, DATEDIFF(dd, 0, fechaE)) between ? and ? AND TipoFac = 'C'";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $codprod);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        $sql->bindValue($i+=1, $codprod);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_bultos_existentes($almacen_principal, $codprod)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT exis.Existen + COALESCE(exis.ExUnidad / NULLIF(prod.cantempaq, 0), 0) as  bultosexis
                FROM SAEXIS exis INNER JOIN SAPROD prod ON prod.CodProd = exis.CodProd
                WHERE CodUbic = ? AND exis.CodProd = ?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $almacen_principal);
        $sql->bindValue($i+=1, $codprod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_productos_no_vendidos($codprod, $fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT COALESCE(SUM((CASE WHEN EsUnid=1 THEN item.Cantidad/CantEmpaq ELSE Cantidad END)), 0) AS cantidadBult
                FROM SAFACT as fact
                    INNER JOIN SAITEMFAC item ON fact.NumeroD = item.NumeroD
                    INNER JOIN SAPROD prod ON prod.CodProd = item.CodItem
                WHERE (fact.NumeroD = item.NumeroD AND fact.TipoFac = item.TipoFac)
                  AND (SUBSTRING(CONVERT(VARCHAR,fact.FechaE,120),1,10) >= ? AND SUBSTRING(CONVERT(VARCHAR,fact.FechaE,120),1,10) <= ?)
                  AND CodItem = ? AND item.NroLineaC = 0 AND item.TipoFac = 'F' AND fact.Monto <> 0";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $codprod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}