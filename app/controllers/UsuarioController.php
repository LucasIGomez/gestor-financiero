<?php
require_once 'app/models/UsuarioModel.php';

class UsuarioController {
    private $modelo;

    public function __construct() {
        $this->modelo = new UsuarioModel();
    }

    // Procesa el registro y encripta la contraseña
    public function procesarRegistro($nombre, $email, $password) {
        // Encriptación de grado estándar utilizando el algoritmo BCRYPT
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $exito = $this->modelo->registrarUsuario($nombre, $email, $password_hash);
        if ($exito) {
            return true;
        } else {
            return "Error: El correo electrónico ya está registrado.";
        }
    }

    // Verifica las credenciales e inicia la sesión
    public function procesarLogin($email, $password) {
        $usuario = $this->modelo->obtenerUsuarioPorEmail($email);

        // Si el usuario existe y la contraseña coincide con el hash de la base de datos
        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            // Regenerar el ID de sesión previene ataques de fijación de sesión (Session Fixation)
            session_regenerate_id(true); 
            
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre_usuario'] = $usuario['nombre'];
            return true;
        } else {
            return "Error: Correo electrónico o contraseña incorrectos.";
        }
    }

    // Destruye la sesión actual
    public function cerrarSesion() {
        session_unset();
        session_destroy();
    }
}
?>