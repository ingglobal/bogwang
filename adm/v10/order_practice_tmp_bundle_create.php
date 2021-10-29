<?php
$sub_menu = "920110";
include_once('./_common.php');

check_demo();

if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

auth_check($auth[$sub_menu], 'w');

//print_r2($_POST);exit;

check_admin_token();

$com_idx = $_POST['com_idx'];
$orp_order_no = $_POST['orp_order_no'];
$trm_idx_line = $_POST['trm_idx_line'];
$mb_id = $_POST['mb_id'];
$orp_start_date = $_POST['orp_start_date'];
$orp_end_date = $_POST['orp_end_date'];
$orp_status = $_POST['orp_status'];

$chk_arr = $_POST['chk'];

$oro_date_plan_arr = $_POST['oro_date_plan'];
$oro_date_arr = $_POST['oro_date'];
$com_idx_shipto_arr = $_POST['com_idx_shipto'];
$oro_status_arr = $_POST['oro_status'];
$oro_1_arr = $_POST['oro_1'];
$oro_2_arr = $_POST['oro_2'];
$oro_3_arr = $_POST['oro_3'];
$oro_4_arr = $_POST['oro_4'];
$oro_5_arr = $_POST['oro_5'];
$oro_6_arr = $_POST['oro_6'];

$ord_idx_arr = $_POST['ord_idx'];
$ori_idx_arr = $_POST['ori_idx'];
$oro_idx_arr = $_POST['oro_idx'];
$oro_count_arr = $_POST['oro_count'];

//이미 등록된 동일한 지시번호가 존재하는지 확인한다.
$ord_no_sql = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$g5['order_practice_table']} WHERE orp_order = '{$orp_order_no}' AND orp_status NOT IN('delete','del','trash') ");
if($ord_no_sql['cnt'])
    alert('동일한 지시번호가 이미 존재합니다. 다른 지시번호를 입력해 주세요.');

foreach($chk_arr as $oro_idx_v1){
    //삭제,취소 등의 상태값이 아닌 생산실행 레코드가 있으면 중복 레코드를 생성하면 안된다.
    $chk_sql = " SELECT COUNT(*) AS cnt FROM {$g5['order_practice_table']} AS orp 
                    LEFT JOIN {$g5['order_out_practice_table']} AS oop ON orp.orp_idx = oop.orp_idx
                        WHERE oop.oro_idx = '{$oro_idx_v1}' AND orp.orp_status NOT IN('trash','del','delete','cancel') ";
    $chk_result = sql_fetch($chk_sql);
    //기존 생산실행 레코드가 있으면 다음루프로 넘어간다.
    if($chk_result['cnt']) alert('선택하신 항목중에 이미 생산계획에 등록된 항목이 있네요.\\n다시 확인하시기 바랍니다.');
}

//orp테이블에 1개의 레코드를 등록
$sql1 = " INSERT {$g5['order_practice_table']} SET
            com_idx = '".$com_idx."',
            orp_order_no = '".$orp_order_no."',
            trm_idx_operation = '',
            trm_idx_line = '".$trm_idx_line."',
            shf_idx = '',
            mb_id = '".$member['mb_id']."',
            orp_start_date = '".$orp_start_date."',
            orp_done_date = '',
            orp_memo = '',
            orp_status = '".$orp_status."',
            orp_reg_dt = '".G5_TIME_YMDHIS."'
";
sql_query($sql1,1);
$orp_idx = sql_insert_id();

foreach($chk_arr as $oro_idx_v){
    //ori_idx에 해당하는 bom_idx를 조회
    $bom_idx_sql = sql_fetch(" SELECT bom_idx FROM {$g5['order_item_table']} WHERE ori_idx = '".$ori_idx_arr[$oro_idx_v]."' ");
    $bom_idx = $bom_idx_sql['bom_idx'];

    //천단위 제거 
    $oro_count_arr[$oro_idx_v] = preg_replace("/,/","",$oro_count_arr[$oro_idx_v]);

    $sql = " UPDATE {$g5['order_out_table']} SET
                oro_count = '".sql_real_escape_string($oro_count_arr[$oro_idx_v])."',
                oro_date_plan = '".$oro_date_plan_arr[$oro_idx_v]."',
                oro_date = '".$oro_date_arr[$oro_idx_v]."',
                com_idx_shipto = '".$com_idx_shipto_arr[$oro_idx_v]."',
                oro_status = '".$oro_status_arr[$oro_idx_v]."',
                oro_update_dt = '".G5_TIME_YMDHIS."',
                oro_1 = '".$oro_1_arr[$oro_idx_v]."',
                oro_2 = '".$oro_2_arr[$oro_idx_v]."',
                oro_3 = '".$oro_3_arr[$oro_idx_v]."',
                oro_4 = '".$oro_4_arr[$oro_idx_v]."',
                oro_5 = '".$oro_5_arr[$oro_idx_v]."',
                oro_6 = '".$oro_6_arr[$oro_idx_v]."'
            WHERE oro_idx = '".$oro_idx_v."'
    ";

    sql_query($sql,1);

    //oop테이블에 등록
    $sql2 = " INSERT {$g5['order_out_practice_table']} SET
                ord_idx = '".$ord_idx_arr[$oro_idx_v]."',
                ori_idx = '".$ori_idx_arr[$oro_idx_v]."',
                oro_idx = '".$oro_idx_arr[$oro_idx_v]."',
                orp_idx = '".$orp_idx."',
                bom_idx = '".$bom_idx."',
                oop_count = '".$oro_count_arr[$oro_idx_v]."',
                oop_history = '',
                oop_1 = '".$oro_count_arr[$oro_idx_v]."'
    ";
    sql_query($sql2,1);
}

$qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들
goto_url('./order_out_list.php?'.$qstr);