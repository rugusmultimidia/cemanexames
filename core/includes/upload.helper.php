<?php

class Upload {

    protected $path = "/themes/files/uploads/", $file, $fileName, $fileTmpName, $extension;

    
    public function setPath($path) {
        
        $this->path = $path;
        
    }
    
    
    public function setFile( $file ){
        
        $this->file = $file;
        //print_r($file);
        $this->setFileName();
        $this->setFileTmpName();
        
    }
    
    protected function setFileName() {
        
         $this->fileName = $this->file['name'];


         $ext = explode("/", $this->file['type']);         
		 //$ext = explode('.',$this->fileName);
         $this->extension = $ext[1];        
        
    }
    
    protected function setFileTmpName() {
        
        $this->fileTmpName = $this->file['tmp_name'];
        
        
    }
    
    
    public function _upload($fileName = null){		
		
		if($fileName) {$this->fileName = $fileName;}
		
		$this->fileName = md5($this->fileName.time()).'.'.$this->extension ;	
		
		//print $this->fileTmpName .$_SERVER['DOCUMENT_ROOT'] . $this->path . $this->fileName; die();

        if (is_dir('/var/www/exames2cemanmogi/public_html/cemanexames/themes/files/uploads/') && is_writable('/var/www/exames2cemanmogi/public_html/cemanexames/themes/files/uploads/')) {
            if (move_uploaded_file($this->fileTmpName, getcwd() . $this->path . $this->fileName)) {
              die('O arquivo foi movido com sucesso!');
            } else {
              die('Houve um erro ao mover o arquivo.');
            }
          } else {
            die('O diretório de destino não existe ou não tem permissão de gravação.');
          }
		
        // if (move_uploaded_file($this->fileTmpName, getcwd() . $this->path . $this->fileName))
            
        //         return $this->fileName;
        // else
        //     // echo "<pre>";
        //     // print_r(error_get_last());
        //     // die();
        //         return false ;
     
        
    }
    
    
    
    
}




?>
