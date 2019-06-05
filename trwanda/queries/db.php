<?php
   	//Defining hoost and database related constants
    define("_HOST", "localhost");    
    define("_DBUNAME", "root");    
    define("_DBPWD", "");    
    define("_DBNAME", "tourisma");
class Database
{
 
    //Defining hoost and database related constants
    private $host = _HOST;
    private $db_name = _DBNAME;
    private $username = _DBUNAME;
    private $password = _DBPWD;
    public $conn;  
    public function dbConnection()
	{
     
	    $this->conn = null;    
        try
		{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
			// $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
        }
		catch(PDOException $exception)
		{
            echo "Connection error: " . $exception->getMessage();
        }
         
        return $this->conn;
    }
}
?>