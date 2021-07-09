<?php
    session_start();
    
    if(isset($_POST["its_text"]) && $_POST["its_text"] == "get_default_text"){
        
        require_once "controllers/component_default_get_text.php";
        
    }else if(isset($_POST["its_text"]) && $_POST["its_text"] == "get_user_text"){
        
        require_once "controllers/component_user_get_text.php";
        
    }else if(isset($_POST["operation"])){
        
        require_once "controllers/component_user.php";
        
    }
    
    
    /**
    * Этот код выводит полученные html данные от компоненов. А точнее, возвращает его ajax коду в js
    *
    * Ожидается массив с данными. Если это не массив, значит ошибка, просто выводим её как текст
    */
    if(isset($data) && is_array($data)){
        foreach($data as $value){
            echo $value;
//            print_r($value);
        }
    }else if(isset($data)){
        echo $data;
    }else {
        echo "ajax_user.php , ответ пуст.";
    }
