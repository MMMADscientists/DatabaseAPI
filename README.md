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
