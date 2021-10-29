<?php
$sub_menu = "945110";
include_once('./_common.php');

check_demo();

if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

// print_r2($_POST);
// exit;
auth_check($auth[$sub_menu], 'w');

check_admin_token();

if ($_POST['act_button'] == "선택수정") {

    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        // 천단위 제거
        $_POST['mtr_price'][$k] = preg_replace("/,/","",$_POST['mtr_price'][$k]);
        $_POST['mtr_lead_time'][$k] = preg_replace("/,/","",$_POST['mtr_lead_time'][$k]);

        $sql = "UPDATE {$g5['material_table']} SET
                    mtr_barcode = '".sql_real_escape_string($_POST['mtr_barcode'][$k])."',
                    mtr_lot = '".$_POST['mtr_lot'][$k]."',
                    mtr_update_dt = '".G5_TIME_YMDHIS."'
                WHERE mtr_idx = '".$_POST['mtr_idx'][$k]."'
        ";
        // echo $sql.'<br>';
        sql_query($sql,1);
    
    }

} else if ($_POST['act_button'] == "선택삭제") {

    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        // 
        $sql = "UPDATE {$g5['material_table']} SET
                    mtr_status = 'trash'
                    , mtr_history = CONCAT(mtr_history,'\n삭제 by ".$member['mb_name'].", ".G5_TIME_YMDHIS."')
                WHERE mtr_idx = '".$mb['mtr_idx']."'
        ";
        sql_query($sql,1);
    }

}

if ($msg)
    //echo '<script> alert("'.$msg.'"); </script>';
    alert($msg);

// exit;
$qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들
goto_url('./material_list.php?'.$qstr);
?>
