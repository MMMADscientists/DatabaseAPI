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
    
    function resultToArray($result) {
        $rows = array();
        while($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
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
        $result = $this->mysql->query("INSERT INTO User(username, password, email, salt) VALUES( '$name', '$encrypted_password', '$email', '$salt' )");
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
            if ($encrypted_password == substr($hash,0,45)) {
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
             $rows = $this->resultToArray($result);
             return $rows;
        }
        else{
            //user has no homes created
            return false;
        }
    }
    
    public function gethouseDataForRooms($propertyID){
        $result = $this->mysql->query("SELECT * FROM Property WHERE idProperty = '$propertyID'");
        $no_of_rows = $this->mysql->affected_rows;
        if($no_of_rows > 0){
             $rows = $this->resultToArray($result);
             return $rows;
        }
        else{
            //user has no homes created
            return false;
        }
    }
    
    public function getRoomFromHouse($propertyID){
        $result = $this->mysql->query("SELECT * FROM Room WHERE idProperty = '$propertyID'");
        $no_of_rows = $this->mysql->affected_rows;
        if($no_of_rows > 0){
             $rows = $this->resultToArray($result);
             return $rows;
        }
        else{
            //user has no homes created
            return false;
        }
    }
    
    public function getConnections($roomID){
        $result = $this->mysql->query("SELECT * FROM Connection WHERE idSource = '$roomID'");
        $no_of_rows = $this->mysql->affected_rows;
        if($no_of_rows > 0){
             $rows = $this->resultToArray($result);
             return $rows;
        }
        else{
            //user has no homes created
            return false;
        }
    }
    
    public function createRoom($name, $propertyID, $url){
        $result = $this->mysql->query("INSERT INTO Room(name,idProperty,roomURL) VALUES('$name', '$propertyID', '$url')");
        if ($result) {
            // get user details 
            $uid = $this->mysql->insert_id; // last inserted id
            $result = $this->mysql->query("SELECT * FROM Room WHERE idRoom = $uid");
            // return user details
            //echo PHP_EOL;
            //echo  json_encode($result->fetch_array(MYSQLI_ASSOC));
            return $result->fetch_array(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
    
    public function createHouse($address, $username, $url, $defaultRoom){
        $result = $this->mysql->query("INSERT INTO Property(address, username, houseURL, idDefaultRoom) VALUES('$address', '$username', '$url', '$defaultRoom')");
        if ($result) {
            // get user details 
            $uid = $this->mysql->insert_id; // last inserted id
            $result = $this->mysql->query("SELECT * FROM Property WHERE idProperty = $uid");
            // return user details
            //echo PHP_EOL;
            //echo  json_encode($result->fetch_array(MYSQLI_ASSOC));
            return $result->fetch_array(MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
    
    public function createConnection($source, $destination, $doorX, $doorY, $doorZ){
        $result = $this->mysql->query("INSERT INTO Connection(idSource, idDestination, doorX, doorY, doorZ) VALUES('$source', '$destination', '$doorX', '$doorY', '$doorZ')");
        if($result){
            $uid = $this->mysql->insert_id; // last inserted id
            $result = $this->mysql->query("SELECT * FROM Connection WHERE idConnection = $uid");
            return $result->fetch_array(MYSQLI_ASSOC);
        }
        else return false;
    }
    
    public function deleteRoom($roomID){
        $result = $this->mysql->query("DELETE FROM Room WHERE idRoom = '$roomID'");
        $no_of_rows = $this->mysql->affected_rows;
        if($no_of_rows > 0){
             return true;
        }
        else{
            //user has no homes created
            return false;
        }
    }
    
    public function deleteProperty($propertyID){
        $result = $this->mysql->query("DELETE FROM Property WHERE idProperty = '$propertyID'");
        $no_of_rows = $this->mysql->affected_rows;
        if($no_of_rows > 0){
             $result2 = $this->mysql->query("DELETE FROM Room WHERE idProperty = '$propertyID'");
             $no_of_rows = $this->mysql->affected_rows;
             if($no_of_rows > 0){
                 return true;
             }
             return false;
        }
        else{
            //user has no homes created
            return false;
        }
    }
    
    public function deleteConnection($connectionID){
        $result = $this->mysql->query("DELETE FROM Connection WHERE idConnection = '$connectionID'");
        $no_of_rows = $this->mysql->affected_rows;
        if($no_of_rows > 0){
             return true;
        }
        else{
            //user has no homes created
            return false;
        }
    }
    
    public function changeRoomURL($roomID, $URL){
        $result = $this->mysql->query("UPDATE Room SET(roomURL = '$URL') WHERE idRoom = '$roomID'");
        if($result) return $result;
        else return false;
    }
    
    public function changeConnectiontarget($connectionID, $doorX, $doorY, $doorZ){
        $result = $this->mysql->query("UPDATE Connection SET(doorX = '$doorX', doorY='$doorY', doorZ='$doorZ') WHERE idConnection = '$connectionID'");
        if($result) return $result;
        else return false;
    }
    
    public function changeHouseURL($propertyID, $URL){
        $result = $this->mysql->query("UPDATE Property SET(houseURL = '$URL') WHERE idProperty = '$propertyID'");
        if($result) return $result;
        else return false;
    }
    
    public function changeHouseDefaultRoom($propertyID, $defaultRoom){
        $result = $this->mysql->query("UPDATE Property SET(idDefaultRoom = '$defaultRoom') WHERE idProperty = '$propertyID'");
        if($result) return $result;
        else return false;
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
 
        $hash = base64_encode(sha1($password . $salt) . $salt);
 
        return $hash;
        //return $password;
    }
 
}
 
?>
