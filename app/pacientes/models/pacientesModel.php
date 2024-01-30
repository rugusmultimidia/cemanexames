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

        return $this->read(null, $this->_id.' DESC', $n);

    }    

    public function getSearch($n = '', $l = "", $id_dependente = null) {

        // die($_SESSION['@userApp']['clinica']);

        $clinica = $_SESSION['@userApp']['clinica'];

        if(is_null($id_dependente))
            return $this->read("nome LIKE '%".$n."%'  ", 'nome ASC', $l);
        else
            return $this->read("nome LIKE '%".$n."%'  and id_responsavel = ".$id_dependente, 'nome ASC', $l);



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