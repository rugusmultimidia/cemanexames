<?php



class bootstrap {
	
	private $ih_message;
	public $ini, $core, $__template_xml;	
	
	public function __construct() {		
		
		$this->setIni();
		
		$this->define_constants();
		
		$this->getErrors();
		
		
	}

	
	
	private function getErrors() {		
		
		return ini_set("display_errors", $this->ini['production']['ERRORS']); 
		
	}
	
	public function setIni(){
		$this->ini = parse_ini_file('config/application.ini', true);		
	}
	
	public function getIni(){
		return $this->ini;
	}
	
	
	public function setTemplateConfig(){
		
	}
	
	public function getTemplateConfig(){
		
	}
	
	
	public function verify_routes($controller) {
	
		$ini = $this->getIni();
		$this->module_key = array_keys($ini['routes']);
		
		if(in_array($controller,  $this->module_key)) {			
			return true;			
		} else {				
			return false;				
		}	
	}
	
	
	private function define_constants() {
		
		define('HOST'  ,$this->ini['base_application']['host'] );

		define('USER'  ,$this->ini['base_application']['user'] );
		
		define('PASS'  ,$this->ini['base_application']['pass'] );
		
		define('DBNAME',$this->ini['base_application']['dbname'] );

		define('PATH', $this->ini['path_application']['PATH']);

		define('MYSQL_LOG', $this->ini['production']['MYSQL_LOG']);
		
		define('ERRORS', $this->ini['production']['ERRORS']);
		
		define('ih_base_path', str_replace('index.php', '', $_SERVER['PHP_SELF']));
		
		define('ih_current_path', "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
		
		define('ih_ItemsPerPage', $this->ini['pagination']['itensPerPage']);
		
		define('ih_visibleItems', $this->ini['pagination']['visibleItems']);			

	}

	
	public function getLayoutModule($module) {
		
		$this->modules_val = array_values($this->ini['routes']);
		
		$this->module_key = array_keys($this->ini['routes']);
		
		$key_layout = $this->array_key_index($this->ini['routes'], $module);

		return $this->modules_val[$key_layout];
		
	}
	
	public function array_key_index(&$arr, $key) {
		$i = 0;
		foreach(array_keys($arr) as $k) {
			if($k == $key) return $i;
			$i++;	
			
		}
	}
	
	
	
}

?>