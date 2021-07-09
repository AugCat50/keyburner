<?php
require_once "model/model.php";

if($_POST["operation"] == "add"){
    
    $answer = add_user_text($pdo, $_POST["name"], $_POST["theme"], $_POST["text"]);
    require_once "controllers/component_user_get_name_texts.php";
    //$result содержит результат работы component_user_get_name_texts , а именно html блока "Ваши темы"
    $data = $answer . $result;
    
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
    
}else if($_POST["operation"] == "search"){
    //В $_POST["text"] содержится search_word
    $answer = search_user_texts($pdo, $_POST["text"]);
    
    if(isset($answer) && is_array($answer)){
        $data = "<h4 class='user-text-list__head bright-blue-neon'>Результаты поиска:</h4>
            <div class='select__wrapper blue-neon-box'>
                <span class='select__arrow'>&#9660;</span>
                <select class='select js_select'>";
        
        $size_ans = count($answer);
        for($i=0; $i < $size_ans; $i++){
            $id    = $answer["text $i"]["id"];
            $area  = $answer["text $i"]["area"];
            $name  = $answer["text $i"]["name"];
            $opt_v = $name." -- ".$area." ID:".$id;
            $data  = $data."<option 
                                class='user-text-list__name select__option blue-neon' 
                                data-id='".$id."' 
                                data-area='".$area."'
                                name='".$opt_v."'>"
                                .$opt_v.
                            "</option>";
        }
        $data = $data."</select></div></ul><button class='button saerch-close pink-neon-box js_saerch-close'>&#215;</button>";
    }else{
        $data = $answer."<button class='button saerch-close pink-neon-box js_saerch-close'>&#215;</button>";
    }
    
}else{
    print_r($_POST);
}
