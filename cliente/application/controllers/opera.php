<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Opera extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('opera_menu');
	}
	
	/**
	 * Invocal al servicio Suma que calcula la suma de dos números
	 * @param int $op1
	 * @param int $op2
	 */
	public function Suma($op1, $op2)
	{
		$this->load->library('JSON_WebClient');
		
		$service_url='http://localhost/dwes/CIJSON_Service/servidor/index.php/operaciones'; //base_url().'../servidor/index.php/operaciones';
		
		$this->json_webclient->Debug();
		$this->json_webclient->SetURL($service_url);
		
		$this->HTML_Head();
		$res=$this->json_webclient->Call('suma', array('op1'=>$op1, 'op2'=>$op2));
		
		if ($this->json_webclient->IsOkCall())
		{
			echo "<h1>Resultado suma</h1>";
			if ($res->error)
			{
				// Hay error en la operacion
				echo "<p>Error: ".$res->desc.'</p>';
			}
			else
			{
				echo "<p>$op1 + $op2 = ".$res->res;
			}
		}
	}
	
	/**
	 * Invoca al servicio cuadrado que calcula el cuadrado de un número y lo muestra por pantalla
	 * @param int $num	Número del que queremos obtener el cuadrado
	 */
	public function Cuadrado($num)
	{
		$this->load->library('JSON_WebClient');
		
		$service_url='http://localhost/dwes/CIJSON_Service/servidor/index.php/operaciones'; //base_url().'../servidor/index.php/operaciones';
		
		$this->json_webclient->Debug();
		$this->json_webclient->SetURL($service_url);

		$this->HTML_Head();
		
		$res=$this->json_webclient->Call('cuadrado', array('num'=>$num));
		
		
		if ($this->json_webclient->IsOkCall())
		{
			echo "<h1>Resultado cuadrado</h1>";
			if ($res->error)
			{
				// Hay error en la operacion
				echo "<p>Error: ".$res->desc.'</p>';
			}
			else
			{
				echo "<p>cuadradado de $num = ".$res->res;
			}
		}		
	}
	
	
	public function ListaNumeros($n)
	{
		$this->load->library('JSON_WebClient');
	
		$service_url='http://localhost/dwes/CIJSON_Service/servidor/index.php/numeros'; 
	
		$this->json_webclient->Debug();
		$this->json_webclient->SetURL($service_url);
	
		$this->HTML_Head();
	
		$res=$this->json_webclient->Call('lista', array('nElementos'=>$n));
	
	
		if ($this->json_webclient->IsOkCall())
		{
			echo "<h1>Lista de números</h1>";
			if ($res->error)
			{
				// Hay error en la operacion
				echo "<p>Error: ".$res->desc.'</p>';
			}
			else
			{
				echo "<p>";
				foreach($res->lista as $num)
				{
					echo $num.',';
				}
				echo "</p>";
			}
		}
		}
	
	/**
	 * Función auxiliar que muestra el encabezado HTML. 
	 * Su único objetivo es mejorar la presentación y no estaría incluida en una aplicación real
	 */
	public function HTML_Head()
	{
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Cliente servicio web</title>
</head>
<body>
<?php 
	}
}

