<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * <p>CodeIgniter JSON_WebServer_Controller</p>
 *
 * <p>Clase base para los controladores que deseen realizar de servidores
 * de la clase JSON_WebClient</p>
 * 
 * <p>Basado en el <a href="http://www.programacionweb.net/articulos/articulo/sencillo-servicio-web-json-con-php">
 * siguiente artículo Web Services JSON con PHP</a></p>
 * 
 * <p>Los métodos ser invocarán pasando la información de la llamada en formato
 * JSON. La información trasmitida en el objeto JSON tendrá la siguiente 
 * estructura array('method'=>'nombre', 'arguments'=>'argumentos')<br/>
 * Donde</p>
 * <ul>
 *   <li>method: Será el nombre del metodo del controlador a invocar, que deberá
 *               estar definido como public o protected</li>
 *   <li>arguments: Serán los argumentos a pasar a la función. Será un valor
 *      simple o un array si hay más de un parámetro.</li>
 * </ul>
 * <p>Las funciones que se implementen en el controlador deberán ser public o 
 * protected para que puedan ser invocadas desde la clase 
 * JSON_WebServer_Controller</p>
 * 
 * <p>Para cada método llamado se devolverá en JSON un objeto con el siguiente
 * formato array('error'=>'', 'return'=>'', 'debug'=>'')</p>
 * <ul>
 *  <li>error: Indica si la llamada ha tenido (FALSE) o no (TRUE) exito</li>
 *  <li>return: valor devuelto. Si hay error será la descripción del error
 *      en otro caso lo que devuelva la función.</li>
 *  <li>debug: Si está activado el modo depuración en el servidor
 *      se devolverá información sobre los parámetros recibidos</li>
 * </ul>
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Servicios_Web
 * @author        	Santiago D.
 * @license             MIT
 * @link		
 * @version             0.0.1
 */
abstract class JSON_WebServer_Controller extends CI_Controller
{
       
        /**
         * Array que contiene la documentación de las funciones que proporcionara
         * el servicio y que automáticamente se publicará si se accede al 
         * controlador.
         * 
         * Cada elemento decribira del array describirá las funciones disponibles
         * con el array siguiente
         *     array('signature'=>'text', 'help'=>'text')
         * 
         * @var array 
         */
	protected $documentacionFunciones=array();
        
        /**
         * Indica si está activa la depuración
         * @var boolean
         */
        protected $debug=FALSE;
        
        /**
         * Si estamos depurando se registran los parámetros de entrada recibidos
         * @var string
         */
        protected $debug_input_params='';
        
        /*
         * Array que se devolvera en todas las invocaciones a métodos que se
         * realicen. Estará formado por los siguientes campos
         *  - error: Indica si la llamada ha tenido o no exito
         *  - return: valor devuelto. Si hay error será la descripción del error
         *      en otro caso lo que devuelva la función
         *  - debug: Si está activado el modo depuración en el servidor
         *      se devolverá información sobre los parámetros recibidos
         *
	 * @see http://www.programacionweb.net/articulos/articulo/sencillo-servicio-web-json-con-php/
	 */
	public function Index()
	{
            $method = $_SERVER['REQUEST_METHOD'];

            // Dependiendo del método de la petición ejecutaremos la acción correspondiente.
            switch ($method) {
                case 'POST':
                    $this->ProcessCall();
                    break;
                case 'GET':
                default:
                    // Mostramos ayuda
                    $this->ShowHelp();
                    break;
            }
	}
        
        /**
         * Procesa una llamada POST a un método
         */
        private function ProcessCall()
        {
            $input_params=$this->ProcesaParametrosJSON();
            
            //
            // Realizamos comprobaciones con método y parámetros
            // 
            // Comprobamos si han indicado el método
            if ( ! isset($input_params['method']) )
            {
                $this->ReturnError(
                   'No se ha indicado el método que se debe invocar. Envíe en ' .
                   'el POST, JSON con el formato ("method":XXX, "arguments":XXX)');
            }
            $method_name=$input_params['method'];
            if (! method_exists($this, $method_name))
            {
                $this->ReturnError(
                   'No está declarado el método <'.$method_name.'> en el servicio');
            }
            
            // Han indicado los argumentos de la función
            if ( ! isset($input_params['arguments']) )
            {
                $this->ReturnError(
                   'No se ha indicado los argumentos del método. Envíe en ' .
                   'el POST, JSON con el formato ("method":XXX, "arguments":XXX)');
            }
            else if (! is_array($input_params['arguments']))
            {
                // Los argumentos no son un array, pero deseamos que lo sea
                $aguments=array($input_params['arguments']);
            }
            else
            {   // Los argumentos son un array
                $arguments=$input_params['arguments'];
            }
            
            //
            // Llamamos al metodo seleccionado
            // Evitamos mostrar cualquier mensaje de error que se produzca. Si
            // no se indican los parámetros apropiados se sustituirán por NULL
            $return_value=
                    call_user_func_array(array($this, $method_name), $arguments);
            
            if ($return_value===NULL)
            {
                // Ha habido error en la llamada
                $str_args='';
                foreach($arguments as $arg)
                {
                    $str_args.=($str_args!==''?', ':'').$arg;
                }
                $this->ReturnError(
                        "Error en la llamada al metodo [$method_name] ".
                        "con los parámetros [$str_args]");
            }
            
            // Todo correcto devolvemos el valor
            $this->ReturnValues($return_value);
        }
        

