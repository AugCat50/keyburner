<?php
    require_once "model/model.php";

    if(isset($_GET["key"])){
        $ans = activation($pdo, $_GET["key"]);
        echo "
        <script>
            if (!(performance.navigation.type == 1)) {
                alert('$ans');
            }
        </script>
        ";
    }