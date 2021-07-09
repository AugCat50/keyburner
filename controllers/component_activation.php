<?php
    require_once "model/model.php";
    
    $data = 'Ключ не получен';
    
    if(isset($_GET["key"])){
        $data = activation($pdo, $_GET["key"]);
//        echo "
//        <script>
//            if (!(performance.navigation.type == 1)) {
//                alert('$ans');
//            }
//        </script>
//        ";
    }
