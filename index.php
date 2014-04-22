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
    //$response["error"] = 1;
    //$response["error_msg"] = $error;
    //echo json_encode($response);
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
            $response['tuples'] = $username;
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
                $response['tuples'] = $username;
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
    }else if ($tag == "renameProperty"){
        if($post){
             $propertyID = $_POST['propertyID'];
             $address = $_POST['address'];
         }
         else{
             $propertyID = $_GET['propertyID'];
             $address = $_GET['address'];
         }
         
         $tuples = $db->changePropertyName($propertyID, $address);
         if($tuples){
             $response['success'] = 1;
             $response['tuples'] = $tuples;
             echo json_encode($response);
         }
         else{
             $response["error"] = 1;
             $response["error_code"] = 0;
             $response["error_msg"] = "No data found";
             echo json_encode($response);
         }
    }else if ($tag == "renameRoom"){
         if($post){
             $roomID = $_POST['roomID'];
             $roomName = $_POST['roomName'];
         }
         else{
             $roomID = $_GET['roomID'];
             $roomName = $_GET['roomName'];
         }
         
         $tuples = $db->changeRoomName($roomID, $roomName);
         if($tuples){
             $response['success'] = 1;
             $response['tuples'] = $tuples;
             echo json_encode($response);
         }
         else{
             $response["error"] = 1;
             $response["error_code"] = 0;
             $response["error_msg"] = "No data found";
             echo json_encode($response);
         }
    }else if ($tag == "houses"){
         if($post){
             $username = $_POST["username"];
             $tuples = $db->getHouseData($username);
         }
         else{
             $username = $_GET["username"];
             $tuples = $db->getHouseData($username);
         }
         
         if($tuples){
             $response["success"] = 1;
             $response["tuples"] = $tuples;
             echo json_encode($response);
         }else{
             $response["error"] = 1;
             $response["error_code"] = 0;
             $response["error_msg"] = "No data found";
             echo json_encode($response);
         }
    
    }else if ($tag == 'rooms'){
        if($post){
            $propertyID = $_POST['propertyID'];
        }
        else{
            $propertyID = $_GET['propertyID'];
        }
        $tuples = $db->getRoomFromHouse($propertyID);
        $houseInfo = $db->gethouseDataForRooms($propertyID);
        if($tuples){
             $response["success"] = 1;
             $response["tuples"] = $tuples;
             $response["houseInfo"] = $houseInfo;
             echo json_encode($response);
         }else{
             $response["error"] = 1;
             $response["error_code"] = 0;
             $response["error_msg"] = "No data found";
             echo json_encode($response);
         }
        
    }else if($tag == 'createHouse'){
        if($post){
               $address = $_POST['address'];
               $username = $_POST['username'];
               $houseURL = $_POST['houseURL'];
               $defaultRoom = $_POST['defaultRoom'];
           }
           else{
               $address = $_GET['address'];
               $username = $_GET['username'];
               $houseURL = $_GET['houseURL'];
               $defaultRoom = $_GET['defaultRoom'];
           }
           $tuples = $db->createHouse($address, $username, $houseURL, $defaultRoom);
           if($tuples){
                $response["success"] = 1;
                $response["tuples"] = $tuples;
                echo json_encode($response);
            }else{
                $response["error"] = 1;
                $response["error_code"] = 0;
                $response["error_msg"] = "No data found";
                echo json_encode($response);
            }
    }else if($tag == 'createRoom'){
        if($post){
               $name = $_POST['name'];
               $propertyID = $_POST['propertyID'];
               $roomURL = $_POST['roomURL'];
               
           }
           else{
               $name = $_GET['name'];
               $propertyID = $_GET['propertyID'];
               $roomURL = $_GET['roomURL'];
           }
           $tuples = $db->createRoom($name, $propertyID, $roomURL);
           if($tuples){
                $response["success"] = 1;
                $response["tuples"] = $tuples;
                echo json_encode($response);
            }else{
                $response["error"] = 1;
                $response["error_code"] = 0;
                $response["error_msg"] = "No data found";
                echo json_encode($response);
            }
    }else if($tag == 'deleteRoom'){
        if($post){
            $roomID = $_POST['roomID'];
        }
        else{
            $roomID = $_GET['roomID'];
        }
        $tuples = $db->deleteRoom($roomID);
        if($tuples){
                $response["success"] = 1;
                $response["tuples"] = $tuples;
                echo json_encode($response);
            }else{
                $response["error"] = 1;
                $response["error_code"] = 0;
                $response["error_msg"] = "No data found";
                echo json_encode($response);
            }
    }else if($tag == 'deleteProperty'){
        if($post){
            $propertyID = $_POST['propertyID'];
        }
        else{
            $propertyID = $_GET['propertyID'];
        }
        $tuples = $db->deleteProperty($propertyID);
        if($tuples){
                $response["success"] = 1;
                $response["tuples"] = $tuples;
                echo json_encode($response);
            }else{
                $response["error"] = 1;
                $response["error_code"] = 0;
                $response["error_msg"] = "No data found";
                echo json_encode($response);
        }
    }else if($tag == 'connections'){
        if($post){
            $roomID = $_POST['roomID'];
        }
        else{
            $roomID = $_GET['roomID'];
        }
        $tuples = $db->getConnections($roomID);
        if($tuples){
                $response["success"] = 1;
                $response["tuples"] = $tuples;
                echo json_encode($response);
            }else{
                $response["error"] = 1;
                $response["error_code"] = 0;
                $response["error_msg"] = "No data found";
                echo json_encode($response);
        }
    }else if($tag == 'createConnection'){
        if($post){
            $sourceID = $_POST['sourceID'];
            $destinationID = $_POST['destinationID'];
            $locationX = $_POST['doorX'];
            $locationY = $_POST['doorY'];
            $locationZ = $_POST['doorZ'];
        }
        else{
            $sourceID = $_GET['sourceID'];
            $destinationID = $_GET['destinationID'];
            $locationX = $_GET['doorX'];
            $locationY = $_GET['doorY'];
            $locationZ = $_GET['doorZ'];
        }
        $tuples = $db->createConnection($sourceID, $destinationID, $locationX, $locationY, $locationZ);
        if($tuples){
                $response["success"] = 1;
                $response["tuples"] = $tuples;
                echo json_encode($response);
            }else{
                $response["error"] = 1;
                $response["error_code"] = 0;
                $response["error_msg"] = "No data found";
                echo json_encode($response);
        }
    }else if($tag == 'deleteConnection'){
        if($post){
            $idConnection = $_POST['connectionID'];
        }
        else{
            $idConnection = $_GET['connectionID'];
        }
        $tuples = $db->deleteConnection($idConnection);
        if($tuples){
                $response["success"] = 1;
                $response["tuples"] = $tuples;
                echo json_encode($response);
            }else{
                $response["error"] = 1;
                $response["error_code"] = 0;
                $response["error_msg"] = "No data found";
                echo json_encode($response);
        }
    }else if($tag =='changeRoomURL'){
        if($post){
            $roomID = $_POST['roomID'];
            $URL = $_POST['newURL'];
        }
        else{
            $roomID = $_GET['roomID'];
            $URL = $_GET['newURL'];
        }
        $tuples = $db->changeRoomURL($roomID, $URL);
        if($tuples){
                $response["success"] = 1;
                $response["tuples"] = $tuples;
                echo json_encode($response);
            }else{
                $response["error"] = 1;
                $response["error_code"] = 0;
                $response["error_msg"] = "No data found";
                echo json_encode($response);
        }
    }else if($tag =='changeConnectionTarget'){
        if($post){
            $connectionID = $_POST['connectionID'];
            $doorX = $_POST['doorX'];
            $doorY = $_POST['doorY'];
            $doorZ = $_POST['doorZ'];
        }
        else{
            $connectionID = $_GET['connectionID'];
            $doorX = $_GET['doorX'];
            $doorY = $_GET['doorY'];
            $doorZ = $_GET['doorZ'];
        }
        $tuples = $db->changeConnectionTarget($connectionID, $doorX, $doorY, $doorZ);
        if($tuples){
                $response["success"] = 1;
                $response["tuples"] = $tuples;
                echo json_encode($response);
            }else{
                $response["error"] = 1;
                $response["error_code"] = 0;
                $response["error_msg"] = "No data found";
                echo json_encode($response);
        }
    }else if($tag =='changeHouseURL'){
        if($post){
            $propertyID = $_POST['propertyID'];
            $newURL = $_POST['newURL'];
        }
        else{
            $propertyID = $_GET['propertyID'];
            $newURL = $_GET['newURL'];
        }
        $tuples = $db->changeHouseURL($propertyID, $newURL);
        if($tuples){
                $response["success"] = 1;
                $response["tuples"] = $tuples;
                echo json_encode($response);
            }else{
                $response["error"] = 1;
                $response["error_code"] = 0;
                $response["error_msg"] = "No data found";
                echo json_encode($response);
        }
    }else if($tag =='changeHouseDefaultRoom'){
        if($post){
            $propertyID = $_POST['propertyID'];
            $defaultRoom = $_POST['defaultRoom'];
        }
        else{
            $propertyID = $_GET['propertyID'];
            $defaultRoom = $_GET['defaultRoom'];
        }
        $tuples = $db->changeHouseDefaultRoom($propertyID, $defaultRoom);
        if($tuples){
                $response["success"] = 1;
                $response["tuples"] = $tuples;
                echo json_encode($response);
            }else{
                $response["error"] = 1;
                $response["error_code"] = 0;
                $response["error_msg"] = "No data found";
                echo json_encode($response);
        }
    }else if($tag == 'changeConnectionDestination'){
        if($post){
            $connectionID = $_POST['connectionID'];
            $newDest = $_POST['newDest'];
        }
        else{
            $connectionID = $_GET['connectionID'];
            $newDest = $_GET['newDest'];
        }
        $tuples = $db->changeConnectionDest($connectionID, $newDest);
        if($tuples){
                $response["success"] = 1;
                $response["tuples"] = $tuples;
                echo json_encode($response);
            }else{
                $response["error"] = 1;
                $response["error_code"] = 0;
                $response["error_msg"] = "No data found";
                echo json_encode($response);
        }
    }
    else {
        echo "Invalid Request";
    }
?>