        /**
         * Documenta las funciones disponibles. Se utilizará para mostrar
         * ayuda al acceder al controlador sin indicación de ningún método
         * 
         * @param string $signature 
         * @param string $help
         */
        protected function RegisterFunction($signature, $help)
        {
            $this->documentacionFunciones[]=
                    array('signature'=>$signature, 'help'=>$help);
        }
        
        /**
         * Activa o desactiva la información de depuración en el servidor
         * Debug(), Debug(true) -> Activa
         * Debug(false) -> Desactiva
         * @param type $estado
         */
        protected function Debug($estado=TRUE)
        {
            $this->debug=$estado;
        }
        
	/**
	 * Procesa los parámetros recibidos en el servicio, en formato JSON y 
         * los devuelve el formato array
         * 
         * Los parámetros se enviarán en el cuerpo del mensaje
	 */
	private function ProcesaParametrosJSON()
	{
            /* Los parametros los recibiremos en formato JSON mediante POST
             * Dichos parametros se recogerán de la variable 
             * $HTTP_RAW_POST_DATA que contiene los datos de los parametros 
             * sin procesar
             * http://www.php.net/manual/en/reserved.variables.httprawpostdata.php
             * 
             * No se puede utilizar $_POST pues este array procesa los datos
             * recibidos y los transforma en un array. 
             * El problema es que no vienen en el formato apropiado, vienen
             * en formato JSON
             *  
             *  Más información:
             *  http://stackoverflow.com/questions/12194751/what-is-the-raw-post-data
             *  http://stackoverflow.com/questions/3173547/whats-the-difference-between-post-and-raw-post-in-php-at-all
             *  	
             **/

            //
            // Obtenemos la información que viene en el POST sin procesar
            //
            $HTTP_RAW_POST_DATA = file_get_contents('php://input');

            if ($this->debug)
            {
                $this->debug_input_params=$HTTP_RAW_POST_DATA;
            }

            //
            // Comprobamos que hayan enviado parámetros
            //
            if (! isset($HTTP_RAW_POST_DATA))
            {
                // Error fin del script
                $this->RetornaError('No se han enviado datos JSON para operar');

                /*
                 * Otra forma de obtener los datos enviado con POST podría ser 
                 * (http://php.net/manual/es/wrappers.php.php)
                 * 
                 * $rawPost=file_get_contents('php://input');
                 */
            }		
            else 
            {
                // Devolvemos los parametros como array
                return json_decode($HTTP_RAW_POST_DATA, TRUE);
            }
	}
	
        /**
         * Devuelve como resultado error en la operación solicitada. Se indica
         * el motivo del error.
         * 
         * @param string $descripcion
         */
        protected function ReturnError($descripcion)
        {
            $this->RetornaJSON(array(
                'error'=>TRUE, 
                'return'=>$descripcion));
        }
        
        /**
         * Devuelve el resultado de la función
         * @param mixed $value Valor devuelto, cualquier tipo convertible en JSON
         */
        protected function ReturnValues($value)
        {
            $this->RetornaJSON(array(
                'error'=>FALSE,
                'return'=>$value
            ));
        }

        /**
	 * Devuelve en formato JSON la respuesta e incluye la información de 
         * depuración si está activada
	 */
	private function RetornaJSON($respuesta)
	{
            // RFC4627-compliant header
            header('Content-type: application/json');

            // Información de depuración
            if ($this->debug)
            {
                $respuesta['debug']=$this->debug_input_params;
            }	
            echo json_encode($respuesta);

            // Finalizamos el script
            exit;
	}
        
        /**
         * Muestra la ayuda sobre los métodos que se han publicado para el
         * controlador
         */
        protected function ShowHelp()
        {
	 ?>		
<html>
    <head>
        <title>JSON WebServer</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h1>Servicio WEB</h1>
            <p>Están registradas las siguientes funciones:</p>
            <table border="1">
                <tr><td>Método</td><td>Descripción</td></tr>
                <?php foreach($this->documentacionFunciones as $doc) : ?>
                <tr>
                    <td><pre><?=$doc['signature']?></pre></td>
                    <td><?=$doc['help']?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p>Enviar parametros en formato JSON mediante POST utilizando libreria JSON_WebClient</p>    
    </body>
</html>		
	 <?php 
        }        
}

