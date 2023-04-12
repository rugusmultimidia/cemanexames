<?php

###################################################
#												  #
# ---------    Ih Framework           ----------  #
# ---------    Classe controller      ----------  #
# ---------    Versão 3.0 		      ----------  #
# ---------    Caio Cesar - 2015      ----------  #
# ---------    caiocmm@live.com       ----------  #
#												  #
###################################################

class Controller extends Core {
    
   
    public $pageView, $core, $init, $vars, $core_controller, $message, $theme = false, $pageData, $content;
	

	/*public function __call($method, $args){
   	 	$this->c->$method($args[0]);
  	}*/
    
    protected function view($view, $vars = null, $theme = false){
		                
        $this->setPageView($view, $vars);
		
		$this->global_init();

		if($theme) {
			$this->theme = true;
		}

		if($this->layout_module) {			
			return require_once('themes/'.$this->layout_module.'/layout.phtml');			
		} else {			
			return require_once('themes/default/layout.phtml');			 
		}
 
        exit();
        
    }
	
	

    public function viewNoBase($view, $vars = null, $theme = false){

        
        if (is_array($vars) && count ($vars) > 0) {
            
            extract($vars, EXTR_PREFIX_ALL, 'view');
            
        }
		
		if($theme) {
			$this->theme = true;
		}
		
		$this->global_init();
		
		$this->setPageView($view, $vars);
			
		if($this->verify_routes()) {
			
			if(file_exists('app/'.$this->controller.'/views/'.$this->pageView.'.phtml')) {
            
             	require_once('app/'.$this->controller.'/views/'.$this->pageView.'.phtml');
			 
			} else {
				
				$this->message->setMsg("error",'arquivo não encontrado:: ' .'app/'.$this->controller.'/views/'.$this->pageView.'.phtml')->getMsg();
				
			}  
		
		} else {			
			
			if(file_exists('app/'.$this->controller.'/views/'.$this->pageView.'.phtml')) {
			
				return require_once('app/'.$this->controller.'/views/'.$this->pageView.'.phtml');
			
			} else {
				$this->message->setMsg("error",'arquivo não encontrado:: ' .'app/'.$this->controller.'/views/'.$this->pageView.'.phtml')->getMsg();
			}
			
		}  
        
    }
	
	
	public function global_init() {
		
		require_once('app/init.php');
		
		$this->init = new init();
		
		/* instancia a classe init para configurações iniciais para rotas. */
		
		$method_init_route = (string)'init_'.$this->module_controller;
		
		if(method_exists($this->init, $method_init_route)) {			

			$this->init->$method_init_route();
			
		} elseif (method_exists($this->init, 'init')) {

			$this->init->init();
		}
		
	}


    public function setPageView($view, $vars = null) {
        
        $this->pageView = $view;
        
        $this->vars = $vars;        
        
    }
	
	public function setPageContent($data) {
		
		$this->content = $data;
		
	}
	

    public function getPageView($vars = null) {
        
        if (is_array($vars) && count ($vars) > 0) {
            
            extract($vars, EXTR_PREFIX_ALL, 'view');
            
        }
		
							
		if($this->theme){
			
			if(empty($this->layout_module)) {
				$path = 'themes/default/templates/';
			} else {				
				$path = 'themes/'.$this->layout_module.'/templates/';
			}
			
		} else {
			
			$path = 'app/'.$this->controller.'/views/';
		}
			

		
		if($this->verify_routes()) {
	
				if(file_exists($path.$this->pageView.'.phtml')) {
				
					require_once($path.$this->pageView.'.phtml');
				 
				} else {
					
					$this->message->setMsg("error",'arquivo não encontrado:: ' . $path .$this->pageView.'.phtml')->getMsg();
					
				}  
				
		} else {
	
				if(file_exists($path.$this->pageView.'.phtml')) {
				
					require_once($path.$this->pageView.'.phtml');
				 
				} else {
					
					$this->message->setMsg("error",'arquivo não encontrado:: ' . $path .$this->pageView.'.phtml')->getMsg();
					
				} 				
			

        }
		

		
    }     
  
    public function init() {
        
       
        
    }
    
}


?>