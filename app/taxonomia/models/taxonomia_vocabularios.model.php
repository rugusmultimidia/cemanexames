<?php

class taxonomia_vocabularios extends Model {
    
    public $table = "ih_taxonomy";
	public $_id = 'id_taxonomy';
	

   public function get($id) {

        return $this->read( $this->_id.' = '.$id);

        
    }

    
    public function getAll($n = null) {
        
        
        return $this->read(null, 'taxonomy asc', $n);

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
