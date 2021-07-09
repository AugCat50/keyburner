<?php
require_once "model/model.php";
$data = user_get_name_texts($pdo);

//сортировка по ключу area
if(is_array($data)){
    function area_sort($x, $y){
        if($x['area'] < $y['area']){
            return true;
        }else if($x['area'] > $y['area']){
            return false;
        }else{
            return 0;
        }
    }
    usort($data, 'area_sort');
    
    $i = "";
    $j = array();
    $result = "";
    $temp = false;
    foreach($data as $val){
        
        if(!in_array($val["area"], $j)){
            if($temp){
                $result = $result."</select></div></ul>";
            }
            
            array_push($j, $val["area"]);
            $i = $val["area"];
            $result = $result."<ul class='user-text-list'>
                                <h4 class='user-text-list__head bright-blue-neon'>".$val["area"]."</h4>
                                <div class='select__wrapper blue-neon-box'>
                                    <span class='select__arrow'>&#9660;</span>
                                    <select class='select js_select'>";
        }

        $result = $result."<option class='user-text-list__name select__option blue-neon js_user-text-name' data-id=".$val['id']." data-area='".$val["area"]."' name='".$val['name']."'>" . $val['name'] . "</option>";
        $temp   = true;
    }

    if($result){
        $result = $result."</select></div></ul>";
        unset($i, $j, $temp);
    }else{
        $result = "<p>Пока пусто</p>";
        unset($i, $j, $temp);
    }
}else{
    $result = $data;
}
