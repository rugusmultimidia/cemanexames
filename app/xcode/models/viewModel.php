<?php

class viewModel extends Model {
    
    public $table = "ih_views";

	
	

   public function get($id) {

        return $this->read( 'id_controller = '.$id);

        
    }
    
    
    public function getAll($n = null) {        
        
        return $this->read(null, 'id_controller desc', $n);
                
    }
	
	public function drop($table){
		
			return $this->executeSqlUpdate('DROP TABLE '.$table);	
	}
	
	

	
	private function field_type($type, $multiple = false){		

		if($type == "textfield") {			
			$t = 'VARCHAR(255)';			
		}
		if($type == "image") {			
			$t = 'VARCHAR(255)';			
		}
		if($type == "entity") {			
			if($multiple == 'true') {
					$t = 'TEXT';
				} else {			
					$t = 'int'; 
			}		
		}
		if($type == "textarea") {			
			$t = 'TEXT';		
		}
		if($type == "select") {			
			$t = 'VARCHAR(255)';			
		}
		if($type == "taxonomy") {			
			$t = 'int';			
		}
		if($type == "boolean") {			
			$t = 'boolean';			
		}
		
		return $t;
		
	}
    
    
    public function save(array $dados) {
            
        return $this->insert($dados);
        
    }
    
    public function edit(array $dados, $where) {
        
        return $this->update($dados, $where);
        
    }
    
    public function del($where){
        
        return $this->delete($where);
        
    }
    
      public function count($where = null, $order = null, $limite = null) {
        
        return $this->countLines($this->table, $where, $order, $limite);
        
    }
    
    
        
 }

 



?>
