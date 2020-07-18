<?php
    require_once "model/model.php";
    $data = get_one_default_text($pdo, $_POST["id"]);
