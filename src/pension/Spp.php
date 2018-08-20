<?php
    namespace SPP;


    class Spp
	{
		var $path = "";
  
        protected $_useragent = 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:53.0) Gecko/20100101 Firefox/53.0';
        protected $_url;
        protected $_followlocation;
        protected $_timeout;
        protected $_httpheaderData = array();
        protected $_httpheader = array('Expect:');
        protected $_maxRedirects;
        protected $_cookieFileLocation;
        protected $_post;
        protected $_postFields;
        protected $_referer ="https://www.google.com/";

        protected $_session;
        protected $_webpage;
        protected $_includeHeader;
        protected $_noBody;
        protected $_status;
        protected $_binary;
        protected $_binaryFields;

        public    $proxy = false;
        public    $proxy_host = '';
        public    $proxy_port = '';
        public    $proxy_type = CURLPROXY_HTTP;

        public    $authentication = false;
        public    $auth_name      = '';
        public    $auth_pass      = '';
        public $s;

        function __construct()
		{
            $this->s = curl_init();
			$this->path = dirname(__FILE__);
			$this->setCookiFileLocation("cookies.txt");
			$this->setReferer( "https://www.sbs.gob.pe/app/spp/Reporte_Sit_Prev/afil_datos_documento.asp?p=1" );
		}
        public function setCookiFileLocation( $path )
        {
            $this->_cookieFileLocation = $path;
            if ( !file_exists($this->_cookieFileLocation) )
            {
                file_put_contents($this->_cookieFileLocation,"");
            }
        }
        public function setReferer( $referer )
        {
            $this->_referer = $referer;
        }

        public function setPost( $postFields = array() )
        {
            $this->_binary = false;
            $this->_post = false;
            if(count($postFields)>0)
            {
                $this->_post = true;
            }
            $this->_postFields = http_build_query($postFields);
        }



        // simplificado
        public function send( $url, $post = array() )
        {
            $this->_post = false;
            if( count($post)!=0 )
                $this->setPost( $post );


            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec ($ch);
            curl_close ($ch);

            return $response;
        }

		function getDatosSPP( $dni )
		{
			if( $dni!="" )
			{
				$data = array(
					"cmdEnviar" 	=> "Aquí",
					"numdoc" 		=> "00".$dni
				);

				$url = "https://www.sbs.gob.pe/app/spp/Reporte_Sit_Prev/afil_formulario.asp";
				$response = $this->send( $url, $data );
				$ar = explode("\n",$response);
				$ar = array_slice($ar, 42);
				$response = implode("\n", $ar);
				if( $response )
				{
					$doc = new \DOMDocument();
					$doc->strictErrorChecking = FALSE;
					libxml_use_internal_errors(true);
					$doc->loadHTML( mb_convert_encoding( $response, 'HTML-ENTITIES',  'UTF-8' ) );
					libxml_use_internal_errors( false );
					libxml_clear_errors();

					$xml = simplexml_import_dom( $doc );
					$cussp = $xml->xpath("//input[@name='cussp']/@value");
					$nomb1 = $xml->xpath("//input[@name='lprinom']/@value");
					$nomb2 = $xml->xpath("//input[@name='lsegnom']/@value");
					$apapt = $xml->xpath("//input[@name='lapepat']/@value");
					$apmat = $xml->xpath("//input[@name='lapemat']/@value");

					$detalles = $xml->xpath("//table[@id='TABLE2']/tr/td[@colspan=9]/div/table/tr");
					if( isset($cussp[0]["value"]) )
					{
						$result = array(
							"DNI" 				=> $dni,
							"Nombres" 			=> trim( (string)$nomb1[0]["value"]." ".(string)$nomb2[0]["value"]),
							"Paterno" 			=> (string)$apapt[0]["value"],
							"Materno" 			=> (string)$apmat[0]["value"],
							"FechaNacimiento" 	=> (string)$detalles[3]->td[1],
						);
						return $result;
					}
				}
			}
			return false;
		}


        function check( $dni, &$database )
        {
	 print_r($dni);
            if( strlen($dni) == 8 )
            {
                $result = $this->getDatosSPP( $dni );
                if( $result!=false )
                {
                    if(is_null($database['Nombres']))$database['Nombres'] = $result["Nombres"];
                    if(is_null($database['Paterno']))$database['Paterno'] = $result["Paterno"];
                    if(is_null($database['Materno']))$database['Materno'] = $result["Materno"];
                    if(is_null($database['FechaNacimiento']))$database['FechaNacimiento'] = $result["FechaNacimiento"];
                }
            }
        }

	}
