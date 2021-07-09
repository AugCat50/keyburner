"use strict"
function user(){
    //Удаление начальных, конечных пробелов, излишних пробельных символов в тексте
    function user_text_replace(){
        let val = $(".js-main-textarea").val().trim();
        
        let qwe = val.replace(/\n+\s+/g, "\n");
        qwe = qwe.replace(/[ \f\r\t\v\u00A0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u2028\u2029\u2028\u2029\u202f\u205f\u3000][ \f\r\t\v\u00A0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u2028\u2029\u2028\u2029\u202f\u205f\u3000]+/g, " ");
        qwe = qwe.replace(/[ \n\f\r\t\v\u00A0\u1680\u180e\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u2028\u2029\u2028\u2029\u202f\u205f\u3000][ \n]+/g, "\n");
        qwe = qwe.replace(/[\n\u21B5]+/g, "\n");
        
        return qwe;
    }
    
    //Добавление (add), удаление (del), редактирование текста (edit). Для добавления id = false
    function ajaxUser(id, operation, name, theme, text, clss){
        $.ajax({
            url:    "ajax_user.php",
            method: "post",
            data: {
                id:        id,
                operation: operation,
                name:      name,
                theme:     theme,
                text:      text
            },
            success: function (data){
                
                if(operation && operation != 'search'){
                    //В ответ приходит строка ответа из модели и html отрисовки нового меню. (id, <span>Текст ответа</span>, html нового меню) Разделяем ответ и код и отрисовываем в их местах
                    let index, answer, html;
                    if(operation === 'add'){
                        //При добавлении текста надо вернуть id из модели
                        let in_id, id;
                        in_id  = data.indexOf("<span>");
                        index  = data.indexOf("</span>") + 7;
                        id     = data.slice(0, in_id);
                        answer = data.slice(in_id, index);
                        html   = data.slice(index);
                        
                        //Если id нет, не отображаем блок. Это может произойти в случае, когда текст с таким именем уже есть
                        if(id != '') {
                            $('.js_current-text-id-wrapper').html("ID:<span class='js_current-text-id'>"+id+"</span>");
                        }                      
                        localStorage.setItem("id", id);
                    }else{
                        //При изменении текста id уже есть
                        index  = data.indexOf("</span>") + 7;
                        answer = data.slice(0, index);
                        html   = data.slice(index);
                    }
                    
                    localStorage.setItem("name", name);
                    localStorage.setItem("area", theme);
                    localStorage.setItem("text", text);
                    
                    $(clss).html(answer).show();
                    $('.users-theme').html(html);
                    
                    //Количество текстов в категории
                    $(".user-text-list").each(function () {
                        let q = $(this).find('.select__option').length;
                        $(this).children(".user-text-list__head").append(" ["+q+"]");
                    });
                }else {
                    //Код вывода ответа на запрос поиска
                    $(clss).html(data).show();
                    $(clss).attr('search_attr', text);
                    
                    //Количество текстов найдено
                    $(clss).each(function () {
                        let q = $(this).find('.select__option').length;
                        $(this).children(".user-text-list__head").append(" ["+q+"]");
                    });
                }
                
                setTimeout(function(){
                        $(clss).hide();
                }, 3000);
                
            },
            error: function (data){
                $(clss).html(data).show();
            }
        });
    }
    
    //Добавить или изменить текст
    $(".js-main").on("click", ".js_add-text", function(){
        let id    = $('.js_current-text-id').html();
        let name  = $(".js_main-name").val();
        let theme = $(".js_main-theme-name").val();
        let text  = $(".js-main-textarea").val();
        
        function q(){
            setTimeout(function(){
                $(".message").hide();
            }, 100000);
        }
        
        if($(".js_main-theme-name").attr('data') === "Default"){
            $(".message").html("Нельзя добавлять, менять, удалять стандартные тексты.<br> Тема 'Default' зарезервирована.").show();
            q();
            return;
        }else if(name == false){
            q();
            return;
        }else if(theme == false){
            $(".message").html("Заполните тему!").show();
            q();
            return;
        }else if(theme === 'Default'){
            $(".message").html("Тема 'Default' зарезервирована!").show();
            q();
            return;
        }else if(text == false){
            $(".message").html("Заполните текст!").show();
            q();
            return;
        }
        
        let end_text = user_text_replace();
        if(id != undefined){
            ajaxUser(id, "edit", name, theme, end_text, ".message");
        }else{
            ajaxUser(false, "add", name, theme, end_text, ".message");
        }
    });
    
    //Удалить текст
    $(".js-main").on("click", ".js-del", function(){
        $('.dialog_delete').show();
    });
    
    $('.body').on('click', '.js_dialog_delete__hide', function(event){
        $('.dialog_delete').hide();
    })
    
    $('.body').on('click', '.js_dialog_delete__ready', function(event){
        let id         = $('.js_current-text-id').html();
        let theme      = $(".js_main-theme-name");
        let theme_name = theme.val();
        //В случае, если текст дефолтный, но пользователь изменил val в input темы, берём значение для проверки из атрибута
        let theme_attr = theme.attr('data');
        
        
        if(id){
            ajaxUser(id, "del", false, false, false, ".message");
            $('.js_main-name').val("");
            theme.val("");
            $('.current-text-id').html("");
            $(".js-main-textarea").val("");
            localStorage.clear();
        }else if(!id && theme_attr === "Default"){
            $(".js_dialog_delete-message").html("Нельзя удалять стандартные тексты!").show();
        }else{
            $(".js_dialog_delete-message").html("Не удалось получить ID текста. Обратитесь к администратору").show();
        }
        $('.dialog_delete').hide();
    })
    
    
    //Поиск
    $('.search').on('click', '.js_search-button', function(){
        let result_box  = $('.js_serch-result');
        let search_word = $('.js_search-word').val();
        let search_attr = result_box.attr('search_attr');
        
        if(search_attr === search_word){
            //Если данное ключевое слово уже запрашивалось и результаты уже есть, просто показываем
            result_box.show();
        }else if(search_word != undefined && search_word != ""){
            ajaxUser(false, "search", false, false, search_word, ".js_serch-result");
        }
    });
    
    //Поиск нажатием Enter
    $('.js_search-word').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            let result_box  = $('.js_serch-result');
            let search_word = $('.js_search-word').val();
            let search_attr = result_box.attr('search_attr');
            
            if(search_attr === search_word){
                //Если данное ключевое слово уже запрашивалось и результаты уже есть, просто показываем
                result_box.show();
            }else if(search_word != undefined && search_word != ""){
                ajaxUser(false, "search", false, false, search_word, ".js_serch-result");
            }
        }
    });
    
    $('.js_serch-result').on('click', '.js_saerch-close', function(){
        $('.js_serch-result').hide();
        $('.js_search-word').val("");
    });
    
    
//    $('.js_main-name').oninput(function(){
//        let name = $('.js_main-name').val();
//        localStorage.setItem("name", name);
//    });
    
//    $('.js_main-theme-name').oninput(function(){
//        let area = $('.js_main-theme-name').val();
//        localStorage.setItem("area", area);
//    });
    
    
    $('.graph').on('click', '.js_graph-button', function(){
        $('.graph__dialog').show();
    });
    
    $('.graph').on('click', '.js-graph__close', function(){
        $('.graph__dialog').hide();
    });
    
}
document.addEventListener("DOMContentLoaded", user);