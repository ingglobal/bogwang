<?php
$sub_menu = "920100";
include_once('./_common.php');

check_demo();

if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

auth_check($auth[$sub_menu], 'w');
//print_r2($_POST['ord_idx']);
//print_r2($_POST['chk']);
if($w == 'd') {
    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];
		$sql = " UPDATE {$g5['order_table']} SET
                ord_status = 'trash'
            WHERE ord_idx = '{$_POST['ord_idx'][$k]}'
        ";
        //echo $sql;
        //echo "<br>";
        sql_query($sql,1);

        $sql_ori = " UPDATE {$g5['order_item_table']} SET
                        ori_status = 'trash'
                    WHERE ord_idx = '{$_POST['ord_idx'][$k]}'  
        ";
        //echo $sql_ori;
        //echo "<br><br><br>";
        sql_query($sql_ori,1);
    }
}

if ($msg)
    alert($msg);
    //echo '<script> alert("'.$msg.'"); </script>';

goto_url('./order_list.php?'.$qstr, false);