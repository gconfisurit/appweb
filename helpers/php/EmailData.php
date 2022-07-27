<?php

class EmailData
{
    public static function DataRecoverPassword($emailUser, $securityRandomNumber) {
        return array(
            'title'      => "Código de Seguridad para Recuperar Contraseña",
            'body'       => "<p>El codigo de Seguridad es: <b>$securityRandomNumber</b> </p>",
            'recipients' => array($emailUser), // puede ser mas de un destinatario
        );
    }

    public static function DataErrorConexion($data) {
        $usuario = strtoupper($data['usuario']);
        $mensaje = utf8_decode($data['mensaje']);

        return array(
            'title'      => "SE HA GENERADO UN ERROR DE CONEXION",
            'body'       => "<strong>$usuario</strong>, HA TENIDO UN ERROR DE CONEXION.</p> <br>
                             <p>$mensaje</p>",
            'recipients' => array(
                /*'dvilla@gconfisur.com',
                'rpenaloza@gconfisur.com',
                'jcaraballo@gconfisur.com',
                'cjimenez@gconfisur.com',
                'ctrujillo@gconfisur.com',*/
                'llopez@gconfisur.com',
                'jfranco@gconfisur.com',
                'ajcastillo@gconfisur.com',
                'ycenteno@gconfisur.com',
                'it@gconfisur.com'
            ), // puede ser mas de un destinatario
        );
    }

    public static function DataCreacionDeDespacho($data) {
        $usuario = strtoupper($data['usuario']);
        $correl_despacho = str_pad($data['correl_despacho'], 8, 0, STR_PAD_LEFT);
        $vehiculo = $data['vehiculo'];
        $destino = $data['destino'];
        $chofer = $data['chofer'];
        $fechad = $data['fechad'];

        return array(
            'title'      => "SE HA CREADO UN NUEVO DESPACHO NRO: $correl_despacho",
            'body'       => "<strong>$usuario</strong>, HA CREADO EL DESPACHO NRO: <strong>$correl_despacho</strong>.</p> <br>
                             <p>
                                 <strong>CON LA SIGUIENTE INFORMACION:</strong>  <br>
                                 PARA SER DESPACHADO FECHA: $fechad <br>
                                 DESTINO: $destino <br>
                                 CHOFER: $chofer <br>
                                 VEHICULO: $vehiculo
                             </p>",
            'recipients' => array(
                /*'dvilla@gconfisur.com',
                'rpenaloza@gconfisur.com',
                'jcaraballo@gconfisur.com',
                'cjimenez@gconfisur.com',
                'ctrujillo@gconfisur.com',*/
                'llopez@gconfisur.com',
                'jfranco@gconfisur.com',
                'ycenteno@gconfisur.com',
                'it@gconfisur.com'
            ),
        );
    }

    public static function DataDespachoAgregarDocumento($data) {
        $usuario = strtoupper($data['usuario']);
        $correl_despacho = str_pad($data['correl_despacho'], 8, 0, STR_PAD_LEFT);
        $correl_numero_pla = str_pad($data['nroplanilla'], 8, 0, STR_PAD_LEFT);
        $destino = $data['destino'];
        $chofer = $data['chofer'];
        $documento = $data['doc'];

        return array(
            'title'      => "SE HA AGREGADO UN DOCUMENTO AL DESPACHO NRO: $correl_despacho",
            'body'       => "<strong>$usuario</strong>, HA AGREGADO EL DOCUMENTO NRO: <strong>$documento</strong>, AL DESPACHO NRO: <strong>$correl_despacho</strong>, CON DESTINO AH: $destino, A CARGO DEL CHOFER: $chofer. </p>
                             ESTE DESPACHO YA SE ENCONTRABA RELACIONADO, POR ENDE ES NECESARIO QUE SE REIMPRIMA LA PLANILLA DE RELACION DE CHOFERES NRO: $correl_numero_pla.</b> </p> 
                             </p>",
            'recipients' => array(
                /*'dvilla@gconfisur.com',
                'rpenaloza@gconfisur.com',
                'jcaraballo@gconfisur.com',
                'cjimenez@gconfisur.com',
                'ctrujillo@gconfisur.com',*/
                'llopez@gconfisur.com',
                'jfranco@gconfisur.com',
                'ycenteno@gconfisur.com',
                'it@gconfisur.com'
            ),
        );
    }

    public static function DataDespachoEditarDocumento($data) {
        $usuario = strtoupper($data['usuario']);
        $correl_despacho = str_pad($data['correl_despacho'], 8, 0, STR_PAD_LEFT);
        $correl_numero_pla = str_pad($data['nroplanilla'], 8, 0, STR_PAD_LEFT);
        $destino = $data['destino'];
        $chofer = $data['chofer'];
        $documento_viejo = $data['doc_viejo'];
        $documento_nuevo = $data['doc_nuevo'];

        return array(
            'title'      => "SE HA EDITADO UNA FACTURA EN EL DESPACHO NRO: $correl_despacho",
            'body'       => "<strong>$usuario</strong>, HA EDITADO EL NUMERO DE FACTURA: $documento_viejo, POR LA FACTURA NRO: $documento_nuevo 
                             EN EL DESPACHO NRO: <strong>$correl_despacho</strong>, CON DESTINO AH: $destino, A CARGO DEL CHOFER: $chofer </p>
                             ESTE DESPACHO YA SE ENCONTRABA RELACIONADO, POR ENDE ES NECESARIO QUE SE REIMPRIMA LA PLANILLA DE RELACION DE CHOFERES NRO: $correl_numero_pla. </p>
                             YA QUE ESTA PLANILLA HA SIDO AFECTADA POR DICHA EDICION DE FACTURA</b> </p>
                             </p>",
            'recipients' => array(
                /*'dvilla@gconfisur.com',
                'rpenaloza@gconfisur.com',
                'jcaraballo@gconfisur.com',
                'cjimenez@gconfisur.com',
                'ctrujillo@gconfisur.com',*/
                'llopez@gconfisur.com',
                'jfranco@gconfisur.com',
                'ycenteno@gconfisur.com',
                'it@gconfisur.com'
            ),
        );
    }

