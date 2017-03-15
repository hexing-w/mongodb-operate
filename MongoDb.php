<?php

class MongoDb{
	private $con;
	private $db;

	public function __construct($conf) 
	{
		$this->con = new MongoDB\Driver\Manager($conf['host']);
		$this->db = $conf['db'];
	}

	public function insert($tb, $param)
	{
		$bulk = new MongoDB\Driver\BulkWrite;
		//设置操作级别
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);  
		$bulk->insert($param);
		try {  
    		$result = $this->con->executeBulkWrite($this->db.".".$tb, $bulk, $writeConcern);
    		return true;  
		} catch (MongoDB\Driver\Exception\BulkWriteException $e) {  
    		$result = $e->getWriteResult();  
    		return false;
		}
	}

	public function query($tb, $filter = [], $options = [])
	{
		$query = new MongoDB\Driver\Query($filter, $options);  
		$readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);
		$cursor = $this->con->executeQuery($this->db.".".$tb, $query, $readPreference);
		foreach ($cursor as $key ) {
		  	$doc[]=$key;
		  }  
		return $doc;
	}


	public function command($param) {  
        $cmd = new MongoDB\Driver\Command($param);  
        return $this->con->executeCommand($this->db, $cmd);  
    }  

    public function delete($tb, $param){
    	$bulk = new MongoDB\Driver\BulkWrite;
    	//设置操作级别
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);  
    	$bulk->delete($param);
    	try {  
    		$result = $this->con->executeBulkWrite($this->db.".".$tb, $bulk, $writeConcern);
    		return true;  
		} catch (MongoDB\Driver\Exception\BulkWriteException $e) {  
    		$result = $e->getWriteResult();  
    		return false;
		}
    }

    public function update($tb, $where , $param ,$upsert = []){
    	$bulk = new MongoDB\Driver\BulkWrite;
    	//设置操作级别
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000); 
    	$bulk->update($where ,$param , $upsert );
    	try {  
    		$result = $this->con->executeBulkWrite($this->db.".".$tb, $bulk, $writeConcern);
    		return true;  
		} catch (MongoDB\Driver\Exception\BulkWriteException $e) {  
    		$result = $e->getWriteResult();  
    		return false;
		}
    }
}
