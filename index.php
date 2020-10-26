<!DOCTYPE html>
<html lang="ru" class="html">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="Тренажёр слепого набора">
        <meta name="keywords" content="Keyburner, Тренажёр слепого набора">
        <meta name="autor" content="draackul2@gmail.com">
<!--        <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
        <title>Keyburner - Тренажёр слепого набора</title>
        
        <link rel="shortcut icon" type="image/svg" href="img/favicon.jpg"/>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap&subset=cyrillic" rel="stylesheet"> 
        
        <link rel="stylesheet" href="normalize/normalize.css">
        
        <link rel="stylesheet" href="common.blocks/body/body.css">
        
        <link rel="stylesheet" href="common.blocks/main-wrapper/main-wrapper.css">
        <link rel="stylesheet" href="common.blocks/main-header/main-header.css">
        <link rel="stylesheet" href="common.blocks/main/main.css">
        <link rel="stylesheet" href="common.blocks/statistics-section/statistics-section.css">
        <link rel="stylesheet" href="common.blocks/default-text-list/default-text-list.css">
        
        <link rel="stylesheet" href="common.blocks/footer/footer.css">
        
        <link rel="stylesheet" href="common.blocks/html/html.css">
        <link rel="stylesheet" href="common.blocks/neon/neon.css">
        
        <link rel="stylesheet" href="common.blocks/button/button.css">
        
        <link rel="stylesheet" href="common.blocks/dialog/dialog.css">
        
        <script src="js/jquery-3.5.1.min.js"></script>
        <script src="js/textarea_autosize.js"></script>
        <script src="js/main.js"></script>
        <script src="js/log_in.js"></script>
        <script src="js/check_in.js"></script>
<!--        <script src="js/default_text.js"></script>-->
    </head>
    
    <body class="body">
        <div class="body__size">
            <div class="main-wrapper">
                <header class="main-header">
                    <h1 class="h1 main-header_h1 bright-blue-neon">Keyburner</h1>
                    <menu class="main-header-menu js_main-header-menu">
                        <nav class="">
                            <li class="blue-neon main-header-menu__item js_clean-all"><span class='pointer'>&#187;</span> Новый текст</li>
                            <li class="blue-neon main-header-menu__item js_get-random-text-default"><span class='pointer'>&#187;</span> Случайный текст</li>
                            <li class="blue-neon main-header-menu__item js_check-in__show"><span class='pointer'>&#187;</span> Регистрация</li>
                            <li class="blue-neon main-header-menu__item js_authorization__show"><span class='pointer'>&#187;</span> Вход</li>
<!--                            <li class="blue-neon main-header-menu__item"><span class='pointer'>&#187;</span> Добавить текст</li>-->
                        </nav>
                    </menu>
                    <ul class="default-text-list js_ul_list js_default-text-list">
                        <h3 class="default-text-list__head pink-neon">Быстрый старт:</h3>
                        <?php
                            require_once "controllers/component_default_get_name_texts.php";
                            foreach($data as $val){
                                echo "<li class='default-text-list__name blue-neon js_default-text-list__name' data-id='".$val['id']."' name='".$val['name']."'><span class='pointer'>&#187; </span><span class='js_value'>" . $val['name'] . "</span></li>";
                            }
                        ?>
                    </ul>
                </header> 
                
                <main class="main js-main">
                    
                    <section class="section statistics-section main__statistics">
                        <div class="statistics-section__item first">
                            <span class="bright-blue-neon">Последний результат:</span>
                        </div>
                        
                        <div class="statistics-section__item">
                            <span class="pink-neon">Время: </span>
                            <span class="blue-neon js-minute">00</span><span class="blue-neon">:</span><span class="blue-neon js-second">00</span>
                        </div>
                        <div class="statistics-section__item">
                            <span class="pink-neon">Скорость: </span>
                            <span class="blue-neon js-speed">0</span>
                        </div>
                        <div class="statistics-section__item">
                            <span class="pink-neon">Ошибок: </span>
                            <span class="blue-neon js-errors">0</span>
                        </div>
                        <div class="statistics-section__item">
                            <span class="pink-neon">Штраф: </span>
                            <span class="blue-neon js-penalty">0</span>
                        </div>
                        <div class="statistics-section__item blue-neon last"><span class="">(симв. в мин.)</span></div>
                    </section>
                    
                    <section class="section main__section js_section-template">
                        <textarea class="textarea main__textarea blue-neon-box js-main-textarea js-textarea" placeholder='Добавьте ваш текст в это окно или выберите текст из списка'></textarea>
                    </section>
                    
                    <section class="section main__section">
                        <textarea class="textarea main__textarea main__work-textarea blue-neon-box js-work-textarea js-textarea" placeholder='Сначала добавьте текст в верхнее поле' autofocus disabled></textarea>
                    </section>
                    
<!--                    <button class="button pink-neon pink-neon-box test-button" id="test">Test</button>-->
                    <button class="button main__button pink-neon pink-neon-box js-replaceWith" title="Редактировать"><img src="img/edit.png" class="main__ico"></button>
                </main>
            </div>
            
            <footer class="footer">
                <details class="details">
                    <summary>&copy; AugCat50</summary>
                    <p><a href="mailto:draackul2@gmail.com">draackul2@gmail.com</a></p>
                </details>
                <a target="_blank" href="https://www.artstation.com/antoinecollignon">Art by Antoine Collignon</a>
            </footer>
        </div>
        
        <dialog class="dialog authorization">
            <div class="dialog__display">
                <form class="dialog__form" method="post">
                    <h3 class="dialog__h bright-blue-neon">Авторизация</h3>
                    <input name="user" class="dialog__input blue-neon-box js_log-in-name" type="text" placeholder="Login">
                    <input name="password" class="dialog__input blue-neon-box js_log-in-password" type="password" placeholder="Password">
                    <div class="dialog__message pink-neon"></div>
                    <button class="button dialog__button blue-neon-box js_log-in-ready">Войти</button>
                    <button class="button dialog__button blue-neon-box js_authorization__hide">Отмена</button>
                </form>
            </div>
        </dialog>
        <dialog class="dialog check-in">
            <div class="dialog__display">
                <form class="dialog__form" method="post">
                    <h3 class="dialog__h bright-blue-neon">Регистрация</h3>
                    <input name="user" class="dialog__input blue-neon-box js_check-in-name" type="text" placeholder="Login">
                    <input name="password" class="dialog__input blue-neon-box js_check-in-password1" type="text" placeholder="Password">
                    <input name="password-2" class="dialog__input blue-neon-box js_check-in-password2" type="text" placeholder="Repeat password">
                    <input name="mail" class="dialog__input blue-neon-box js_check-in-mail" type="email" placeholder="Email">
                    <div class="dialog__message pink-neon js_check-in-message"></div>
                    <button class="button dialog__button blue-neon-box js_check-in-ready">Регистрация</button>
                    <button class="button dialog__button blue-neon-box js_check-in__hide">Закрыть</button>
                </form>
            </div>
        </dialog>
    </body>
</html>