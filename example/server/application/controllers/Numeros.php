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
class Numeros extends JSON_WebServer_Controller {

	//
	// Activar para incorporar información de depuración en el servicio
	//
	const DEBUG=TRUE;
	
        /**
         * 
         */
	public function __construct()
	{
            parent::__construct();
            
            // Activamos o no depuración
            $this->Debug(self::DEBUG);
            
            // Registramos funciones disponibles
            $this->RegisterFunction('Lista(n)', 'Devuelve una lista de numeros de tamaño [n]');
	}
	
        /*
         * NOTA: No sobreescribir el método Index() pues esta ya está implementado
         * en la clase base y es el que se encarga de realizar toda la funcionalidad
         */
	
 
	/**
	 * Devuelve una lista aleatoria de n numeros
	 * 
	 * @param int $nElem
	 * @return array
	 * 
	 * Si fuese una tarea compleja llamaríamos a las funciones que fuesen
         * precisas del modelo
         * 
         * La función no puede ser privada o tendremos un error
	 */
	protected function Lista($nElem)
	{
            $lista=array();
            for($i=0; $i<$nElem; $i++)
            {
                    $lista[]=rand(1, 10000);
            }
            return $lista;
	}

}

