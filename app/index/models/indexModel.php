<?php

class IndexModel extends Model {
    
    public $table = "users";

	
	public function toDate($date) {
		
		return implode("-", array_reverse(explode("/", $date))); // 1988-07-22
		
	}



    public function getallMedicamentos(){

        $sql = "SELECT id, medicamento from tb_medicacao GROUP BY medicamento order by medicamento ASC ";

        return $this->executeSql($sql);

    }


    public function getInteracoes($post_medicamento){

        $med = array();

        //print_r($medicamento);

        foreach ($post_medicamento as $val) {
            
            $med[] = "'$val'";
            
        }        

        $medicament = implode(",", $med);

        $sql = "SELECT * from tb_medicacao WHERE medicamento IN ($medicament) order by interage ASC ";

         
        $result = $this->executeSql($sql);


        /*----- pega todas interacoes -----*/
        $interacao = array();

        foreach ($result as $value) {
            $interacao[] = $value['interage'];  
        }


        //print_r($interacao);

        /*----- verifica se algum resultado interage -----*/
        $data = array();
        foreach ($result as $key => $value) {

            $data[$key] = $value;
            $data[$key]['conflito'] = false;

            
           if(in_array($data[$key]['medicamento'], $interacao)){
               $data[$key]['conflito'] = true;  
           }
     

        }

        return $data;
       // print_r($data);


    }
    
    
        
 }

 



?>
