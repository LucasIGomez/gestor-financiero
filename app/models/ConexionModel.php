<?php
require_once 'Conexion.php';

class ConexionModel {
    private $db;
    private $encrypt_key = "ClariFiSecretOpenBankingKey2026";
    private $encrypt_method = "AES-256-ECB";

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    private function encriptar($texto) {
        return openssl_encrypt($texto, $this->encrypt_method, $this->encrypt_key);
    }

    private function desencriptar($texto_encriptado) {
        return openssl_decrypt($texto_encriptado, $this->encrypt_method, $this->encrypt_key);
    }

    // Vincula o actualiza una conexión de billetera
    public function vincularBilletera($id_usuario, $billetera, $token, $alias, $tasa, $saldo) {
        $token_encriptado = $this->encriptar($token);

        $sql = "INSERT INTO conexiones_bancarias (id_usuario, billetera, access_token, alias_personal, tasa_anual, saldo_simulado, estado)
                VALUES (:id_usuario, :billetera, :access_token, :alias_personal, :tasa_anual, :saldo_simulado, 'activo')
                ON DUPLICATE KEY UPDATE 
                    access_token = :access_token2,
                    alias_personal = :alias_personal2,
                    tasa_anual = :tasa_anual2,
                    saldo_simulado = :saldo_simulado2,
                    estado = 'activo'";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':billetera', $billetera, PDO::PARAM_STR);
        $stmt->bindParam(':access_token', $token_encriptado, PDO::PARAM_STR);
        $stmt->bindParam(':access_token2', $token_encriptado, PDO::PARAM_STR);
        $stmt->bindParam(':alias_personal', $alias, PDO::PARAM_STR);
        $stmt->bindParam(':alias_personal2', $alias, PDO::PARAM_STR);
        $stmt->bindParam(':tasa_anual', $tasa);
        $stmt->bindParam(':tasa_anual2', $tasa);
        $stmt->bindParam(':saldo_simulado', $saldo);
        $stmt->bindParam(':saldo_simulado2', $saldo);

        return $stmt->execute();
    }

    // Obtiene las billeteras conectadas desencriptando sus tokens
    public function obtenerConexionesUsuario($id_usuario) {
        $sql = "SELECT * FROM conexiones_bancarias WHERE id_usuario = :id_usuario AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $conexiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($conexiones as &$conexion) {
            $conexion['access_token'] = $this->desencriptar($conexion['access_token']);
        }

        return $conexiones;
    }

    // Actualiza el saldo simulado de una billetera conectada
    public function actualizarSaldoBilletera($id_conexion, $id_usuario, $nuevo_saldo) {
        $sql = "UPDATE conexiones_bancarias 
                SET saldo_simulado = :nuevo_saldo 
                WHERE id_conexion = :id_conexion AND id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nuevo_saldo', $nuevo_saldo);
        $stmt->bindParam(':id_conexion', $id_conexion, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Desconecta temporalmente una billetera (baja lógica)
    public function desconectarBilletera($id_conexion, $id_usuario) {
        $sql = "UPDATE conexiones_bancarias 
                SET estado = 'desconectado', saldo_simulado = 0.00 
                WHERE id_conexion = :id_conexion AND id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_conexion', $id_conexion, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
