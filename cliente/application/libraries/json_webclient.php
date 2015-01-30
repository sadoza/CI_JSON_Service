<?php
/**
 * JSON_WebClient es una clase muy sencilla que nos permite realizar llamadas
 * a metodos remotos utilizando
 */
class JSON_WebClient
{
	/**
	 * URL en la que se encuentra ubicada el servicio
	 * @var string
	 */
	private $URL;
	
	/**
	 * Indica si el servicio existe y la llamada se ha realizado con exito
	 * @var boolean
	 */
	private $is_ok_call=TRUE;

	/**
	 * Si se activa la libraría muestra información de depuración
	 * @var boolean
	 */
	private $debug=FALSE;
	
	/**
	 * Constructor
	 * @param array $params		// Parametros disponibles 'url'
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
	 * LLama a un procedimiento remoto
	 * 
	 * @param string $method
	 * @param mixed $args
	 * @param array|string $successCallback		Nombre de la función a invocar en caso de llamada exitosa
	 * @param array|string $errorCallback		Nombre de la función a invocar en caso de error en llamada
	 * 
	 * @return mixed Respuesta devuelta por el servicio
	 */
	public function Call($method, $args)
	{
		$url_method=$this->URL."/".$method;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_URL,$url_method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
		$resposeText = curl_exec($ch);
		$resposeInfo = curl_getinfo($ch);
		
		if($resposeInfo["http_code"] == 200)
		{
			// Llamada exitosa
			$this->is_ok_call=TRUE;
		}
		else
		{
			// No existe el método o servicio
			$this->is_ok_call=FALSE;
		}
		
		
		if ($this->debug) :
			$id="infdbg".time().rand(1000, 100000);
		?>
		
		<!-- Librería jQuery requerida por los plugins de JavaScript -->
		<script src="http://code.jquery.com/jquery.js"></script>
		<div style="font-family:arial; font-size:10px">
		  Información depuración json_webclient: <a href="#" id="mas<?=$id?>">Ver</a>  <a href="#" id="menos<?=$id?>">Ocultar</a>
		  <div id="<?=$id?>" style="display: none; border:solid 1px #FF0; margin:.5em 2em; padding:.5em">
			<p>URL: <?=$this->URL?><br/>
			Method: <?=$method?><br/>
			URL+Method: <a href="<?=$url_method?>" target="_blank"><?=$url_method?></a></p>
			<p>Args: </p><pre><?=json_encode($args)?></pre>
			<fieldset style="border:solid 1px #777; padding:.3em; margin:1em"><legend>Response Text</legend>
			<div ><?=$resposeText?></div>
			</fieldset>
			<fieldset style="border:solid 1px #333; padding:.3em; margin:1em; background:#eee"><legend>Response Info</legend>
			<pre  >
				<?=print_r($resposeInfo)?>
			</pre>
			</fieldset>
		  </div>
		</div>
		<script>
			$("#mas<?=$id?>").click(function() { $("#<?=$id?>").show(); });
			$("#menos<?=$id?>").click(function() { $("#<?=$id?>").hide(); });
		</script>
		<?php endif; 
		
		
		return json_decode($resposeText);
	}
	
	/**
	 * Indica si la última llamada realizada se ha ejecutado en el servidor
	 * @return boolean
	 */
	public function IsOkCall()
	{
		return $this->is_ok_call;
	}
	
	/**
	 * Activa o desactiva el modo de depuración
	 * @param boolean $state
	 */
	public function Debug($state=TRUE)
	{
		$this->debug=$state;
	}
}