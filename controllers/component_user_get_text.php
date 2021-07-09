<?php
    require_once "model/model.php";
    $data = get_one_user_text($pdo, $_POST["id"]);
