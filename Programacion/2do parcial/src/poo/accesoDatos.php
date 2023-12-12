<?php

class AccesoDatos
{
    private static AccesoDatos $objetoAccesoDatos;
    private PDO $objetoPDO;

    private function __construct() {
        try 
        {
            $this->objetoPDO = new PDO('mysql:host=localhost;dbname=jugueteria_bd;charset=utf8', 'root', '', array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->objetoPDO->exec("SET CHARACTER SET utf8");
            
        } catch (PDOException $e) 
        {
            echo("[ Error ] : " . $e->getMessage());
        }
    }

    public static function dameUnObjetoAcceso() : AccesoDatos
    {
        if (!isset(self::$objetoAccesoDatos)) {
            self::$objetoAccesoDatos = new AccesoDatos();
        }
        return self::$objetoAccesoDatos;
    } 

    public function retornarConsulta(string $cadenaSql) : PDOStatement | bool
    {
        return $this->objetoPDO->prepare($cadenaSql);
    }

    public function retornarUltimoIdInsertado() : string | bool
    {
        return $this->objetoPDO->lastInsertId();
    }

    public function __clone()
    {
        trigger_error("La clonacion de este objeto no esta permitida", E_USER_ERROR);
    }

}

?>