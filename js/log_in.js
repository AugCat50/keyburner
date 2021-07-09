"use strict"
function log_in(){
    function ajax_query_log_in(login, password, identifier){
        let name = "";
        let mail = "";
        
        if(identifier){
            name = login;
        }else{
            mail = login;
        }
        
        
        $.ajax({
            url: "ajax_log_in.php",
            method: "post",
            data:{
                name: name,
                mail: mail,
                password: password
            },
            success: function(data){
                if(data === "Есть совпадение!"){
                    localStorage.clear();
                    document.location.href = 'user.php';
                }
                $(".dialog__message").html(data);
            },
            error: function(data){
                alert("ajax error:"+data);
            }
        });
    }
    
    $(".js_log-in-ready").click(function(event){
        event.preventDefault();
        let login     = $(".js_log-in-name").val();
        let password = $(".js_log-in-password").val();
        
        if(!login && !password){
            $(".dialog__message").html("<p>Поля должны быть заполнены!</p>");
        }else if(!login){
            $(".dialog__message").html("<p>Заполните логин!</p>");
        }else if(!password){
            $(".dialog__message").html("<p>Заполните парль!</p>");
        }else{
            
            if(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(login)){
//                alert("Почта! "+login);
                ajax_query_log_in(login, password, false);
            }else{
//                alert("Не почта, "+login);
                ajax_query_log_in(login, password, true);
            }
            
        }
    });
   //BLOK-5 authorization log_in
    $(".js_authorization__show").click(function(){
        $(".authorization").show();
    });
    
    $(".js_authorization__hide").click(function(event){
        event.preventDefault();
        $(".authorization").hide();
    });
//END BLOK-4
}
document.addEventListener("DOMContentLoaded", log_in);