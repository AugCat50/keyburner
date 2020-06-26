<?php
//    require_once "controllers/show_default_text.php";
//    require_once "controllers/log_in.php";
require_once "model/model.php";
    
//    foreach($data as $val){
//        echo "ID - " . $val['id'] . "<br>";
//        echo "name - " . $val['name'] . "<br>";
//        echo "text - " . $val['text'] . "<br>";
//        echo "hidden - " . $val['hidden'] . "<br>";
//        echo "<br>";
//    }

//    foreach($data as $val){
//        echo "ID - " . $val['id'] . "<br>";
//        echo "name - " . $val['name'] . "<br>";
//        echo "mail - " . $val['mail'] . "<br>";
//        echo "pass " . $val['password'] . "<br>";
//        echo "<br>";
//    }
//if(is_array($user_1)){
//    echo "ID - " . $user_1['id'] . "<br>";
//        echo "name - " . $user_1['name'] . "<br>";
//        echo "mail - " . $user_1['mail'] . "<br>";
//        echo "pass " . $user_1['password'] . "<br>";
//        echo "access " . $user_1['access'] . "<br>";
//}else{
//    echo $user_1;
//}
//
//        
////echo $user_1;
//        echo "<br>";
//
//if(is_array($user_2)){
//        echo "ID - " . $user_2['id'] . "<br>";
//        echo "name - " . $user_2['name'] . "<br>";
//        echo "mail - " . $user_2['mail'] . "<br>";
//        echo "pass " . $user_2['password'] . "<br>";
//        echo "access " . $user_2['access'] . "<br>";
//}else{
//    echo $user_2;
//}
$length_solt = rand(0,100);
$name = "test";
$password = "testpass";
$solt = random_bytes($length_solt);
$hashed_name = hash("sha512", $name);
$hashed_pass = hash("sha512", $password.$solt);
echo "Name_hash: -- ".$hashed_name."<br>";
echo "Solt: -- ".$solt."<br>";
//print_r($solt);
//echo "<br>";
echo "Hash: -- ".$hashed_pass."<br>";
//print_r($hashed_pass);
    echo "<br>";

//$query = "INSERT INTO test (name, password, solt) VALUES ('$hashed_name','$hashed_pass','$solt')";
//
//        try{
//            $pdo->exec($query);
//            
//        } catch(PDOException $e){
//            $data = "<p>Error:" . $e->getMessage() . "</p>";
//        }
$name1 = "test";
$hashed_name1 = hash("sha512", $name1);
echo "Name_hash: -- ".$hashed_name1;
$query = "SELECT * FROM test WHERE name = '$hashed_name1'";
echo $query."<br>";
        try{
            $result = $pdo->query($query);
            $result_item = $result->fetch();

            
        } catch(PDOException $e){
            $data = "<p>Error:" . $e->getMessage() . "</p>";
        }

echo "<br>";
echo "<br>";
echo "<br>";
            echo $result_item["name"]."<br>";
            echo $result_item["password"]."<br>";
            echo $result_item["solt"]."<br>";

$password1 = "testpass";
$hashed_pass1 = hash("sha512", $password1.$result_item["solt"]);

if($hashed_name1===$result_item["name"] && $hashed_pass1===$result_item["password"]){
echo "<br>";
echo "<br>";
echo "<br>";
echo "DONE";
echo "<br>";
echo "<br>";
}else{
    echo "<br>";
echo "1. $hashed_name1.<br>";
echo "2. ".$result_item["name"]."<br>";
    echo "<br>";
echo "3. $hashed_pass1.<br>";
echo "4. ".$result_item["password"]."<br>";
}
//echo $result_item["name"]."<br>";
//echo $result_item["password"]."<br>";
//echo $result_item["solt"]."<br>";


//    echo $data_1;
//$ip = $_SERVER['REMOTE_ADDR'];
//echo "<br>ip:  ".$ip;

echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
//$q = date('r');
//echo $q;

//$now_date = new DateTime(date('Y-m-d H:i'));    //время сейчас
//$old_date = new DateTime('2018-01-08 21:16'); //дата с которой отчитываем 
//$interval = $now_date->diff($old_date);
//echo $interval->format("'%R%a дней'"); //выводим результат