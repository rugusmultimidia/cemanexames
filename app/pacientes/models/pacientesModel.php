<?php



class pacientesModel extends Model {

    

   public $table = 'tb_pacientes';



   public $_id = 'id_pacientes';   

   

   public function get($id) {



        return $this->read( $this->_id.' = '.$id);

		

    } 



   public function getCode($id) {



        return $this->read( 'codigo_paciente = "'.$id.'" ');

        

    } 


	

	public function getValue($id, $field) {

        $result = $this->read( $this->_id.' = '.$id);		

		return $result[0][$field];	

    }    

    

    public function getAll($n = null) {

        return $this->read("status='ativo'", $this->_id.' DESC', $n);

    }    

    public function cleanCPF($cpf) {
        // Remove any non-numeric characters
        return preg_replace('/\D/', '', $cpf);

    }

    public function validateCPF($cpf) {
        $cleanedCPF = $this->cleanCPF($cpf);
        if (ctype_digit($cleanedCPF)) {
            return $cleanedCPF;
        } else {
            return $cpf;
        }
    }

    public function getSearch($n = '', $l = "", $id_dependente = null) {

        // die($_SESSION['@userApp']['clinica']);

        $clinica = $_SESSION['@userApp']['clinica'];

        $cpf = $this->validateCPF($n);  

        if(is_null($id_dependente)){

            $query = "
                id_pacientes is not null
                and (
                    nome LIKE '%".$n."%'
                    OR cpf = '".$cpf."'
                    OR codigo_paciente = '".$cpf."'
                    OR id_pacientes = '".$cpf."'
                    OR email = '%".$n."%'
                ) 
                and status = 'ativo' 
                #and clinica = '$clinica'";

                // die($query);

            return $this->read($query,'nome ASC', $l);
        }else{
            return $this->read("nome LIKE '%".$n."%' and status = 'ativo'  and id_responsavel = ".$id_dependente, 'nome ASC', $l);

        }

    }  
    
    public function checkCPF($cpf) {
        $result = $this->read('cpf = "'.$cpf.'"');
        return !empty($result);
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