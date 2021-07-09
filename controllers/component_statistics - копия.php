<?php
require_once "model/model.php";
    
//    $user_id   = $_SESSION['id'];
//    $text_id   = $_POST['id'];
//    $stat_time = $_POST['time'];
//    $speed     = $_POST['speed'];
    
if(isset($_POST['time'], $_POST['speed'])){
    //запись
    statistics($pdo, $_POST['id'], $_POST['time'], $_POST['speed']);
    statistics_best($pdo, $_POST['id'], $_POST['time'], $_POST['speed']);
    
    //чтение
    $statistics      = statistics($pdo, $_POST['id'], false, false);
    $statistics_best = statistics_best($pdo, $_POST['id'], false, false);
}else{
    //чтение
    $statistics      = statistics($pdo, $_POST['id'], false, false);
    $statistics_best = statistics_best($pdo, $_POST['id'], false, false);
}

$stat_first_expl = explode(',', $statistics);
$count = count($stat_first_expl);
for($i=0; $i<$count; $i++){
    $my_data[$i] = explode('-', $stat_first_expl[$i]);
    $x_data["x"][] = $my_data[$i][0];
    $y_data[$i] = $my_data[$i][1];
//    $data1[$i][0] = substr($data1[$i][0], 0, strlen($data1[$i][0]) - 5);
}

//Занчения Y, сначала сортируем по возрастанию, затем удаляем повторяющиеся значения
sort($y_data);
for($i=0, $j=0, $k=0; $i<$count; $i++){
    if($j<$y_data[$i]){
        $k++;
        $y_data_sorted[$k] = $y_data[$i];
        $j                 = $y_data[$i];
    }else{
        $y_data_sorted[$k] = $y_data[$i];
    }
}

//echo '<pre>';
//print_r($y_data_sorted);
//print_r($my_data);
//echo '</pre>';
//exit;

//echo '<pre>';
//print_r($DATA);
//echo '</pre>';
//exit;

// Задаем входные данные ############################################

// Входные данные - три ряда, содержащие случайные данные.
// Деление на 2 и 3 взято для того чтобы передние ряды не 
// загораживали задние.

// Массив $DATA["x"] содержит подписи по оси "X"

//$DATA=Array();
//for ($i=0;$i<100;$i++) {
//    $DATA[0][]=rand(0,100*$i);
////    $DATA[1][]=rand(0,100*$i)/2;
////    $DATA[2][]=rand(0,100*$i)/3;
////    $DATA["x"][]=$i;
//    }

// Функция вывода псевдо-трехмерного куба ###########################

// $im - идентификатор изображения
// $x,$y - координаты верхнего левого угла куба
// $w - ширина куба
// $h - высота куба
// $dx - смещение задней грани куба по оси X
// $dy - смещение задней грани куба по оси Y
// $c1,$c2,c3 - цвета видимых граней куба

function imagebar($im,$x,$y,$w,$h,$dx,$dy,$c1,$c2,$c3) {

    if ($dx>0) {
        //Верхняя грань
        imagefilledpolygon($im,
            Array(
                $x, $y-$h,
                $x+$w, $y-$h,
                $x+$w+$dx, $y-$h-$dy,
                $x+$dx, $y-$dy-$h
            ), 4, $c1);
    
        //Правая грань
        imagefilledpolygon($im,
            Array(
                $x+$w, $y-$h,
                $x+$w, $y,
                $x+$w+$dx, $y-$dy,
                $x+$w+$dx, $y-$dy-$h
            ), 4, $c3);
        }

    imagefilledrectangle($im, $x, $y-$h, $x+$w, $y, $c2);
    }

// Задаем изменяемые значения #######################################

// Размер изображения
$W=1000;
$H=600;

// Псевдо-глубина графика
$DX=10;
$DY=8;

// Отступы
$MB=70; // Нижний
$ML=50; // Левый 
$M=5;   // Верхний и правый отступы. Они меньше, так как там нет текста

// Ширина одного символа
$LW=imagefontwidth(2);

// Подсчитаем количество элементов (столбиков) на графике
//$count=count($DATA[0]);
//if (count($DATA[1])>$count) $count=count($DATA[1]);
//if (count($DATA[2])>$count) $count=count($DATA[2]);

// Количество подписей и горизонтальных линий
// сетки по оси Y.
//$county=25;
$county = count($y_data_sorted);

// Подсчитаем максимальное значение
$max=0;
$max=$y_data_sorted[$county-1];
//for ($i=0;$i<$count;$i++) {
//    $max=$max<$DATA[0][$i]?$DATA[0][$i]:$max;
////    $max=$max<$DATA[1][$i]?$DATA[1][$i]:$max;
////    $max=$max<$DATA[2][$i]?$DATA[2][$i]:$max;
//    }

