<?php

class Redirector {
    
    
    
    public function goToAction($value) {
        
            header("location:".$value);
        
    }
    
    
    public function goToUrl($url) {
        
        
         header("location:".$url);
        
    }
	
	
	public function go($url) {
		
		header("location:".PATH."/".$url);
	}
    
    public function getController() {
        
        
        
        
    }
    
    
    public function getCurrentController(){
        
        global $core;
        return $core->controller;
        
    }
    
    public function getCurrentAction(){
        
        global $core;
        return $core->action;
        
    }   
    
    
    
}



?>
