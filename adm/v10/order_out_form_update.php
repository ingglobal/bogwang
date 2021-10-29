<?php
$sub_menu = "920110";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');

//print_r2($_POST);exit;

//수주idx가 반드시 넘어와야 한다
if(!$_POST['ord_idx'])
    alert('수주번호를 입력해 주세요.');

//넘어온 수주idx가 반드시 존재하는(사용가능한) idx여야 한다.
$ord = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$g5['order_table']} WHERE ord_idx = '{$_POST['ord_idx']}' AND ord_status NOT IN('delete','del','trash','cancel') ");
if(!$ord['cnt'])
    alert('수주번호가 올바르지 않습니다. 다시 확인해서 정확이 입력해 주세요.');

//해당 수주번호로 등록된 출하목록중에 동일한 bom_idx가 존재하면 안된다
$ori_sql = " SELECT COUNT(*) AS cnt FROM {$g5['order_out_table']} AS oro
                LEFT JOIN {$g5['order_item_table']} AS ori ON oro.ori_idx = ori.ori_idx
                WHERE oro.ord_idx = '{$_POST['ord_idx']}' AND ori.bom_idx = '{$_POST['bom_idx']}' AND oro.oro_status NOT IN('delete','del','trash','cancel')               
 ";
$ori = sql_fetch($ori_sql);
if($ori['cnt'])
    alert('선택하신 제품이 동일한 수주번호의 출하목록에 이미 포함되어 있습니다.');

if(!$_POST['oro_count'])
    alert('출하수량을 설정해 주세요.');


// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'order_out';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form_update/","",$g5['file_name']); // _form_update를 제외한 파일명
$qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들

// 변수 재설정
for($i=0;$i<sizeof($fields);$i++) {
    // 공백 제거
    $_POST[$fields[$i]] = trim($_POST[$fields[$i]]);
    // 천단위 제거
    if(preg_match("/_price$/",$fields[$i]) || $fields[$i]=='oro_moq' || $fields[$i]=='oro_lead_time')
        $_POST[$fields[$i]] = preg_replace("/,/","",$_POST[$fields[$i]]);
}

// prior post value setting
$_POST['com_idx'] = $_SESSION['ss_com_idx'];


// 공통쿼리
$skips = array($pre.'_idx',$pre.'_reg_dt',$pre.'_update_dt');
for($i=0;$i<sizeof($fields);$i++) {
    if(in_array($fields[$i],$skips)) {continue;}
    $sql_commons[] = " ".$fields[$i]." = '".$_POST[$fields[$i]]."' ";
}

// after sql_common value setting
// $sql_commons[] = " com_idx = '".$_SESSION['ss_com_idx']."' ";

// 공통쿼리 생성
$sql_common = (is_array($sql_commons)) ? implode(",",$sql_commons) : '';


if ($w == '' || $w == 'c') {
    
    $sql = "INSERT INTO {$g5_table_name} SET 
               {$sql_common} 
                , ".$pre."_reg_dt = '".G5_TIME_YMDHIS."'
                , ".$pre."_update_dt = '".G5_TIME_YMDHIS."'
	";
    sql_query($sql,1);
	${$pre."_idx"} = sql_insert_id();
    
}
else if ($w == 'u') {

	${$pre} = get_table_meta($table_name, $pre.'_idx', ${$pre."_idx"});
    if (!${$pre}[$pre.'_idx'])
		alert('존재하지 않는 자료입니다.');
 
    $sql = "UPDATE {$g5_table_name} SET 
                {$sql_common}
                , ".$pre."_update_dt = '".G5_TIME_YMDHIS."'
            WHERE ".$pre."_idx = '".${$pre."_idx"}."' 
	";
    // echo $sql.'<br>';
    sql_query($sql,1);
        
}
else if ($w == 'd') {

    $sql = "UPDATE {$g5_table_name} SET
                ".$pre."_status = 'trash'
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
$fields[] = "oro_start_date";	// 건너뛸 변수명은 배열로 추가해 준다.
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