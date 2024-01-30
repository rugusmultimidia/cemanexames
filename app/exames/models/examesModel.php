<?php

class examesModel extends Model {
    
   public $table = 'tb_exames';

   public $_id = 'id_exames';   
   
   public function get($id) {

        return $this->read( $this->_id.' = '.$id);
		
    } 
	
	public function getValue($id, $field) {

        $result = $this->read( $this->_id.' = '.$id);
		
		return $result[0][$field];
		
    }    
    
    public function getAll($n = null) {        
        
        return $this->read(null, $this->_id.' DESC', $n);        
        
    }  

    public function countPacients($paciente, $n = null) {

         if($n) {
            $limit = 'Limit '.$n.'';
        } else {
            $limit = "";
        }


        if($paciente) {
            $where = "where P.nome like '%".$paciente."%'";
        }


        $sql = "SELECT count(E.id_exames) as total from tb_exames E
             INNER JOIN tb_pacientes P
             ON P.codigo_paciente = E.codigo_paciente
             ".$where."
             order by E.id_exames DESC ".$limit."
             " ;

             //print($sql); die;

        return $this->executeSql($sql);        

    }

     public function getAllByPacienteName($paciente, $n = null) {    
        
        $clinica = $_SESSION['@userApp']['clinica'];
        
        if($n) {
            $limit = 'Limit '.$n.'';
        } else {
            $limit = "";
        }

        if($paciente) {
            $where = "where P.nome like '%".$paciente."%'";
        }

        $sql =    "SELECT P.*, E.*, E.clinica as clinica_exame from tb_exames E
             INNER JOIN tb_pacientes P
             ON P.codigo_paciente = E.codigo_paciente
             ".$where."
             order by E.id_exames DESC ".$limit."
             " ;

        return $this->executeSql($sql);        
        
    } 

    public function getAllExamesPacienteAdmin($codigo, $n = null) {  
        
        return $this->read("codigo_paciente = '$codigo' ", $this->_id.' DESC', $n);        
        
    }  

    public function getAllExamesPaciente($codigo, $n = null) {  
        
        return $this->read("codigo_paciente = '$codigo'and clinica='ceman'", $this->_id.' DESC', $n);        
        
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