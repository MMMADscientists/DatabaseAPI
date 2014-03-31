<?php
require_once 'config.php';

class DB_Functions {
    // connecting to mysql
    private $mysql;// = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_DATABASE);

    // constructor
    function __construct() {
        $this -> mysql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD,DB_DATABASE)or die("Error " . mysqli_error($mysql));
        /*if ($this -> mysql->connect_errno) {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
        else{
            echo "Connected to MySQL" . PHP_EOL;
        }*/
        /*try{
        require_once 'DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->mysql = $this->db->connect();
        }catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
       }*/
    
    }
 
    // destructor
    function __destruct() {
         
    }
    /**
     * 
     */
     public function changeRoomName($roomID, $newName){
         $result = $this->mysql->query("UPDATE Room SET name = '$newName' WHERE idRoom = '$roomID'");
         if($result) return $result;
         else return false;
     }
     
     public function changePropertyName($propertyID, $newName){
         $result = $this->mysql->query("UPDATE Property SET address = '$newName' WHERE idProperty = '$propertyID'");
         if($result) return $result;
         else return false;
     }
 
 
    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $email, $password) {
        //$uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        //echo PHP_EOL . "INSERT INTO User(username, password, email, salt) VALUES( '$name', '$password', '$email', '$salt' )";
        $result = $this->mysql->query("INSERT INTO User(username, password, email, salt) VALUES( '$name', '$password', '$email', '$salt' )");
        // check for successful store
        if ($result) {
            // get user details 
            $uid = $this->mysql->insert_id; // last inserted id
            $result = $this->mysql->query("SELECT * FROM User WHERE uid = $uid");
            // return user details
            //echo PHP_EOL;
            //echo  json_encode($result->fetch_array(MYSQLI_ASSOC));
            return $result->fetch_array(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
 
    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($username, $password) {
     //echo"email = $email" . PHP_EOL . "password = $password" . PHP_EOL;
        $result = $this->mysql->query("SELECT * FROM User WHERE username = '$username'") or die(mysql_error());
        // check for result 
        $no_of_rows = $this->mysql->affected_rows;
        if ($no_of_rows > 0) {
            $result = $result->fetch_array(MYSQLI_ASSOC);
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
    public function isUserExisted($username) {
        $result = $this->mysql->query("SELECT email from User WHERE username = '$username'");
        if(!$result){return false;}
        $no_of_rows = $this->mysql->affected_rows;
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
        $no_of_rows = $this->mysql->affected_rows;
        if($no_of_rows > 0){
             //user has houses
             $result = $result->fetch_array(MYSQLI_ASSOC);
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
        $encrypted = $this->checkhashSSHA($salt,$password);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
 
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {
 
        //$hash = base64_encode(sha1($password . $salt) . $salt);
 
        //return $hash;
        return $password;
    }
 
}
 
?>
