<!DOCTYPE html>
<html lang="ru" class="html">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="Тренажёр слепого набора">
        <meta name="keywords" content="Keyburner, Тренажёр слепого набора">
        <meta name="autor" content="draackul2@gmail.com">
        <meta name="viewport" content="width=device-width; initial-scale=1.0">
        <title>Keyburner - Тренажёр слепого набора</title>
        
        <link rel="shortcut icon" type="image/svg" href="img/favicon.svg"/>
        
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
<!--
        <link rel="stylesheet" href="css/header.css">
        <link rel="stylesheet" href="css/menu.css">
        <link rel="stylesheet" href="css/body.css">
        <link rel="stylesheet" href="css/footer.css">
        <link rel="stylesheet" href="css/media.css">
-->
        
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap&subset=cyrillic" rel="stylesheet"> 
        
        <script src="js/jquery-3.4.1.min.js"></script>
        <script src="js/textarea_autosize.js"></script>
        <script src="js/main.js"></script>
    </head>
    
    <body class="body">
        <div class="css-body-wrapper">
            <header class="body_header">
                <h1>Keyburner</h1>
                <menu>
                    <nav>
                        <li>Регистрация</li>
                        <li>Вход</li>
                        <li>Добавить текст</li>
                    </nav>
                </menu>
                <ul>
                    Выбрать текст
                    <li>Text1</li>
                    <li>Text2</li>
                </ul>
            </header>
            
            <main class="css-main js-main">
                <section class="css_section css-statistics-section">
                    <div class="css-item first">
                        <span class="">Последний результат:</span>
                    </div>
                    <div class="css-item">
                        <span class="css-h-span">Время: </span>
                        <span class="js-minute blue">00</span>:<span class="js-second blue">00</span>
                    </div>
                    <div class="css-item">
                        <span class="css-h-span">Скорость: </span>
                        <span class="js-speed blue">0</span>
                    </div>
                    <div class="css-item">
                        <span class="css-h-span">Ошибок: </span>
                        <span class="js-errors blue">0</span>
                    </div>
                    <div class="css-item">
                        <span class="css-h-span">Штраф: </span>
                        <span class="js-penalty blue">0</span>
                    </div>
                    <div class="css-item last"><span class="">(симв. в мин.)</span></div>
                </section>
                <section class="css_section css-basetext-section">
                    <textarea class="css-main-textarea css-textarea js-main-textarea js-textarea" placeholder='Добавьте ваш текст в это окно или выберите текст из списка'></textarea>
                </section>
                <section class="css_section">
                    <textarea class="css-work-textarea css-textarea js-work-textarea js-textarea" placeholder='Сначала добавьте текст в верхнее поле' autofocus disabled></textarea>
                </section>
                <button id="test">Test</button>
                <button class="js-replaceWith">Edit text</button>
            </main>
        </div>
                    
        <footer class="footer">
            <details class="details">
                <summary>&copy; AugCat50</summary>
                <p><a href="mailto:draackul2@gmail.com">draackul2@gmail.com</a></p>
            </details>
            <a target="_blank" href="https://www.artstation.com/antoinecollignon">Art by Antoine Collignon</a>
        </footer>
    </body>
</html>