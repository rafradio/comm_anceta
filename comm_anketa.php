<?php 
define ('UPLOAD_ROOT', '/data/media/_transfered/out/pics/appeal/'  . PROJECT_ID );

if (intval($_COOKIE['user_right'])==999) {
    header("Location: /page/show_check/");
// HTTP_REFERER is undefined
}

$chk_id = (int)$_GET['id'];
$path = UPLOAD_ROOT . '/' . $chk_id; 

// Получатели уведомлений о финальных апелляциях
$main_reciepients = [
// 'georgykh@yandex.ru'
//    'khabarov.g@romir.ru'
 'kosheva.a@imystery.ru'
];

//pre ($_REQUEST);
//pre ($_FILES);
//pre ($_FILES,1);

    ini_set('upload_max_filesize', '1024M');
    ini_set('post_max_size', '1024M');
    $this->db = \Mysql::connection($this->getIni('dbs')[0]);
// GEH


    require_once ('show_anketa_lib.php');

    require_once (SERVER_ROOT . '/lib/lib_mail.php');
    $chk_id = (int)$_GET['id'];
    $params[1]=$this->db;
    $params['chk_id']=$chk_id;

// ID волны
    $q="select wave_id  from answers where chk_id= " . $chk_id . " && history=0 limit 1 ";
    $_res_wave = $this->db->read($q);
    $_wave_id = $_res_wave[0]['wave_id'];
    $params['id_wave']=$_wave_id;


    $_points_max = 0;

    $res=get_x5_qa($this->db, $chk_id /* , $base_name */);

    $q="select date, wave, points_max from tmp_stat_total2 where chk_id= " . $chk_id;
    $_res_date = $this->db->read($q);
    $_data = $_res_date[0]['date'];
    $_points_max = $_res_date[0]['points_max'];
    if ($_data!==null) {
        $params['date'] = $_data;
    }
    $q="select id from efes.wave where project_id = " . PROJECT_ID . " && client = " .  $_res_date[0]['wave'];
    $_res_date = $this->db->read($q);
    $_data = $_res_date[0]['id'];
    if ($_data!==null) {
        $params['id_wave'] = $_data;

    }


    if (isset($_POST) && !empty($_POST['text'])){
            $appeal_id       = 0;

            $in['chk_id']    = $chk_id = (int)$_GET['id'];
            $in['time']      = date("Y-m-d H:i:s");
            $in['uid']       = $this->db->ekrval($_COOKIE['user_id']);
            $in['autor']     = $this->db->ekrval($_COOKIE['user_name']);
            $in['text']      = $this->db->ekrval($_POST['qustion_text'] . " " . $_POST['text']);
            $in['type']      = (isset($_POST['appl'])) ? intval($_POST['appl']) : NULL;
            $in['parent_id'] = (isset($_POST['cid']))  ? intval($_POST['cid'])  : NULL;
            $in['q_id']      = (isset($_POST['q_id'])) ? intval($_POST['q_id']) : NULL;
                if ($in['q_id']==0) {
                    $in['q_id']      =   NULL;
                }

            $in['corrected'] = (isset($_POST['crr']))  ? intval($_POST['crr'])  : 10; //NULL;

//pre($in,1);
            if ($in['q_id']!==null) {
                $q="update comms set q_id=" . $in['q_id'] . " where id = ". $in['parent_id']. " && q_id is null";
                $res=$this->db->query($q );
            }


//pre($_FILES,1);

            $appeal_id = $this->db->add("comms",$in);
            $q="select  c1.chk_id,  count(c1.id) comments_count,  count(c2.id) comments_answers_count from comms c1
                    left join   comms c2 on c1.id=c2.parent_id
                        where ( c1.type is null || c1.type in ( 1 )  || c1.type in ( 3 ) || c1.type in ( 5 )) && c1.chk_id = " . $chk_id  . "
                        ";
            $_rc = $this->db->read($q);

            $_id_tmp = $this->db->add("comms_stat",$_rc[0], true);

            if (!empty($_FILES['attach'])){
                $_i=0;
                if (!file_exists($path )) {
                    mkdir($path);
                }
// mkdir
                foreach($_FILES["attach"]["tmp_name"] as $key=>$val){

//            $_FILES["attach"]["name"][$key];

                    $_d=date('YmdHis');
                    $_ext = strpos($_FILES["attach"]["type"][$key], '/');
                    $_ext = substr($_FILES["attach"]["type"][$key], $_ext+1);
                    $_ext = pathinfo($_FILES["attach"]["name"][$key], PATHINFO_EXTENSION);

                    ++$_i;


                    $local_name = '/appeal_attach/' . $chk_id . '/' . $appeal_id  . '_' . $_d . '_' . $_i . '.' . $_ext;
                    $full_local_name = $path . '/' . $appeal_id  . '_' . $_d . '_' . $_i . '.' . $_ext;

                    if(move_uploaded_file($val, //  $_FILES["filename"]["tmp_name"],
                         $full_local_name))
                    {
                        $_q = "insert into comms_attach (chk_id, comment_id, name, original_name, size) values (" . $chk_id . ", " . $appeal_id  . ", '" . $local_name . "', '" .  $_FILES["attach"]["name"][$key]. "', " . $_FILES["attach"]["size"][$key] . ")";
                        $this->db->query($_q);
                    }
                    else { /*exit('Not moved');*/ }
                }
            }
            if ($res!==false) {
/////////////////////////////////
/*
 $in['chk_id'] 
$in['text']
$in['type'] 
$in['parent_id']
$in['q_id']
$in['corrected']


*/
//pre($in);
    $m='';
    if ($in['type']==5 ) { // Финальная, отправляем коллегам в работу

    }


    if ($in['type']==2  || $in['type']==4 || $in['type']==5 ) { // Ответ на апелляцию или ответ на повторную апелляцию
        $q="
select coalesce(u.post, u.login) as email, s.shop_n as shop_name, c.* from comms c
  join user u on c.uid = u.id
  join tmp_stat_total2 t on t.chk_id=c.chk_id
  join shops s on s.shop_id=t.shop_id
                where c.chk_id=" . $in['chk_id'] . " && c.id = " . $in['parent_id'] . " limit 1
        ";


//pre($in['text'],1);
//pre($q,1);
        $parent_app = $this->db->read($q);
// pre($parent_app,1);
        if (count($parent_app)>0) {
            if ($in['type']==5 ) { // Финальная, отправляем коллегам в работу
                $r = $main_reciepients ;
                $r[] = $parent_app[0]['email'];
                $h = "Финальная апелляция по проекту " .PROJECT. ", " . $parent_app[0]['shop_name'] . ". ";

                $m='
                Добрый день.
                <br />
                Поступила Финальная апелляция по анкете <a href="' . DOMAIN .'/page/comm_anketa/?id='.$in['chk_id']  . '">' . $in['chk_id']  . '</a> <br />
               <hr />
                <br />
                <br />
                <br />
                <div style="padding: 10px 10px 10px 60px ; font:14px Arial; color:#ff4545"><b>' . str_replace("\\r\\n","<br />", $in['text']) . ' </b> </div>
                <hr />
                <br />
                    <br><i><a href="http://admin.imystery.ru/show_anketa/?task_id=' . $in['chk_id']  . '" target="_blank">(перейти к анкете ' . $in['chk_id']  . ')</a></i>
                <br />
                <br />
                <i>(' . date('Y-m-d H:i:s') . ')</i>.
                <br/>
                <br/>
                SY, Robot iMystery
                ';

            }
            else {
            $m='
            Добрый день.
                <br />
                <br />
                    На Вашу апелляцию в анкете <a href="' . DOMAIN .'/page/comm_anketa/?id='.$in['chk_id']  . '">' . $in['chk_id']  . '</a> на оценку по вопросу -  <br>
                <i>' . $parent_app[0]['text'] . '</i> <br />-  получен ответ:
                <br />
            <hr />
                <br />
                <br />
                <br /> 
                ' . str_replace("\\r\\n","<br />", $in['text']) . ' <br />

                <br />
                <i>(' . date('Y-m-d H:i:s') . ')</i> 
                <br/>
                <br/>
        ';
                        if ($in['corrected']>0) {
                        $m .= '<b>(оценка исправлена)</b>';
                }
                else  {
                    $m .= '<b>(оценка без изменений)</b>';
                    }
            $m .= '
            <hr />
                <br /> 
                <br>С уважением, <br>Команда Romir Mystery';
            $h = "Тайный Покупатель. " . $parent_app[0]['shop_name'] . ". Ответ на апелляцию.";
            $r = [

// Один из вариантов
//                'khabarov.g@romir.ru'
                 $parent_app[0]['email']
            ];
            }
// Один из вариантов
            if (notifyUsers($r, $h, $m ) ) {
//            if (false )
                        $mail_log_filename = SERVER_ROOT . '/mlog/send_log_' . date('Ymd') . '.txt';
                        file_put_contents( $mail_log_filename , $in['chk_id'] . ": " . date('Y-m-d H:i:s') . " - answer to appelation " . $in['parent_id'] . " sended Ok for " . $r[0] . " \r\n", FILE_APPEND);
                        chgrp( $mail_log_filename ,'www-data');
                        chmod( $mail_log_filename ,0666);

            }
        }
    }


/////////////////////////////////
            }
            header("Location: /page/comm_anketa/?id=".(int)$_GET['id']);
    }

    $q="
        select c1.*,
          c2.id as id2, c2.time as time2,   c2.text as answer,  c2.autor answer_author, c2.corrected as q_corrected,
          c3.id as id3, c3.time as time3,   c3.text as answer3,  c3.autor answer_author3, c3.corrected as q_corrected3,
          c4.id as id4, c4.time as time4,   c4.text as answer4,  c4.autor answer_author4, c4.corrected as q_corrected4,
          c5.id as id5, c5.time as time5,   c5.text as answer5,  c5.autor answer_author5, c5.corrected as q_corrected5,

          ut.wave_id

        from

          (select * from " . BASE_NAME . ".comms where chk_id = ".(int)$_GET['id']." && (type < 2 || type is null ) ) c1
        left join
          (select * from " . BASE_NAME . ".comms where chk_id = ".(int)$_GET['id']." && type = 2) c2
        on c1.id=c2.parent_id

        left join
          (select * from " . BASE_NAME . ".comms where chk_id = ".(int)$_GET['id']." &&  type = 3) c3
        on c2.id=c3.parent_id

        left join
          (select * from " . BASE_NAME . ".comms where chk_id = ".(int)$_GET['id']." &&  type = 4) c4
        on c3.id=c4.parent_id

        left join
          (select * from " . BASE_NAME . ".comms where chk_id = ".(int)$_GET['id']." &&  type = 5) c5
        on c4.id=c5.parent_id

        join efes.user_task ut on ut.id =  ".(int)$_GET['id']." 

           order by c2.text , c1.time DESC
        ";

