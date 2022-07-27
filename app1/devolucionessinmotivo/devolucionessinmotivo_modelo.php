
<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Devolucionessinmotivo extends Conectar{

    public function getDevolucionesFactura($data) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $parameter = hash_equals('1', $data['tipodespacho'])
            ? ', saclie.fechae as fecha_ini_clie, desp.numerod, ID_Correlativo as correl,notas1, notas2, observacion as motivo'
            : '';

        $relation = hash_equals('1', $data['tipodespacho'])
            ? ' inner join APPWEBAJ.dbo.Despachos_Det desp on desp.numerod = NumeroR '
            : '';

        $condition = hash_equals('0', $data['tipodespacho'])
            ? " AND (numerod NOT IN (SELECT numerod FROM APPWEBAJ.dbo.Despachos_Det) AND NumeroR NOT IN (SELECT numerod FROM APPWEBAJ.dbo.Despachos_Det)) "
            : " AND (observacion IS NULL OR observacion = '') ";

        //QUERY
        $sql = "SELECT safact.codvend AS code_vendedor, safact.tipofac, safact.numerod, numeror, safact.fechae AS fecha_fact,
                       safact.codclie AS cod_clie, safact.descrip AS cliente, monto $parameter
                FROM SAFACT
                        INNER JOIN saclie ON safact.codclie = saclie.codclie
                        $relation
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) BETWEEN ? AND ? AND safact.TipoFac = 'B' $condition
                ORDER BY fecha_fact DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);

        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getDevolucionesNotadeEntrega($data) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $parameter = hash_equals('1', $data['tipodespacho'])
            ? ', saclie.fechae AS fecha_ini_clie, nt.observacion AS motivo, ID_Correlativo, desp.numerod, notas1'
            : '';

        $relation = hash_equals('1', $data['tipodespacho'])
            ? ' INNER JOIN APPWEBAJ.dbo.Despachos_Det AS desp ON desp.numerod = numerof'
            : '';

        $condition = hash_equals('0', $data['tipodespacho'])
            ? " AND (numerod NOT IN (SELECT numerod FROM APPWEBAJ.dbo.Despachos_Det) AND numerof NOT IN (select numerod FROM APPWEBAJ.dbo.Despachos_Det))"
            : " AND (nt.observacion IS NULL or nt.observacion = '')";

        //QUERY
        $sql = "SELECT nt.codvend AS code_vendedor, nt.tipofac, numerof AS numeror, nt.numerod, nt.fechae AS fecha_fact, nt.codclie AS cod_clie,
                       nt.rsocial AS cliente, total AS monto $parameter
                FROM SANOTA nt
                         INNER JOIN saclie ON nt.codclie = saclie.codclie
                         $relation
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, nt.FechaE)) BETWEEN ? AND ? AND nt.TipoFac = 'D' $condition
                ORDER BY fecha_fact DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);

        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getDocumentoEnDespacho($data) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "SELECT numerod FROM Despachos_Det WHERE Numerod = ? or Numerod = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['numeror']);
        $sql->bindValue($i+=1,$data['numerod']);

        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }

    public function editar_motivo($data)
    {
        $i=0;
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE Despachos_Det SET  Observacion=? WHERE Numerod = ? or Numerod = ?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $data["motivo"]);
        $sql->bindValue($i+=1, $data["numerod"]);
        $sql->bindValue($i+=1, $data["numeror"]);

        return $sql->execute();
    }

    public function insertar_motivo($data)
    {
        $i=0;
        $conectar = parent::conexion();
        parent::set_names();

        $numerod = hash_equals('1', $data['op'])
            ? $data['numeror']
            : $data['numerod'];

        $usuario_id = (is_array($_SESSION)==true and count($_SESSION)>0)
            ? $_SESSION['cedula']
            : '';

        $sql = "INSERT INTO Despachos_Det (Numerod, Observacion, Nnotacre, Fecha_Liqui, Fecha_Entre, ID_Usuario, Tipofac) VALUES (?,?,?,?,?,?,?)";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $numerod);
        $sql->bindValue($i+=1, $data["motivo"]);
        $sql->bindValue($i+=1, $data["numerod"]);
        $sql->bindValue($i+=1, date(FORMAT_DATETIME_FOR_INSERT));
        $sql->bindValue($i+=1, date(FORMAT_DATETIME_FOR_INSERT));
        $sql->bindValue($i+=1, $usuario_id);
        $sql->bindValue($i+=1, $data["tipo_nota"]);

        return $sql->execute();
    }


}

