"use strict"
function main_ready(){
    var BLOCK_STATUS = false;
    var WORK_AREA   = $('.js-work-textarea');
    var CLONE_TEXT  = "";
    
    //Удаление начальных, конечных пробелов, излишних пробельных символов в тексте
    function text_replace(){
        let val = $(".js-main-textarea").val().trim();
        
        let qwe = val.replace(/\n+\s+/g, "\n");
        qwe = qwe.replace(/[ \f\r\t\v\u00A0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u2028\u2029\u2028\u2029\u202f\u205f\u3000][ \f\r\t\v\u00A0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u2028\u2029\u2028\u2029\u202f\u205f\u3000]+/g, " ");
        qwe = qwe.replace(/[ \n\f\r\t\v\u00A0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u2028\u2029\u2028\u2029\u202f\u205f\u3000][ \n]+/g, "\n");
        qwe = qwe.replace(/[\n\u21B5]+/g, "\u21B5\n");
        
        if(qwe!==val){
            $(".js-main-textarea").val(qwe);
        }else{
            $(".js-main-textarea").val(val);
        }
        
        return qwe;
    }
    
    //Обнуление переменных
    function null_var(){
        BLOCK_STATUS    = false;
        startStr        = 0;
        old_work        = "";
        old_work_length = 0;
        oldVal          = "";
        oldLength       = 0;
        errors          = 0;
        start_time      = 0;
        end_time        = 0;
        template_length = 0;
        wrong_length    = 0;
        errors          = 0;
    }
    
    //Количество текстов в категории
    $(".user-text-list").each(function () {
        let q = $(this).find('.select__option').length;
        $(this).children(".user-text-list__head").append(" ["+q+"]");
    });
    
    
//BLOK-1 В основном работа с первым блоком (Template textarea)
    //Обработка текста при начальной загрузке, если он есть
    let load_val = $(".js-main-textarea").val();
    WORK_AREA.val("");
    if(load_val != false){
        text_replace();
        WORK_AREA.removeAttr("disabled");
        WORK_AREA.attr("placeholder", "Готовы приступать? :) \nШаблон блокируется на время теста.");
    }
    
    //Template textarea содержит текст - разблокируется рабочее поле work textarea. Пуст - блокируется
    $('.js-main').on('input' , '.js-main-textarea', function(){
        let val = $(".js-main-textarea").val();
        let id   = $(".js_current-text-id").html();
        let name = $(".js_main-name").val();
        let area = $(".js_main-theme-name").val();
        localStorage.setItem("id", id);
        localStorage.setItem("name", name);
        localStorage.setItem("area", area);
        localStorage.setItem("text", val);
        
        if(val != false){
            WORK_AREA.removeAttr("disabled");
            WORK_AREA.attr("placeholder", "Готовы приступать? :) \nШаблон блокируется на время теста.");
        }else{
            WORK_AREA.attr("disabled", "true");
            WORK_AREA.attr("placeholder", "Сначала добавьте текст в верхнее поле");
            WORK_AREA.val("");
        }
    });
    
    //Вызов функции удаления пробельных символов
    $(".js_section-template").on("focusout", ".js-main-textarea", function(){
        let r_t = text_replace();
        localStorage.setItem("text", r_t);
    });
//END BLOK-1
    
    
    
    
//BLOK-2   Второй блок (work textarea)
    //Начальные значения для буффера должны быть ВНЕ функции, данные циклически обновляются
        
    //Обновляются раз в слово
    let old_work        = "";
    let old_work_length = 0;
    let startStr        = 0;
    
    //Обновляются раз в символ
    let oldVal    = "";
    let oldLength = 0;
    
    //template_length -- Длина шаблонного текста, определяется при блокировке шаблонного текста
    //start_time      -- время начала, определяется при блокировке шаблонного текста
    let start_time = 0, end_time = 0;
    let template_length = 0;
    let wrong_length = 0;
    let errors = 0;
    
    let change_work_textarea     = document.querySelector('.js-work-textarea');
    change_work_textarea.oninput = function(e){
        let word        = [];
        let work_text   = WORK_AREA.val();
        let work_length = work_text.length;
        let my_time, result_speed, penalty_speed, my_minute, my_second;
        
        
        //Блокировка текста work main_area, если это ещё не сделано
        //Прямое обращение к $(".js-main-textarea"), поскольку элемент может быть удалён и создан скриптом
        if(BLOCK_STATUS === false){
            CLONE_TEXT = $(".js-main-textarea").val();
            $(".js-main-textarea").replaceWith("<div class='textarea main__textarea blue-neon-box js-main-textarea js-div-main-textarea'><pre><div class='main-inner'></div><span class='js-main-span'>"+CLONE_TEXT+"</span></pre></div>");
            BLOCK_STATUS = true;
            
            //Длина шаблона и время старта для вычисления статистики
            template_length = CLONE_TEXT.length;
            start_time = new Date;
        }
        
        //Удаление первого пробела, когда рабочее поле ещё пустое
        if(work_text===" "){
            WORK_AREA.val("");
            return;
        }
        
        //Запрет ctrl+v
        if(work_length - oldLength > 1){
            WORK_AREA.val(oldVal);
            return;
        }
        
        //Запрет повторных " "
        if(work_text[work_length-2]===" " && work_text[work_length-1]===" "){
            WORK_AREA.val(oldVal);
            return;
        }
        
        //Пошагово перемещаемся по строке, меняя startStr на конец предыдущего слова
        if((work_length != old_work_length) && (work_text[work_length-1]===" " || work_text[work_length-1]==="\n")){
            //Работа с набираемым текстом
            word["start"] = startStr;
            word["end"]   = work_length - 1;
            word["word"]  = work_text.slice(word["start"], word["end"]);
            startStr      = work_length;
            
            if(work_text[work_length-1]==="\n"){
                word["word"] = word["word"]+"\n";
            }
            
            
            //Работа с шаблонным текстом
            let tempText    = "";
            let tempWord    = [];
            let tempResidue = "";
            
            tempText = $(".js-main-span").html();             
            tempText = tempText.replace(/\n+/g, "\n ");
            
            //Разбиваем строку на массив
            tempWord = tempText.split(/[ \f\r\t\v\u00A0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u2028\u2029\u2028\u2029\u202f\u205f\u3000]/);
            
            //Остаток текста - длина прошлого слова + 1 пробел, длина всего текста tempText
            tempResidue = tempText.slice(tempWord[0].length+1);
            tempResidue = tempResidue.replace(/\n\s+/g, "\n");
            $(".js-main-span").html(tempResidue);
            
            //Удаление символа показывающего перенос пользователю
            tempWord[0] = tempWord[0].replace(/\u21B5/g, "");
            
            let z;
            let temp_word_l = tempWord[0].length - 1;
//            console.log(tempWord[0][temp_word_l]);
            
            if(word["word"] === tempWord[0]){
                
                //Добавить пробел если нет \n
                if(tempWord[0][temp_word_l] != "\n"){
                    z = tempWord[0]+" ";
                }else{
                    z = tempWord[0];
                }
                
                $(".main-inner").append(z);
            }else{
                wrong_length = wrong_length + tempWord[0].length;
                errors++;
                
                //Добавить пробел если нет \n
                if(tempWord[0][temp_word_l] != "\n"){
                    z = "<span class='blue'>"+tempWord[0]+"</span> ";
                }else{
                    z = "<span class='blue'>"+tempWord[0]+"</span>";
                    
                    //При ошибке в слове с переносом строки, каретка в рабочей зоне переносится на следующую строку
                    let add_enter_in_work = $(".js-work-textarea").val();
                    $(".js-work-textarea").val(add_enter_in_work+"\n");
                }
                
                $(".main-inner").append(z);
            }
            
            //Блок вычисления статистики когда текст закончился
            if(tempResidue===""){
                end_time = new Date;
                
                //Время в минутах
                my_time = (end_time - start_time)/(1000*60);
                my_minute = Math.floor(my_time);
                my_second = Math.round( ((end_time - start_time)/1000) - my_minute*60 );
                if(my_minute < 10){
                   my_minute = "0"+my_minute;
                }
                if(my_second < 10){
                   my_second = "0"+my_second;
                }
                
                //Скорость набора в минуту
                result_speed = (template_length - wrong_length)/my_time;
                penalty_speed = result_speed - template_length/my_time;
                
                $(".js-minute").html(("0"+my_minute).slice(-2));
                $(".js-second").html(my_second);
                $(".js-speed").html(result_speed.toFixed(3));
                $(".js-errors").html(errors);
                $(".js-penalty").html(penalty_speed.toFixed(3));
                
                //                console.log(my_time);
                //                console.log(my_minute+":"+my_second);
                //                console.log(errors);
                //                console.log(result_speed.toFixed(3));
                //                console.log(penalty_speed.toFixed(3));
            }
            
            old_work        = WORK_AREA.val();
            old_work_length = old_work.length;
        }
        
        //Запрет удаления после каждого слова
        if(old_work_length!==0 && (work_length < old_work_length)){
            WORK_AREA.val(old_work);
            work_length++;
        }
           
        oldLength = work_length;
        oldVal    = work_text;
    }
//END BLOK-2    
    
    
//BLOK-3 Кнопка "Редактировать текст"    
    //Возврат возможности редактирования текста
    $(".js-main").on('click', ".js-replaceWith", function(){
        if(BLOCK_STATUS === true){
            $(".js-div-main-textarea").replaceWith("<textarea class='textarea main__textarea blue-neon-box js-main-textarea js-textarea' placeholder='Добавьте ваш текст в это окно или выберите текст из списка'>"+CLONE_TEXT+"</textarea>");
            WORK_AREA.attr("placeholder", "Сначала добавьте текст в верхнее поле");
            autosize( $('.js-textarea') );
            BLOCK_STATUS = false;
            WORK_AREA.val("");
            
            //Обнуление переменных.
            null_var();
        } else{
            alert("Поле для ввода уже разблокировано");
        }
    });
//END BLOK-3    
    
    
//BLOK-4    Подгрузка и работа с текстами из базы
    function ajaxQuery (id, its_text, clss){
        $.ajax({
            url:    "ajax_user.php",
            method: "post",
            data: {
                id: id,
                its_text: its_text
            },
            success: function(msg){
                //                $(clss).html(msg);
                $(clss).replaceWith("<textarea class='textarea main__textarea blue-neon-box js-main-textarea js-textarea' placeholder='Добавьте ваш текст в это окно или выберите текст из списка'>"+msg+"</textarea>");
                
                WORK_AREA.val("");
                let result = text_replace();
                localStorage.setItem("text", result);
                
                //Обнуление переменных.
                null_var();
            }
        });
    }
    
    //Получение дефолтного текста
    $(".default-text-list").on("click", ".default-text-list__name", function(){
        let id = $(this).attr("data-id");
        let name = $(this).html();
        let area = "Default";
        ajaxQuery(id, "get_default_text", ".js-main-textarea");
        
        $('.js-work-textarea').removeAttr("disabled");
        $('.js-work-textarea').attr("placeholder", "Готовы приступать? :) \nШаблон блокируется на время теста.");
        
        localStorage.setItem("name", name);
        localStorage.setItem("area", area);
    });
    
    
    //Получение пользовательского текста
    $(".users-theme").on("click", ".user-text-list__name", function(){
        let id   = $(this).attr("data-id");
        let name = $(this).html();
        let area = $(this).attr("data-area");
        ajaxQuery(id, "get_user_text",".js-main-textarea");
        
        $('.js-work-textarea').removeAttr("disabled");
        $('.js-work-textarea').attr("placeholder", "Готовы приступать? :) \nШаблон блокируется на время теста.");
        $('.js_main-name').val(name);
        $('.js_main-theme-name').val(area);
        $('.current-text-id').html("ID:<span class='js_current-text-id'>"+id+"</span>");
        
        localStorage.setItem("id", id);
        localStorage.setItem("name", name);
        localStorage.setItem("area", area);
        
    });
    

    //При перезагрузке страницы вставляем старые данные
    if (performance.navigation.type == 1) {
        let r_id   = localStorage.getItem("id");
        let r_name = localStorage.getItem("name");
        let r_area = localStorage.getItem("area");
        let r_text = localStorage.getItem("text");
        if(typeof(r_text) != "undefined" && r_text !== null){
            $('.js_main-name').val(r_name);
            $('.js_main-theme-name').val(r_area);
            $('.current-text-id').html("ID:<span class='js_current-text-id'>"+r_id+"</span>");
            $(".js-main-textarea").replaceWith("<textarea class='textarea main__textarea blue-neon-box js-main-textarea js-textarea' placeholder='Добавьте ваш текст в это окно или выберите текст из списка'>"+r_text+"</textarea>");
        }
        
        if(r_text != false){
            WORK_AREA.removeAttr("disabled");
            WORK_AREA.attr("placeholder", "Готовы приступать? :) \nШаблон блокируется на время теста.");
        }else{
            WORK_AREA.attr("disabled", "true");
            WORK_AREA.attr("placeholder", "Сначала добавьте текст в верхнее поле");
            WORK_AREA.val("");
        }
    }
    
//    window.onunload = function(){
//        localStorage.clear();
//    }
    
    //Случайный текст
    $(".main-header-menu").on("click", ".js_get-random-text", function(){
        let q = $(".select").find('.default-text-list__name').length;
        
        function getRandomInt(min, max) {
          return Math.floor(Math.random() * (max - min)) + min;
        }
        let qwe = getRandomInt(0, q);

        
        alert(qwe);
//        ajaxQuery(id, ".js-main-textarea");
    });
//END BLOK-4
    
    
//BLOK-5    Кнопка 'новый текст', очищаем все поля
    $(".main-header-menu").on('click', '.js_clean-all', function(){
        $('.js_main-name').val("");
        $('.js_main-theme-name').val("");
        $('.current-text-id').html("");
        $(".js-main-textarea").val("");
        localStorage.clear();
        null_var();
    });
//END BLOK-5    
}
document.addEventListener("DOMContentLoaded", main_ready);