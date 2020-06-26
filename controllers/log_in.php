<?php
    /**
    * Component login
    *
    * Компонент отвечает за авторизацию в login.php
    *
    * @author A.Yushko <draackul2@gmail.com>
    * @package Calculator
    */
    require_once("../model/model.php");
//    $session = session_start();
    if($_POST['name'] && $_POST['password']){
        
//        echo "Имя: ".$_POST['name']." <br> Пароль: ".$_POST['password']."<br>";
        $name_md5 = md5($_POST['name']);
        $user     = get_user($pdo, $name_md5);
        
    }else if($_POST['mail'] && $_POST['password']){
        
//        echo "Почта: ".$_POST['mail']." <br> Пароль: ".$_POST['password']."<br>";
        $mail_md5 = md5($_POST['mail']);
        $user     = get_user($pdo, false, $mail_md5);
        
    }else{
        echo "Чёт пошло не так";
        echo "<pre>";
        print_r($_POST);
        echo "</pre><br>";
    }

//        echo "<pre>";
//        print_r($user);
//        echo "</pre><br>";
    
    if($user && $_POST['name']){
        $password_md5 = md5($_POST['password']);
        
        if($name_md5 === $user['name'] && $password_md5 === $user['password']){
            echo "Есть совпадение!";
        }else{
            echo "Пароль введен не верно";
        }
        
    }else if($user && $_POST['mail']){
        
        $password_md5 = md5($_POST['password']);
        
        if($mail_md5 === $user['mail'] && $password_md5 === $user['password']){
            echo "Есть совпадение!";
        }else{
            echo "Пароль введен не верно";
        }
    }else{
        echo "Пользователя с таким логином нет!";
//        echo "<pre>";
//        print_r($_POST);
//        echo "</pre><br>";
//        echo "<pre>";
//        print_r($user);
//        echo "</pre><br>";
    }
//    $user_1 = get_user($pdo, 'test');
//    $user_2 = get_user($pdo, false, 'draackul2@gmail.com');

//    echo "ID - " . $user_1['id'] . "<br>";
//        echo "name - " . $user_1['name'] . "<br>";
//        echo "mail - " . $user_1['mail'] . "<br>";
//        echo "pass " . $user_1['password'] . "<br>";
//        echo "access " . $user_1['access'] . "<br>";
