<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Operaciones extends CI_Controller {

	//
	// Activar para incorporar información de depuración en el servicio
	//
	const DEBUG=FALSE;
	
	/**
	 * Parametros de entrada recibidos por el servicio
	 * @var string
	 */
	private $debug_params_in='';
	
	public function __construct()
	{
		
	}
	
	/**
	 * Servicio web que utiliza JSON como formato de intercambio creado en CodeIgniter
	 * 
	 * @see http://www.programacionweb.net/articulos/articulo/sencillo-servicio-web-json-con-php/
	 */
	public function index()
	{
	?>		
		<h1>Servicio WEB</h1>
		<p>Disponibles las siguientes funciones:</p>
		<ul>
			<li>Suma(op1, op2): Devuelve la suma de los dos números</li>
			<li>Cuadrado(num): Devuelve el cuadrado de un número</li>
		</ul>
		<p>Enviar parametros en formato JSON mediante POST</p>
	<?php 
	}
	
	/**
	 * Servicio web que recibe por post en formato JSON los siguientes par�metros
	 * 
	 * 		{"op1":(int), "op2":(int)} - Operador 1 y 2
	 * 
	 *  y devuelve en formato JSON la suma de ambos números
	 *  	{"res":suma, "error":boolean, "desc":string}
	 */
	public function Suma()
	{
		// Los parametros los recibiremos en la variable enviada por POST y que se llama PARAMS_POST_VAR. Los datos vendr�n en formato JSON
			
		$params=$this->ProcesaParametrosJSON();
		
		//
		// Comprobamos que el n�mero de parametros sea correcto
		//
		if (isset($params->op1) && isset($params->op2))
		{
			// Todo correcto
			$resu=$this->_Suma($params->op1, $params->op2);
			$this->RetornaJSON(array(
				'error'	=>FALSE,
				'res'	=>$resu
			));
		}
		else
		{
			// Error en el n�mero de parametros
			$this->RetornaJSON(array(
					'error'	=>TRUE,
					'desc'	=>'Nº de parametros incorrectos'
			));
		}
	} 
	
	/**
	 * Devuelve la suma de dos n�meros
	 * Funcionalidad del servicio WEB que aislaremos 
	 * @param int $op1
	 * @param int $op2
	 * @return number
	 * 
	 * 
	 * Esta funci�n podr�a ser desempe�ada por el modelo
	 */
	private function _Suma($op1, $op2)
	{
		return $op1+$op2;
	}

	/**
	 * Servicio web que recibe por post en formato JSON los siguientes par�metros
	 *
	 * 		{"num":(int) }
	 *
	 *  y devuelve en formato JSON el cuadrado de ambos n�meros
	 *  	{"res":cuadrado, "error":boolean, "desc":string}
	 */
	
	public function Cuadrado()
	{
			// Los parametros los recibiremos en la variable enviada por POST y que se llama PARAMS_POST_VAR. Los datos vendr�n en formato JSON
			
		$params=$this->ProcesaParametrosJSON();
		
		//
		// Comprobamos que el n�mero de parametros sea correcto
		//
		if (isset($params->num))
		{
			// Todo correcto
			$resu=$this->_Cuadrado($params->num);
			$this->RetornaJSON(array(
				'error'	=>FALSE,
				'res'	=>$resu
			));
		}
		else
		{
			// Error en el n�mero de parametros
			$this->RetornaJSON(array(
					'error'	=>TRUE,
					'desc'	=>'No se ha indicado el parametro'
			));
		}	
	}
	
	/**
	 * Devuelve el cuadrado de un n�mero
	 * @param int $num
	 * @return number
	 * 
	 * Esta funci�n podr�a ser desempe�ada por el modelo
	 */
	private function _Cuadrado($num)
	{
		return $num*$num;
	}

	/**
	 * Procesa los parámetros recibidos en el servicio, en formato JSON y los devuelve el formato array
	 */
	private function ProcesaParametrosJSON()
	{
		/* Los parametros los recibiremos en formato JSON mediante POST
		 * Dichos parametros se recoger�n de la variable $HTTP_RAW_POST_DATA que contiene los
		 * datos de los parametros sin procesar
		 * http://www.php.net/manual/en/reserved.variables.httprawpostdata.php
		 * 
		 *  No se puede utilizar $_POST pues este array procesa los datos recibidos y los
		 *  transforma en un array. El problema es que no vienen en el formato apropiado.
		 *  
		 *  Más información:
		 *  	http://stackoverflow.com/questions/12194751/what-is-the-raw-post-data
		 *  	http://stackoverflow.com/questions/3173547/whats-the-difference-between-post-and-raw-post-in-php-at-all
		 *  	
		 **/
		
		//
		// Obtenemos la información que viene en el POST sin procesar
		//
		$HTTP_RAW_POST_DATA = file_get_contents('php://input');
		
		if (self::DEBUG)
		{
			$this->debug_params_in=$HTTP_RAW_POST_DATA;
		}
		
		
		//
		// Comprobamos que hayan enviado parámetros
		//
		if (! isset($HTTP_RAW_POST_DATA))
		{
			// Error fin del script
			$this->RetornaJSON(array(
					'error'	=>TRUE,
					'desc'	=>'No se han enviado parámetros'
			));

			/*
			 * Otra forma de obtener los datos enviado con POST podría ser (http://php.net/manual/es/wrappers.php.php)
			 * 
			 * $rawPost=file_get_contents('php://input');
			 */
		}		
		else 
		{
			// Devolvemos los parametros como array
			return json_decode($HTTP_RAW_POST_DATA);
		}
	}
	
	/**
	 * Devuelve en formato JSON la respuesta
	 */
	private function RetornaJSON($respuesta)
	{
		// RFC4627-compliant header
		header('Content-type: application/json');
		
		// Información de depuración
		if (self::DEBUG)
		{
			$respuesta['debug_params_in']=$this->debug_params_in;
		}	
		echo json_encode($respuesta);
		
		// Finalizamos el script
		exit;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */