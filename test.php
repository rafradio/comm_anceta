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
        $sql = "SELECT w.id, w.name, w.indicator_id, i.`name` as attr
            FROM efes.wave AS w
            JOIN wave_indicator AS i ON w.indicator_id=i.id
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

        $rBegin = '<input type="checkbox" id="all" name="all" value="all">';
        $rBegin .= '<label for="all"> - Все - </label><br><br>';
        echo $rBegin;
        $_label = "id_wave";
        $_style = '';
        
        // Отсюда идет вставка
        
        $waves = [];
        foreach ($rows as $row) {
            $waves[$row["attr"]][] = $row;
        }
        $indicatorNumber = 1;
        foreach (array_keys($waves) as $keys) {
            $r = '<div class="category-name" >';
            $r .= $keys;
            $r .= '</div>';
            $r .= '<div class="category-details">';
            $r .= '<input type="checkbox" id="all_' . $indicatorNumber . '" name="all_' . $indicatorNumber . '" value="all_' . $indicatorNumber . '">';
            $r .= '<label for=all_' . $indicatorNumber . '>- Все -</label><br>';
            foreach ($waves[$keys] as $row) {
                $r .= '<input type="checkbox"' . getChecked($_label, $row['id']). 'class="' . $_label . '_checkbox" id="' . $_label . '_'. $row['id'] . '" name="' . $_label . '[]" value="' .  $row ['id'] . '" onChange="" />'. PHP_EOL;
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
            }
        });
    </script>
</body>
</html>
