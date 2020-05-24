<?php
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=keyburner', 'user_1', 'S8uGbAmSciyAid8u');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e){
        echo "Соединение с БД не успешно: " . $e->getMessage() . "<br>";
        exit;
    }
    
    
    function get_default_text($pdo, $hidden){
        if($hidden){
            $query = "SELECT * FROM default_text WHERE hidden != true";
        } else{
            $query = "SELECT * FROM default_text";
        }
        
        try{
            $boock = $pdo->query($query);
            
            if(is_array($boock)){
                for($i=0; $text = $boock->fetch(); $i++){
                    $data["text $i"]["id"]     = $text["id"];
                    $data["text $i"]["name"]   = $text["name"];
                    $data["text $i"]["text"]   = $text["text"];
                    $data["text $i"]["hidden"] = $text["hidden"];
                }
            }
        }catch(PDOException $e){
            $data = "Ошибка в get_default_text: " . $e->getMessage() . "<br>";
        }
        
        return $data;
    }
























//class User_Text
//{
//    public $name;
//    public $text;
//    public $user;
//    
//    function __construct(){
//        
//    }
//    
//    function __destruct(){
//        
//    }
//    
//    public function get_user_text(){
//        
//    }
//    
//    public function add_user_text(){
//        
//    }
//}
//
//class Default_Text
//{
//    public $name;
//    public $text;
//    public $user;
//    
//    function __construct(){
//        
//    }
//    
//    function __destruct(){
//        
//    }
//    
//    public function get_default_text(){
//        
//    }
//    
//    public function add_default_text(){
//        
//    }
//}
