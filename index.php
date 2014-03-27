<?php
/**
 * File to handle all API requests
 * Accepts GET and POST
 * 
 * Each request will be identified by TAG
 * Response will be JSON data
 
  /**
 * check for POST request 
 */
    register_shutdown_function('errorHandler');

function errorHandler() { 
    $error = error_get_last();
    $type = $error['type'];
    $message = $error['message'];
    if ($type = 64 && !empty($message)) {
        echo "
            <strong>
              <font color=\"red\">
              Fatal error captured:
              </font>
            </strong>
        ";
        echo "<pre>";
        print_r($error);
        echo "</pre>";
    }
} 
 
if (isset($_POST['tag']) && $_POST['tag'] != '' ) {
    // get tag
    $tag = $_POST['tag'];
    $post = true;
}
else{
    $tag = $_GET["tag"];
    $post = false;
    //echo "using GET, $tag ";
}
    try{
        if(!file_exists('/var/www/API/DB_Functions.php')){
            echo "file does not exist";
        }else{
            echo "file found" .PHP_EOL;
        }
        require_once '/var/www/API/DB_Functions.php';
        //echo " required file included" . PHP_EOL;
        $db = new DB_Functions();
    }catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    
    //echo "creating response:" . PHP_EOL;
    // response Array
    $response = array("tag" => $tag, "success" => 0, "error" => 0);
 
    // check for tag type
    if ($tag == 'login') {
        if($post){
        // Request type is check Login
           $name = $_POST['username'];
           $password = $_POST['password'];
        }
        else{
           $name = $_GET['username'];
           $password = $_GET['password'];
        }
 
        // check for username
        $username = $db->getUserByEmailAndPassword($name, $password);
        if ($username != false) {
            // username found
            // echo json with success = 1
            $response["success"] = 1;
            $response["uid"] = $username["uid"];
            $response["username"]["name"] = $username["username"];
            $response["username"]["email"] = $username["email"];
            //$response["username"]["created_at"] = $username["created_at"];
            //$response["username"]["updated_at"] = $username["updated_at"];
            echo json_encode($response);
        } else {
            // username not found
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "Incorrect username or password!";
            echo json_encode($response);
        }
    } else if ($tag == 'register') {
        if($post){
        // Request type is Register new username
            $name = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
        }
        else{
            $name = $_GET['username'];
            $email = $_GET['email'];
            $password = $_GET['password'];
            //echo "name = $name \n";
            //echo "email = $email \n";
            //echo "password = $password \n";
        }
 
        // check if username is already existed
        if ($db->isUserExisted($email)) {
            // username is already existed - error response
            $response["error"] = 2;
            $response["error_msg"] = "username already existed";
            echo json_encode($response);
        } else {
            // store username
            $username = $db->storeUser($name, $email, $password);
            if ($username) {
                // username stored successfully
                $response["success"] = 1;
                $response["uid"] = $username["uid"];
                $response["username"]["name"] = $username["username"];
                $response["username"]["email"] = $username["email"];
                //$response["username"]["created_at"] = $username["created_at"];
                //$response["username"]["updated_at"] = $username["updated_at"];
                echo json_encode($response);
            } else {
                // username failed to store
                $response["error"] = 1;
                $response["error_msg"] = "Error occured in Registartion";
                echo json_encode($response);
            }
        }
    }else if ($tag == "houses"){
         if($post){
             $name = $_POST["username"];
             $tuples = $db->getHouseData($name);
         }
         else{
             $name = $_POST["username"];
             $tuples = $db->getHouseData($name);
         }
         
         if($username){
             $response["success"] = 1;
             $response["tuples"] = $tuples;
             echo json_encode($response);
         }else{
             $response["error"] = 1;
             $response["error_code"] = 0;
             $response["error_msg"] = "No data found";
             echo json_encode($response);
         }
    
    }else {
        echo "Invalid Request";
    }
?>
