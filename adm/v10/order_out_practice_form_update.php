<?php
$sub_menu = "930100";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');

$first_flag = ($orp_order_no && $trm_idx_line && $mb_id && $orp_start_date && $orp_end_date) ? true : false;
if($first_flag){
    $sql1 = " INSERT {$g5['order_practice_table']} SET
                com_idx = '".$_SESSION['ss_com_idx']."',
                orp_order_no = '".$orp_order_no."',
                trm_idx_operation = '',
                trm_idx_line = '".$trm_idx_line."',
                shf_idx = '',
                mb_id = '".$mb_id."',
                orp_start_date = '".$orp_start_date."',
                orp_done_date = '".$orp_end_date."',
                orp_memo = '',
                orp_status = 'confirm',
                orp_reg_dt = '".G5_TIME_YMDHIS."',
                orp_update_dt = '".G5_TIME_YMDHIS."'
    ";
    sql_query($sql1,1);
    $orp_idx = sql_insert_id();
}



// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'order_out_practice';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form_update/","",$g5['file_name']); // _form_update를 제외한 파일명
//$qstr .= '&ser_mms_idx='.$ser_mms_idx; // 추가로 확장해서 넘겨야 할 변수들

// 변수 재설정fff
for($i=0;$i<sizeof($fields);$i++) {
    // 공백 제거
    $_POST[$fields[$i]] = trim($_POST[$fields[$i]]);
    // 천단위 제거
    if(preg_match("/_price$/",$fields[$i]) || preg_match("/_count$/",$fields[$i]) || preg_match("/_cnt$/",$fields[$i]))
        $_POST[$fields[$i]] = preg_replace("/,/","",$_POST[$fields[$i]]);
}



// 공통쿼리
$skips = array($pre.'_idx',$pre.'_history',$pre.'_reg_dt',$pre.'_update_dt');
for($i=0;$i<sizeof($fields);$i++) {
    if(in_array($fields[$i],$skips)) {continue;}
    $sql_commons[] = " ".$fields[$i]." = '".$_POST[$fields[$i]]."' ";
}

//print_r2($sql_commons);exit;

$sql_common = (is_array($sql_commons)) ? implode(",",$sql_commons) : '';

//history 저장내용
$hskip = array($pre.'_idx',$pre.'_memo',$pre.'_history',$pre.'_reg_dt',$pre.'_update_dt',$pre.'_1',$pre.'_2',$pre.'_3',$pre.'_4',$pre.'_5',$pre.'_6',$pre.'_7',$pre.'_8',$pre.'_9',$pre.'_10');
for($i=0;$i<sizeof($fields);$i++){
    if(in_array($fields[$i],$hskip)) {continue;}
    $historys[] = $fields[$i].'='.$_POST[$fields[$i]];
}

$history = (is_array($historys)) ? implode(',',$historys).','.$pre.'_update_dt='.G5_TIME_YMDHIS.'\n' : '';

if ($w == '' || $w == 'c') {
    
    $sql = " INSERT into {$g5_table_name} SET 
                {$sql_common} 
                , ".$pre."_history = '".$history."'
                , ".$pre."_reg_dt = '".G5_TIME_YMDHIS."'
                , ".$pre."_update_dt = '".G5_TIME_YMDHIS."'
    ";
    sql_query($sql,1);
    ${$pre."_idx"} = sql_insert_id();
    if($first_flag){
        $sql = " UPDATE {$g5_table_name} SET orp_idx = '{$orp_idx}' WHERE oop_idx = '".${$pre."_idx"}."' ";
        sql_query($sql,1);
    }
}
else if ($w == 'u') {

    ${$pre} = get_table_meta($table_name, $pre.'_idx', ${$pre."_idx"});
    if (!${$pre}[$pre.'_idx'])
        alert('존재하지 않는 자료입니다.');
    //$mod_history = 'ord_idx=>'.${'ord_idx'}
    $sql = "	UPDATE {$g5_table_name} SET 
                    {$sql_common}
                    , ".$pre."_history = CONCAT('".$history."',".$pre."_history)
                    , ".$pre."_update_dt = '".G5_TIME_YMDHIS."'
                WHERE ".$pre."_idx = '".${$pre."_idx"}."' 
    ";
    // echo $sql.'<br>';exit;
    sql_query($sql,1);
        
}
else if ($w == 'd') {
    $sql = "UPDATE {$g5_table_name} SET
                ".$pre."_status = 'trash'
                , ".$pre."_history = CONCAT('".$history."',".$pre."_history)
            WHERE ".$pre."_idx = '".${$pre."_idx"}."'
            ";
    sql_query($sql,1);
    goto_url('./'.$fname.'_list.php?'.$qstr, false);
    
}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');


//-- 체크박스 값이 안 넘어오는 현상 때문에 추가, 폼의 체크박스는 모두 배열로 선언해 주세요.
$checkbox_array=array();
for ($i=0;$i<sizeof($checkbox_array);$i++) {
	if(!$_REQUEST[$checkbox_array[$i]])
		$_REQUEST[$checkbox_array[$i]] = 0;
}

//-- 메타 입력 (디비에 있는 설정된 값은 입력하지 않는다.) --//
$fields[] = "mms_zip";	// 건너뛸 변수명은 배열로 추가해 준다.
$fields[] = "mms_sido_cd";	// 건너뛸 변수명은 배열로 추가해 준다.
foreach($_REQUEST as $key => $value ) {
	//-- 해당 테이블에 있는 필드 제외하고 테이블 prefix 로 시작하는 변수들만 업데이트 --//
	if(!in_array($key,$fields) && substr($key,0,3)==$pre) {
		//echo $key."=".$_REQUEST[$key]."<br>";
		meta_update(array("mta_db_table"=>$table_name,"mta_db_id"=>${$pre."_idx"},"mta_key"=>$key,"mta_value"=>$value));
	}
}

// exit;
goto_url('./'.$fname.'_list.php?'.$qstr.'&w=u&'.$pre.'_idx='.${$pre."_idx"}, false);
// goto_url('./'.$fname.'_form.php?'.$qstr.'&w=u&'.$pre.'_idx='.${$pre."_idx"}, false);
?>