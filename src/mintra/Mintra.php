<?php
	namespace MinTra;

	use CURL\Curl;

class Mintra
	{
		function __construct()
		{
			$this->cc = new Curl();
			$this->cc->setReferer('http://senep.trabajo.gob.pe:8080/');
		}
		function getDataMinTra( $dni )
		{
			if(strlen(trim($dni))==8)
			{
				$url = 'http://senep.trabajo.gob.pe:8080/empleoperu/Ajax.do?method=obtenerCiudadanotoXML&POST_NUMDOCUM='.$dni;
				$response = $this->cc->send( $url );



				if($this->cc->getHttpStatus()==200 && $response!="")
				{
					$xml = new \SimpleXMLElement($response);
					
					$persona = $xml->CIUDADANO;
					if( $dni == (string)$persona->DNI )
					{
						$rtn = array(
							"DNI" 			=>(string)$persona->DNI,
							"Paterno" 		=>(string)$persona->APELLIDOPAT,
							"Materno" 		=>(string)$persona->APELLIDOMAT,
							"Nombres" 		=>(string)$persona->NOMBRES,
							"Sexo" 			=>(string)$persona->SEXO,
							"FechaNacimiento" 	=>(string)$persona->FECHANAC,
						);
						return $rtn;
					}
				}
			}
			return false;
		}
		function check( $dni, &$database )
		{

			if( strlen($dni) == 8 )
			{
				$result = $this->getDataMinTra( $dni );
				if( $result!=false )
				{
					print_r($result);
					if(is_null($database['Nombres']))$database['Nombres'] = $result["Nombres"];
                    if(is_null($database['Paterno']))$database['Paterno'] = $result["Paterno"];
                    if(is_null($database['Materno']))$database['Materno'] = $result["Materno"];
                    if(is_null($database['Distrito']))$database['Distrito'] = $result["Distrito"];
                    if(is_null($database['Provincia']))$database['Provincia'] = $result["Provincia"];
                    if(is_null($database['Departamento']))$database['Departamento'] = $result["Departamento"];
                    if(is_null($database['Sexo']))$database['Sexo'] = $result["Sexo"];
                    if(is_null($database['FechaNacimiento']))$database['FechaNacimiento'] = $result["FechaNacimiento"];
				}
			}
		}
	}
?>
