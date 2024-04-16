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
        require_once ('SeconfPartFormat.php');
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

//        $rBegin = '<input type="checkbox" id="all" name="all" class="id_wave_checkbox" value="all" onchange="setAll(this);">';
//        $rBegin .= '<label for="all"> - Все - </label><br><br>';
//        echo $rBegin;
        $_label = "id_wave";
        $_style = '';
        $r = '';
        
        // Отсюда идет вставка  Отсюда идет вставка  Отсюда идет вставка  Отсюда идет вставка
        
        $sql1 = "SELECT * FROM efes.wave_indicator;";   // Заменить параметром передающимся в функцию  drawCheckboxesWaves()
        $result1 = $conn->query($sql1);                 // Заменить методом в lists_queries.php
        $rows1 = [];
        if ($result1->num_rows > 0) {
            while($row = $result1->fetch_assoc()) {
                $rows1[] = $row;
            }
        }  // Заменить методом в lists_queries.php и получить $rows1
        
        echo drawCheckboxesWaves($rows, 'id_wave', 'Волна', true, $rows1);
        

        // вставка закончилась
        
        
        $sql1 = "select * from efes.questionnaire where project_id = 17 "  . "
             &&  ( hidden_on_web= 0 || hidden_on_web is null ) ";
        $result = $conn->query($sql1);
        $rows = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        
        
        // Вторая часть задачи с questionnaire
        
        echo drawCheckboxesFormat($rows, 'id_project', 'Формат', true);
        
        // конец второй части
        
        $conn->close();
        
        function getChecked($name, $val) {
            $r='';
            return ($r);
        }
        function getProjectListFilter($params) {
            $db=$params[1];
            $q="select * from efes.wave_indicator ";
            return($db->read($q)) ;
        }
    ?> 
    <script>
        // вставка
        let categoryName = document.querySelectorAll(".category-name");
        categoryName.forEach((el, index) => {
            el.onclick = () => {
                el.nextElementSibling.classList.toggle("category-details-up");
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
        // конец вставки
        
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
        function showonoff(v) {
            if (document.getElementById(v).style.display=="") {
                document.getElementById(v).style.display="none";
            }
            else {
                document.getElementById(v).style.display="";
            }
        }
        
    </script>
</body>
</html>
