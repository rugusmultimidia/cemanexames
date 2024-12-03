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
        
        return $this->read(null, $this->_id.' DESC');        
        
    }  

    public function getAllNull($n = null) {   
        
        return $this->read("id_pacientes is null", 'date_update DESC', 10);        
        
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
             LEFT JOIN tb_pacientes P
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

        $cpf = preg_replace('/[^0-9]/', '', $paciente);

        if($cpf) {
            $q_cpf = "
                OR P.cpf like '%".$cpf."%'
                OR E.cpf like '%".$cpf."%'
                OR E.codigo_paciente = '".$cpf."'
                ";
        }

        if($paciente) {
            $where = "
                and (
                    P.nome like '%".$paciente."%' 
                    OR E.paciente like '%".$paciente."%' 
                    OR P.id_pacientes like '%".$paciente."%' 
                    OR E.id_pacientes like '%".$paciente."%' 
                    OR P.email like '%".$paciente."%' 
                    OR P.codigo_paciente like '%".$paciente."%' 
                )";
        }

        $sql =    "SELECT P.*, E.*, E.clinica as clinica_exame , P.cpf as cpf_user, E.data_nascimento as data_nascimento_exame
            from tb_exames E
             LEFT JOIN tb_pacientes P ON P.id_pacientes = E.id_pacientes
             WHERE E.clinica='$clinica' AND E.ativo='ativo'
             ".$where."
             order by E.date_update DESC ".$limit."
             " ;

        // echo "<pre>";
        // print($sql); die;            

        return $this->executeSql($sql);        
        
    } 

    public function getPacienteByCode($cpf, $codigo_paciente) {
        $clinica = $_SESSION['@userApp']['clinica'];
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
        $where = "WHERE ativo='ativo' AND E.clinica='$clinica'";
    
        if ($cpf) {
            $where .= " AND P.cpf LIKE '%$cpf%'";
        }
    
        if ($codigo_paciente) {
            $where .= " AND P.codigo_paciente LIKE '%$codigo_paciente%'";
        }
    
        $sql = "SELECT P.*, E.*, E.clinica as clinica_exame, P.cpf as cpf_user
                FROM tb_exames E
                LEFT JOIN tb_pacientes P ON P.codigo_paciente = E.codigo_paciente
                $where
                ORDER BY E.id_exames DESC
                LIMIT 1";
    
        return $this->executeSql($sql);
    }

    public function getAllExamesPacienteAdmin($codigo, $n = null) {  
        
        return $this->read("codigo_paciente = '$codigo' ", $this->_id.' DESC', $n);        
        
    } 
    
    
    public function getIdPaciente($codigo, $n = null) {  

        // die($codigo);
        
        $clinica = $_SESSION['@userApp']['clinica'];
        return $this->read("id_pacientes = '$codigo' and clinica = '$clinica'", $this->_id.' DESC', $n);        
        
    } 

    public function getCodPaciente($codigo, $n = null, $name=null) {  


        // die($codigo.' - '.$name);
        
        $clinica = $_SESSION['@userApp']['clinica'] == "ceman" ? "" : "and clinica = '$clinica'";
        return $this->read("(codigo_paciente = '$codigo' OR paciente = '$name') $clinica", $this->_id.' DESC', $n);        
        
    }  

    public function getIdPacienteResultSite($id, $n = null) {  

        $clinica = $_SESSION['@paciente']['clinica'];
        return $this->read("id_pacientes = '$id' and clinica = '$clinica'", $this->_id.' DESC', $n);        
        
    }  

    public function getAllExamesPaciente($id, $n = null) {  
        
        return $this->read("codigo_paciente = '$id'and clinica='ceman'", $this->_id.' DESC', $n);        
        
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