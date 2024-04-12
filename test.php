<!DOCTYPE html lang="ru">
<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
        .category-name {
            display: block;
            padding: 5px;
            width: 100%;
            margin-bottom: 10px;
            border: 1px solid #CCC;
            border-radius: 4px;
            box-sizing: border-box;
            cursor: pointer;
        }
        .category-details {
            display: none;
            padding: 5px 20px;
            margin-bottom: 7px;
            width: 100%;
            border: 1px solid #CCC;
            border-radius: 4px;
            box-sizing: border-box;
            cursor: pointer;
        }
        .category-details-up {
            display: block;
        }
    </style>
</head>
<body>
    <?php
        
        $dataDB = [];
        $input = fopen("config.txt", "r");
        while(!feof($input)) { 
            $dataDB[] = trim(fgets($input));
        }
        $servername = $dataDB[0];
        $username = $dataDB[1];
        $password = $dataDB[2];
        $dbname = $dataDB[3];

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        $conn->set_charset('utf8');
//        $sql = "SELECT w.id, w.name, w.indicator_id, i.`name` as attr
//            FROM efes.wave AS w
//            JOIN wave_indicator AS i ON w.indicator_id=i.id
//            WHERE w.project_id=17;";
        $sql = "SELECT *
            FROM efes.wave AS w
            WHERE w.project_id=17;";
        
        $result = $conn->query($sql);
        $rows = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        
        // Check connection
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        $rBegin = '<input type="checkbox" id="all" name="all" class="id_wave_checkbox" value="all" onchange="setAll(this);">';
        $rBegin .= '<label for="all"> - Все - </label><br><br>';
        echo $rBegin;
        $_label = "id_wave";
        $_style = '';
        
        // Отсюда идет вставка  Отсюда идет вставка  Отсюда идет вставка  Отсюда идет вставка
        
        $sql1 = "SELECT * FROM efes.wave_indicator;";   // Заменить методом в lists_queries.php
        $result1 = $conn->query($sql1);                 // 
        $rows1 = [];
        if ($result1->num_rows > 0) {
            while($row = $result1->fetch_assoc()) {
                $rows1[] = $row;
            }
        }  // Заменить методом в lists_queries.php
        
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
            echo $r;
            $indicatorNumber += 1;
        }

        // вставка закончилась
        
        
        $keysToDict = array("2020", "2021", "2022", "2023", "2024", "2024", "Test");
        $conn->close();
        function getChecked($name, $val) {
            $r='';
            return ($r);
        }
    ?> 
    <script>
        let categoryName = document.querySelectorAll(".category-name");
        let categoryDetails = document.querySelectorAll(".category-details");
        categoryName.forEach((el, index) => {
            el.onclick = () => {
                categoryDetails[index].classList.toggle("category-details-up");
            };
        });
        function setAllIndicator(o) {
            let v = o.checked;
            o.parentNode.childNodes.forEach((el, index) => {
                if (el.nodeName == "INPUT") {
                    el.checked=v;
                }
            });
        };
        
        
        function setAll(o) {
            v =o.checked;
            nn=o.id;
//            console.log(o.parentNode);
//            let classArr = o.className.split(" ");
//            console.log(Array.isArray(classArr));
//            console.log(classArr[0]);
//            let className = classArr.length > 1 ? classArr[1] : classArr[0];
            let cb = document.getElementsByClassName(o.className);
            nn = o.name;
            nn = nn.substr(0,nn.length-2);
            i=0;
            for (let e of cb) {
                e.checked=v;
                i++;
            }

        }
        
    </script>
</body>
</html>
