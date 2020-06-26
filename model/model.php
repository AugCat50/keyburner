<?php
     //Адрес почты от кого отправляем
     define('SENDER','Регистрация на http://keyburner.com <augcat50@mail.com>');

    try {
        $pdo = new PDO('mysql:host=localhost;dbname=keyburner', 'user_1', 'S8uGbAmSciyAid8u');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e){
        echo "Соединение с БД не успешно: " . $e->getMessage() . "<br>";
        exit;
    }
    
    
    function get_default_text($pdo, $hidden){
        if(!$hidden){
            $query = "SELECT * FROM default_text WHERE hidden = false";
        } else{
            $query = "SELECT * FROM default_text";
        }
        
        try{
            $boock = $pdo->query($query);
            
            for($i=0; $text = $boock->fetch(); $i++){
                $data["text $i"]["id"]     = $text["id"];
                $data["text $i"]["name"]   = $text["name"];
                $data["text $i"]["text"]   = $text["text"];
                $data["text $i"]["hidden"] = $text["hidden"];
            }
        }catch(PDOException $e){
            $data = "Ошибка в get_default_text: " . $e->getMessage() . "<br>";
        }
        
        return $data;
    }
    
    function get_one_default_text($pdo, $id){
        $query = "SELECT text FROM default_text WHERE id = $id";
        
        try{
            $text = $pdo->query($query);
            $content = $text->fetch();
            $data = $content["text"];
        }catch(PDOException $e){
            $data = "Ошибка в get_one_default_text: " . $e->getMessage() . "<br>";
        }
        
        return $data;
    }

    function get_user($pdo, $name = false, $mail = false){
        if($name){
            //            $query = "SELECT * FROM users WHERE name = '$name'";
            $query = "SELECT * FROM users WHERE name = :name";
        }else if($mail){
            //            $query = "SELECT * FROM users WHERE mail = '$mail'";
            $query = "SELECT * FROM users WHERE mail = :mail";
        }else{
            $data = "Ошибочный вызов функции get_user. В вызове должно быть не пустое имя или почта";
            return $data;
        }
        
        try{
            
            $stmt = $pdo->prepare($query);
            if($name){
                $stmt->bindParam(':name', $name);
            }else if($mail){
                $stmt->bindparam(':mail', $mail);
            }
            
            $stmt->execute();
            $data = $stmt->fetch();
            //            $user = $pdo->query($query);
            //            $data = $user;
            
            if(!is_array($data)){
                $data = "Пользователь не обнаружен!";
            }
        }catch(PDOException $e){
            $data = "Ошибка в model -- get_user:" . $e->getMessage() . "<br>";
        }
        
        return $data;
    }

    function check_in_user($pdo, $name, $password, $mail){
        
        //Солим пароль, хешируем. Логин и почта без соли
        $length_solt = rand(0,100);
        $solt        = random_bytes($length_solt);
        $hashed_name = hash("sha512", $name);
        $hashed_pass = hash("sha512", $password.$solt);
        $hashed_mail = hash("sha512", $mail);
        
        //Записываем пользователя в таблицу пользователей
        try{
            $query = "INSERT INTO users (name, password, solt, mail) VALUES (:name, :password, :solt, :mail)";
            $stmt  = $pdo->prepare($query);
            $stmt->bindParam(':name', $hashed_name);
            $stmt->bindParam(':password', $hashed_pass);
            $stmt->bindParam(':solt', $solt);
            $stmt->bindParam(':mail', $hashed_mail);
            $stmt->execute();
            $data="Пользователь зарегистрирован!<br>";
        }catch(PDOException $e){
            $data = "Ошибка в model -- check_in_user при записи пользователя в таблицу users:" . $e->getMessage() . "<br>";
        }
        
        //Получаем id записи
        try{
            $query    = "SELECT `id` FROM `users` WHERE `name` = '$hashed_name'";
            $user_obj = $pdo->query($query);
            $user_id  = $user_obj->fetch();
        }catch(PDOException $e){
            $data = "Ошибка в model -- check_in_user при получении id пользователя из users:" . $e->getMessage() . "<br>";
        }
        
        //Генерируем ключ для активации
        $length_key = rand(0,100);
        $key        = random_bytes($length_key);
        $hashed_key = hash("sha512", $key);
        
        //Отправляем письмо
        $title = "Активация аккаунта на keyburner.com";
        $message = "Для активации пройдите по ссылке: <a href='http://94.244.191.245/keyburner/index.php?key=$hashed_key'>Активировать</a>";
        $and_mail = send_activation_mail($mail, SENDER, $title, $message);
        
        //Делаем запись в таблицу ожидающих активации
        try{
            $query = "INSERT INTO temp (id_user, key_act, mail) VALUES (:id_user, :key_act, :mail)";
            $stmt  = $pdo->prepare($query);
            $stmt->bindParam(':id_user', $user_id["id"]);
            $stmt->bindParam(':key_act', $hashed_key);
            $stmt->bindParam(':mail', $mail);
            $stmt->execute();
            $data=$data."Активируйте учётную запись<br>";
        }catch(PDOException $e){
            $data = "Ошибка в model -- check_in_user при получении id пользователя из users:" . $e->getMessage() . "<br>";
        }
        
        $data = $data.$and_mail;
        return $data;
    }

     /**Отпровляем сообщение на почту
     * @param string  $to
     * @param string  $from
     * @param string  $title
     * @param string  $message
     */
    function send_activation_mail($to, $from, $title, $message){
        //Формируем заголовок письма
        $subject = $title;
        $subject = '=?utf-8?b?'. base64_encode($subject) .'?=';

        //Формируем заголовки для почтового сервера
        $headers  = "Content-type: text/html; charset=\"utf-8\"\r\n";
        $headers .= "From: ". $from ."\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Date: ". date('D, d M Y h:i:s O') ."\r\n";

        //Отправляем данные на ящик админа сайта
        if(!mail($to, $subject, $message, $headers)){
            return 'Ошибка отправки письма!';  
        }else{
            return 'Письмо отправлено!';
        } 
    }