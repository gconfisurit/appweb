<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("auth_modelo.php");

//INSTANCIAMOS EL MODELO
$auth = new Auth();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "login_in":
        $login = $_POST["login"];
        $clave = md5($_POST["clave"]);

        if (!empty($login) and !empty($clave))
        {
            $resultado = $auth->login($login, $clave);

            if (is_array($resultado) and count($resultado) > 0)
            {
                include_once (PATH_HELPERS_PHP . "php/Session.php");
                Session::create($resultado);

                /*IMPORTANTE: la session guarda los valores de los campos de la tabla de la bd*/
                $output = array(
                    'status'  => true,
                    'message' => 'ok',
                    'data'    => $resultado
                );

            } else {
                $output = array(
                    'status'  => false,
                    'message' => 'El correo y/o password es incorrecto o no tienes permiso!',
                    'data'    => array()
                );
            }
        } else {
            $output = array(
                'status'  => false,
                'message' => 'Los campos estan vacios.',
                'data'    => array()
            );
        }

        echo json_encode($output);
        break;

    case 'generate_code_recovery_user':

        $user = $_POST["user"];

        if (!empty($user)) {
            $datosUser = Usuarios::byUserName($user);

            if (is_array($datosUser) and count($datosUser) > 0)
            {
                $status_send = $insert_recover = false;

                //extrae los codigos de seguridad para evitar que se repitan
                $except_codes = array_map(function($arr) { return $arr['codigo_recuperacion']; }, RecoverUser::all());

                //genera un codigo de seguridad
                $securityRandomNumber = Numbers::generateRandomNumber(4, $except_codes);

                //obtinene los datos para enviar el correo
                $dataEmail = EmailData::DataRecoverPassword($datosUser['email'], $securityRandomNumber);

                //enviar correo
                $status_send = Email::send_email(
                    $dataEmail['title'],
                    $dataEmail['body'],
                    $dataEmail['recipients'],
                );

                //almacenar en la bd
                $insert_recover = (count(RecoverUser::getByUser($datosUser['login']))==0)
                    ? RecoverUser::insert($datosUser['login'], $securityRandomNumber)
                    : RecoverUser::update($datosUser['login'], $securityRandomNumber);

                if ($status_send and $insert_recover) {
                    $output = array(
                        'status'  => true,
                        'message' => 'ok'
                    );
                } else {
                    $output = array(
                        'status'  => false,
                        'message' => 'Error al Generar el Código de recuperacion !.'
                    );
                }

            } else {
                $output = array(
                    'status'  => false,
                    'message' => 'El usuario no existe !'
                );
            }
        } else {
            $output = array(
                'status'  => false,
                'message' => 'campo vacios.'
            );
        }

        echo json_encode($output);
        break;

    case 'validate_code_recovery_user':

        $user = $_POST["user"];
        $code = $_POST["code"];

        if (!empty($user) or !empty($code)) {
           $recovery_code_in_bd = RecoverUser::getByUser($user);

           if (is_array($recovery_code_in_bd)==true and count($recovery_code_in_bd)>0)
           {
               if (hash_equals($code, $recovery_code_in_bd[0]['codigo_recuperacion']))
               {
                   $output = array(
                       'status'  => true,
                       'message' => 'ok'
                   );

               } else {
                   $output = array(
                       'status'  => false,
                       'message' => 'Código de seguridad inválido !.'
                   );
               }

           } else {
               $output = array(
                   'status'  => false,
                   'message' => 'Error al validar código de seguridad'
               );
           }
        } else {
            $output = array(
                'status'  => false,
                'message' => 'campos vacios.'
            );
        }

        echo json_encode($output);
        break;

    case 'change_password_user':

        break;

}