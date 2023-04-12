<?php

class model {
    
    protected $db;
    
    public $table, $message;
    
    public function __construct() {
        
        $this->message = new Message();
        
        $this->db = new PDO('mysql:host='.HOST.';dbname='.DBNAME.'', ''.USER.'', ''.PASS.'', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); 
        
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
    }
    
    
    public function insert(array $dados) {
                
        $campos = implode(', ', array_keys($dados));
        
        $valores = "'" . implode("','", array_values($dados)) . "'";
        
        if(MYSQL_LOG) {
            $this->message->setMsg("log","INSERT INTO {$this->table} ({$campos}) VALUES ({$valores})")->getMsg();
        }
		
        return $this->db->query("INSERT INTO {$this->table} ({$campos}) VALUES ({$valores})");
        
    }
    
    
    public function read($where = null, $order = null, $limite = null ) {
        
        $where = ($where != null ? "WHERE {$where}" : "");
        
        $limite = ($limite != null ? "LIMIT {$limite}" : "");
        
        $order = ($order != null ? "ORDER BY {$order}" : "");
		
        if(MYSQL_LOG) {
            $this->message->setMsg("log","SELECT * FROM {$this->table} {$where} {$order} {$limite}")->getMsg();
        }
        
        $q = $this->db->query("SELECT * FROM {$this->table} {$where} {$order} {$limite}");
        
        $q->setFetchMode(PDO::FETCH_ASSOC);
        
        return $q->fetchAll();
        
    }
    
    
    public function update(array $dados, $where) {
        
        foreach ($dados as $inds => $vals) {
            
            $campos[] = "{$inds} = '{$vals}'";
            
        }
        
        $campos = implode(", ", $campos);
		
		if(MYSQL_LOG) {
            $this->message->setMsg("log","UPDATE {$this->table} SET {$campos} WHERE {$where}")->getMsg();
        }
        
        return $this->db->query("UPDATE {$this->table} SET {$campos} WHERE {$where}");
        
        
    }
    
    
    public function delete($where) {
        
        if(MYSQL_LOG) {
             $this->message->setMsg("log","DELETE FROM {$this->table} WHERE {$where}")->getMsg();
        }

        return $this->db->query("DELETE FROM {$this->table} WHERE {$where}");
        
    }
    
    
    public function countLines($table, $where = null, $order = null, $limite = null ) {
        
        $where = ($where != null ? "WHERE {$where}" : "");
        
        $limite = ($limite != null ? "LIMIT {$limite}" : "");
        
        $order = ($order != null ? "ORDER BY {$order}" : "");   
		
		//echo "SELECT * FROM {$table} {$where} {$order} {$limite}"  ;   
        
        $q = $this->db->query("SELECT * FROM {$table} {$where} {$order} {$limite}");
        
        return $q->rowCount(); 

        
    }
 
    
    
    public function executeSql($sql) {
		
		$q = $this->db->query($sql);
		
		$q->setFetchMode(PDO::FETCH_ASSOC);
        
        return $q->fetchAll();

        
    }

 public function executeSqlUpdate($sql) {
		
		
		return $this->db->query($sql);	

        
    }
	
	
	public function getLastId() {
		
		$this->db->query("select * from {$table}"); 

		return $this->db->lastInsertId();
		
	}
    
    
    
    
}


?>