// Увеличим максимальное значение на 10% (для того, чтобы столбик
// соответствующий максимальному значение не упирался в в границу
// графика
//$max=intval($max+($max/25));

// Работа с изображением ############################################

// Создадим изображения
$im=imagecreate($W,$H);

// Задаем основные цвета 

// Цвет фона (белый)
$bg[0]=imagecolorallocate($im,255,255,255);

// Цвет задней грани графика (светло-серый)
$bg[1]=imagecolorallocate($im,231,231,231);

// Цвет левой грани графика (серый)
$bg[2]=imagecolorallocate($im,212,212,212);

// Цвет сетки (серый, темнее)
$c=imagecolorallocate($im,184,184,184);

// Цвет текста (темно-серый)
$text=imagecolorallocate($im,136,136,136);

// Цвета для столбиков
//$bar[2][0]=imagecolorallocate($im,255,128,234);
//$bar[2][1]=imagecolorallocate($im,222,95,201);
//$bar[2][2]=imagecolorallocate($im,191,65,170);
$bar[0][0]=imagecolorallocate($im,222,214,0);
$bar[0][1]=imagecolorallocate($im,181,187,65);
$bar[0][2]=imagecolorallocate($im,161,155,0);
//$bar[1][0]=imagecolorallocate($im,128,234,255);
//$bar[1][1]=imagecolorallocate($im,95,201,222);
//$bar[1][2]=imagecolorallocate($im,65,170,191);



// Подравняем левую границу с учетом ширины подписей по оси Y
$text_width=strlen($max)*$LW;
$ML+=$text_width;

// Вывод фона графика
imageline($im, $ML, $M+$DY, $ML, $H-$MB, $c);
imageline($im, $ML, $M+$DY, $ML+$DX, $M, $c);
imageline($im, $ML, $H-$MB, $ML+$DX, $H-$MB-$DY, $c);
imageline($im, $ML, $H-$MB, $W-$M-$DX, $H-$MB, $c);
imageline($im, $W-$M-$DX, $H-$MB, $W-$M, $H-$MB-$DY, $c);

imagefilledrectangle($im, $ML+$DX, $M, $W-$M, $H-$MB-$DY, $bg[1]);
imagerectangle($im, $ML+$DX, $M, $W-$M, $H-$MB-$DY, $c);

imagefill($im, $ML+1, $H/2, $bg[2]);

// Вывод неизменяемой сетки (горизонтальные линии на
// нижней грани и вертикальные линии сетки на левой
// грани
//for ($i=1;$i<3;$i++) {
//    imageline($im, $ML+$i*intval($DX/3),
//                   $M+$DY-$i*intval($DY/3),
//                   $ML+$i*intval($DX/3),
//                   $H-$MB-$i*intval($DY/3),
//                   $c);
//    imageline($im, $ML+$i*intval($DX/3),
//                   $H-$MB-$i*intval($DY/3),
//                   $W-$M-$DX+$i*intval($DX/3),
//                   $H-$MB-$i*intval($DY/3),
//                   $c);
//    }
for ($i=1;$i<2;$i++) {
    imageline($im, $ML+$i*intval($DX),
                   $M+$DY-$i*intval($DY),
                   $ML+$i*intval($DX),
                   $H-$MB-$i*intval($DY),
                   $c);
    imageline($im, $ML+$i*intval($DX),
                   $H-$MB-$i*intval($DY),
                   $W-$M-$DX+$i*intval($DX),
                   $H-$MB-$i*intval($DY),
                   $c);
    }

// Пересчитаем размеры графика с учетом подписей и отступов
$RW=$W-$ML-$M-$DX;
$RH=$H-$MB-$M-$DY;

// Координаты нулевой точки графика
$X0=$ML+$DX;
$Y0=$H-$MB-$DY;

// Вывод изменяемой сетки (вертикальные линии сетки на нижней грани графика
// и вертикальные линии на задней грани графика)
for ($i=0;$i<$count;$i++) {
    imageline($im,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count)-$DX,$Y0+$DY,$c);
    imageline($im,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count),$Y0-$RH,$c);
    }

// Горизонтальные линии сетки задней и левой граней.
$step=$RH/$county;
for ($i=0;$i<=$county;$i++) {
    imageline($im,$X0,$Y0-$step*$i,$X0+$RW,$Y0-$step*$i,$c);
    imageline($im,$X0,$Y0-$step*$i,$X0-$DX,$Y0-$step*$i+$DY,$c);
    imageline($im,$X0-$DX,$Y0-$step*$i+$DY,
                  $X0-$DX-($ML-$text_width)/4,$Y0-$step*$i+$DY,$text);
    }

