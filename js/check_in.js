"use strict"
function check_in(){
    function ajax_query_check_in(name, pass_1, pass_2, mail){
        $.ajax({
            url:    "ajax_check_in.php",
            method: "post",
            data:{
                name:   name,
                pass_1: pass_1,
                pass_2: pass_2,
                mail:   mail
            },
            success:function(answer){
                $(".js_check-in-message").html(answer);
                
//                setTimeout(function(){
//                    $(".check-in").hide();
//                }, 2000);
            },
            error:function(answer){
                $(".js_check-in-message").html(answer);
            }
        });
    }
    
    //Нажатие по ссылке "Регистрация", вывод формы на экран
    $(".js_check-in__show").click(function(){
        $(".check-in").show();
    });
    
    //Нажатие кнопки "Отмена"
    $(".js_check-in__hide").click(function(event){
        event.preventDefault();
        $(".check-in").hide();
    });
    
    //Нажати по кнопке "Регистрация"
    $(".check-in").on("click", ".js_check-in-ready", function(event){
        event.preventDefault();
        let name       = $(".js_check-in-name").val();
        let password_1 = $(".js_check-in-password1").val();
        let password_2 = $(".js_check-in-password2").val();
        let mail       = $(".js_check-in-mail").val();
        
        //Проверки
        if(!name){
            $(".js_check-in-message").html("Заполните имя!");
        }else if(!password_1){
            $(".js_check-in-message").html("Заполните пароль!");
        }else if(!password_2){
            $(".js_check-in-message").html("Заполните проверочный пароль!");
        }else if(!mail){
            $(".js_check-in-message").html("Запотните почту!");
        }else if(password_1 != password_2){
            $(".js_check-in-message").html("Пароли не совпадают!");
        }else{
            ajax_query_check_in(name, password_1, password_2, mail);
        }

    });
}
document.addEventListener("DOMContentLoaded", check_in);