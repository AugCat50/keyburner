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
                if(operation){
                    //В ответ приходит строка ответа из модели и html отрисовки нового меню. Разделяем ответ и код и отрисовываем в их местах
                    let index = data.indexOf("</span>") + 7;
                    let answer = data.slice(0, index);
                    let html = data.slice(index);
                    
                    $(clss).html(answer).show();
                    $('.users-theme').html(html);
                    
                    //Количество текстов в категории
                    $(".user-text-list").each(function () {
                        let q = $(this).find('.select__option').length;
                        $(this).children(".user-text-list__head").append(" ["+q+"]");
                    });
                }else{
                    $(clss).html(data).show();
                }
            },
            error: function (data){
                $(clss).html(data).show();
            }
        });
    }
    
    //Добавить текст
    $(".js-main").on("click", ".js_add-text", function(){
        let id    = $('.js_current-text-id').html();
        let name  = $(".js_main-name").val();
        let theme = $(".js_main-theme-name").val();
        let text  = $(".js-main-textarea").val();
        if(name == false){
            $(".message").html("Заполните имя!").show();
            return;
        }else if(theme == false){
            $(".message").html("Заполните тему!").show();
            return;
        }else if(text == false){
            $(".message").html("Заполните текст!").show();
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
        let id   = $('.js_current-text-id').html();
        if(id){
            ajaxUser(id, "del", false, false, false, ".message");
            $('.js_main-name').val("");
            $('.js_main-theme-name').val("");
            $('.current-text-id').html("");
            $(".js-main-textarea").val("");
            localStorage.clear();
        }
    });
    
    
    
    
    
    
    
    
//    $(".main-header-menu").on("click", ".js_desroy", function(){
//        $.ajax({
//            url:"";
//        });
//    })
}
document.addEventListener("DOMContentLoaded", user);