    public static function DataEditarChoferesyDestinoDeDespacho($data) {
        $usuario = strtoupper($data['usuario']);
        $correl_despacho = str_pad($data['correl_despacho'], 8, 0, STR_PAD_LEFT);
        $fechad_ant = $data['fechad_ant'];
        $destino_ant = $data['destino_ant'];
        $chofer_ant = $data['chofer_ant'];
        $vehiculo_ant = $data['vehiculo_ant'];
        $fechad = $data['fechad'];
        $destino = $data['destino'];
        $chofer = $data['chofer'];
        $vehiculo = $data['vehiculo'];

        return array(
            'title'      => "MODIFICACION DE CHOFERES Y DESTINOS EN EL DESPACHO NRO $correl_despacho",
            'body'       => "<strong>$usuario</strong>, HA REALIZADO UNA MODIFICACION EN EL DESPACHO NRO: <strong>$correl_despacho</strong>.</p> <br>
                             <p>
                                 <strong>LA SIGUIENTE INFORMACION:</strong>  <br>
                                 FECHA DESPACHO: $fechad_ant <br>
                                 DESTINO: $destino_ant <br>
                                 CHOFER: $chofer_ant <br>
                                 VEHICULO: $vehiculo_ant
                             </p>
                             <p>
                                <strong>FUE MODIFICADO A:</strong> <br>
                                 FECHA DESPACHO: $fechad <br>
                                 DESTINO: $destino <br>
                                 CHOFER: $chofer <br>
                                 VEHICULO: $vehiculo<br>
                             </p>",
            'recipients' => array(
                /*'dvilla@gconfisur.com',
                'rpenaloza@gconfisur.com',
                'jcaraballo@gconfisur.com',
                'cjimenez@gconfisur.com',
                'ctrujillo@gconfisur.com',*/
                'llopez@gconfisur.com',
                'jfranco@gconfisur.com',
                'ycenteno@gconfisur.com',
                'it@gconfisur.com'
            ),
        );
    }

    public static function DataEliminarDocumentoDespacho($data) {
        $usuario = strtoupper($data['usuario']);
        $correl_despacho = str_pad($data['correl_despacho'], 8, 0, STR_PAD_LEFT);
        $correl_numero_pla = str_pad($data['nroplanilla'], 8, 0, STR_PAD_LEFT);
        $documento = $data['doc'];

        return array(
            'title'      => "SE HA ELIMINADO UNA DOCUMENTO DEL DESPACHO NRO: $correl_despacho",
            'body'       => "<strong>$usuario</strong>, HA ELIMINADO LA FACTURA NRO: <strong>$documento</strong>, PERTENECIENTE AL DESPACHO NRO: <strong>$correl_despacho</strong></p> <br>
                             ESTE DESPACHO YA SE ENCONTRABA RELACIONADO, POR ENDE ES NECESARIO QUE SE REIMPRIMA LA PLANILLA DE RELACION DE CHOFERES NRO: $correl_numero_pla.  
                             YA QUE ESTA PLANILLA HA SIDO AFECTADA POR LA ELIMINACION DE DICHA FACTURA</p>",
            'recipients' => array(
                /*'dvilla@gconfisur.com',
                'rpenaloza@gconfisur.com',
                'jcaraballo@gconfisur.com',
                'cjimenez@gconfisur.com',
                'ctrujillo@gconfisur.com',*/
                'llopez@gconfisur.com',
                'jfranco@gconfisur.com',
                'ycenteno@gconfisur.com',
                'it@gconfisur.com'
            ), // puede ser mas de un destinatario
        );
    }

    public static function DataEliminarDespacho($data) {
        $usuario = strtoupper($data['usuario']);
        $correl_despacho = str_pad($data['correl_despacho'], 8, 0, STR_PAD_LEFT);
        $correl_numero_pla = str_pad($data['nroplanilla'], 8, 0, STR_PAD_LEFT);

        return array(
            'title'      => "SE HA ELIMINADO EL DESPACHO NRO $correl_despacho",
            'body'       => "<strong>$usuario</strong>, HA ELIMINADO EL DESPACHO NRO: <strong>$correl_despacho</strong></p> <br>
                             POR ENDE TAMBIEN SE HA ELIMINADO LA PLANILLA DE RELACION DE CHOFERES NRO: $correl_numero_pla.</p>",
            'recipients' => array(
                /*'dvilla@gconfisur.com',
                'rpenaloza@gconfisur.com',
                'jcaraballo@gconfisur.com',
                'cjimenez@gconfisur.com',
                'ctrujillo@gconfisur.com',*/
                'llopez@gconfisur.com',
                'jfranco@gconfisur.com',
                'ycenteno@gconfisur.com',
                'it@gconfisur.com'
            ), // puede ser mas de un destinatario
        );
    }
}