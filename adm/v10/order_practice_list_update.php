<?php
$sub_menu = "930105";
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
        $_POST['orp_count'][$k] = preg_replace("/,/","",$_POST['orp_count'][$k]);
        /*
        $sql = "UPDATE {$g5['order_practice_table']} SET
                    orp_count = '".sql_real_escape_string($_POST['orp_count'][$k])."',
                    orp_update_dt = '".G5_TIME_YMDHIS."'
                WHERE orp_idx = '".$_POST['orp_idx'][$k]."'
        ";
        */
        $sql = "UPDATE {$g5['order_practice_table']} SET
                    orp_update_dt = '".G5_TIME_YMDHIS."'
                WHERE orp_idx = '".$_POST['orp_idx'][$k]."'
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
        $sql = "UPDATE {$g5['order_practice_table']} SET
                    orp_status = 'trash'
                    , orp_history = CONCAT(orp_history,'\n삭제 by ".$member['mb_name'].", ".G5_TIME_YMDHIS."')
                WHERE orp_idx = '".$mb['orp_idx']."'
        ";
        sql_query($sql,1);
    }

}

if ($msg)
    //echo '<script> alert("'.$msg.'"); </script>';
    alert($msg);

// exit;
$qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들
goto_url('./order_out_list.php?'.$qstr);
?>
