<?php
    require_once "controllers/component_check_in.php";
    
    /**
    * Этот код выводит полученные html данные от компоненов. А точнее, возвращает его ajax коду в js
    *
    * Ожидается массив с данными. Если это не массив, значит ошибка, просто выводим её как текст
    */
    if(isset($data) && is_array($data)){
        foreach($data as $value){
            echo $value;
        }
    }else if(isset($data)){
        echo $data;
    }
