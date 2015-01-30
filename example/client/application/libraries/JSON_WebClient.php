<?php
/**
 * JSON_WebClient es una clase muy sencilla que nos permite realizar llamadas
 * a metodos remotos utilizando JSON como lenguaje de intercambio. 
 * 
 * Utilizando JSON_WebServer_Controller crearemos un controlador que será el
 * servicio que contenga los métodos que deseamos invocar
 * 
 * Este clase es muy sencilla y tan solo pretende ser utilizada
 */
class JSON_WebClient
{
	/**
	 * URL en la que se encuentra ubicada el servicio
	 * @var string
	 */
	private $URL;
	
	/**
	 * Guarda el resultado de la última llamada realizada. Si ha sido exitosa
	 * @var boolean
	 */
	private $call_ok=TRUE;

        /**
         * Almacena la descripción del error que se ha producido en la última
         * llamada que produjo error
         * @var string 
         */
        private $last_error='';
        
	/**
	 * Si se activa la libraría muestra información de depuración
	 * @var boolean
	 */
	private $debug=FALSE;
	
	/**
	 * Constructor
         * Los parametros deben ser pasados como un array.
         * Actualmente solamente se usan los siguientes:
         *   array('url'=>'xxx')
         * 
         *   - url: URL base en la que realizará la llamada la clase. Puede
         *      ser fijado igualmente con el método SetURL();
         * 
	 * @param array $params		
	 */
	public function __construct($params=NULL)
	{
            if (isset($params['url']))
            {
                    $this->url=$params['url'];
            }
		
	}
	
	/**
	 * Establece la URL en la que se encuentra el servicio
	 * @param string $url
	 */
	public function SetURL($url)
	{
		$this->URL=$url;		
	}
	
        /**
         * <p>LLama a un procedimiento remoto y le pasa los argumentos indicados
         * en formato JSON mediante un mensaje POST en el mismo formato esperado
         * por  JSON_WebServer_Controller</p>
         * 
         * @param string $method    Nombre del método a invocar
         * @param mixed $args       Parametros requeridos para invocar función
         *                          en un array si tiene más de un valor.<br/>
         *                          Si en una llamada no se indican suficientes
         *                          parametros, se rellará con valores NULL.
         * @return NULL | mixed     NULL=Fallo llamada mixed=valor devuelto
         */
	public function Call($method, $args)
	{
            // $args debe ser un array
            if (!is_array($args))
            {
                $args=array($args);
            }
            
            $json_args=json_encode(array(
                'method'=>$method,
                'arguments'=>$args
            ));
            
            // Realizamos la petición POST con los datos en formato JSON
            // a la URL del servicio
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
            curl_setopt($ch, CURLOPT_URL,$this->URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_args);
            $responseText = curl_exec($ch);
            $responseInfo = curl_getinfo($ch);

            // Suponemos llamada exitosa
            $this->call_ok=TRUE;
            $this->last_error='';
            
            // Comprobamos si la petición ha localizado la URL del servicio
            // y ha devuelto un código de exito
            if($responseInfo["http_code"] != 200)
            {   
                // Algo fallo. No existe o no está disponible el servicio
                $this->call_ok=FALSE;
                $this->last_error="No existe o no está disponible el servicio";
            }

            $val_return=json_decode($responseText, TRUE);
            
            if (! isset($val_return['error']) || ! isset($val_return['return']) )
            {
                // Algo fallo. No existe o no está disponible el servicio
                $this->call_ok=FALSE;
                $this->last_error="No se ha devuelto un formato válido de respuesta";
            }
            
            if ($this->debug) 
            {
                $this->ShowDebugInfo($method, $json_args, $responseText, $responseInfo);
            }

            // Devolvemos el valor devuelto si la llamada ha sido exitosa
            if ($this->call_ok)
            {
                return $val_return['return'];
            }
            else
            {   // Fallo en la llamada
                return NULL;
            }
	}
	
	/**
	 * Indica si la última llamada realizada se ha ejecutado en el servidor
	 * @return boolean
	 */
	public function IsLastCallOk()
	{
		return $this->call_ok;
	}
	
        /**
         * Devuelve la descripción del error que se ha producido en la última
         * llamada.
         * @return string
         */
        public function DescError()
        {
            return $this->last_error;
        }
        
	/**
	 * Activa o desactiva el modo de depuración
	 * @param boolean $state
	 */
	public function Debug($state=TRUE)
	{
		$this->debug=$state;
	}
        
        /**
         * Muestra en formato HTML información de depuración que puede resultar 
         * util para probar el funcionamiento
         * @param string $method        Nombre del método
         * @param string $responseText  Texto devuelto en la llamada a la función
         *                              (formato JSON)
         * @param type $responseInfo    Información devuelta por la invocación
         *                              del método curl_getinfo()
         */
        private function ShowDebugInfo($method, $json_args, $responseText, $responseInfo)
        {
            $id="json_wc_dbg".time().rand(1000, 100000);
?>
<!-- DEBUG = ON JSON_WebClient -->
<!-- Librería jQuery requerida por los plugins de JavaScript -->
<script src="http://code.jquery.com/jquery.js"></script>
<div style="font-family:arial; font-size:10px">
  Información depuración json_webclient: <a href="#" id="mas<?=$id?>">Ver</a>  <a href="#" id="menos<?=$id?>">Ocultar</a>
  <div id="<?=$id?>" style="display: none; border:solid 2px #00F; margin:.5em 2em; padding:.5em">
      <p>Service URL: <a href="<?=$this->URL?>" target="_blank"><?=$this->URL?></a><br/>
         Method: <?=$method?><br/>
      </p>
      <p>Parameters: </p>
      <pre><?=$json_args?></pre>
      <fieldset style="border:solid 1px #777; padding:.3em; margin:1em">
          <legend>Response Text</legend>
          <div ><?=$responseText?></div>
      </fieldset>
      <fieldset style="border:solid 1px #333; padding:.3em; margin:1em; background:#eee"><legend>Response Info</legend>
        <pre><?=print_r($responseInfo)?></pre>
      </fieldset>
  </div>
</div>
<script>
    $("#mas<?=$id?>").click(function() { $("#<?=$id?>").show(); });
    $("#menos<?=$id?>").click(function() { $("#<?=$id?>").hide(); });
</script>
<?php
        }
}