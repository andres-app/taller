
<?php
session_start();
require_once __DIR__ . '/../models/MdUsuarios.php';

class CtrUsuarios {

    public static function login() {

        if(isset($_POST['usuario'])) {

            $usuario = $_POST['usuario'];
            $password = $_POST['password'];

            $respuesta = MdUsuarios::login($usuario, $password);

            if($respuesta) {

                $_SESSION['login'] = true;
                $_SESSION['usuario'] = $respuesta['usuario'];
                $_SESSION['empresa'] = $respuesta['empresa'];
                $_SESSION['empresa_id'] = $respuesta['empresa_id'];

                header("Location: dashboard.php");
                exit;

            } else {

                echo "<script>alert('Credenciales incorrectas');</script>";
            }
        }
    }
}
?>
