<?php
	namespace EsSalud;

	use CURL\Curl;

class EsSalud
	{
		function __construct()
		{
			$this->path = dirname(__FILE__);
			$this->cc = new Curl();
			$this->cc->setReferer('https://ww1.essalud.gob.pe');
			$this->cc->setCookiFileLocation(__DIR__ . '/cookie.txt');
		}
		function objectToArray($d) {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map(__FUNCTION__, $d);
        }
        else {
            // Return array
            return $d;
        }
		}

		function check( $dni,&$database)
		{
			$data = array(
				"strDni" 		=> $dni
			);
			$url = "https://ww1.essalud.gob.pe/sisep/postulante/postulante/postulante_obtenerDatosPostulante.htm";
			$response = $this->cc->send( $url, $data );
			if( $this->cc->getHttpStatus() == 200 && $response != "")
			{
				$json_Response = json_decode( $response );
				if( isset($json_Response->DatosPerson[0]) && $json_Response->DatosPerson[0]->ApellidoPaterno!=":\"" > 0 && strlen($json_Response->DatosPerson[0]->DNI)>=8 )
				{
                    $result=$json_Response->DatosPerson[0];
                    if(is_null($database['Nombres']))$database['Nombres'] = $result->Nombres;
                    if(is_null($database['Paterno']))$database['Paterno'] = $result->ApellidoPaterno;
                    if(is_null($database['Materno']))$database['Materno'] = $result->ApellidoMaterno;
                    if(is_null($database['Sexo']))$database['Sexo'] = $result->Sexo==2?1:0;
                    if(is_null($database['FechaNacimiento']))$database['FechaNacimiento'] = $result->FechaNacimiento;

				}

			}
		}
	}
?>
