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
if (isset($_POST['tag']) && $_POST['tag'] != '' ) {
    // get tag
    $tag = $_POST['tag'];
    $post = true;
}
else{
    $tag = $_GET["tag"];
    $post = false;
    echo "using GET \n";
}
 
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("tag" => $tag, "success" => 0, "error" => 0);
 
    // check for tag type
    if ($tag == 'login') {
        if($post){
        // Request type is check Login
           $email = $_POST['email'];
           $password = $_POST['password'];
        }
        else{
           $email = $_GET['email'];
           $password = $_GET['password'];
        }
 
        // check for username
        $username = $db->getUserByEmailAndPassword($email, $password);
        if ($username != false) {
            // username found
            // echo json with success = 1
            $response["success"] = 1;
            $response["uid"] = $username["unique_id"];
            $response["username"]["name"] = $username["name"];
            $response["username"]["email"] = $username["email"];
            //$response["username"]["created_at"] = $username["created_at"];
            //$response["username"]["updated_at"] = $username["updated_at"];
            echo json_encode($response);
        } else {
            // username not found
            // echo json with error = 1
            $response["error"] = 1;
            $response["error_msg"] = "Incorrect email or password!";
            echo json_encode($response);
        }
    } else if ($tag == 'register') {
        if($post){
        // Request type is Register new username
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
        }
        else{
            $name = $_GET['name'];
            $email = $_GET['email'];
            $password = $_GET['password'];
            echo "name = $name \n";
            echo "email = $email \n";
            echo "password = $password \n";
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
                $response["uid"] = $username["unique_id"];
                $response["username"]["name"] = $username["name"];
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
             $name = $_POST["name"];
             $tuples = $db->getHouseData($name);
         }
         else{
             $name = $_POST["name"];
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
