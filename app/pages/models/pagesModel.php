<?php

class pagesModel extends Model {
    
   public $table = 'ih_page';

   public $_id = 'id_page';   
   
   public function get($path) {

        return $this->read( 'path = "'.$path.'" ');
		
    } 
	
	public function getValue($id, $field) {

        $result = $this->read( $this->_id.' = '.$id);
		
		return $result[0][$field];
		
    }    
    
    public function getAll($n = null) {        
        
        return $this->read(null, $this->_id.' DESC', $n);        
        
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