<?php

class Db{
	
	private static $instance;
	private $link;
	protected $dsn;
	private static $pdo = null;
    public static $statement = null;
    private static $is_addsla = false;
    public static $options = array(
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ",
    );

    private function __construct($config = [])
    {
        $config = MConfig::get('db');
        $host=$config['host'];
        $user=$config['user'];
        $pass=$config['pass'];
        $dbname=$config['dbname'];
        $persistent=$config['persistent'];
        $charset=$config['charset'];
        if(!strpos(self::$options[\PDO::MYSQL_ATTR_INIT_COMMAND], $charset))
        {
            self::$options[\PDO::MYSQL_ATTR_INIT_COMMAND] .= $charset;
        }
        if($persistent){
            self::$options[\PDO::ATTR_PERSISTENT] = true;
        }
        $this->dsn = "mysql:host={$host};dbname={$dbname}";
        try {  
                self::$pdo = new PDO($dsn,$user, $pass,self::$options);  
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);  
            } catch (PDOException $e) {  
                print "Error:".$e->getMessage()."<br/>";  
                die();  
            }  
    }  

    private function __clone()
    {

    }

    public static function getInstance()
    {
    	if(!(self::$pdo instanceof Db))
    	{
    		self::$pdo = new self();
    	}
    	return self::$pdo;

    }
}