<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Анкета тайника</title>
        <link href="index.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php
            require_once('pic_block.php');
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
            $task_id = "2436352";
            $qaz="SELECT user_task.*,  location.address as loc_adress , q_nair.name as qnair_name FROM user_task 
                        LEFT JOIN location ON location.id=user_task.location_id 
                        join questionnaire as q_nair on q_nair.id = user_task.parent_questionnaire_id
                        WHERE status in (1,0) AND   user_task.id=".$task_id;

            $result = $conn->query($qaz);
            $rows = [];
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $row_qaz = $rows[0];
            $controler_comment = "";
            if (!empty($row_qaz["comment"])){
                    $controler_comment = $row_qaz["comment"];
            }
            $wave_id=$row_qaz["wave_id"];
            $loc_adress=$row_qaz["loc_adress"];
            $qnair_name = $row_qaz["qnair_name"];
            $task_questionnaire_id=$row_qaz["parent_questionnaire_id"];
            echo "<span style='font-size:20px;'>Анкета № <b>$task_id</b></span><br><br>";
            echo "<span style='font-size:25px;'>Адрес: <b>".$loc_adress."</b></span><br><br><br>";
            echo "<span style='font-size:25px;'>Тип анкеты: <b>".$qnair_name."</b></span><br><br><br>";
            $for_correct="";
            $sub_section_name="";
            $section_name="";
            $query="select user_question_set.*, user_task.location_id as loc_id, user_task.user_id, 
#		question.is_section_name, question.is_comment as is_comment, 
question.is_date, question.is_time,  question.describe,
#		question.text_tag as  question_text_tag,  question.is_text_result as is_text_result_from_question, question.is_answer_digit as  is_answer_digit_from_question,question.is_jti, question.is_hidden,
		section.name_rus as section_name,
		sub_section.name_rus as sub_section_name,
question.name_eng,

		user_answer.answer_set_id as real_answer, user_answer.comment_text as real_comment_text, user_answer.digit_result as real_digit_result
		from user_question_set
		left /* inner */ join question on question.id=user_question_set.parent_question_id || question.id is null
		
		left join section ON section.id=user_question_set.section_id
		left join sub_section ON sub_section.id=user_question_set.sub_section_id
		
		
		left join user_task on user_task.id=user_question_set.user_task_id
		
		left join user_answer on user_answer.question_set_id=user_question_set.id
		
		where  user_question_set.questionnaire_id=$task_questionnaire_id and user_question_set.user_task_id=$task_id and user_question_set.is_picture=0
#		and question.id not in(196,241,340,602,1601) 
		$for_correct
		order by user_question_set.sorting /* trap f_au show_a eca */ ";
            
