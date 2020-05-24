<?php
    require_once "controllers/show_default_text.php";
    
    foreach($data as $val){
        echo "ID - " . $val['id'] . "<br>";
        echo "name - " . $val['name'] . "<br>";
        echo "text - " . $val['text'] . "<br>";
        echo "hidden - " . $val['hidden'] . "<br>";
    }
