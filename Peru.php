<?php
	namespace DatosPeru;

	class Peru
	{

		private $database;



		function __construct()
		{
			$this->reniec = new \Reniec\Reniec(); 
			$this->essalud = new \EsSalud\EsSalud();
			$this->mintra = new \MinTra\Mintra();

			$this->database = array(
                "DNI" 			=> null,
                "Nombres" 		=> null,
                "Paterno" 	=> null,
                "Materno" 	=> null,
                "Distrito" 		=> null,
                "Provincia" 	=> null,
                "Departamento" 	=> null,
				"Sexo" => null,
				"FechaNacimiento"  => null
			);
		}
		function search( $dni )
		{
            header('Content-type: application/json');
			$this->database['DNI']=$dni;
			$this->reniec->search( $dni,$this->database);
			$this->essalud->check($dni,$this->database);
			$this->mintra->check($dni,$this->database);
			
            if(is_null($dni)){
                return array("status"=>false,"mensaje"=>"ERROR DE COMUNICACION.");
            }
            else if(strlen($dni)<8) return array("status"=>false,"mensaje"=>"DNI INCORRECTO");
			$datos=0;
			$vacio= function($dato) use (&$datos)
            {
                if(is_null($dato))$datos++;
            };
            array_map($vacio,$this->database);

            if($datos==8){

                return array("status"=>false,"mensaje"=>"NO HAY INFORMACION EN NUESTRA BASE DE DATOS.");
			}
			else{
                return array("status"=>true,"resultado"=> (object) $this->database);
			}
		}
	}
	
	// MODO DE USO
	/*  */
	require_once( __DIR__ . "/src/autoload.php" );
	$dni=$_GET['dni'] ;
	$test = new Peru();
	echo  json_encode($test->search($dni)) ;
?>