//            pre ('<b>');
//pre ($query);
//pre ('</b>');

            $result = $conn->query($query);
            $cq = 0;
            $sectionCheck = 0;
            $user_got_answers=0;
            $show_edit_tp_pic = false;
            $sectionNumber = 0;
            while($row = $result->fetch_assoc()) {
                $cq=$cq+1;  
			$q_text_block='';
			$qwrap_sty_txt="";
			if ($user_got_answers==0 && $cq>1 ){
				$qwrap_sty_txt="style='display:none;'";
			}
			  
			  
			 
					$qwrap_sty_txt="style='display:block;'";
			
			  
			  
			  
			$qwrap_sty_txt =  "<div ".$qwrap_sty_txt." id='qwrap".$row["parent_question_id"]."'>";
//                $rows[] = $row;
                        $task_questionnaire_id=(int)$row["questionnaire_id"];
			  
		  
			  

			if (isset($row["digit_result"])&& $row["digit_result"]!="")	{ 
				$digit_result=$row["digit_result"];	
				$tarr=explode("|-|",$row["digit_result"]);
			}
			
			
			
			$is_section_name=$row["is_section_name"];
			$question_describe="";
			if ($row["describe"]!=""){
				$question_describe="<div style='clear:both; height:10px;'></div><i class='question-style'>".$row["describe"]."</i>";
			}
                        if ($is_section_name==0 ) 
			{
				$sectionCheck = 0;
				$section_draw=0;
				
				if ($row["section_name"]!=""&&$section_name!=$row["section_name"]) {
					$section_name=$row["section_name"];
					$section_draw=1;
				}

				$sub_sectio_draw=0;
				
				if ($row["sub_section_name"]!=""&&$sub_section_name!=$row["sub_section_name"]) {
					$sub_section_name=$row["sub_section_name"];
					$sub_sectio_draw=1;
				}		
					 
				
				if ($section_draw==1) {
					 echo "<p class='section_class'>".$section_name."</p>";
//                                        $current_x5cat = $row ['x5cat'];
//
                                        if ($show_edit_tp_pic) {
//////////////////// ************************
                                            echo  drawPictureBlock($auditor_pic_array, $x5_cat_arr_list, $row['x5cat'] /* ,  $row['x5subcat'],  $row['lat'] , $row['lng'] */ );
                                    }
                                }
				
				  if ($sub_sectio_draw==1) {
					 echo "<p class='sub_section_class'>".$sub_section_name."</p>";
				 }
				
				
				
				
				
				
				
				$question_text_tag=$row["text_tag"];
/*
				echo $qwrap_sty_txt;

				echo "<a name='".$row["parent_question_id"]."'/><b>".$question_text_tag."</b> <b>".$row["question_text"]."</b>".$question_describe."<div style='clear:both; height:10px;'></div>";
*/
                                $q_text_block = $qwrap_sty_txt .
                                    "<a name='".$row["parent_question_id"]."'/><div class='flex-div'><b>".$question_text_tag."</b> <b class='question-style'>".$row["question_text"]."</b>".$question_describe;
			}
                        else
                        {

                            $sectionNumber += 1;
//				echo "<b style='font-size:22px;'>".$row["question_text"]."</b><div style='clear:both; height:10px;'></div>";
//				$q_text_block = "<b style='font-size: 22px;'>".$row["question_text"]."</b><div style='clear:both; height:10px;'></div>";
                                $q_text_block = "<div class='flex-div'><p class='question-style'><b class='maxscore' id='section_". $sectionNumber ."' style='font-size: 22px;'>".$row["question_text"]."</b></p>";
			
                                $sectionCheck = 1;
                                
                        }
                        
//                        echo "<div class='answer-style'>" . $q_text_block . "</div>";
                        echo $q_text_block;
                        $text_v_s = "<table>";
		$text_v="";
	
			
			
		$query_v="select *
		from user_answer_set
# f_auditor
		where question_set_id=".$row["id"]." ORDER BY sorting, parent_answer_id";
                
                if ($row["is_answer_multiple_select"]==0) {
//if (geh()) pre($query_v);
// GEH Оформление ответов ДА НЕТ
		
//		$result_v=mysql_log_query($query_v);
                    $text_score = "";
                    $result_v = $conn->query($query_v);
			while($row_v = $result_v->fetch_assoc())
		  {
                            $text_v_s .= "<tr>";
                        
			  $ch_label="";
			  
			  if ($row_v["id"]==$row["real_answer"]) {
				  
				   $ch_label=" checked='checked' ";
			  }
			  
			  $x5txt = mb_strtoupper($row_v["text"], 'UTF-8');
			  
				$text_dop=$row_v["text"];
				
				 $lavel_x5_class=""; 
				 $label_dop=""; 
				 $default_br="<br>"; 
				 $radio_class="";
				 
				 if ($x5txt=="ДА") {
//                                     echo '<script>console.log("Да");</script>';
					 $default_br="";
					 $radio_class="class='input_radio_gv'";	
					 $x5txt="";
					 $lavel_x5_class="class='yes'";  
                     $label_dop="<label ".$lavel_x5_class."  for='add_radio_".$row_v["id"]."'>".$x5txt."</label><div style='clear:both; height:10px;'></div>";	
				     $text_dop="";
				 }
				  if ($x5txt=="НЕТ") {
					 $default_br="";
					 $radio_class="class='input_radio_gv'";	
					 $x5txt="";
					 $lavel_x5_class="class='no'"; 
				     $label_dop="<label ".$lavel_x5_class."  for='add_radio_".$row_v["id"]."'>".$x5txt."</label><div style='clear:both; height:10px;'></div>";	
				     $text_dop="";					
				 }
				 
				  if ($x5txt=="Н/Д" || $x5txt=="Н\Д"  || $x5txt=="НД" || $x5txt=="NA" || $x5txt=="N/A" || $x5txt=="N\A" ) {
					 $default_br="";
					 $radio_class="class='input_radio_gv'";	
					 $x5txt="";
					 $lavel_x5_class="class='nd'";
                     $label_dop="<label ".$lavel_x5_class."  for='add_radio_".$row_v["id"]."'>".$x5txt."</label><div style='clear:both; height:10px;'></div>";	
				     $text_dop="";
				 }
			  
			  
			  //$text_v.="<label><input ".$ch_label." onclick='var dfg=$(\"input[name=add_radio_".$row["id"]."]:checked\").val(); add_user_answer({user_id:".$row["user_id"].",task_id:".$task_id.",qid:".$row["id"].",answer_set_id:dfg, type:\"radio\"});' type='radio' value='".$row_v["id"]."' name='add_radio_".$row["id"]."'/>".$row_v["text"]."</label><br>";
			  
			  
			  $text_v.="<input Q='5657' ".$ch_label." onclick='var dfg=$(\"input[name=add_radio_".$row["id"]."]:checked\").val(); add_user_answer({user_id:".$row["user_id"].",task_id:".$task_id.",qid:".$row["id"].",answer_set_id:dfg, type:\"radio\"});'  id='add_radio_".$row_v["id"]."' type='radio' ".$radio_class."  value='".$row_v["id"]."' name='add_radio_".$row["id"]."'/>".$text_dop.$label_dop.$default_br;
//			  $text_v.="<input          ".$ch_label." onclick='var dfg=$(\"input[name=add_radio_".$row["id"]."]:checked\").val(); add_user_answer({user_id:".$row["user_id"].",task_id:".$task_id.",qid:".$row["id"].",answer_set_id:dfg, type:\"radio\"});'  id='add_radio_".$row_v["id"]."' type='radio' ".$radio_class."  value='".$row_v["id"]."' name='add_radio_".$row["id"]."'/>".$label_dop.$default_br;
			  
                          $text_v_s .= "<td>".$text_v."</td>";
                  $text_score .= "<p>" . $row_v["score"] . "</p>";
                  $text_v_s .= "<td>".$text_score."</td></tr>";
			  
		  }
                 
                  $text_v_s .= "</table>";
                 
//                  $text_v = $text_v_s;
		
//		$num_rows = mysql_num_rows($result_v); 
//			 if ($num_rows>0) {
				 
			//	 $text_v.="<div style='clear:both; height:10px;'></div><input type='button' onclick='var dfg=$(\"input[name=add_radio_".$row["id"]."]:checked\").val(); add_user_answer({user_id:".$row["user_id"].",task_id:".$task_id.",qid:".$row["id"].",answer_set_id:dfg, type:\"radio\"});' value='Сохранить'/><div style='clear:both; height:10px;'></div>";
//			 }
		
		
		
		}
                
                if ($row["is_answer_multiple_select"]==1) {
		
		$result_v = $conn->query($query_v);
			while($row_v = $result_v->fetch_assoc())
		  {
                            $text_v_s .= "<tr>";
			  
			    $ch_label="";
			  
			  // if ($row_v["id"]==$row["real_answer"]) {
				  
				   // $ch_label=" checked='checked' ";
			  // }
			  
			  if ($row["real_digit_result"]!="") {
			  	$rfv=explode("|-|",$row["real_digit_result"]);
				// $key = array_search($row_v["id"], $rfv); 
				// if (is_bool($key)==false)
			    // {
					 // $ch_label="";
				// }
				// else
				// {
					// $ch_label=" checked='checked' ";
				// }

				
				$pos = array_search($row_v["id"], $rfv); 
				if ($pos === false) {
				} else {
					$ch_label=" checked='checked' ";
				}


				
			  }
			  
			  
			  $text_v.="<label><input Q='5712' ".$ch_label." onclick='var dfg=GetMultiChecked(".$row["id"]."); add_user_answer({user_id:".$row["user_id"].",task_id:".$task_id.",qid:".$row["id"].",val:dfg, type:\"checkbox\"});' id='multiselect_".$row_v["id"]."' type='checkbox'  name='multi_select_".$row["id"]."'/>".$row_v["text"]."</label><br>";
		  
                          $text_v_s .= "<td>".$text_v."</td>";
                      }
	
		
//		$num_rows = mysql_num_rows($result_v); 
//			 if ($num_rows>0) {
		//		 $text_v.="<div style='clear:both; height:10px;'></div><input type='button' onclick='var dfg=GetMultiChecked(".$row["id"]."); add_user_answer({user_id:".$row["user_id"].",task_id:".$task_id.",qid:".$row["id"].",val:dfg, type:\"checkbox\"});' value='Сохранить'/><div style='clear:both; height:10px;'></div>";
//			 }
		
		}
                
                if ($row["is_date"]>=1) {
				
				 $text_v.="<input Q='5730' readonly class='is_date' type='text' value='".$row["real_digit_result"]."' task_id='".$task_id."' user_id='".$row["user_id"]."' id='add_answer_digit_".$row["id"]."' /><div style='clear:both; height:10px;'></div><div style='clear:both; height:10px;'></div>";
			}
			
			if ($row["is_time"]>=1) {
				
				 $text_v.="<input Q='5735' user_id='".$row["user_id"]."' qid='".$row["id"]."' task_id='".$task_id."' readonly class='is_time'  type='text' value='".$row["real_digit_result"]."' id='add_answer_digit_".$row["id"]."' /><div style='clear:both; height:10px;'></div><div style='clear:both; height:10px;'></div>";
				 
				 $text_v.="<script>
				 $(document).ready(function() {
					$('#add_answer_digit_".$row["id"]."').timepicker();
				 });
				 </script>"; 
				 
			}
                        
                        if ($row["is_answer_digit"]==1) {
				
				 $text_v.="<input Q='5967' onchange='var dfg=$(\"#add_answer_digit_".$row["id"]."\").val(); add_user_answer({user_id:".$row["user_id"].",task_id:".$task_id.",qid:".$row["id"].",val:dfg,type:\"answer_digit\"});' type='number' value='".$row["real_digit_result"]."' id='add_answer_digit_".$row["id"]."' /><div style='clear:both; height:10px;'></div>";
			}
			
			
			if ($row["is_text_result"]==1) {
				
				 $text_v.="<input Q='5755' onchange='var dfg=$(\"#add_answer_digit_".$row["id"]."\").val(); add_user_answer({user_id:".$row["user_id"].",task_id:".$task_id.",qid:".$row["id"].",val:dfg,type:\"answer_digit\"});' type='text' value='".$row["real_digit_result"]."' id='add_answer_digit_".$row["id"]."' /><div style='clear:both; height:10px;'></div>";
			}
			
			
			
			
			
			if ($row["is_answer_float_digit"]==1) {
			
				// $text_v.="<input type='text'  value='' id='add_answer_digit_".$row["id"]."' /><div style='clear:both; height:10px;'></div><input type='button' onclick='var dfg=$(\"#add_answer_digit_".$row["id"]."\").val(); add_user_answer({user_id:".$row["user_id"].",task_id:".$task_id.",qid:".$row["id"].",val:dfg,type:\"answer_digit\"});' value='Сохранить'/><div style='clear:both; height:10px;'></div>";
				
				 $text_v.="<input Q='5967'  type='text'  value='".$row["real_digit_result"]."' id='add_answer_digit_".$row["id"]."'  onchange='var dfg=$(\"#add_answer_digit_".$row["id"]."\").val(); add_user_answer({user_id:".$row["user_id"].",task_id:".$task_id.",qid:".$row["id"].",val:dfg,type:\"answer_digit\"});' /><div style='clear:both; height:10px;'></div>";
				

			}
                        
                        if ($row["is_comment"]>=1) {
				 $text_v.="<textarea onblur='var dfg=$(\"#comment_".$row["id"]."\").val(); dfg=$.trim(dfg); if (dfg.length===0){return;};  add_user_answer({user_id:".$row["user_id"].",task_id:".$task_id.",qid:".$row["id"].",val:dfg,type:\"answer_comment\"});' style='width:300px; height:150px;'  id='comment_".$row["id"]."'>".$row["real_comment_text"]."</textarea><div style='clear:both; height:10px;'></div><div style='clear:both; height:10px;'></div>";
				 
				  
				 
				 //<input type='button' onclick='var dfg=$(\"#comment_".$row["id"]."\").val(); add_user_answer({user_id:".$row["user_id"].",task_id:".$task_id.",qid:".$row["id"].",val:dfg,type:\"answer_comment\"});' //value='Сохранить'/>

			}
			
			
			//загрузка видео аудио
			//спортмастер
			
			//if ($row["parent_question_id"]==1945&&($user_id==21376||$user_id==21415)) {
			if ($row["parent_question_id"]==1945) {	
				 $text_v.="<div style='clear:both; height:10px;'></div><b>Для отправки аудио/видео прикрепите файл:</b><div style='clear:both; height:10px;'></div><form id='video_add_".$_GET["task_id"]."' action='../edit_copyright_anketa/?action=video_add&question_set_id=".$row["id"]."&task_id=".$_GET["task_id"]."' method='post' class='pic_add' enctype='multipart/form-data'><input type='file' name='video[]' multiple><input onclick='SendVideo(".$_GET["task_id"].");' type='button' id='button' value='Отправить аудио/видео'></form><div style='clear:both; height:50px;'></div>";
				 

			}
                        $additional = "<p>is_mandatory: ". $row["is_mandatory"] . "<br></p>";
                        $additional .= "<p>is_section_name: ". $row["is_section_name"] . "<br></p>";
                        $additional .= "<p>is_hidden: ". $row["is_hidden"] . "<br></p>";
//                       $text_v = $text_v_s;
//                       $text_v_s = "";
                       
                            
                            if ($sectionCheck == 0) {
                                echo "<div style='clear:both; display:block;' class='answer-style' id='add".$row["id"]."'>".$text_v."</div>";
                                echo "<div class='score-style'>".$text_score."</div>";
    //                        echo "<div class='flex-answers' id='add".$row["id"]."'>".$text_v."</div>";
                            echo "<div style='clear:both; height:10px;'></div>";
                            echo "<div class='answer-style' id='score_". $sectionNumber ."'>" . $row["max_score"] . "</div>";
                            }
                            
                            if ($sectionCheck == 1) {
                                echo "<div class='score-style'><p id='maxscore_". $sectionNumber ."'>max-score: </p></div>";
                            }
                            echo "<div class='score-style'>". $additional . "</div></div></div>";
//                            $text_v = "";
                  
                            

                        
//                        echo "<div class='answer-style'>" . $row["max_score"] . "</div><div style='clear:both; height:10px;'></div></div></div>";
                
            }
            
        ?>
        <script>
            
        </script>
    </body>
</html>
