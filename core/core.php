<?php

###################################################
#												  #
# ---------    Ih Framework           ----------  #
# ---------    Core Class             ----------  #
# ---------    Version 3.0 		      ----------  #
# ---------    Caio Cesar - 2015      ----------  #
# ---------    caiocmm@live.com       ----------  #
#												  #
###################################################


class Core extends functions  {
    
    private $url;
    private $explode;
    public  $controller;
    public  $action;
    public  $params;
	public  $module_controller;
	public  $module;
	public  $layout_module;
	public  $ih_message;
	public  $route;
	public  $data_content_page = null;
    
    
    
	public function __construct() {
        
        $this->setUrl();
        $this->setExplode();
        $this->setController();
		$this->setRoute();
		$this->set_module();	
        $this->setAction();
        $this->setParams();    
		
		$this->message = new Message();

    }
	
	
    
    private function setUrl(){		
		        
        $_GET['page'] = (isset($_GET['page']) ? $_GET['page'] : 'index/index_action' );
        
        $this->url = $_GET['page'];
        
    }
    
    
    private function setExplode() {
        
        $this->explode = explode('/', $this->url);
        
        
    }
    
    private function setController() {
		
        
        $this->controller = $this->explode[0];
				
    }
	
	public static function getController(){
		
		return $this->controller;
		
	}
	
	
	 private function setRoute() {
        
        $this->module_controller = $this->explode[0];
		
        
    }
	
	public function getRoute() {
		
		
		return $this->module_controller;
		
		
	}
    
    private function setAction() {
        
		if($this->verify_routes()) {

        	$act = (!isset($this->explode[2]) || $this->explode[2] == null || $this->explode[2] == "index" ? "index_action" : $this->explode[2]);
		
		} else {

			
			$act = (!isset($this->explode[1]) || $this->explode[1] == null || $this->explode[1] == "index" ? "index_action" : $this->explode[1]);
		}
        
        $this->action = $act;
		
		
    }
    
    private function setParams() {
		
		if($this->verify_routes()) {
			
			unset($this->explode[0] , $this->explode[1], $this->explode[2]);
			
		} else {
			
			unset($this->explode[0] , $this->explode[1]);
			
		}
		
			
		if( end( $this->explode ) == NULL) {
			
			array_pop($this->explode);
			
		}
		
		$i = 0;
		
		if(!empty($this->explode)) {
			
			foreach($this->explode as $val) {
				
				if($i % 2 == 0) {
					
					$ind[] = $val;
					
				} else {
					
					$value[] = $val;
					
				}
				
				$i++;
				
			}
			
		} else {
			
			$ind	= array();
			$value  = array();
			
		}
		
		if(count($ind) == count($value) && !empty($ind) && !empty($value)) {
			
			$this->params = array_combine($ind, $value);	
			
		} else {
			
			$this->params = array();
			
		}
		
		if(!$this->params) { $this->params = null; }

    }
	

	

    
    public function _get($name) {          
			 
		return  $this->params[$name];

    }
	
	public function _post($name = null){
		
		if(!empty($name)) {
			
			if(isset($_POST[$name])){
				
				if(is_string($_POST[$name])) {			
					return 	addslashes(htmlentities($_POST[$name]));
				} else {
					return $_POST[$name];
				}
			
			} else {
				
				return false;
				
			}
			
		} else {
			
			return 	$_POST;
		}		
		
	}
	
	private function set_module() {
		
		global $application;

		if($application->verify_routes($this->module_controller)) {
			
			if(empty($this->explode[1])){
				$this->controller = 'index';
			} else {				
				$this->controller = $this->explode[1];
			}
			
			$this->layout_module = $application->getLayoutModule($this->module_controller);
			
			$this->route = true;
			
			return true;
			
		} else {
			
			$this->route = false;
			
			return false;
		}
		
	}
	
	public function verify_routes() {
		
		return 	$this->route;
		
	}
	
   
    
    public function run () {     		
	
		if($this->controller) { $controller = $this->controller; } else { $controller = 'index'; }			
		
		if($this->route) {			
			$controller_path =  'app/'.$controller .'/'.$this->module_controller.".".$controller .".controller.php";				
		} else {		
			$controller_path =  'app/'.$controller .'/'.$controller .".controller.php";				
		}

		
		$custom_view = false;
		
		//check if the controller exists
		//controller does not exists, then we check if has a custom page 			
			
		//$pageModel = new pagesModel();
		//$page = $pageModel->get($_GET['page']);	
		
		//if(isset($page[0])){
				
				//$this->data_content_page = $page[0];   
		//}
		

		
		if(!file_exists($controller_path)) {
			
			$custom_view = true; 
			
			require_once('app/pages/admin.pages.controller.php');
				
			$controller = 'pages';				
			
			if(isset($page[0])) {
				
				$this->action = 'loadPage';					
				
			} else {
				
				//controller does not exists, then we check if has a custom page 			
				
				if(ERRORS) { //ERRORS
					//log error 			
					$this->message->setMsg("error",'Controller nao encontrado, verifique se existe o controller '.$this->controller.' :: '.$controller_path)->getMsg();
					die();
						
				} else {
					//404 page ( not found ) 			
					$this->action = 'not_found';
				}
				
			}
			
		} else {			
			require_once($controller_path);			
		}
 
        $app = new $controller;        

        if (method_exists($app, $this->action)) {
			
			$action = $this->action; 
			
			if(isset($page[0])){
				
				$app->setPageContent($page[0]);   
			}

			$app->global_init();
			$app->init();
			$app->$action();   
			
			

			
        } else {
			
			$this->message->setMsg("error",
			'Metodo não encontrado, verifique se existe o metodo 
			<strong style="text-decoration:underline">'.$this->action.'</strong> no controller <strong style="text-decoration:underline">'.$controller.'</strong>')
			->getMsg();
			
			die();
		}
		


    }

}