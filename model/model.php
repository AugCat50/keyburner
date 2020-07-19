<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    if(defined('CHECKIN')){
        require_once "vendor/autoload.php";
//        echo "true<br>";
    }else{
//        echo "false<br>";
    }

    //Адрес почты от кого отправляем
    define('SENDER','augcat50@gmail.com');
    
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=keyburner', 'user_1', 'S8uGbAmSciyAid8u');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e){
        echo "Соединение с БД не успешно: " . $e->getMessage() . "<br>";
        exit;
    }
    
    
    function default_get_neme_texts($pdo, $hidden){
        if(!$hidden){
            $query = "SELECT id, name FROM default_text WHERE hidden = false";
        } else{
            $query = "SELECT id, name FROM default_text";
        }
        
        try{
            $boock = $pdo->query($query);
            
            for($i=0; $text = $boock->fetch(); $i++){
                $data["text $i"]["id"]   = $text["id"];
                $data["text $i"]["name"] = $text["name"];
//                $data["text $i"]["text"]   = $text["text"];
//                $data["text $i"]["hidden"] = $text["hidden"];
            }
        }catch(PDOException $e){
            $data = "Ошибка в default_get_neme_texts: " . $e->getMessage() . "<br>";
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
    
    
    function user_get_name_texts($pdo){
        $id    = $_SESSION['id'];
        $query = "SELECT id, area, name FROM texts WHERE id_user = $id";
        
        try{
            $boock = $pdo->query($query);
            for($i=0; $text = $boock->fetch(); $i++){
                $data["text $i"]["id"]   = $text["id"];
                $data["text $i"]["area"] = $text["area"];
                $data["text $i"]["name"] = $text["name"];
            }
        }catch(PDOException $e){
            $data = "Ошибка в user_get_name_texts: " . $e->getMessage() . "<br>";
        }
        
        return $data;
    }
    
    
    //Не ясно, стоит ли проверять и id пользователя. Учитывая что он видит в списках только свои тексты
    function get_one_user_text($pdo, $id){
        $query = "SELECT text FROM texts WHERE id = $id";
        
        try{
            $text    = $pdo->query($query);
            $content = $text->fetch();
            $data    = $content["text"];
        }catch(PDOException $e){
            $data = "Ошибка в get_one_user_text: " . $e->getMessage() . "<br>";
        }
        
        return $data;
    }
    
    
    //Поиск по ключевому слову в названиях и текстах
    function search_user_texts($pdo, $search_word){
        $search_word = "%".$search_word."%";
        $id          = $_SESSION['id'];
        try{
            
            $query = "SELECT id, area, name 
                    FROM texts 
                    WHERE id_user = :id AND name LIKE :search 
                    OR id_user = :id AND text LIKE :search";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':search', $search_word);
            $stmt->execute();
            
            for($i=0; $text = $stmt->fetch(); $i++){
                $data["text $i"]["id"]   = $text["id"];
                $data["text $i"]["area"] = $text["area"];
                $data["text $i"]["name"] = $text["name"];
            }
        }catch(PDOException $e){
            $data = "Ошибка в search_user_texts: " . $e->getMessage() . "<br>";
        }
        
        if(!isset($data)){
            $data = "Ничего не найдено!";
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

    
     /**Отпровка почты
     * @param string  $to
     * @param string  $title
     * @param string  $message_html
     * @param string  $message_nohtml
     */
    function send_mail($to, $title, $message_html, $message_nohtml){

        $mail = new PHPMailer(true);
        try{
            $mail->CharSet = 'UTF-8';
            //Server settings
            //Подробный лог на страницу
            //$mail->SMTPDebug  = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = "smtp.gmail.com";
            $mail->SMTPAuth   = true;
            $mail->Username   = "augcat50@gmail.com";
            $mail->Password   = "Germiona8000";
            //    $mail->SMTPSecure = "ssl";
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            //    $mail->Port       = "465";
            $mail->Port       = 587;
            
            //Recipients
            $mail->setFrom("augcat50@gmail.com", "Keyburner.com");
            $mail->addAddress($to);
            //$mail->addAddress("wilcher@mail.ru");
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $title;
            //$mail->Subject = "Активация учётной записи";
            $mail->Body    = $message_html;
            $mail->AltBody = $message_nohtml;
            
            $mail->send();
            $data = "На почту отправлено письмо, активируйте учётную запись!";
        }catch (Exception $e){
            $data = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        
        return $data;
    }

    
    //Функция для регистрации пользователей
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
        $title = "Активация учётной записи на keyburner.com";
        $message_html = "Активируйте учётную запись, пройдя по ссылке: <a href='http://94.244.191.245/keyburner/user.php?key=$hashed_key'>Активировать</a>";
        $message_nohtml = "Активируйте учётную запись пройдя по ссылке: http://94.244.191.245/keyburner/user.php?key=$hashed_key";
        $and_mail = send_mail($mail, $title, $message_html, $message_nohtml);
        
        //Делаем запись в таблицу ожидающих активации
        try{
            $query = "INSERT INTO temp (id_user, key_act, mail) VALUES (:id_user, :key_act, :mail)";
            $stmt  = $pdo->prepare($query);
            $stmt->bindParam(':id_user', $user_id["id"]);
            $stmt->bindParam(':key_act', $hashed_key);
            $stmt->bindParam(':mail', $mail);
            $stmt->execute();
//            $data=$data."Активируйте учётную запись<br>";
        }catch(PDOException $e){
            $data = "Ошибка в model -- check_in_user при получении id пользователя из users:" . $e->getMessage() . "<br>";
        }
        
        $data = $data.$and_mail;
        return $data;
    }


    function activation($pdo, $key){
        try{
            $query = "SELECT 1 FROM temp WHERE key_act = :key";
            $stmt  = $pdo->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->execute();
            $user = $stmt->fetch();
        }catch(PDOException $e){
            $data = "Ошибка в model -- activation:" . $e->getMessage() . "<br>";
        }
        
        if($user){
            try{
                $query = "DELETE FROM temp WHERE key_act = :key";
                $stmt  = $pdo->prepare($query);
                $stmt->bindParam(":key", $key);
                $stmt->execute();
                $data = "Аккаунт активирован!";
            }catch(PDOException $e){
                $data = "Ошибка в model -- activation при удалении из temp:" . $e->getMessage() . "<br>";
            }
        }else{
            $data = "Ключ активации не подошёл!";
        }
        return $data;
    }


    function add_user_text($pdo, $name, $theme, $text){
        if(!$pdo){
            return "add_user_text -- необходимо передать объект PDO в пером аргументе!";
        }else if(!$name){
            return "add_user_text -- второй аргумент - имя - не должен быть пуст!";
        }else if(!$theme){
            return "add_user_text -- третий аргумент - тема - не должен быть пуст!";
        }else if(!$text){
            return "add_user_text -- четвёртый аргумент - текст - не должен быть пуст!";
        }
        
//        try{
//            $query = "SELECT 1 FROM texts WHERE (id_user = :id_user) AND (name = :name) AND (area = :theme)";
//            $stmt  = $pdo->prepare($query);
//            $stmt->bindParam(":id_user", $_SESSION["id"]);
//            $stmt->bindParam(":theme", $theme);
//            $stmt->bindParam(":name", $name);
//            $stmt->execute();
//            $exist_text = $stmt->fetch();
//            
//            if($exist_text){
//                $data = "У вас уже есть текст с этим именем в этой теме!";
//                return $data;
//            }
//        }catch(PDOException $e){
//            $data = "Ошибка в model -- add_user_text при попытке проверить наличие текста в texts:" . $e->getMessage() . "<br>";
//        }
        
        try{
            $query = "INSERT INTO texts (id_user, name, area, text) VALUES (:id_user, :name, :theme, :text)";
            $stmt  = $pdo->prepare($query);
            $stmt->bindParam(":id_user", $_SESSION["id"]);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":theme", $theme);
            $stmt->bindParam(":text", $text);
            $stmt->execute();
            $data = "Текст сохранён!";
        }catch(PDOException $e){
            $data = "Ошибка в model -- add_user_text при попытке добавить текст в texts:" . $e->getMessage() . "<br>";
        }
        
        return $data;
    }
    
    
    function edit_user_text($pdo, $id, $name, $theme, $text){
        try{
            $query = "UPDATE texts SET area = :theme, name = :name, text = :text WHERE id = :id";
            $stmt  = $pdo->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":theme", $theme);
            $stmt->bindParam(":text", $text);
            $stmt->execute();
            $data = "Текст обновлён!";
        }catch(PDOException $e){
            $data = "Ошибка в model -- edit_user_text при попытке обновить текст в texts:" . $e->getMessage() . "<br>";
        }
        
        return $data;
    }
    
    
    function del_user_text($pdo, $id){
        try{
            $query = "DELETE FROM texts WHERE id = :id";
            $stmt  = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $data = "Текст удалён!";
        }catch(PDOException $e){
            $data = "Ошибка в model -- del_user_text при попытке удалить текст из texts:" . $e->getMessage() . "<br>";
        }
        
        return $data;
    }