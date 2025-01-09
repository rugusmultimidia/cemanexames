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

        $paciente = trim($paciente);
        
        $clinica = $_SESSION['@userApp']['clinica'];
        
        if($n) {
            $limit = 'Limit '.$n.'';
        } else {
            $limit = "";
        }

        $cpf = preg_replace('/[^0-9]/', '', $paciente);

        if (isset($_GET['ativo']) && $_GET['ativo'] == "all") {
            $q_ativo .= "E.ativo IN ('apagado','ativo')";
        }elseif (isset($_GET['ativo']) && $_GET['ativo'] != "all") {
            $ativo = $_GET['ativo'];
            $q_ativo .= "E.ativo = '$ativo'";
        }else{
            $q_ativo .= "E.ativo IN ('apagado','ativo')";
        }

        if ($_GET['clinica'] == "vazio") {
            $q_clinica .= " AND (E.clinica is null or E.clinica = '')";
        }else if (isset($_GET['clinica']) && $_GET['clinica'] != "all") {
            $clinica = $_GET['clinica'];
            $q_clinica .= " AND E.clinica = '$clinica'";
        }else{
            $q_clinica .= "";
        }

        if ($cpf) {
            $q_cpf = "
            and (
                P.cpf like '%".$cpf."%'
                OR E.cpf like '%".$cpf."%'
                OR E.codigo_paciente = '".$cpf."'
            )
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
                )
                ".$q_cpf."
                ";
        }

        $sql =    
            "SELECT 
                P.*, 
                E.*, 
                E.id_pacientes as id_paciente_exame,
                E.clinica as clinica_exame , 
                P.cpf as cpf_user, 
                E.data_nascimento as data_nascimento_exame
            from tb_exames E
             LEFT JOIN tb_pacientes P ON P.id_pacientes = E.id_pacientes
             WHERE 
                ".$q_ativo."
             ".$where."
             ".$q_clinica."

             order by E.date_update DESC ".$limit."
             " ;

        // echo "<pre>";
        // print($sql); die;            

        return $this->executeSql($sql);        
        
    } 

    public function getPacienteByCode($cpf, $codigo_paciente, $name = null) {
        $clinica = $_SESSION['@userApp']['clinica'];
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
        $where = "WHERE ativo='ativo' AND E.clinica='$clinica'";
    
        if ($cpf) {
            $where .= " AND P.cpf = '$cpf'";
        }
    
        if ($codigo_paciente) {
            $where .= " AND P.codigo_paciente = '$codigo_paciente'";
        }
    
        $sql = "SELECT P.*, E.*, E.clinica as clinica_exame, P.cpf as cpf_user
                FROM tb_exames E
                LEFT JOIN tb_pacientes P ON P.codigo_paciente = E.codigo_paciente
                $where
                ORDER BY E.id_exames DESC
                LIMIT 1";

                echo "<pre>";
                print($sql); die;
    
        return $this->executeSql($sql);
    }


    public function getPacienteByNome($nome, $data_nascimento) {
        $clinica = $_SESSION['@userApp']['clinica'];
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
        $where = "
            WHERE ativo='ativo' 
                AND P.nome='$nome'
                AND P.data_nascimento='$data_nascimento'
                
            ";
    
    
        $sql = "SELECT P.*, E.*, E.clinica as clinica_exame, P.cpf as cpf_user
                FROM tb_exames E
                LEFT JOIN tb_pacientes P ON P.codigo_paciente = E.codigo_paciente
                $where
                ORDER BY E.id_exames DESC
                LIMIT 1";

                // echo "<pre>";
                // print($sql); die;
    
        return $this->executeSql($sql);
    }

    public function getAllExamesPacienteAdmin($codigo, $n = null) {  
        
        return $this->read("codigo_paciente = '$codigo' ", $this->_id.' DESC', $n);        
        
    } 
    
    
    public function getIdPaciente($codigo, $n = null) {  

        // die($codigo);
        
        // $clinica = $_SESSION['@userApp']['clinica'] == "ceman" ? "" : "and clinica = '$clinica'";
        return $this->read("id_pacientes = '$codigo' and ativo='ativo'", $this->_id.' DESC', $n);        
        
    } 

    public function getCodPaciente($codigo, $n = null, $name=null) {  


        // die($codigo.' - '.$name);
        
        // $clinica = $_SESSION['@userApp']['clinica'] == "ceman" ? "" : "and clinica = '$clinica'";
        return $this->read("(codigo_paciente = '$codigo' OR paciente = '$name') and ativo='ativo'", $this->_id.' DESC', $n);        
        
    }  

    public function getIdPacienteResultSite($id, $n = null) {  

        $clinica = $_SESSION['@paciente']['clinica'];
        return $this->read("id_pacientes = '$id' and clinica = '$clinica' and ativo='ativo'", $this->_id.' DESC', $n);        
        
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