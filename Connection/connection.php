<?php 
class DatabaseConnection{
    private $hostname="localhost";
    private $username="root";
    private $password="";
    private $dbname="stock";
    private $conn;
    // private $PDO;


    function __construct()
        {
            try{
                $this->conn=new PDO("mysql:host=$this->hostname; dbname=$this->dbname",
                $this->username,
                $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            }catch(PDOException $e){
                die ("Connection failed" .$e->getMessage());
            }
        }
        public function getConnection(){

            return $this->conn;
        }
    
}


?>