// pre($q,1);

    $allmsg = $this->db->read($q);
//pre($allmsg,1);
    $allmsg = (!empty($allmsg)) ? $allmsg : array();
    ob_start();

?>

<style>
.isDisabled {
  color: currentColor;
  cursor: not-allowed;
  opacity: 0.5;
  text-decoration: none;
}
.comm_text_value {
  width: 100%; 
  height: 25px !important; 
  box-sizing: border-box; 
  font: 15px Arial;
  margin-bottom: 10px;
}


	td{text-align: center; padding:5px;}
	td:first-child{text-align: left;}
	.ht td{background: #d2efc2;}
	.hth td{border-color:#FFF; border:1px solid #999; border-top:0px solid #999;}
	.no{color:#A00;}
	.yes{color:#080;}
</style>


<div class="mainBoxDiv">

	<div style="text-align: right;"><a href="/page/show_anketa/?chk_id=<?=(int)$_GET['id']?>">Назад</a></div>
	<h4>Комментарий к проверке</h4>


        <?php
// GEH Условие для первой апелляции
/*
$_excepted_permissions = [
// Исключения. Разрешено подавать первичные апелляции до. Внести в базу
    12646  => '2199-01-01',
    12647  => '2199-01-01',
    12958  => '2199-01-01',
    11266  => '2199-01-01',
    16787  => '2199-01-01'

// ПРОПИСЫВАТЬ в show_anketa_lib.php !!!
в showAppForm!
];

*/
 if (  showAppForm($params, true, $appelation_wave_arr) /* && !in_array($allmsg[0]['wave_id'], $appelation_wave_arr ) */  
      && intval($_COOKIE['user_id']) != 47358
//      || intval($_COOKIE['user_id'])== 12647
//      || intval($_COOKIE['user_id'])== 12958
//      || intval($_COOKIE['user_id'])== 11266
//      || intval($_COOKIE['user_id'])== 16787

    ) :  ?>
<?php 

?>
	<form action="" method="post" style="margin-bottom: 30px;" ENCTYPE="multipart/form-data" >
                <input name="qustion_text" id="comm_text_value" class="comm_text_value" placeholder="Текст вопроса" type="text" disabled>
		<textarea name="text" id="comm_text" cols="30" rows="10" style="width: 100%; height:100px; box-sizing: border-box; font:15px Arial;" disabled="disabled"></textarea>
                <input type="hidden" id="q_id" name="q_id" value="" />

Добавить файл (ы):
<input type="file" accept="*/*" id="attach" name="attach[]"  multiple="true" /> &nbsp;
<!-- input type="submit" value="Добавить"  onclick="javascript:if((document.getElementById(\'video_filenames\').value)>\'\') {document.getElementById(\'db\').value=\'ui\';}else return(false);" /> -->

		<div style="text-align: right;">
 <!--				<input type="submit" value="Комментировать" name="appl"> -->

				<input type="submit" value="Комментировать" >
		</div>
	</form>



        <?php
             $clear_links = 0;
             else: 
             $clear_links = 1;
    
                    ?>
                <u>Вопросы к апелляции:</u> <br/>
                        <!-- button -->

        <?php 
//print_r($res);
        endif; ?>




	<?php 
        $_q_arr=array();
// Уже откомментированные - убираем <a href>
        foreach ($allmsg as $key => $val) {
            $_q_arr[]=$val['q_id'];
        }
//print_r($_q_arr);
//exit;
//pre($res,1);
?>
<style>
.appelation_list  {
    text-align: left;
    vertical-align:top;
    border: none;
    font-size: 14px;
}

.appelation_list_blue  {
    color:blue  !important;
    text-align: center !important;
}

.appelation_list_center {
    text-align: center !important;
}

.appelation_list_header {
    text-align: center !important;
/*    border: none; */
    font-size: 14px;
}

</style>

<table width=100%>
<tr>
<td class="appelation_list_header">Вес вопроса</td>
<td class="appelation_list_header" >Текст вопроса</td>
</tr>
<?php
        foreach ($res as /* $key => */ $val) {
            echo get_app_question_str($val, $_q_arr, $clear_links, $_points_max);
        }
//exit;
        ?>
</table>

<hr />

	<?php foreach ($allmsg as $key => $val):?>
<?php 
                $attaches = $this->db->read("select * from comms_attach where comment_id = ".$val['id']." ");
?>

		<div style="min-height: 100px; border:1px solid #DDD; margin-top: 5px;">
			<div style="background:<?=(0 && $val['type'])?'#bba088':'#98bb88'?>; padding: 3px 3px 4px; font:11px Verdana; color:#EEE">
				<?=$val['time']?> Автор: <?=$val['autor']?>
				<?=(0 && $val['type'] == 1)?' (Апелляция)':''?>
				<?=(0 && $val['type'] == 2)?' (Ответ на апелляцию)':''?>
			</div>
			<div style="padding: 10px; font:14px Arial; color:#454545"><?=$val['text']?></div>
                	<?php foreach ($attaches as $key1 => $val1):?>	
        			<div style="padding: 10px; font:14px Arial; color:#454545"><a href="<?=$val1['name']?>"><?=$val1['original_name']?> </a> <i><?= round($val1['size']/1024,2) ?>(Kb)</i></div>
                	<?php endforeach; ?>

<!--		</div> -->

<?php  if ($val['answer']!==null) {
// Ответы в этом же <div> 
                $attaches2 = $this->db->read("select * from comms_attach where comment_id = ".$val['id2']." ");
?>
                        <div class="<?=  ($val['q_corrected']==1) ? 'ap_ans_yes'  : 'ap_ans_no' ?>" style=" padding: 3px 3px 4px; font:11px Verdana; color:#EEE; background: #bbbbbb;">
                                <?=$val['time2']?> Автор: <?=$val['answer_author']?>
                                <?= ($val['answer'] !==null) ? ' (Ответ на аппеляцию)':''?>
                        </div>
                        <div style="padding: 10px 10px 10px 40px ; font:14px Arial; color:#454545"><?=$val['answer']?></div>
                        <?php foreach ($attaches2 as $key1 => $val1):?>	
                                <div style="padding: 10px; font:14px Arial; color:#454545"><a href="<?=$val1['name']?>"><?=$val1['original_name']?> </a> <i><?= round($val1['size']/1024,2) ?>(Kb)</i></div>
                        <?php endforeach; ?>

<?php  } else {  ?>
                        <form id="div_<?= $val['id'] ?>" action="" method="post" style="margin-bottom: 30px;  display : <?= ($val['id']==$_GET['cid'])  ? '' : 'none' ?>" class="ReplyForm"  ENCTYPE="multipart/form-data">
                        <textarea name="text" id="" cols="30" rows="10" style="width: 100%; height:100px; box-sizing: border-box; font:15px Arial;"></textarea>
                        <input type="hidden"  name ="cid" value="<?= $val['id'] ?>" />
                        <input type="hidden" name ="q_id" id="qid_div_<?= $val['id'] ?>" value="0" />
                        <input type="hidden" name ="crr" id="crr_<?= $val['id'] ?>" value="0" />
                        <input type="hidden" name ="appl" value="2" />  <?php /* тип апелляция ==1, ответ ==2 */ ?>
Добавить файл (ы):
                        <input type="file" accept="*/*" id="attach" name="attach[]"  multiple="true" /> &nbsp;

                        <div style="text-align: right;">  
                        <div style="float: left; padding: 15px;"><a href="http://admin.imystery.ru/show_anketa/?task_id=<?= (int)$_GET['id'] ?>#<?= $val['q_id'] ?>" target=_blank> Перейти к вопросу в анкете </a>
                        </div> 

                        <input type="submit" value="Оценка исправлена. Romir"                style="background: #33CC99;" onclick="return correction(1,<?= $val['id'] ?>)"  />
                        <input type="submit" value="Оценка исправлена. <?= PROJECT_NAME ?>"     style="background: #33CC33;" onclick="return correction(2,<?= $val['id'] ?>)"  />
                        <input type="submit" value="Оценка без изменений"  style="background: #FF9900;" onclick="       correction(0,<?= $val['id'] ?>)") />
                        </div>

                    </form>


<?php
                    showReplyLink($val['id']);
?>

<?php  }  ?>


<?php  if ($val['answer3']!==null) {
// Повторная апелляция 

                $attaches3 = $this->db->read("select * from comms_attach where comment_id = ".$val['id3']." ");
?>
                        <div class="<?=  ($val['q_corrected4']==1) ? 'ap_ans_yes'  : 'ap_ans_no' ?>" style=" padding: 3px 3px 4px; font:11px Verdana; color:#EEE;  <?= ($val['q_corrected4']===null) ? 'background:#98bb88;' : '' ?>">
                                <?=$val['time3']?> Автор: <?=$val['answer_author3']?>
                                 (Повторная апелляция)
                        </div>
                        <div style="padding: 10px 10px 10px 60px ; font:14px Arial; color:#454545"><?=$val['answer3']?></div>
                	<?php foreach ($attaches3 as $key1 => $val1):?>	
        			<div style="padding: 10px; font:14px Arial; color:#454545"><a href="<?=$val1['name']?>"><?=$val1['original_name']?> </a> <i><?= round($val1['size']/1024,2) ?>(Kb)</i></div>
                	<?php endforeach; ?>

<?php  } else {  ?>
<?php      if(!in_array($allmsg[0]['wave_id'], $appelation_wave_arr ) ) {

 ?>
                        <form id="div_<?= $val['id2'] ?>" action="" method="post" 
                             ENCTYPE="multipart/form-data"  
                                style="margin-bottom: 30px;  display : <?= /* ($val['id']==$_GET['cid'])  ? '' : */ 'none' ?>" class="ReplyForm">
                        <textarea name="text" id="" cols="30" rows="10" style="width: 100%; height:100px; box-sizing: border-box; font:15px Arial;"></textarea>
                        <input type="hidden"  name ="cid" value="<?= $val['id2'] ?>" />
                        <input type="hidden" name ="q_id" id="qid_div_<?= $val['id2'] ?>" value="0" />
                        <input type="hidden" name ="crr" id="crr_<?= $val['id2'] ?>" value="0" />
                        <input type="hidden" name ="appl" value="3" />  <?php /* тип апелляция ==1, ответ ==2 */ ?>

Добавить файл (ы):
                        <input type="file" accept="*/*" id="attach" name="attach[]"  multiple="true" /> &nbsp;

                        <div style="text-align: right;">
                        <input type="submit" value="Еще раз комментировать"  style="1background: #FF9900;" onclick="       correction(0,<?= $val['id2'] ?>)") />
                        </div>


                    </form>
<?php
                    if ($val['answer']!==null) {
//                            $app_2nd_expired = $this->db->read("select efes.fn_get_workday_diff('" . $val['time2'] .  "',1) as app_expired ")[0]['app_expired'];
//                            pre($app_2nd_expired );
                        if ( ($this->db->read("select efes.fn_get_workday_diff('" . $val['time2'] .  "', 4 ) as app_expired ")[0]['app_expired']) > date('Y-m-d H:i:s') ) {
                            showReplyLink($val['id2'],2);
                        }
                    }
?>

<?php      }  ?>
<?php  }  ?>



<?php  if ($val['answer4']!==null) {  
// Ответ на Повторная апелляция 

                        $attaches4 = $this->db->read("select * from comms_attach where comment_id = ".$val['id4']." ");

?>
                        <div class="<?=  ($val['q_corrected']==1) ? 'ap_ans_yes'  : 'ap_ans_no' ?>" style=" padding: 3px 3px 4px; font:11px Verdana; color:#EEE; background: #bbbbbb;">
                                <?=$val['time4']?> Автор: <?=$val['answer_author4']?>
                                 (Ответ на повторную апелляцию)
                        </div>
                        <div style="padding: 10px 10px 10px 80px ; font:14px Arial; color:#454545"><?=$val['answer4']?></div>
                        <?php foreach ($attaches4 as $key1 => $val1):?>	
                                <div style="padding: 10px; font:14px Arial; color:#454545"><a href="<?=$val1['name']?>"><?=$val1['original_name']?> </a> <i><?= round($val1['size']/1024,2) ?>(Kb)</i></div>
                        <?php endforeach; ?>

<?php  } else {  ?>
                        <form id="div_<?= $val['id3'] ?>" action="" method="post" style="margin-bottom: 30px;  display : <?= /* ($val['id']==$_GET['cid'])  ? '' : */ 'none' ?>" class="ReplyForm"
                         ENCTYPE="multipart/form-data"  
                        >
                        <textarea name="text" id="" cols="30" rows="10" style="width: 100%; height:100px; box-sizing: border-box; font:15px Arial;"></textarea>
                        <input type="hidden"  name ="cid" value="<?= $val['id3'] ?>" />
                        <input type="hidden" name ="q_id" id="qid_div_<?= $val['id3'] ?>" value="0" />
                        <input type="hidden" name ="crr" id="crr_<?= $val['id3'] ?>" value="0" />
                        <input type="hidden" name ="appl" value="4" />  <?php /* тип апелляция ==1, ответ ==2 */ ?>

Добавить файл(ы):
                        <input type="file" accept="*/*" id="attach" name="attach[]"  multiple="true" /> &nbsp;

                        <div style="text-align: right;">
                        <div style="float: left; padding: 15px;"><a href="http://admin.imystery.ru/show_anketa/?task_id=<?= (int)$_GET['id'] ?>#<?= $val['q_id'] ?>" target=_blank> Перейти к вопросу в анкете </a>
                        </div> 

                        <input type="submit" value="Оценка исправлена. Romir"                style="background: #33CC99;" onclick="return correction(1,<?= $val['id3'] ?>)"  />
                        <input type="submit" value="Оценка исправлена. <?= PROJECT_NAME ?>"     style="background: #33CC33;" onclick="return correction(2,<?= $val['id3'] ?>)"  />
                        <input type="submit" value="Оценка без изменений"  style="background: #FF9900;" onclick="       correction(0,<?= $val['id3'] ?>)") />

                        </div> 


                    </form>
<?php
                    if ($val['answer3']!==null) {
                        showReplyLink($val['id3'],3);
                    }
?>

<?php  }  ?>



<?php  if ($val['answer5']!==null) {  
// Финальная апелляция 

                $attaches5 = $this->db->read("select * from comms_attach where comment_id = ".$val['id5']." ");
?>
                        <div class="<?=  ($val['q_corrected5']==1) ? 'ap_ans_yes'  : 'ap_ans_no' ?>" style=" padding: 3px 3px 4px; font:11px Verdana; color:#EEE;  <?= ($val['q_corrected4']===null) ? 'background:#98bb88;' : '' ?>">
                                <?=$val['time5']?> Автор: <?=$val['answer_author5']?>
                                 (Финальная апелляция)
                        </div>
                        <div style="padding: 10px 10px 10px 60px ; font:14px Arial; color:#ff4545"><b><?=$val['answer5']?></b><br />
                        <?php if (false !== array_search($_COOKIE['user_id'], $admuser)): ?>
                                <br /><i><a href="http://admin.imystery.ru/show_anketa/?task_id=<?=$val['chk_id']?>" target=_blank>(перейти к анкете <?=$val['chk_id']?>)</a></i>
                        <?php endif; ?>

                        </div>
                	<?php foreach ($attaches5 as $key1 => $val1):?>	
        			<div style="padding: 10px; font:14px Arial; color:#454545"><a href="<?=$val1['name']?>"><?=$val1['original_name']?> </a> <i><?= round($val1['size']/1024,2) ?>(Kb)</i></div>
                	<?php endforeach; ?>

<?php  } else {  ?>
<?php      if(!in_array($allmsg[0]['wave_id'], $appelation_wave_arr ) ) {   ?>

                        <form id="div_<?= $val['id4'] ?>" action="" method="post" 
                             ENCTYPE="multipart/form-data"  
                                style="margin-bottom: 30px;  display : <?= /* ($val['id']==$_GET['cid'])  ? '' : */ 'none' ?>" class="ReplyForm">
                        <textarea name="text" id="" cols="30" rows="10" style="width: 100%; height:100px; box-sizing: border-box; font:15px Arial;"></textarea>
                        <input type="hidden"  name ="cid" value="<?= $val['id4'] ?>" />
                        <input type="hidden" name ="q_id" id="qid_div_<?= $val['id4'] ?>" value="0" />
                        <input type="hidden" name ="crr" id="crr_<?= $val['id4'] ?>" value="0" />
                        <input type="hidden" name ="appl" value="5" />  <?php /* тип апелляция ==1, ответ ==2 */ ?>

Добавить файл (ы):
                        <input type="file" accept="*/*" id="attach" name="attach[]"  multiple="true" /> &nbsp; 

                        <div style="text-align: right;">
                        <input type="submit" value="Отправить финальную апелляцию" onclick="       correction(0,<?= $val['id4'] ?>)") />
                        </div>


                    </form>
<?php
                    if ($val['answer4']!==null) {
                        showReplyLink($val['id4'],4);
                    }
?>
<?php     } ?>
<?php } ?>


        </div>


	<?php endforeach; ?>


