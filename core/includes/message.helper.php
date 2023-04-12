<?php

class Message {
    
    protected $message;
	

    
    public function getMsg() {
        
      	if(!empty($this->type)) {
		   
	  		echo $this->type;
	  	}
		
    } 
    
    
    public function setMsg($type, $msg)  {
       
	   $this->msg = $msg;
		
		if($type == 'success') {
		
			$this->type = '<div class="message success">'.$this->msg.'</div>';
			
		} 
		
		else if($type == 'warning') {
			
			$this->type = '<div class="message warning">'.$this->msg.'</div>';
			
		}

		else if($type == 'log') {
			
			$this->type = '<div class="message warning">'.$this->msg.'</div>';
			
		}
		
		else if($type == 'error') {
		
			$this->type = '<div class="message error">'.$this->msg.'</div>';
		
		} else {
			
			die('escolha o tipo entre: success, warning, error');
			
		}

		return $this;
		
	
		
    }
    
    
    
    
    
    
    
}



?>
