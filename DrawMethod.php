<?php
    function drawOutletList($rows, $_label, $myStatus , $title ='', $expose_on_start = false) {
        $_counter = '<b>(' . count($rows ) . ')</b>';
        $expose_on_start = ($expose_on_start===false) ? 'none;' : '';
        $r = '
            <div style="padding:5px;
            border: 1px solid #CCC;
            border-radius: 4px;
            box-sizing: border-box;" >' ;
        $title ='ababkova_yv@magnit.ru, РГА, СВ, Магазин, личный аккаунт. Рассылка';
        $r .='    <div style="padding:5px; border:0px; margin-top:5px; display: ' . $expose_on_start  . '" class="hidden_links" >  ' . $title . '

            <span id="' . $_label  . '_counter">' . $_counter . '</span>
            <p id="query_result" style="color:blue;">' . $myStatus . '</p>
            </div>


            <div style="padding:5px; border:0px; display: ' . $expose_on_start  . '" id="' . $_label. '_block" > <table id="table_user" style="width: 100%!important;">   ';
        $r .='<tr><th>Код ТТ</th>
            <th>Название</th>
            <!-- <th>Адрес</th> -->
            <th>Макрорегион</th>
            <th>Регион</th>
            <th>Город</th>
            <th>Нерабочие даты точки</th>
            <th>Даты ревизии</th>
            </tr>'. PHP_EOL  ;
        
        $inputForDate = '<div class="closed-dates">';
        $inputForDate .= '<p>с</p><input class="new-dates" type="date" required pattern="\d{2}-\d{2}-\d{2}" >';
        $inputForDate .= '<p>по</p><input class="new-dates" type="date">';
        $inputForDate .= '</div>';
        
        $inputForClosedDate = '<div class="closed-dates">';
        $inputForClosedDate .= '<input class="new-closed-dates" type="text" >';
        foreach ($rows as $row) {
            $r .='<tr>
                    <td style="text-align: left;">' .$row ['sap_id'].'</td>
                    <td style="text-align: left;">' .$row ['name'].'</td>
                <!--     <td style="text-align: left;">' .$row ['address'].'</td> -->
                    <td style="text-align: left;">' .$row ['devision_name'].'</td>

                    <td style="text-align: left;">' .$row ['region_name'].'</td>
                    <td style="text-align: left;">' .$row ['city_name'].'</td>
                    <td style="text-align: left;">' . $inputForDate . '</td>
                    <td style="text-align: left;">' . $inputForClosedDate . '</td>
                <!--    <td>' . $row ['activate_date'] . '</td>  --> ' . PHP_EOL  ;
        }
        
        $r .= '
                </table>
                

                </div>
            ';
        $r .= '<button id="form-submit" style="margin-right: 20px;">Обновить даты</button>';
        $r .= '<span id="form-checking" style="color: red;"></span>';
        return $r;
    }
    function insertIntoDB($data, $conn) {
        foreach ($data AS $key => $value) {
            $indicator = substr($key, -1);
            $shopID = substr($key, 0, -1);
            if ($indicator == "c") {
                $q = 'INSERT INTO magnit._test_date_updates (shop_id, closed_dates) VALUES ("'. $shopID . '","' . $value . '");';
                $result = $conn->query($q);
            } else {
                $q = 'INSERT INTO magnit._test_date_updates (shop_id, revision_dates) VALUES ("'. $shopID . '","' . $value . '");';
                $result = $conn->query($q);
            }
        }
    }
?>

