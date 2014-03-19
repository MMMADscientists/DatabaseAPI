To use API:

make a URLrequest with a list with at least a tag and the information required for the request

For each tage, there is associated information needed, i.e. login needs username and password

tags: 
  "login"
  
  "register"
  
  "houses"
  
  "rooms"
  
  "connections"
  
note: all items in < > are provided by you.

  example Login:
  
  "tag" = "login"
  
  "username" = "admin"
  
  "password" = "admin"
  

Process:

  Login: 
  
    "tag" = "login"
    
    requirements:
    
      "username" = < username >
      
      "password" = < password >
      
      
  Register:
  
    "tag" = "register"
    
    requirements:
    
      "name" = < name >
      
      "email" = < email >
      
      "password" = <password>


  Get Homes:
  
    "tag" = "houses"
    
    requirements:
    
      "name" = <username>
      
      
  More to follow
  
  
  
  Return Data:
  Each call returns JSON containing:
  
  Successful user registration
  
  {
    "tag": "register",
    "success": 1,
    "error": 0,
    "uid": "4f074ca1e3df49.06340261",
    "user": {
        "name": "admin",
        "email": "PromenadeVT@gmail.com"
    }
}

Error: error in storing

{
    "tag": "register",
    "success": 0,
    "error": 1,
    "error_msg": "Error occured in Registartion"
}

Error: user already exists

{
    "tag": "register",
    "success": 0,
    "error": 2,
    "error_msg": "User already existed"
}

Success: user logged in
{
    "tag": "login",
    "success": 1,
    "error": 0,
    "uid": "4f074eca601fb8.88015924",
    "user": {
        "name": "admin",
        "email": "PromenadeVT@gmail.com"
    }
}
