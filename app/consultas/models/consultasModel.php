<?php

class consultasModel extends Model {
    
   public $table = 'tb_consultas';

   public $_id = 'id_consultas';   
   
   public function get($id) {

        return $this->read( $this->_id.' = '.$id);
		
    } 
	
	public function getValue($id, $field) {

        $result = $this->read( $this->_id.' = '.$id);
		
		return $result[0][$field];
		
    }    
    
    public function getAll($id_paciente, $n = null) {        
        
        return $this->read('id_paciente = '.$id_paciente, $this->_id.' DESC', $n);        
        
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