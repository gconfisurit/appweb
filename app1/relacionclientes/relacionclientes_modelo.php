
<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class RelacionClientes extends Conectar
{

    public function get_todos_los_clientes()
    {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT saclie.codclie, saclie.descrip, saclie.id3, saclie.direc1 AS direccion, saclie.telef AS telefono, saclie.movil AS movil, saclie.diascred AS diascredito, saclie.limitecred AS limitecredito, saclie.descto AS descuento, saclie.CodVend AS edv, saclie.fechae AS fechae, limitecred, 
                    tipoid3 AS idtid3, saclie.Activo AS idactivo,
                    (SELECT COALESCE(SUM(saldo), 0) FROM saacxc WHERE saacxc.codclie=saclie.codclie AND tipocxc='10' and saacxc.saldo>0) 
                    AS saldo, 
                    (SELECT count (numerod) FROM saacxc WHERE saacxc.codclie=saclie.codclie AND tipocxc='10') 
                    AS facturas
                    FROM saclie 
                    WHERE saclie.activo ='1' /*ORDER BY saclie.codclie ASC*/";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cliente_por_id($codclie)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT COUNT(codclie) AS cont FROM [APPWEBAJ].dbo.Saclie_Ext WHERE codclie = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codclie);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);

        //QUERY_1
        if ($result[0]['cont'] != "0") {
            $sql1 = "SELECT cli.codclie AS codigo, cli.descrip AS descrip, cli.id3 AS id3, cli.clase AS clase, cli.represent AS represent, cli.direc1 AS direc1, cli.direc2 AS direc2, cli.estado AS idestado, cli.ciudad AS idciudad, cli.telef AS telef, cli.codzona AS idzona, cli.codvend AS idvend, cli.tipocli AS idtcli, cli.tipopvp AS idtpvp, cli.escredito AS credito, cli.limitecred AS lcred, cli.diascred AS dcred, cli.estoleran AS toleran, cli.diastole AS dtoleran, cli.descto AS descto, cli.activo AS idactivo, cli.movil AS movil, cli.Email AS email, cli.tipoid3 AS idtid3, (SELECT descrip FROM saestado WHERE estado=cli.estado) AS estado, (SELECT descrip FROM saciudad WHERE ciudad=cli.ciudad) AS ciudad, (SELECT descrip FROM sazona WHERE codzona=cli.codzona) AS zona, (SELECT descrip FROM savend WHERE codvend=cli.codvend) AS vend, 
                    (SELECT COALESCE(SUM(saldo), 0) FROM saacxc WHERE saacxc.codclie=cli.codclie AND tipocxc='10' and saacxc.saldo>0) 
                    AS saldo,
                    cli2.Dia_Visita AS dvisitas, cli2.codnestle AS idnestle, cli2.ruc AS ruc, cli2.Municipio AS municipio, (SELECT descripcion FROM sanestle WHERE codnestle=cli2.codnestle) AS nestle, cli2.Latitud AS latitud, cli2.Longitud AS longitud, cli2.Observacion AS observa
                    FROM saclie AS cli, [APPWEBAJ].dbo.Saclie_Ext AS cli2
                    WHERE cli.CodClie = ? AND cli.CodClie=cli2.CodClie";
        } else {
            $sql1 = "SELECT cli.codclie AS codigo, cli.descrip AS descrip, cli.id3 AS id3, cli.clase AS clase, cli.represent AS represent, cli.direc1 AS direc1, cli.direc2 AS direc2, cli.estado AS idestado, cli.ciudad AS idciudad, cli.telef AS telef, cli.codzona AS idzona, cli.codvend AS idvend, cli.tipocli AS idtcli, cli.tipopvp AS idtpvp, cli.escredito AS credito, cli.limitecred AS lcred, cli.diascred AS dcred, cli.estoleran AS toleran, cli.diastole AS dtoleran, cli.descto AS descto, cli.activo AS idactivo, cli.movil AS movil, cli.Email AS email, cli.tipoid3 AS idtid3, (SELECT descrip FROM saestado WHERE estado=cli.estado) AS estado, (SELECT descrip FROM saciudad WHERE ciudad=cli.ciudad) AS ciudad, (SELECT descrip FROM sazona WHERE codzona=cli.codzona) AS zona, (SELECT descrip FROM savend WHERE codvend=cli.codvend) AS vend, 
                    (SELECT COALESCE(SUM(saldo), 0) FROM saacxc WHERE saacxc.codclie=cli.codclie AND tipocxc='10' and saacxc.saldo>0) 
                    AS saldo
                    FROM saclie AS cli 
                    WHERE cli.CodClie = ?";
        }

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql1 = $conectar->prepare($sql1);
        $sql1->bindValue(1, $codclie, PDO::PARAM_STR);
        $sql1->execute();
        return $result1 = $sql1->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cliente_por_codigo_o_rif($codclie, $id3)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT * FROM saclie WHERE CodClie = ? OR ID3 = ?";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $codclie, PDO::PARAM_STR);
        $sql->bindValue(2, $id3, PDO::PARAM_STR);
        $sql->execute();

        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cliente_Ext_por_codigo($codclie)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT * FROM Saclie_Ext WHERE codclie= ?";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $codclie, PDO::PARAM_STR);
        $sql->execute();

        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar_cliente($tipo_cliente, $codclie, $descrip, $descorder, $id3, $clase, $represent, $direc1, $direc2, $pais, $estado, $ciudad, $email, $telef, $movil, $activo, $codzona, $codvend, $tipocli, $tipopvp, $escredito, $limitecred, $diascred, $estoleran, $diastole, $fecha_creacion, $descto)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "INSERT INTO SACLIE (tipoid3, codclie, descrip, descorder, id3, clase, represent, direc1, direc2, pais, estado, ciudad, Email, telef, movil, activo, codzona, codvend, tipocli, tipopvp, escredito, limitecred, diascred, estoleran, diastole, fechae, descto) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $tipo_cliente);
        $sql->bindValue(2, $codclie);
        $sql->bindValue(3, $descrip);
        $sql->bindValue(4, $descorder);
        $sql->bindValue(5, $id3);
        $sql->bindValue(6, $clase);
        $sql->bindValue(7, $represent);
        $sql->bindValue(8, $direc1);
        $sql->bindValue(9, $direc2);
        $sql->bindValue(10, $pais);
        $sql->bindValue(11, $estado);
        $sql->bindValue(12, $ciudad);
        $sql->bindValue(13, $email);
        $sql->bindValue(14, $telef);
        $sql->bindValue(15, $movil);
        $sql->bindValue(16, $activo);
        $sql->bindValue(17, $codzona);
        $sql->bindValue(18, $codvend);
        $sql->bindValue(19, $tipocli);
        $sql->bindValue(20, $tipopvp);
        $sql->bindValue(21, $escredito);
        $sql->bindValue(22, $limitecred);
        $sql->bindValue(23, $diascred);
        $sql->bindValue(24, $estoleran);
        $sql->bindValue(25, $diastole);
        $sql->bindValue(26, $fecha_creacion);
        $sql->bindValue(27, $descto);

        return $sql->execute();
    }

    public function registrar_cliente_ext($codclie, $municipio, $diasvisita, $ruc, $latitud, $longitud, $codnestle, $observacion)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO Saclie_Ext (codclie, Municipio, Dia_Visita, ruc, latitud, longitud, codnestle, Clasificacion, Observacion) 
                VALUES (?,?,?,?,?,?,?,(SELECT descripcion FROM [AJ].dbo.SANESTLE WHERE codnestle = ?),?)";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $codclie);
        $sql->bindValue(2, $municipio);
        $sql->bindValue(3, $diasvisita);
        $sql->bindValue(4, $ruc);
        $sql->bindValue(5, $latitud);
        $sql->bindValue(6, $longitud);
        $sql->bindValue(7, $codnestle);
        $sql->bindValue(8, $codnestle);
        $sql->bindValue(9, $observacion);

        return $sql->execute();
    }

    public function actualizar_cliente($codclie, $descrip, $descorder, $id3, $clase, $represent, $direc1, $direc2, $pais, $estado, $ciudad, $email, $telef, $movil, $activo, $codzona, $codvend, $tipocli, $tipopvp, $escredito, $limitecred, $diascred, $estoleran, $diastole, $descto)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "UPDATE saclie SET descrip = ?, descorder = ?, id3 = ?, clase = ?, represent = ?, direc1 = ?, direc2 = ?, Pais = ?, estado = ?, ciudad = ?, Email = ?, telef = ?, movil = ?, activo = ?, codzona = ?, codvend = ?, tipocli = ?, tipopvp = ?, escredito = ?, limitecred = ?, diascred = ?, estoleran = ?, diastole = ?, descto = ? 
                WHERE codclie = ?";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $descrip);
        $sql->bindValue(2, $descorder);
        $sql->bindValue(3, $id3);
        $sql->bindValue(4, $clase);
        $sql->bindValue(5, $represent);
        $sql->bindValue(6, $direc1);
        $sql->bindValue(7, $direc2);
        $sql->bindValue(8, $pais);
        $sql->bindValue(9, $estado);
        $sql->bindValue(10, $ciudad);
        $sql->bindValue(11, $email);
        $sql->bindValue(12, $telef);
        $sql->bindValue(13, $movil);
        $sql->bindValue(14, $activo);
        $sql->bindValue(15, $codzona);
        $sql->bindValue(16, $codvend);
        $sql->bindValue(17, $tipocli);
        $sql->bindValue(18, $tipopvp);
        $sql->bindValue(19, $escredito);
        $sql->bindValue(20, $limitecred);
        $sql->bindValue(21, $diascred);
        $sql->bindValue(22, $estoleran);
        $sql->bindValue(23, $diastole);
        $sql->bindValue(24, $descto);
        $sql->bindValue(25, $codclie);

        return $sql->execute();
    }

    public function actualizar_cliente_ext($codclie, $municipio, $diasvisita, $ruc, $latitud, $longitud, $codnestle, $observacion)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE Saclie_Ext SET Municipio = ?,Dia_Visita = ?, ruc = ?, latitud = ?, longitud = ?, codnestle = ?, Clasificacion = (SELECT descripcion FROM [AJ].dbo.SANESTLE WHERE codnestle = ?), Observacion = ?
                WHERE codclie= ?    ";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $municipio);
        $sql->bindValue(2, $diasvisita);
        $sql->bindValue(3, $ruc);
        $sql->bindValue(4, $latitud);
        $sql->bindValue(5, $longitud);
        $sql->bindValue(6, $codnestle);
        $sql->bindValue(7, $codnestle);
        $sql->bindValue(8, $observacion);
        $sql->bindValue(9, $codclie);

        return $sql->execute();
    }

    public function editar_estado($id,$estado){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar=parent::conexion2();
        parent::set_names();

        $sql="UPDATE saclie SET Activo=? WHERE CodClie=?";

        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$estado);
        $sql->bindValue(2,$id);
        return $resultado = $sql->execute();
    }

    public function get_estados()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT estado, descrip FROM saestado WHERE pais = 1 ORDER BY descrip ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getCanales(){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $sql= "SELECT DISTINCT Clase FROM SAVEND WHERE Clase IS NOT NULL AND LEN(Clase) > 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    public function get_ciudades_por_estado($estado)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT ciudad, descrip FROM saciudad WHERE estado = ? AND pais = '1' ORDER BY descrip ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $estado, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_zona()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT codzona, descrip FROM sazona WHERE codzona != 'codzona'  ORDER BY descrip ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_Edv()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT codvend, descrip FROM savend ORDER BY descrip ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_Cnestle()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT codnestle, descripcion as descrip FROM sanestle where codnestle != 'codnestle'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_existe_factura_pendiente($codclie)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT count(*) AS cuenta FROM saacxc WHERE codclie= ? AND tipocxc='10' AND saldo>0";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codclie, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ultima_venta($codclie)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT TOP 1 CodClie, codusua, numerod, tipofac,  fechae, MtoTotal FROM safact WHERE CodClie= ? AND TipoFac ='A' ORDER BY FechaE DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codclie, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ultimo_pago($codclie)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT TOP 1 CodClie, codusua, numerod, tipocxc,  fechae, monto  FROM SAACXC WHERE CodClie= ? AND Tipocxc ='41' ORDER BY FechaE DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codclie, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cxc_por_codclie($codclie)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT codclie, numerod, fechae, codvend, codusua, codvend,  DATEDIFF(DD, fechae, CONVERT( date ,GETDATE())) AS DiasTransHoy, saldo,
                    (SELECT TOP(1) TipoFac FROM safact s WHERE s.numerod = numerod) AS tipofac
                FROM saacxc WHERE codclie= ? AND  tipocxc='10' AND saldo>0";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codclie, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_factura_cliente_por_id($codclie, $numerod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT numerod, fechae, codusua, codvend, saldo, 
                (SELECT Descrip FROM saclie WHERE saclie.CodClie = saacxc.CodClie) 
                AS descrip
                FROM saacxc WHERE codclie = ? AND  tipocxc='10' AND NumeroD = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codclie, PDO::PARAM_STR);
        $sql->bindValue(2, $numerod, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_detalle_factura_por_id($numerod, $tipofact)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT tipofac, numerod, nrolinea, coditem, codvend, descrip1, cantidad, totalitem, esunid,
                   (CASE WHEN saitemfac.esunid = 0 THEN
                             COALESCE((SELECT saprod.tara FROM saprod WHERE codprod = saitemfac.coditem), 0) * saitemfac.cantidad
                         ELSE
                             COALESCE((SELECT (saprod.tara / saprod.cantempaq) FROM saprod WHERE codprod = saitemfac.coditem), 0) * saitemfac.cantidad
                    END) AS peso
                FROM saitemfac WHERE numerod = ? AND tipofac = ? ORDER BY nrolinea";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $numerod, PDO::PARAM_STR);
        $sql->bindValue(2, $tipofact, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_totales_factura_por_id($numerod, $tipofact)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT safact.NumeroD, safact.TipoFac, safact.codvend AS vendedor, safact.codclie AS codcliente, safact.descrip AS cliente, safact.fechae AS fechaemi,safact.Monto AS subtotal,safact.Descto1 AS descuento, safact.texento AS exento, safact.tgravable AS base, safact.MtoTax AS impuesto, SATAXVTA.MtoTax AS iva, mtototal AS total, codusua 
                FROM safact 
                INNER JOIN saclie ON safact.codclie = saclie.codclie 
                INNER JOIN sataxvta ON sataxvta.numerod = safact.NumeroD 
                WHERE safact.numerod = ? AND safact.tipofac = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $numerod, PDO::PARAM_STR);
        $sql->bindValue(2, $tipofact, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}