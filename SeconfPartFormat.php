<?php
    function drawCheckboxesWaves($rows, $_label, $title, $expose_on_start, $rows1) {
        $rStart='
        <div style="padding:5px; 
        border: 1px solid #CCC;
        border-radius: 4px;
        box-sizing: border-box;" >
        <div style="padding:5px; border:0px; margin-top:5px; display: ' . $expose_on_start  . '" class="hidden_links" > <a href="" onClick="showonoff(\'' .$_label . '_block\'); return(false);"> ' . $title . '</a> 
        <span id="' . $_label  . '_counter">' . '</span>
        </div>

        <div style="padding:5px; border:0px; display: ' . $expose_on_start  . '" id="' . $_label. '_block" >
            <input  type="checkbox" ' . getChecked($_label, 'all'). 'class="' . $_label . '_checkbox" id="' . $_label . '_all" name="' . $_label . '[]" value="all" onChange="setAll(this);" /> <label for="' . $_label . '_all" style="color: #aa6666;">- Все -</label><br><br>'. PHP_EOL;
        $waves = [];
        foreach ($rows1 as $row) {
            foreach ($rows as $finalRow) {
                if ($finalRow['indicator_id'] == $row['id']) {
                    $waves[$row['name']][] = $finalRow;
                }
            }
        }
        $testElement = $waves["Test"];
        unset($waves["Test"]);
        krsort($waves);
        $waves["Test"] = $testElement;
        $indicatorNumber = 1;
        foreach (array_keys($waves) as $key) {
            $r = '<div class="category-name" >';
            $r .= $key;
            $r .= '</div>';
            $r .= '<div class="category-details">';
            $r .= '<input type="checkbox" id="all_waves_' . $indicatorNumber . '" name="all_waves_' . $indicatorNumber . '" value="all_waves_' . $indicatorNumber . '" onchange="setAllIndicator(this);">';
            $r .= '<label for=all_waves_' . $indicatorNumber . '>- Все -</label><br>';
            foreach ($waves[$key] as $row) {
                $r .= '<input type="checkbox"' . getChecked($_label, $row['id']). 'class="' . $_label . '_checkbox" id="' . $_label . '_'. $row['id'] . '" name="' . $_label . '[]" value="' .  $row ['id'] . '" onChange="">'. PHP_EOL;
                $r .= '<label for="' . $_label . '_'. $row['id'] . '">' . $row["name"] . '</label><br>';
            }
            $r .= '</div>';
            $rStart .= $r;
            $indicatorNumber += 1;
        }
        $rStart .= '
            </div>

            </div>
        ';
        return $rStart;
    }
    
    function drawCheckboxesFormat($rows, $_label, $title, $expose_on_start) {
        $rStart='
            <div style="padding:5px; 
            border: 1px solid #CCC;
            border-radius: 4px;
            box-sizing: border-box;" >
                <div style="padding:5px; border:0px; margin-top:5px; display: ' . $expose_on_start  . '" class="hidden_links" > <a href="" onClick="showonoff(\'' .$_label . '_block\'); return(false);"> ' . $title . '</a> 
        <span id="' . $_label  . '_counter">' . '</span>
        </div>

                <div style="padding:5px; border:0px; display: ' . $expose_on_start  . '" id="' . $_label. '_block" >
                    <input  type="checkbox" ' . getChecked($_label, 'all'). 'class="' . $_label . '_checkbox" id="' . $_label . '_all" name="' . $_label . '[]" value="all" onChange="setAllIndicator(this);;" /> <label for="' . $_label . '_all" style="color: #aa6666;">- Все -</label><br><br>'. PHP_EOL;
        $waves = [];
        foreach ($rows as $row) {
            $waves[$row['current']][] = $row;
        }
        krsort($waves);
        $indicatorNumber = 1;
        $rOld = '<div class="category-name" >';
        $rOld .= "Старые форматы";
        $rOld .= '</div>';
        $rOld .= '<div class="category-details">';
        $rOld .= '<input type="checkbox" id="all_format_' . $indicatorNumber . '" name="all_format_' . $indicatorNumber . '" value="all_format_' . $indicatorNumber . '" onchange="setAllIndicator(this);">';
        $rOld .= '<label for=all_format_' . $indicatorNumber . '>- Все -</label><br>';
        foreach (array_keys($waves) as $key) {
            
            if ($key == '2') {
                foreach ($waves[$key] as $row) {
                    $r = '<input type="checkbox"' . getChecked($_label, $row['id']). 'class="' . $_label . '_checkbox" id="' . $_label . '_'. $row['id'] . '" name="' . $_label . '[]" value="' .  $row ['id'] . '" onChange="">'. PHP_EOL;
                    $r .= '<label for="' . $_label . '_'. $row['id'] . '">' . $row["name"] . '</label><br>';
                    $rStart .= $r;
                }
                $rStart .= "<br><br>";
            } else {
                foreach ($waves[$key] as $row) {
                    $rOld .= '<input type="checkbox"' . getChecked($_label, $row['id']). 'class="' . $_label . '_checkbox" id="' . $_label . '_'. $row['id'] . '" name="' . $_label . '[]" value="' .  $row ['id'] . '" onChange="">'. PHP_EOL;
                    $rOld .= '<label for="' . $_label . '_'. $row['id'] . '">' . $row["name"] . '</label><br>';
                }
                $indicatorNumber += 1;
            }
        }
        $rOld .= '</div>';
        $rStart .= $rOld;
        $rStart .= '
            </div>

            </div>
        ';
        return $rStart;
        
    }
    
?>

