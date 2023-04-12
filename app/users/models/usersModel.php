<?php

class usersModel extends Model {
    
    public $table = "ih_users";


   public function get($id) {

        return $this->read( 'id_user = '.$id);

        
    }    
    
    public function getAll($n = null) {        
        
        return $this->read(null, 'id_user desc', $n);
                
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
