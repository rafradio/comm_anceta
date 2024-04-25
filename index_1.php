<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Outlets</title>
        <style>
            #datepicker1, #datepicker2{font:20px Arial; padding:7px; width:150px; vertical-align: middle; border:1px solid #CCC; border-radius: 4px;}
            .ui-draggable, .ui-droppable {background-position: top;}
            table {
                font-size: 1em;
                width:200px !important;
                min-width:20px !important;
                font-family: Arial;
                border: 0px solid;
                border-collapse: collapse;
            }
            th:not(.th__type){
                height:25px;
                min-width:27px;
            }
            td, th {
                border: 1px solid #999;
                padding: 10px 8px;
                font-size: 12px;
                text-align: center;
                font-family: LatoWeb;
                vertical-align: middle;
            }
            .closed-dates {
                margin: 5px;
                display: flex;
                position: relative;
                align-items: center;
                justify-content: flex-start;
                flex-direction: row;
                width: 100%;
            }
            .new-dates {
                width: 100px;
                margin-left: 7px;
                margin-right: 7px;
                font-size: 12px;
                padding: 2px;
            }
            .new-closed-dates {
                width: 160px;
            }
            .form-class {
                display: none;
            }
            #form-submit {
                width: 30%;
                text-align: center;
                margin: 5px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="mainBoxDiv" style="width:100%;">

            <div style="border:0;">&nbsp;</div>
            <div style="border:0;"><b>Ответственные сотрудники</b> <br /></div>
        <?php
            $myData = '';
            $myDate = '';
            $myCount = '';
            $mySap = '';
            $myStatus = '';
            $dataDB = [];
            $input = fopen("config.txt", "r");
            while(!feof($input)) { 
                $dataDB[] = trim(fgets($input));
            }
            $servername = $dataDB[0];
            $username = $dataDB[1];
            $password = $dataDB[2];
            $dbname = $dataDB[3];
            $conn = new mysqli($servername, $username, $password, $dbname);
            $conn->set_charset('utf8');
            require_once ('DrawMethod.php');
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                insertIntoDB($_POST, $conn);
                $myStatus = 'Данные обновлены';
//                    $myData = $_POST['input_2'];
//                    $date = DateTimeImmutable::createFromFormat('Y-m-d', $myData);
//                    $mySap = $_POST['sap_2'];
//                    $myData = new DateTimeImmutable($_POST['input_2']);
//                    $myDate = $date->format('d.m');
//                    $myCount = count($_POST);
            } else {
                $myStatus = '';
            }
//            if (isset($_POST['input_2'])) {
//                if ($_SERVER['REQUEST_METHOD'] == "POST") {
//                    $myData = $_POST['input_2'];
//                    $myCount = count($_POST);
//                }
//            }
            
            

            $result = get_outlets($conn, 48009);
            echo drawOutletList($result, 'id_outlet', $myStatus, 'none', true);

//            echo "Hello wolrd " . $dbname . $result[6]["name"];
            $conn->close();
            
            function get_outlets($conn = null,  $uid = null) {
                if ($conn===null) { return; }
                        $q = "
                select  s.sap_id, shop_n as name, adr as address, d.name as devision_name, r.name as region_name, c.name as city_name, ur.activate_date, s.lat, s.lng from user_right ur
                join shops s on s.shop_id=ur.shop
                join devisions d on d.id=div_id
                join regs r on r.id=reg_id
                join cities c on c.id=city_id
                where  expired_date >now() && ur.uid = " . $uid;

                        $q = "
                select  s.sap_id, shop_n as name, adr as address, division as devision_name, reg as region_name, city as city_name, ur.activate_date /* , s.lat, s.lng */ from user_right ur
                  join report_shops s on s.shop_id=ur.shop
                where  expired_date >now() && ur.uid = " . $uid;

                // pre($q,1);
                //exit($q);
                        $result = $conn->query($q);
                    $rows = [];
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $rows[] = $row;
                        }
                    }
                //pre($res,1);
                        return $rows;
            }
        ?>
        
        <p><?php echo $myData . "  " . $myCount . "  " . $myDate . "  " . $mySap ?></p>
        </div>
        <script>
            let button = document.getElementById("form-submit");
            let data = document.querySelectorAll(".new-dates");
            let revData = document.querySelectorAll(".new-closed-dates");
            window.onload = function() {
                    setTimeout(() => {
                        document.getElementById("query_result").innerHTML = "";
                    }, 2000);
            };
            button.onclick = function () {
                let form = document.createElement("form");
                let table = document.getElementById("table_user");
                form.method = "post";
                form.action = "";
                form.setAttribute("class", "form-class");
                let counter = true;
                let checked = true;
                let revChecking = true;
                let rowCounter = [];
                for (var i = 0; i < data.length-1; i += 2) {
                    if (data[i].value != "" && data[i+1].value == "") {
                        counter = false;
                        break;
                    }
                    if (data[i].value == "" && data[i+1].value != "") {
                        counter = false;
                        break;
                    }
                    if (data[i+1].value < data[i].value) {
                            checked = false;
                            break;
                    } else {
                        if (data[i].value != "" && data[i+1].value != "") {
                            rowCounter.push(i);
                        }
                    }
                }
                Array.from({ length: revData.length }, (_, i) => {
                    if (!(/^[0-9\,\s]*$|^\NULL$/.test(revData[i].value))) {
                        revChecking = false;
                    }
                });
                
                if (counter && checked && revChecking) {
                    
                    console.log(counter, checked, revChecking);
                    console.log(rowCounter);
                    Array.from({ length: revData.length }, (_, i) => {
                        if (revData[i].value != "") {
                            let name = table.rows[i+1].cells[0].textContent+"r";
                            console.log(name);
                            let inputForm = document.createElement("input");
                            inputForm.setAttribute("type", "text");
                            inputForm.setAttribute("id", "input_r_"+i);
                            inputForm.setAttribute("name", name);
                            inputForm.setAttribute("value", revData[i].value);
                            form.appendChild(inputForm);
                        }
                    });
                    Array.from({ length: data.length }, (_, i) => {
                        if (rowCounter.includes(i)) {
                            console.log(i/2+1);
                            let name = table.rows[i/2+1].cells[0].textContent+"c";
                            console.log(name);
                            let myDate = new Date(data[i].value);
                            let myDate1 = new Date(data[i+1].value);
                            let inputValue = "с " + myDate.getDate() + "." + (myDate.getMonth()+1).toString();
                            inputValue += " по " + myDate1.getDate() + "." + (myDate1.getMonth()+1).toString();
                            let inputForm = document.createElement("input");
                            inputForm.setAttribute("type", "text");
                            inputForm.setAttribute("id", "input_c_"+i);
                            inputForm.setAttribute("name", name);
                            inputForm.setAttribute("value", inputValue);
                            console.log(inputValue);
                            form.appendChild(inputForm);
                        }

                    });
                    document.body.appendChild(form);
                    form.submit();
                } else {
                    document.getElementById("form-checking").innerHTML = "Проверьте данные";
                    setTimeout(() => {
                        document.getElementById("form-checking").innerHTML = "";
                    }, 2000);
                }
            
                
            };
            
        </script>
    </body>
</html>
