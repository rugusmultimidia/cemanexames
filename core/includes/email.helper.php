<?php

class EmailHelper {
    
    public $from, $destinatario, $subject;
	

    
    public function setFrom($email) {
        
        $this->from = $email;
        
        return $this;
    }
    
    
    public function setDestinatario($destinatario) {
        
        $this->destinatario = $destinatario;
        
        return $this;
    }
    
    public function setSubject($subject) {
        
        $this->subject = $subject;
        
        return $this;
    }
	
	
	public function replyTo($replyTo) {
        
        $this->replyTo = $replyTo;
        
        return $this;
    }
    
    
    public function html($html) {
        
        
        $data      = date("d/m/y");
		$hora      = date("H:i"); 
        $this->html = $html;
		return $this;
        
    }
	
	
	public function anexo($file, $path) {

		$this->files = $file;
		
		$this->fileTmpName = $this->files['tmp_name'];
		
		$this->path = $path;
		
		$this->fileName = $this->files['name'];
		 
		if(move_uploaded_file($this->fileTmpName, $_SERVER['DOCUMENT_ROOT'] ."/". $this->path ."/". $this->fileName)) {
			
			
			
		} else {
			
			
		};
		
		
		$this->anexos = '
		
		<table width="462" border="0" cellpadding="10" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
		  <tr>
			<td bgcolor="#F2F2F2" >Arquivo Anexo:<strong> <a href="'.$_SERVER['HTTP_HOST'].'/'.$this->path.'/'.$this->fileName.'" target="_blank"> '.$this->fileName.' </a></strong></td>
		  </tr>
		   <tr>
			<td></td>
		  </tr>
		  
		</table>';
		 
		 
		return $this;
		
		
	}
	
	

    
   public function enviar() {



    $headers = "MIME-Version: 1.1\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\n";
	$headers .= "From: ".$this->from."\n"; // remetente
	$headers .= "Return-Path: ".$this->from."\n"; // return-path
	$headers .= "Reply-To: " . $this->replyTo . "\n";


	if(isset($this->file)) {	
	
		mail($this->destinatario,  $this->subject, $this->html, $headers);
		
	} else {
		
		mail($this->destinatario,  $this->subject, $this->anexos.$this->html, $headers);
		
	}
        
	//die($this->destinatario.$this->subject.$this->html.$headers);
    }	
    
    
    
    
       
       
   
    
    
}


?>
