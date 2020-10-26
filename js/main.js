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
    if(load_val != false && load_val !== null){
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
    let start_time      = 0; 
    let end_time        = 0;
    let template_length = 0;
    let wrong_length    = 0;
    let errors          = 0;
    
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
                my_time   = (end_time - start_time)/(1000*60);
                my_minute = Math.floor(my_time);
                my_second = Math.round( ((end_time - start_time)/1000) - my_minute*60 );
                if(my_minute < 10){
                   my_minute = "0"+my_minute;
                }
                if(my_second < 10){
                   my_second = "0"+my_second;
                }
                
                //Скорость набора в минуту
                result_speed  = (template_length - wrong_length)/my_time;
                penalty_speed = result_speed - template_length/my_time;
                result_speed  = result_speed.toFixed(3);
                
                $(".js-minute").html(("0"+my_minute).slice(-2));
                $(".js-second").html(my_second);
                $(".js-speed").html(result_speed);
                $(".js-errors").html(errors);
                $(".js-penalty").html(penalty_speed.toFixed(3));
                
                
                //Отправка-получение статистики
                let stat_id = $('.js_current-text-id').html();
                
                function ajax_statistics(id, time, speed){
                    let stat_time = time.getDate() +"."+ time.getMonth() +"."+ time.getFullYear();
                    
                    $.ajax({
                        url:    "ajax_statistics.php",
                        method: "post",
                        data:{
                            id:     id,
                            time:   stat_time,
                            speed:  speed
                        },
                        success: function (data){
                            $('.message').html(data);
                            $('.message').show();
                        },
                        error: function (data){
                            $('.message').html(data);
                            $('.message').show();
                        }
                    });
                }
                
                
                if(stat_id != undefined && stat_id != ""){
                    let qwe = ajax_statistics(stat_id, end_time, result_speed);
                }
                //                console.log(my_time);
                //                console.log(my_minute+":"+my_second);
                //                console.log(errors);
                //                console.log(result_speed.toFixed(3));
                //                console.log(penalty_speed.toFixed(3));
                ajaxQuery_stat(id, 'user');
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
        //its_text = "get_user_text" если пользовательский или "get_default_text" если дефолтный
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
                
                let val = $(".js-main-textarea").val();
                if(val != false){
                    WORK_AREA.removeAttr("disabled");
                    WORK_AREA.attr("placeholder", "Готовы приступать? :) \nШаблон блокируется на время теста.");
                }else{
                    WORK_AREA.attr("disabled", "true");
                    WORK_AREA.attr("placeholder", "Сначала добавьте текст в верхнее поле");
                    WORK_AREA.val("");
                }
            }
        });
    }
    
    function ajaxQuery_stat (id, access){
        $.ajax({
            url: "ajax_statistics.php",
            method: "post",
            data: {
                id: id
            },
            success: function(data){
                let max, image;
                let index = data.indexOf("---");
                if(index){
                    max   = data.slice(0, index);
                    image = data.slice(index+3);
                    $(".js_stat-best").html(max);
                    
                    //График есть - показываем кнопку, графика нет - скрываем кнопку
                    if(image != '0'){
                        $(".graph__inner").html(image).show();
                        $(".graph").show();
                    }else{
                        $(".graph").hide();
                    }
                }
            },
            error: function(data){
                    $(".message").html(data).show();
            }
        });
    }
    
    
    //Получение дефолтного текста, для списка ul на главной
    $(".js_ul_list.js_default-text-list").on('click', '.js_default-text-list__name', function(){
        localStorage.clear();
        let id = $(this).attr('data-id');
        
        ajaxQuery(id, "get_default_text", ".js-main-textarea");
        
    });
    
    //Получение дефолтного текста
    /*
        Как обойти отсутствие ивента клик по <select><option></option></select> в браузерах на движке хрома. 
        Как это работает в нормальных браузерах - есть просто ивент клик и ты можешь получить любые атрибуты элемента option по которому ты кликнул. Доступ ко всем данным одним кликом, удобненько.
        
        Как это работает в хром подобных браузерах. Ивента клик по option нет, и соответственно получить доступ к нему напрямую ты никак не можешь. Но можно обойти это невъебаться хитрым образом. Есть ивент клик по самому <select>. Это значит, можно получить доступ к данным самого селекта. У выпадающего списка есть такой параметр val() - значение выбранного элемента, а именно - это текст внутри <option></option>. За это можно зацепиться, если этот же текст добавить в какой-то артибут самого тэга. Селектором атрибута, в который подставляешь текст из val() можно получить доступ к самому option, по которому кликнули, и извлечь уже из него данные. Но это ещё не всё, ведь когда ты открываешь список - это один клик, а когда кликаешь по пункту списка - это уже второй клик. А надо обрабатывать только второй. Поэтому по первому клику, надо добавлять к select флаг, например класс 'open' и получать val() только когда флаг есть. После выполнения кода флаг удаляется. И в итоге, вся эта схема работает как простой ивент клик в нормальных браузерах.
        
        change, :selected использовать нельзя, поскольку не будет обрабатываться клик по уже выбранному пункту списка.
    */
    $(".default-select").click(function(){
        let my_this = $(this);
        
        //Если флаг уже есть, то это клик по <option> внутри списка, а згачит можно получить val селекта
        if(my_this.hasClass('opened')){
            localStorage.clear();
            let name      = my_this.val();
            //Получаем элемент по селектору типа $("[attr = 'val']")
            let my_option = $('[name = "'+name+'"]');
            let id        = my_option.attr("data-id");
            let area      = "Default";
            
            ajaxQuery(id, "get_default_text", ".js-main-textarea");
            //Пока дефолтные тексты без статистики
            //ajaxQuery_stat(id, 'user');
            
            //Заполняем данными поля "Имя", "Тема", атрибут data инпута "Тема", затираем ID
            $('.js_main-name').val(name);
            $('.js_main-theme-name').val(area);
            $('.js_main-theme-name').attr('data', area);
            $('.js_current-text-id-wrapper').html('');
            
            $('.js-work-textarea').removeAttr("disabled");
            $('.js-work-textarea').attr("placeholder", "Готовы приступать? :) \nШаблон блокируется на время теста.");
            
            //localStorage необходимо очистить, на случай если там сохранён ID пользовательского текста
            localStorage.setItem("name", name);
            localStorage.setItem("area", area);
            localStorage.setItem("area-attr", area);
            //Код выполнен, удаляем флаг
            my_option.removeClass('opened');
        }else{
            //Если флага нет, то это первый клик по списку, раскрывающий его. Добавляем флаг к <select>
            my_this.addClass('opened');
        }
    });
    
    
    //При клике по документу вне раскрытого <select> удаляем класс opened у выпадающих списков
    //Это обязательная часть кода для 'получения пользовательского текста по клику' и 'получения дефолтного текста по клику'
    $(document).click(function(e){
        let select = $('.select');
        
        if(!select.is(e.target)){
            select.removeClass('opened');
        }
    });
    
    
    //Получение пользовательского текста и текста из поиска. По аналогии с дефолтным, см выше
    //Search не ищет в дефолтных текстах. Но эту возможность можно добавить, поэтому я решил не убирать проверки.
    $(".js_users-theme, .js_serch-result").on("click", ".js_select", function(){
        let my_this = $(this);
        
        //Если флаг уже есть, то это клик по <option> внутри списка, а згачит можно получить val селекта
        if(my_this.hasClass('opened')){
            localStorage.clear();
            let name      = my_this.val();
            //Получаем элемент по селектору типа $("[attr = 'val']")
            let my_option = $('[name = "'+name+'"]');
            let id        = my_option.attr("data-id");
            let area      = my_option.attr("data-area");
            
            //Удаление лишнего при клике по результатам поиска
            let index = name.indexOf(" --");
            if(index != -1){
                name = name.slice(0, index);
            }
            
            ajaxQuery(id, "get_user_text",".js-main-textarea");
            ajaxQuery_stat(id, 'user');
            
            //Удалить атрибут data='Default', защищающий стандартнные тексты от изменения
            $('.js_main-theme-name').removeAttr('data');
            
            //Заполняем данными поля "Имя", "Тема", атрибут data инпута "Тема", затираем ID
            $('.js-work-textarea').removeAttr("disabled");
            $('.js-work-textarea').attr("placeholder", "Готовы приступать? :) \nШаблон блокируется на время теста.");
            $('.js_main-name').val(name);
            $('.js_main-theme-name').val(area);
            
            
            //Если в поиске дефолтный текст, добавляем атрибут маркер data='Default' инпуту "Тема" и не выводим ID
            if(area !== "Default"){
                $('.js_current-text-id-wrapper').html("ID:<span class='js_current-text-id'>"+id+"</span>");
            }else{
                $('.js_current-text-id-wrapper').html("");
                $('.js_main-theme-name').attr('data', 'Default');
            }
            
            //Если в поиске дефолтный текст, сохраняем маркер area-attr='Default' и не сохраняем ID
            if(area !== "Default"){
                localStorage.setItem("id", id);
            }else{
                localStorage.setItem("area-attr", area);
            }
            
            localStorage.setItem("name", name);
            localStorage.setItem("area", area);
            
            my_option.removeClass('opened');
        }else{
            //Если флага нет, то это первый клик по списку, раскрывающий его. Добавляем флаг к <select>
            my_this.addClass('opened');
        }        
    });
    

    //При перезагрузке страницы вставляем старые данные
    if (performance.navigation.type == 1) {
        let r_id    = localStorage.getItem("id");
        let r_name  = localStorage.getItem("name");
        let r_area  = localStorage.getItem("area");
        let r_ar_at = localStorage.getItem("area-attr");
        let r_text  = localStorage.getItem("text");
        
        if(typeof(r_text) !== "undefined" && r_text !== null && r_text !== ""){
            $('.js_main-name').val(r_name);
            $('.js_main-theme-name').val(r_area);
            
            if(r_id && typeof(r_id) !== 'undefined' && r_id !== 'undefined'){
                $('.current-text-id').html("ID:<span class='js_current-text-id'>"+r_id+"</span>");
            }
            
            if(r_ar_at && typeof(r_ar_at) !== 'undefined'){
                $('.js_main-theme-name').attr('data', r_ar_at);
            }
            
            $(".js-main-textarea").replaceWith("<textarea class='textarea main__textarea blue-neon-box js-main-textarea js-textarea' placeholder='Добавьте ваш текст в это окно или выберите текст из списка'>"+r_text+"</textarea>");
        }
        
        if(r_text != false && r_text !== null){
            WORK_AREA.removeAttr("disabled");
            WORK_AREA.attr("placeholder", "Готовы приступать? :) \nШаблон блокируется на время теста.");
        }else{
            WORK_AREA.attr("disabled", "true");
            WORK_AREA.attr("placeholder", "Сначала добавьте текст в верхнее поле");
            WORK_AREA.val("");
        }
    }
    
    
    //Случайный дефолтный текст
    var BUFFER = -1;
    $(".js_default-text-list, .main-header-menu").on("click", ".js_get-random-text-default", function(){
        let list_length = $(".js_default-text-list").find('.js_default-text-list__name').length;
        
        function getRandomInt(min, max) {
          return Math.floor(Math.random() * (max - min)) + min;
        }
        
        //Чтобы не повторялись числа
        let number = getRandomInt(0, list_length);
        while(BUFFER === number){
            number = getRandomInt(0, list_length);
        }
        BUFFER = number;
        
        let find_elem = $('.js_default-text-list__name:eq('+number+')');
        let elem_id   = find_elem.attr('data-id');
        let elem_name = find_elem.attr('name');
        let elem_area = 'Default';
        
        $('.js_main-name').val(elem_name);
        $('.js_main-theme-name').val(elem_area);
        $('.js_main-theme-name').attr('data', elem_area);
        $('.js_current-text-id-wrapper').html('');
        
        //localStorage необходимо очистить, на случай если там сохранён ID пользовательского текста
        localStorage.clear();
        localStorage.setItem("name", elem_name);
        localStorage.setItem("area", elem_area);
        localStorage.setItem("area-attr", elem_area);
        
        ajaxQuery(elem_id, "get_default_text", ".js-main-textarea");
        

    });
    
    
    //Случайный пользовательский текст
    var BUFFER_1 = -1;
    $(".main-header-menu").on("click", ".js_get-random-text", function(){
        let list_length = $(".js_users-theme").find('.js_user-text-name').length;
        
        function getRandomInt(min, max) {
          return Math.floor(Math.random() * (max - min)) + min;
        }
        
        //Чтобы не повторялись числа
        let number = getRandomInt(0, list_length);
        while(BUFFER_1 === number){
            number = getRandomInt(0, list_length);
        }
        BUFFER_1 = number;
        
        let find_elem = $('.js_user-text-name:eq('+number+')');
        let elem_id   = find_elem.attr('data-id');
        let elem_name = find_elem.attr('name');
        let elem_area = find_elem.attr('data-area');
        
        $('.js_main-name').val(elem_name);
        $('.js_main-theme-name').val(elem_area);
        $('.js_main-theme-name').removeAttr('data');
        $('.js_current-text-id-wrapper').html("ID:<span class='js_current-text-id'>"+elem_id+"</span>");
        
        //localStorage необходимо очистить, на случай если там сохранён ID пользовательского текста
        localStorage.clear();
        localStorage.setItem("name", elem_name);
        localStorage.setItem("area", elem_area);
        localStorage.setItem("id", elem_id);
        
        ajaxQuery(elem_id, "get_user_text", ".js-main-textarea");
    });
//END BLOK-4
    
    
//BLOK-5    Кнопка 'новый текст', очищаем все поля
    $(".main-header-menu").on('click', '.js_clean-all', function(){
        $('.js_main-name').val("");
        $('.js_main-theme-name').val("");
        $('.js_main-theme-name').removeAttr('data');
        $('.current-text-id').html("");
        $(".js-main-textarea").val("");
        localStorage.clear();
        null_var();
    });
//END BLOK-5    
}
document.addEventListener("DOMContentLoaded", main_ready);