<?php 

	class functions {
		
		public 
		
		$attach_header_css = array(),
		$attach_header_js = array(), 
		$attach_footer_js = array(),
		$title,
		$__template_xml;
		
		//create a temporary session
		public function sessionTemp (){		
			if (empty($this->sessao_temp)) {		
				$this->sessao_temp = session_id();		
			} else {		
				session_regenerate_id();		
				$this->sessao_temp = session_id();		
			}
			
			return $this->sessao_temp;		
		}
		
		
		//invert the data order
		public function toDate($delimiter, $date) {		
			return implode($delimiter, array_reverse(explode($delimiter, $date))); 		
		}
		
		
		//add js to template, scope can be footer or header, if empty header is default
		public function add_js($path, $scope = false){

			if($scope) {	
			
				$this->attach_footer_js[] = $path;			
				
			} else {
				
				$this->attach_header_js[] = $path;
				
			}
		}
		
		//add css to template
		public function add_css($path){
			
			$this->attach_header_css[] = $path;
		}		
		
		//get template files by xml 
		public function setTemplateFile(){			

			if(empty($this->__template_xml)){

				if(!$this->route){
					
					if(file_exists('themes/default/theme.xml')) {

						$xml = 'themes/default/theme.xml';
					
						$this->__template_xml= simplexml_load_file($xml);
					}
					
				} else {
					
					if(file_exists('themes/'.$this->module_controller.'/theme.xml')) {
					
						$xml = 'themes/'.$this->module_controller.'/theme.xml';
					
						$this->__template_xml= simplexml_load_file($xml);

					}					
				}
			}			
		}	
		
		public function get_title(){
			
			$this->__template_xml->title[0];
		}		
		
		public function get_head(){
			
			$this->setTemplateFile();
			
			$header = '';			
			$header .= '<link href="core/includes/assets/css/theme.css" rel="stylesheet"  />'. PHP_EOL;
			
			if(is_object($this->__template_xml)) {
				foreach($this->__template_xml->header->css as $css ){				
					if(file_exists($css)) {
						$header .= '<link href="'.$css.'" rel="stylesheet"  />'. PHP_EOL;
					}				
				}
			}
			
			foreach($this->attach_header_css as $css) {				
				if(file_exists($css)) {
					$header .= '<link href="'.$css.'" rel="stylesheet"  />'. PHP_EOL;;
				}
			}
			
			if(is_object($this->__template_xml)) {
				foreach($this->__template_xml->header->js as $js ){				
					if(file_exists($js)) {
						$header .= '<script type="text/javascript" src="'.$js.'"></script>'. PHP_EOL;;
					}				
				}
			}
			
			foreach($this->attach_header_js as $js) {
				if(file_exists($js)) {
					$header .= '<script type="text/javascript" src="'.$js.'"></script>'. PHP_EOL;;
				}
			}
			
			return $header;
			
		}
		
		function get_memory_usage(){
			
			$size = memory_get_usage(true);
			
			$unit=array('b','kb','mb','gb','tb','pb');
			return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];

		}


		public function getUserType() {
			return $_SESSION['@userApp']['user_type'];
		}

		public function getUserName() {
			return $_SESSION['@userApp']['name'];
		}

		public function getFirstName() {
			$fullName = $this->getUserName();
			$nameParts = explode(' ', $fullName);
			return $nameParts[0];
		}

		public function getUserTypeName() {
			return $_SESSION['@userApp']['name_role'];
		}

		public function getUserPermissions($controler=false, $action=false) {


			if ($this->getUserType() == 1 || $this->getUserType() == 2) {
				return true;
				die();
			}

			if($controler && $action) {
				$permissions = $_SESSION['@userApp']['permissions'];
				if(in_array($action, $permissions[$controler])) {
					return true;
				}
				return false;
			}

			if($controler && !$action) {
				$permissions = $_SESSION['@userApp']['permissions'];
				if(isset($permissions[$controler])) {
					return true;
				}
				return false;
			}

			return $_SESSION['@userApp']['permissions'];
			
		}
		
		
		public static function removeSpecialChars($str) {
			$str = preg_replace('/[áàãâä]/ui', 'a', $str);
			$str = preg_replace('/[éèêë]/ui', 'e', $str);
			$str = preg_replace('/[íìîï]/ui', 'i', $str);
			$str = preg_replace('/[óòõôö]/ui', 'o', $str);
			$str = preg_replace('/[úùûü]/ui', 'u', $str);
			$str = preg_replace('/[ç]/ui', 'c', $str);
			// $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
			$str = preg_replace('/[^a-z0-9]/i', '_', $str);
			$str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
			return $str;
		} 

		public static function cleanCPF($cpf) {
			return preg_replace('/\D/', '', $cpf);
		}

		public static function formatCPF($cpf) {
			$cpf = self::cleanCPF($cpf);
			return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf);
		}

		public function printar($data=false)
		{
			if($data){
				echo "<pre>";
				print_r($data);
				die();
			}else{
				echo 'Sem parametro para printar';
				die();
			}
		}

		public function clinica() {
			
			return $_SESSION['@userApp']['clinica'];

		}

		public function formatDateTimeToBR($dateTime) {
			return date('d/m/Y', strtotime($dateTime));
		}


		public function validateAndFormatDate($date) {
			$dateTime = DateTime::createFromFormat('d/m/Y', $date);
			if ($dateTime && $dateTime->format('d/m/Y') === $date) {
				return $dateTime->format('Y-m-d');
			}
			return false;
		}

		public function formatDateToBR($date) {
			$dateTime = DateTime::createFromFormat('Y-m-d', $date);
			if ($dateTime && $dateTime->format('Y-m-d') === $date) {
				return $dateTime->format('d/m/Y');
			}
			return false;
		}

		
		
	}



?>