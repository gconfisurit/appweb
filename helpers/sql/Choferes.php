<?php


class Choferes extends Conectar {

    public static function todos()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

              //  $sql = "SELECT Cedula, Nomper, Fecha_Registro, Estado FROM choferes WHERE deleted_at IS NULL";
        //$sql= "SELECT id_chofer, cedula as Cedula, descripcion as Nomper, estatus as Estado FROM appChofer";
        $sql= "SELECT * from appchofer where estatus ='1' order by descripcion";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByDni($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

            $sql="SELECT id_chofer, cedula, descripcion , estatus FROM appchofer WHERE cedula=?";
//        $sql= "SELECT descripcion as Nomper,* FROM appChofer WHERE cedula=?";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

     public static function DocumentosporDespachar($fechai,$fechaf)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $sql= "SELECT NumeroD, Descrip, TipoFac from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fechai' and '$fechaf' and SA.TipoFac in ('A','C') and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) and SA.NumeroD not in (SELECT numeros FROM appfacturas_det  where TipoFac='A' or TipoFac='C') and SA.NumeroD not in (SELECT numerof FROM sanota) order by SA.NumeroD";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

       public static function  getCabeceraDespacho($correlativo) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        //QUERY
     
       $sql = "select * from appfacturas where correl = '$correlativo'";

      $result = (new Conectar)->conexion2()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}