<?php

class xcodeModel extends Model {
    
    public $table = "ih_controller";

	
	

   public function get($id) {

        return $this->read( 'id_controller = '.$id);

        
    }
    
    
    public function getAll($n = null) {        
        
        return $this->read(null, 'id_controller desc', $n);
                
    }
	
	public function drop($table){
		
			return $this->executeSqlUpdate('DROP TABLE '.$table);	
	}
	
	
	public function createTable($table, $data){
		
		$__fields = '';
		
		foreach($data['field'] as $_field) {
			
			if($_field['field_type'] <> 'markup'){
			
				$__fields .= $_field['field_machine_name'].' '.$this->field_type($_field['field_type'],$_field['field_multiple_values']).',';
			
			}
		}
		
		$__fields .= 'id_user int, date_created datetime, date_update datetime';

		return $this->executeSqlUpdate('
			CREATE TABLE tb_'.strtolower(functions::removeSpecialChars($table)).' (
			id_'.strtolower($table).' INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
			'.$__fields.' )
		');	
		
	}
	
	public function updateTable($table, $data){		
		
		foreach($data['field'] as $_field) {
			
			if($_field['field_type'] <> 'markup'){
			
				if(isset($_field['field_storage']) and !empty($_field['field_machine_name_old'])){
					
					//Update the field name in table.
					$this->executeSqlUpdate('ALTER TABLE `tb_'.strtolower(functions::removeSpecialChars($table)).'` 
					CHANGE `'.$_field['field_machine_name_old'].'` `'.$_field['field_machine_name'].'` '.$this->field_type($_field['field_type'],$_field['field_multiple_values'] ).' ');
					
				} elseif(!isset($_field['field_storage']) and !isset($_field['deleted'])) {
					
					$this->executeSqlUpdate('ALTER TABLE `tb_'.strtolower(functions::removeSpecialChars($table)).'` 
					ADD `'.$_field['field_machine_name'].'`  '.$this->field_type($_field['field_type'],$_field['field_multiple_values']).' ');				
					
				}
				
				if(isset($_field['deleted'])) {
					
					$this->executeSqlUpdate('ALTER TABLE `tb_'.strtolower(functions::removeSpecialChars($table)).'` DROP `'.$_field['deleted'].'`');
				}	
			
			}
		}
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
		
		if($type == "radio") {			
			$t = 'VARCHAR(255)';			
		}
		if($type == "checkbox") {			
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
