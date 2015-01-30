<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Incluimos definición de clase padre
require_once(APPPATH.'/libraries/JSON_WebServer_Controller.php');

/**
 * <p>Controlador que implementa un servicio web utilizando la libreria 
 * JSON_WebServer_Controller</p>
 * 
 * <p>Las llamadas a este servicio se realizarán utilizando la librería
 * JSON_WebClient</p>
 */
class Operaciones extends JSON_WebServer_Controller {

    //
    // Activar para incorporar información de depuración en el servicio
    //
    const DEBUG=FALSE;

    public function __construct()
    {
        parent::__construct();
        
        // Activamos o no depuración
        $this->Debug(self::DEBUG);

        // Registramos funciones disponibles
        $this->RegisterFunction('Suma(op1, op2)', 'Devuelve la suma de los dos números');
        $this->RegisterFunction('Cuadrado(num)', 'Devuelve el cuadrado de un número');
        
    }

    /*
     * NOTA: No sobreescribir el método Index() pues esta ya está implementado
     * en la clase base y es el que se encarga de realizar toda la funcionalidad
     */
    
    /**
     * Devuelve la suma de dos números
     * 
     * @param int $op1
     * @param int $op2
     * @return number
     * 
     * La función no puede ser privada o tendremos un error
     */
    protected function Suma($op1, $op2)
    {
        return $op1+$op2;
    }

 
    /**
     * Devuelve el cuadrado de un número
     * 
     * @param int $num
     * @return number
     * 
     * La función no puede ser privada o tendremos un error
     */
    protected function Cuadrado($num)
    {
        return $num*$num;
    }

 
}