// Вывод кубов для всех трех рядов
for ($i=0;$i<$count;$i++) 
//    imagebar($im, $X0+$i*($RW/$count)+4-1*intval($DX/3),
//                  $Y0+1*intval($DY/3),
//                  intval($RW/$count)-4,
//                  $RH/$max*$DATA[0][$i],
//                  intval($DX/3)-5,
//                  intval($DY/3)-3,
//                  $bar[0][0], $bar[0][1], $bar[0][2]);
//    imagebar($im, $X0+$i*($RW/$count)+4-1*intval($DX/1),
//                  $Y0+1*intval($DY/1),
//                  intval($RW/$count)-4,
//                  $RH/$max*$y_data[$i],
//                  intval($DX/1)-5,
//                  intval($DY/1)-3,
//                  $bar[0][0], $bar[0][1], $bar[0][2]);

    imagebar($im, $X0+$i*($RW/$count)+4-1*intval($DX/1),
                  $Y0+$DY,
                  intval($RW/$count)-4,
                  $RH/$max*$y_data[$i],
                  intval($DX/1)-5,
                  intval($DY/1)-3,
                  $bar[0][0], $bar[0][1], $bar[0][2]);

//for ($i=0;$i<$count;$i++) 
//    imagebar($im, $X0+$i*($RW/$count)+4-2*intval($DX/3),
//                  $Y0+2*intval($DY/3),
//                  intval($RW/$count)-4,
//                  $RH/$max*$DATA[1][$i],
//                  intval($DX/3)-5,
//                  intval($DY/3)-3,
//                  $bar[1][0], $bar[1][1], $bar[1][2]);
//
//for ($i=0;$i<$count;$i++) 
//    imagebar($im, $X0+$i*($RW/$count)+4-3*intval($DX/3), 
//                  $Y0+3*intval($DY/3),
//                  intval($RW/$count)-4,
//                  $RH/$max*$DATA[2][$i],
//                  intval($DX/3)-5,
//                  intval($DY/3)-3,
//                  $bar[2][0], $bar[2][1], $bar[2][2]);

// Вывод подписей по оси Y
for ($i=1;$i<$county;$i++) {
    $str=$y_data_sorted[$i];
    imagestring($im,2, $X0-$DX-strlen($str)*$LW-$ML/4-2,
                       $Y0+$DY-$step*$i-imagefontheight(2)/2,
                       $str,$text);
    }

//for ($i=1;$i<=$county;$i++) {
//    $str=intval(($max/$county)*$i);
//    imagestring($im,2, $X0-$DX-strlen($str)*$LW-$ML/4-2,
//                       $Y0+$DY-$step*$i-imagefontheight(2)/2,
//                       $str,$text);
//    }


//echo '<pre>';
//print_r($DATA);
//echo '</pre>';
//exit;


// Вывод подписей по оси X
$prev=100000;
$twidth=$LW*strlen($x_data["x"][0])+6;
$i=$X0+$RW-$DX;

while ($i>$X0-$DX) {
    if ($prev-$twidth>$i) {
        $drawx=$i+1-($RW/$count)/2;
        if ($drawx>$X0-$DX) {
            $str=$x_data["x"][round(($i-$X0+$DX)/($RW/$count))-1];
            imageline($im,$drawx,$Y0+$DY,$i+1-($RW/$count)/2,$Y0+$DY+5,$text);
            imagestringup($im,2, $drawx-7 ,$Y0+$DY+7+50,$str,$text);
            }
        $prev=$i;
        }
    $i-=$RW/$count;
    }

//while ($i>$X0-$DX) {
//    if ($prev-$twidth>$i) {
//        $drawx=$i+1-($RW/$count)/2;
//        if ($drawx>$X0-$DX) {
//            $str=$DATA["x"][round(($i-$X0+$DX)/($RW/$count))-1];
//            imageline($im,$drawx,$Y0+$DY,$i+1-($RW/$count)/2,$Y0+$DY+5,$text);
//            imagestringup($im,2, $drawx+1-(strlen($str)*$LW)/2 ,$Y0+$DY+7,$str,$text);
//            }
//        $prev=$i;
//        }
//    $i-=$RW/$count;
//    }

//запускаем буферизацию выходного потока
ob_start();

header("Content-Type: image/png");

// Генерация изображения
ImagePNG($im);

$contents = ob_get_contents();
ob_end_clean();

$base64 = base64_encode($contents);
$data = "<img src='data:image/png;base64,".$base64."' />";
$data = $statistics_best.'---'.$data;

echo $data;

imagedestroy($im);

//echo '<pre>';
//print_r($data1);
//echo '</pre>';

//    echo "<pre>";
//    print_r($data);
//    echo '</pre>';
//    echo "<br>";
//    echo $statistics;
//    echo "<br>";
//    echo $statistics_best;
