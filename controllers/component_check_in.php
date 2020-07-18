<?php
    define('CHECKIN','true');
    require_once("model/model.php");
    
    
    if(isset($_POST["name"], $_POST["pass_1"], $_POST["pass_2"], $_POST["mail"])){
        //Смотрим есть ли в базе такой логин или почта
        $h_name = hash("sha512", $_POST["name"]);
        $h_mail = hash("sha512", $_POST["mail"]);
        //Для тестов:
        //        $h_name = $_POST["name"];
        //        $h_mail = $_POST["mail"];
        
        $is_name_user = get_user($pdo, $h_name);
        $is_mail_user = get_user($pdo, false, $h_mail);
        
        //Проверки
        if(is_array($is_mail_user)){
            $data = "На этот почтовый адрес уже зарегистрирован аккаунт!";
        }else if(is_array($is_name_user)){
            $data = "Login уже занят!";
        }else if($_POST["pass_1"] != $_POST["pass_2"]){
            $data = "Пароли не совпадают!";
        }else{
            $data = check_in_user($pdo, $_POST["name"], $_POST["pass_1"], $_POST["mail"]);
        }
        
    }