</div>
<script src="/web/main/js/app_form.js"></script>

<?php
    if (isset($_GET['cid']) ) {
        echo "
            <script>
                showReplyForm('div_" . intval($_GET['cid']) . "');
            </script>";
    }

$out = ob_get_contents(); ob_end_clean();
$this->setToPoint('main', $out, 1);



// Формирование ссылки или текста 
// вопроса для апелляции.
// тут нужно добавить обработку прав доступа клиента

function get_app_question_str($val, $_q_arr, $clear_link = 0, $_points_max ) {

// pre($val);
// pre($_q_arr,1); // Уже апеллированные вопросы

$r='';

// Безбалльные вопросы, по которым нельзя подавать апелляции
$excluded_question = [5535, 5536, 5838, 5839, 5710, 5711, 5180, 5181, 5275, 5276, 5987, 5988, 5646, 5648, 5760, 5761, 5376, 5377, 6340, 6341, 7652, 7656, 7690, 7691, 8207, 8210];
if (in_array($val['qid'], $excluded_question) ) {
    return($r);
}
            if (

// Не набран максимум баллов по вопросу
            ($val['score']< $val['max_score'] && $val['score']!==null ) 

// Ответы НЕТ и N/A, если они безбалльные (+ вопрос исключение 6533 - на ответ ДА)
                ||
//false
            ( ($val['max_score']===null || $val['max_score']===0) &&( (mb_strtoupper($val['name_rus'])=="НЕТ" || mb_strtoupper($val['name_rus'])=="N/A") || (mb_strtoupper($val['name_rus'])=="ДА" && $val['qid']==6533) )  ) 
                )  { 

// Вопрос к апелляции по условию

                if (! in_array($val['qid'], $_q_arr) &&  $clear_link == 0) {
                    $r='<a href="" class="app_question" onclick="get_question(\'' . str_replace(chr(13).chr(10)," ",$val['question_text']) . '\',' . $val['qid'] . '); return false;">' . $val['question_text'] .' </a>' ; // <br />';
                } else {
                    $r = $val['question_text'] . "<br />";
                    if (in_array($val['qid'], $_q_arr) &&  $clear_link !== 0) $r= '<b>' . $r . '</b>';

                }

            }
            elseif( in_array($val['qid'], $_q_arr) ) {

// формулировка вопроса с измененном результатом
                    $r = $val['question_text'] ; //. "<br />";
                    if (in_array($val['qid'], $_q_arr)&&  $clear_link !== 0) $r= '<b>' . $r . '</b>';

            }
            else {
                 $r=false;
//                 $r='<i>' . mb_strtoupper($val['question_text']) .'</i><br/ >';
            }
            if ($r!==false) {
                $r = '<td class="appelation_list">' . $r . "</td>";
                if( $val['max_score'] !==null && $val['max_score'] !=0){
                    $r = '<td class="appelation_list appelation_list_blue">' . round100percent($val['max_score'] / $_points_max) . "</td> " . $r;
                }
                else {
                    $r = '<td class="appelation_list appelation_list_center">---</td>' . $r ;
                }
                $r = "<tr>" . $r . "</tr>";
            }
            return $r;

}



