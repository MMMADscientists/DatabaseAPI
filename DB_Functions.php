<?php
 
class DB_Functions {
 
    private $db;
    private $mysql;
 
    //put your code here
    // constructor
    function __construct() {
        echo " creating db_functions";
        try{
        require_once 'DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->mysql = $this->db->connect();
        }catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
       }
    
    }
 
    // destructor
    function __destruct() {
         
    }
 
    /**
     * DO NOT USE YET
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $email, $password) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        $result = $this->mysql->query("INSERT INTO User(uid, username, password, email, salt) VALUES('$uuid', '$name', '$password', '$email', '$salt' ");
        // check for successful store
        if ($result) {
            // get user details 
            $uid = mysql_insert_id(); // last inserted id
            $result = $this->mysql->query("SELECT * FROM User WHERE uid = $uid");
            // return user details
            return mysql_fetch_array($result);
        } else {
            return false;
        }
    }
 
    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {
        $result = $this->mysql->query("SELECT * FROM User WHERE email = '$email'") or die(mysql_error());
        // check for result 
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            $salt = $result['salt'];
            $encrypted_password = $result['password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $result;
            }
        } else {
            // user not found
            return false;
        }
    }
 
    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
        $result = $this->mysql->query("SELECT email from User WHERE email = '$email'");
        if(!$result){return false;}
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            // user existed 
            return true;
        } else {
            // user not existed
            return false;
        }
    }
    
    public function getHouseData($name){
        $result = $this->mysql->query("SELECT * FROM Property WHERE username = '$name'");
        $no_of_rows = mysql_num_rows($result);
        if(no_of_rows > 0){
             //user has houses
             return $result;
        }
        else{
            //user has no homes created
            return false;
        }
    }
 
    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
 
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {
 
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
 
        return $hash;
    }
 
}
 
?>
