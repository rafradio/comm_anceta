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
            $r .= '<input type="checkbox" id="all_' . $indicatorNumber . '" name="all_' . $indicatorNumber . '" value="all_' . $indicatorNumber . '" onchange="setAllIndicator(this);">';
            $r .= '<label for=all_' . $indicatorNumber . '>- Все -</label><br>';
            foreach ($waves[$key] as $row) {
                $r .= '<input type="checkbox"' . getChecked($_label, $row['id']). 'class="' . $_label . '_checkbox" id="' . $_label . '_'. $row['id'] . '" name="' . $_label . '[]" value="' .  $row ['id'] . '" onChange="">'. PHP_EOL;
                $r .= '<label for="' . $_label . '_'. $row['id'] . '">' . $row["name"] . '</label><br>';
            }
            $r .= '</div>';
            $rStart .= $r;
            $indicatorNumber += 1;
        }
        return $rStart;
    }
    
    function drawCheckboxesFormat($conn) {
        $rBegin = '<input type="checkbox" id="all_projects" name="all" class="id_project_checkbox" value="all" onchange="setAll(this);">';
        $rBegin .= '<label for="all_projects"> - Все - </label><br><br>';
        echo 'Формат<br><br>';
        echo $rBegin;
        $sql1 = "select * from efes.questionnaire where project_id = 17 "  . "
             &&  ( hidden_on_web= 0 || hidden_on_web is null ) ";
        $result = $conn->query($sql1);
        $rows = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
    }
    
?>

