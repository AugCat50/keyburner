<!DOCTYPE html>
<html lang="ru" class="html user">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="Тренажёр слепого набора">
        <meta name="keywords" content="Keyburner, Тренажёр слепого набора">
        <meta name="autor" content="draackul2@gmail.com">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <link rel="stylesheet" href="common.blocks/user-text-list/user-text-list.css">
        
        <link rel="stylesheet" href="common.blocks/footer/footer.css">
        
        <link rel="stylesheet" href="common.blocks/html/html.css">
        <link rel="stylesheet" href="common.blocks/neon/neon.css">
        
        <link rel="stylesheet" href="common.blocks/button/button.css">
        
        <link rel="stylesheet" href="common.blocks/dialog/dialog.css">
        
        <link rel="stylesheet" href="common.blocks/select/select.css">
        
        <script src="js/jquery-3.5.1.min.js"></script>
        <script src="js/textarea_autosize.js"></script>
        <script src="js/main.js"></script>
        <script src="js/log_in.js"></script>
        <script src="js/check_in.js"></script>
        <script src="js/user.js"></script>
    </head>
    
    <body class="body">
        <div class="body__size">
            <div class="main-wrapper">
                <header class="main-header">
                    <h1 class="h1 main-header_h1 bright-blue-neon">Keyburner</h1>
                    <?php 
                        require "controllers/component_session.php";
//                        require "controllers/component_user.php";
                        echo "<h2 class='h1 main-header_h2 pink-neon'>".$_SESSION['name']."</h2>";
                    ?>
                    <menu class="main-header-menu">
                        <nav>
                            <li class="blue-neon main-header-menu__item js_clean-all"><span class='pointer'>&#187;</span> Новый текст</li>
                            <li class="blue-neon main-header-menu__item js_get-random-text"><span class='pointer'>&#187;</span> Случайный текст</li>
                            <li class="blue-neon main-header-menu__item js_desroy"><span class='pointer'>&#187;</span> <a class="link blue-neon" href="user.php?exit=exit">Выйти</a></li>
                            <li class="blue-neon-box main-header-menu__item search js_search">
                                <input class="search__input js_search-word" type="text" placeholder="Поиск">
                                <button class="search__go blue-neon js_search-button" title="Искать" label="Искать">&#10140;</button>
                            </li>
                        </nav>
                        <ul class="serch-result users-theme blue-neon-box js_serch-result" search_attr=""></ul>
                    </menu>
                    
                    
<!--<div class="neon-line"></div>-->
                    
                    <div class="users-theme-wrapper blue-neon-box js_users-theme-wrapper">
                        <h2 class='h1 main-header_h2 pink-neon'>Ваши темы:</h2>
                        <div class="users-theme js_users-theme">
                            <?php
                                require "controllers/component_user_get_name_texts.php";
                                echo $result;
                            ?>
                        </div>
                    </div>
                    
                    <div class="default-text-list blue-neon-box js_default-text-list">
                        <h2 class="default-text-list__head main-header_h2 pink-neon">Быстрый старт:</h2>
                        <div class="select__wrapper blue-neon-box">
                            <span class="select__arrow">&#9660;</span>
                            <select class="select default-select">
                                <?php
                                    require_once "controllers/component_default_get_name_texts.php";
                                    foreach($data as $val){
                                        echo "<option 
                                            class='default-text-list__name select__option blue-neon js_default-text-list__name' 
                                            data-id='".$val['id']."' 
                                            name='".$val['name']."'>
                                                <span class='js_value'>" . $val['name'] . "</span>
                                            </option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <p class="blue-neon main-header-menu__item js_get-random-text-default"><span class='pointer'>&#187;</span> Случайный текст</p>
                    </div>
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
                        <div class="statistics-section__item stat-best"><span class="pink-neon">Топ: </span><span class="blue-neon js_stat-best">0</span></div>
                        <div class="statistics-section__item blue-neon last"><span class="">(симв. в мин.)</span></div>
                    </section>
                    
                    <section class="section main__head main__section">
                        <input class="main__name blue-neon-box js_main-name" type="text" placeholder="Название">
                        <input class="main__theme-name blue-neon-box js_main-theme-name" type="text" placeholder="Тема">
                        <p class="current-text-id pink-neon js_current-text-id-wrapper"></p>
                        <div class="graph">
                            <button class="graph__button pink-neon pink-neon-box js_graph-button">&#9660;</button>
                            <dialog class="dialog graph__dialog">
                                <div class="graph__inner">
                                    <p class="blue-neon">Выберите текст для отображения статистики</p>
                                </div>
                                <button class="graph__close pink-neon pink-neon-box js-graph__close">&#10006;</button>
                            </dialog>
                        </div>
                    </section>
                    <p class="message pink-neon">Привет!</p>
                    
                    <section class="section main__section js_section-template">
                        <textarea class="textarea main__textarea blue-neon-box js-main-textarea js-textarea" placeholder='Добавьте ваш текст в это окно или выберите текст из списка'></textarea>
                    </section>
                    
                    <section class="section main__section">
                        <textarea class="textarea main__textarea main__work-textarea blue-neon-box js-work-textarea js-textarea" placeholder='Сначала добавьте текст в верхнее поле' autofocus disabled></textarea>
                    </section>        
                    
                    <button class="button main__button pink-neon pink-neon-box js-del" title="Удалить"><img src="img/del.png" class="main__ico"></button>
                    <button class="button main__button pink-neon pink-neon-box save-button js_add-text" title="Сохранить"><img src="img/save.png" class="main__ico main__ico-save"></button>
                    <button class="button main__button main__button-edit pink-neon pink-neon-box js-replaceWith" title="Редактировать"><img src="img/edit.png" class="main__ico"></button>
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
        
        <dialog class="dialog dialog_delete">
            <div class="dialog__display">
                <div class="dialog__form" method="post">
                    <h3 class="dialog__h bright-blue-neon">Удалить текст?</h3>
                    <div class="dialog__message pink-neon js_dialog_delete-message"></div>
                    <button class="button dialog__button blue-neon-box js_dialog_delete__ready">Удалить</button>
                    <button class="button dialog__button blue-neon-box js_dialog_delete__hide">Отмена</button>
                </div>
            </div>
        </dialog>
    </body>
</html>