// Отвечать могут только аккредитованные пользователи
// тут настройка

function showReplyLink($id, $_level = 0) { 

// Апелляция Appelation
 $r = '';
 $_link_label = 'Ответить';
 $r_link = '<a href="javascript:showReplyForm(\'div_' . $id .'\');">';
 $r_link_tail = ' </a> ';
 $ur=intval($_COOKIE['user_right']);
 switch ($_level) {
    case 0  :  if ($ur==0 /* || $ur==5 */ )
                {
//                $_link_label = 'Подать апелляцию';
                $r = $r_link . $_link_label .$r_link_tail;
                }
                break;

// GEH. Условие подачи вторичной апелляции
    case 2  :  if (  $ur==0 || ( $ur>=10  && $ur<=11 /* 14 */ )  || ( $ur>=16  && $ur<=19  ) )
                {
                $_link_label = 'Подать повторную апелляцию';
                $r = $r_link . $_link_label .$r_link_tail;
                }
                break;

// GEH. Условие подачи Финальной апелляции, Суперапелляции, по которой нам следует исправить балл
    case 4  :  if (  $ur==0 ) 
                {
                $_link_label = 'Подать финальную апелляцию';
                $r = $r_link . $_link_label .$r_link_tail;
                }
                break;

    default :  if (  $ur==0 ) $r = $r_link . $_link_label .$r_link_tail; break;
 }
 echo $r;
}

