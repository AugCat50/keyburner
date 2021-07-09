<?php
    /**
    * Component session
    *
    * Компонент инициирует сессию при входе в админ панель и уничтожает её при выходе по нажатию кнопки Выйти
    *
    * @author A.Yushko <draackul2@gmail.com>
    * @package Calculator
    */
//    if(isset($_SESSION['id'])){
//        unset($_SESSION['id']);
//    }
    
    session_start();
    
    //Если нажата кнопка "Выйти", уничтожаем сессию, переходим на главную
    if(isset($_REQUEST["exit"]) && $_REQUEST["exit"]==="exit"){
        $_SESSION = [];
        unset($_COOKIE[session_name()]);
        session_destroy();
        header("Location: index.php");
        exit;
    }
    
    if(!isset($_SESSION['id'])){
        header("Location: index.php");
        exit;
    }
