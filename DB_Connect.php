<?php
 
class DB_Connect {
 
    private $db;
 
    // constructor
    function __construct() {
         
    }
 
    // destructor
    function __destruct() {
        // $this->close();
    }
 
    // Connecting to database
    public function connect() {
        require_once 'config.php';
        // connecting to mysql
        $con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD,DB_DATABASE);
        // selecting database (depreciated, moved to connect stmt)
        //mysql_select_db(DB_DATABASE);
        $db = $con;
        // return database handler
        return $con;
    }
 
    // Closing database connection
    public function close() {
        $db->close();
    }
 
}
 
?>
