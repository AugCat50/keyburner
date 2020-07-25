<?php
    require_once "model/model.php";
    
//    $user_id   = $_SESSION['id'];
//    $text_id   = $_POST['id'];
//    $stat_time = $_POST['time'];
//    $speed     = $_POST['speed'];
    
    statistics($pdo, $_POST['id'], $_POST['time'], $_POST['speed']);
    statistics_best($pdo, $_POST['id'], $_POST['time'], $_POST['speed']);
//    $a = "привет мир";
//    echo "bite:".strlen($a);
// ответ - 10
//    echo "bite:".mb_strlen($a);