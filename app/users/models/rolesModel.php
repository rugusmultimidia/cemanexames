<?php

class rolesModel extends Model {
    
    public $table = "ih_users_type";


   public function get($id) {

        return $this->read( 'id_user_type = '.$id);

        
    }    
    
    public function getAll($n = null) {        
        
        return $this->read(null, 'id_user_type desc', $n);
                
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
