<?php
    /**
    * Component login
    *
    * Компонент отвечает за авторизацию в login.php
    *
    * @author A.Yushko <draackul2@gmail.com>
    * @package keyburner
    */
    require_once "model/model.php";
    
    session_start();
    
    if(!isset($_POST['name'], $_POST['password'], $_POST['mail'])){
        header ('Location: http://94.244.191.245/keyburner/index.php');
        exit;
    }
    
    
    if($_POST['name'] && $_POST['password']){
        
        $name_hash = hash("sha512", $_POST['name']);
        $user      = get_user($pdo, $name_hash);
        
    }else if($_POST['mail'] && $_POST['password']){
        
        $mail_hash = hash("sha512", $_POST['mail']);
        $user      = get_user($pdo, false, $mail_hash);
        
    }
//else{
//        echo "Unexpected error in component_log_in.php!";
//    }
    
    
    if(is_array($user) && $_POST['name']){
        $password_hash = hash("sha512", $_POST['password'].$user['solt']);
        
        if($name_hash === $user['name'] && $password_hash === $user['password']){
            
            $data             = "Есть совпадение!";
            $_SESSION['id']   = $user['id'];
            $_SESSION['name'] = $_POST['name'];
            
        }else{
            $data = "Пароль введен не верно";
        }
        
    }else if(is_array($user) && $_POST['mail']){
        
        $password_hash = hash("sha512", $_POST['password'].$user['solt']);
        
        if($mail_hash === $user['mail'] && $password_hash === $user['password']){
            
            $data             = "Есть совпадение!";
            $_SESSION['id']   = $user['id'];
            $_SESSION['name'] = $_POST['name'];
            
        }else{
            $data = "Пароль введен не верно";
        }
    }else{
        $data = "<br>".$user."<br>";
    }
