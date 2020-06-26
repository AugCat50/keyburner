<?php
    require_once("../model/model.php");
    
    if($_POST["name"] && $_POST["pass_1"] && $_POST["pass_2"] && $_POST["mail"]){
        //Смотрим есть ли в базе такой логин или почта
//        $h_name = hash("sha512", $_POST["name"]);
//        $h_mail = hash("sha512", $_POST["mail"]);
        $h_name = $_POST["name"];
        $h_mail = $_POST["mail"];
        
        $is_name_user = get_user($pdo, $h_name);
        $is_mail_user = get_user($pdo, false, $h_mail);
        
        //Проверки
        if(is_array($is_mail_user)){
            echo "На этот почтовый адрес уже зарегистрирован аккаунт!";
        }else if(is_array($is_name_user)){
            echo "Login уже занят!";
        }else if($_POST["pass_1"] != $_POST["pass_2"]){
            echo "Пароли не совпадают!";
        }else{
            $data = check_in_user($pdo, $_POST["name"], $_POST["pass_1"], $_POST["mail"]);
            echo $data;
        }
        
    }