<?php
    require_once "model/model.php";
    

    if($_POST["operation"] == "add"){
        
        $answer = add_user_text($pdo, $_POST["name"], $_POST["theme"], $_POST["text"]);
        require_once "controllers/component_user_get_name_texts.php";
        //$result содержит результат работы component_user_get_name_texts , а именно html блока "Ваши темы"
        $data = "<span>" . $answer . "</span>" . $result;
        
    }else if($_POST["operation"] == "edit"){
        
        $answer = edit_user_text($pdo, $_POST["id"], $_POST["name"], $_POST["theme"], $_POST["text"]);
        require_once "controllers/component_user_get_name_texts.php";
        //$result содержит результат работы component_user_get_name_texts , а именно html блока "Ваши темы"
        $data = "<span>" . $answer . "</span>" . $result;
        
        
    }else if($_POST["operation"] == "del" && isset($_POST["id"])){
        
        $answer = del_user_text($pdo, $_POST["id"]);
        require_once "controllers/component_user_get_name_texts.php";
        //$result содержит результат работы component_user_get_name_texts , а именно html блока "Ваши темы"
        $data = "<span>" . $answer . "</span>" . $result;
        
    }else{
        print_r($_POST);
    }
