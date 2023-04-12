<?php

class TermosModel extends Model {
    
    public $table = "ih_taxonomy_entity";
	public $_id = 'id_entity';
	

   public function get($id) {

        return $this->read( $this->_id.' = '.$id);

        
    }

    
    public function getAll($vocabulario) {
        
        
        return $this->read($this->_id.' = '.$vocabulario, 'taxonomy_entity asc', null);

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
