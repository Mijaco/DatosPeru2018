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
			$this->database['DNI']=$dni;
			$this->reniec->search( $dni,$this->database);
			$this->essalud->check($dni,$this->database);
			$this->mintra->check($dni,$this->database);
			$datos=0;
			$vacio= function($dato) use (&$datos)
            {
                if(is_null($dato))$datos++;
            };
            array_map($vacio,$this->database);
			if($datos==8){

            	print_r("NO HAY INFORMACIÓN EN NUESTRA BASE DE DATOS.");
			}
			else{
                print_r($this->database);
			}
		}
	}
	
	// MODO DE USO
	/*  */
	require_once( __DIR__ . "/src/autoload.php" );
	$test = new Peru();
	$test->search("76696881") ;
?>
