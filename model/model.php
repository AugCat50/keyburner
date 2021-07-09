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
        $pdo = new PDO('mysql:host=localhost;dbname=keyburner', 'root', '');
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
        
        if(!isset($data) || !is_array($data)) $data = 'У вас ещё нет текстов.';
        return $data;
    }
    
    
    //Не ясно, стоит ли проверять и id пользователя. Учитывая что он видит в списках только свои тексты
    function get_one_user_text($pdo, $id){
        if(!$id){
            return "Отсутствует ID.";
        }
        
        try{
            $query = "SELECT text FROM texts WHERE id = $id";
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
            $mail->Username   = "test@tect.test";
            $mail->Password   = "testMailPassword";
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
            $data="Пользователь зарегистрирован!<br><br>";
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
        $message_html = "Активируйте учётную запись, пройдя по ссылке: <a href='http://94.244.191.245/keyburner/activation.php?key=$hashed_key'>Активировать</a><br>Если вы не создавали учётную запись, проигнорируйте это письмо.";
        $message_nohtml = "Активируйте учётную запись пройдя по ссылке: http://94.244.191.245/keyburner/activation.php?key=$hashed_key   Если вы не создавали учётную запись, проигнорируйте это письмо.";
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
            return "<span>add_user_text -- необходимо передать объект PDO в пером аргументе!</span>";
        }else if(!$name){
            return "<span>add_user_text -- второй аргумент - имя - не должен быть пуст!</span>";
        }else if(!$theme){
            return "<span>add_user_text -- третий аргумент - тема - не должен быть пуст!</span>";
        }else if(!$text){
            return "<span>add_user_text -- четвёртый аргумент - текст - не должен быть пуст!</span>";
        }
        
        try{
            $query = "SELECT 1 FROM texts WHERE (id_user = :id_user) AND (name = :name) AND (area = :theme)";
            $stmt  = $pdo->prepare($query);
            $stmt->bindParam(":id_user", $_SESSION["id"]);
            $stmt->bindParam(":theme", $theme);
            $stmt->bindParam(":name", $name);
            $stmt->execute();
            $exist_text = $stmt->fetch();
            
            if($exist_text){
                $data = "<span>У вас уже есть текст с этим именем в этой теме!</span>";
                return $data;
            }
        }catch(PDOException $e){
            $data = "<span>Ошибка в model -- add_user_text при попытке проверить наличие текста в texts:" . $e->getMessage() . "</span>";
        }
        
        try{
            $query = "INSERT INTO texts (id_user, name, area, text) VALUES (:id_user, :name, :theme, :text)";
            $stmt  = $pdo->prepare($query);
            $stmt->bindParam(":id_user", $_SESSION["id"]);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":theme", $theme);
            $stmt->bindParam(":text", $text);
            $stmt->execute();
            $n_id = $pdo->lastInsertId();
            $data = $n_id."<span>Текст сохранён!</span>";
        }catch(PDOException $e){
            $data = "<span>Ошибка в model -- add_user_text при попытке добавить текст в texts:" . $e->getMessage() . "</span>";
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
    
    
    //Функции работы с статистикой
    //Обновление строки статистики в базе
    function stat_update($pdo, $id, $it, $column){
        try{
            $query = "UPDATE texts SET $column = :statistics WHERE id = :id";
            $stmt  = $pdo->prepare($query);
            $stmt->bindParam(':statistics', $it);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $data = true;
        }catch(PDOException $e){
            $data = "Ошибка в model -- statistics при попытке записать статистику в $column -- texts:" . $e->getMessage() . "<br>";
        }
        return $data;
    }

    
    //Получение строк статистики
    function stat_get($pdo, $id, $column){
            
        try{
            $query = "SELECT $column FROM texts WHERE id = :id";
            $stmt  = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $statistics = $stmt->fetch();
            if(is_array($statistics)){
                $data = $statistics[0];
            }else{
                $data = false;
            }
            
        }catch(PDOException $e){
            $data = "Ошибка в model -- statistics при попытке получить статистику из $column -- texts:" . $e->getMessage() . "<br>";
        }
        
        return $data;
    }
    
    //Статистика общая
    function statistics($pdo, $id, $time, $speed){
        $user_id = $_SESSION['id'];
        
        //Получение общей статистики
        $statistics = stat_get($pdo, $id, 'statistics');
        
        if($id && $time && $speed){
            
            //Данные для записи есть
            //этот код возвращает true или текст ошибки
            if(!$statistics){
                
                //Строка пустая, Данные для записи есть
                $it   = '{' . $user_id . ',' . $time . '-' . $speed . ',' . $user_id . '}';
                $data = stat_update($pdo, $id, $it, 'statistics');;
                return $data;
            }else{
                //Строка есть, Данные для записи есть
                
                //Позиция начала строки
                $open_str       = '{' . $user_id;
                $start_position = strripos($statistics, $open_str);
                
                //Позиция конца строки
                $end_str        = ',' . $user_id . '}';
                $end_position   = strripos($statistics, $end_str);
                
                $len_stat       = strlen($statistics);
                $first_string   = substr($statistics, 0, $start_position);
                $last_string    = substr($statistics, $end_position, $len_stat);
                
                if(!$end_position){
                    
                    //статистика есть, но не для этого пользователя. Просто добавляем в конец данные для записи
                    $it   = $statistics . '{' . $user_id . ',' . $time . '-' . $speed . ',' . $user_id . '}';
                    $data = stat_update($pdo, $id, $it, 'statistics');
                    return $data;
                }else{
                    
                    //Статистика для этого пользователя есть, работаем с ней
                    $length_stat_str  = $end_position - $start_position;
                    $user_stat_string = substr($statistics, $start_position, $length_stat_str) . ',' . $time . '-' . $speed;
                    $it               = $first_string . $user_stat_string . $last_string;
                    $data             = stat_update($pdo, $id, $it, 'statistics');
                    return $data;
                }
            }
        }else{
            
            //Данных для записи нет
            //этот код возвращает данные статистики пользователю
            if(!$statistics){
                
                //Строка пустая, Данных для записи нет
                return "Статистика пуста";
            }else{
                //Строка есть, Данных для записи нет
                
                $open_str        = '{' . $user_id;
                $start_position = strripos($statistics, $open_str) + strlen($open_str) + 1;
                
                $end_str        = ',' . $user_id . '}';
                $end_position   = strripos($statistics, $end_str);
                
                $length_stat_str  = $end_position - $start_position;
                $user_stat_string = substr($statistics, $start_position, $length_stat_str);
                
//                $arr_of_stat_val = explode(',', $user_stat_string);
                //здесь надо разбирать массив на данные и возвращать
//                print_r($user_stat_string);
                return $user_stat_string;
            }
        }
    }
    
    
    //Работа с лучшим результатом
    function statistics_best($pdo, $id, $time, $speed){
        $user_id = $_SESSION['id'];
        //Получение строки статистики лучших результатов
        $statistics_best = stat_get($pdo, $id, 'statistics_best');
        
        if($id && $time && $speed){
            //Данные для записи есть
            
            if(!$statistics_best){
                //Строки нет, Данные есть
                
                $it   = '{' . $user_id . ',' . $time . '-' . $speed . ',' . $user_id . '}';
                $data = stat_update($pdo, $id, $it, 'statistics_best');
                
                //Если пришла строка - это текст ошибки, если true - просто возвращаем текущую сткорость
                if(is_string($data)){
                    return $data;
                }else{
                    return $speed;
                }
            }else{
                //Строка есть, Данные есть
                
                $open_str_best         = '{' . $user_id;
                $start_position_best   = strripos($statistics_best, $open_str_best);
                
                $end_str_best      = ',' . $user_id . '}';
                $end_position_best = strripos($statistics_best, $end_str_best);
                
                if(!$end_position_best){
                    //Если пользовательской cтроки нет и есть данные для записи, просто записываем в конец общей строки
                    
                    $it = $statistics_best . '{' . $user_id . ',' . $time . '-' . $speed . ',' . $user_id . '}';
                    $data = stat_update($pdo, $id, $it, 'statistics_best');
                    
                    //Если пришла строка - это текст ошибки, если true - просто возвращаем текущую сткорость
                    if(is_string($data)){
                        return $data;
                    }else{
                        return $speed;
                    }
                }else{
                    //Есть пользовательская строка и есть данные для записи
                    
                    //Длина строки пользователя и сама строка
                    $length_stat_str_best  = $end_position_best - $start_position_best;
                    $user_stat_string_best = substr($statistics_best, $start_position_best, $length_stat_str_best);
                    $arr_best              = explode('-', $user_stat_string_best);
                    
                    //Если новый результат больше того, что в базе, просто обновляем его
                    if($arr_best[1] < $speed){
                        $len_stat_best     = strlen($statistics_best);
                        $first_string_best = substr($statistics_best, 0, $start_position_best);
                        $last_string_best  = substr($statistics_best, $end_position_best, $len_stat_best);
                        $it                = $first_string_best . '{' . $user_id . ',' . $time . '-' . $speed . $last_string_best;
                        $data              = stat_update($pdo, $id, $it, 'statistics_best');
                        //Если пришла строка - это текст ошибки, если true - просто возвращаем текущую сткорость
                        if(is_string($data)){
                            return $data;
                        }else{
                            return $speed;
                        }
                    }
                } 
            }
        }else{
            //Данных для записи нет
            //Данный код возврщает значение лучшего результата
            
            $end_str_best          = ',' . $user_id . '}';
            $end_position_best     = strripos($statistics_best, $end_str_best);
            
            if(!$end_position_best){
                //Нет строки, нет данных для записи
                
                return "Статистика пуста.";
            }else{
                //Есть строка, нет данных для записи
                
                $open_str_best       = '{' . $user_id;
                $start_position_best = strripos($statistics_best, $open_str_best);
                
                //Длина строки пользователя и сама строка
                $length_stat_str_best  = $end_position_best - $start_position_best;
                $user_stat_string_best = substr($statistics_best, $start_position_best, $length_stat_str_best);
                $arr_best              = explode('-', $user_stat_string_best);
                return  $arr_best[1];
            }
        }
    }
