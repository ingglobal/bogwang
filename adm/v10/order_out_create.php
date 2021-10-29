<?php
$sub_menu = "920100";
include_once('./_common.php');

if( auth_check($auth[$sub_menu],"w",1) ) {
    alert('메뉴 접근 권한이 없습니다.');
}

if(!$ord_idx)
    alert('주문번호가 제대로 넘어오지 않았습니다.');
/*
$sql = " SELECT * FROM {$g5['order_table']} WHERE ord_idx = '{$ord_idx}' ";
$ord = sql_fetch($sql);
*/
$sql_it = " SELECT * FROM {$g5['order_item_table']} WHERE ord_idx = '{$ord_idx}' AND ori_status NOT IN('trash','delete','del','cancel') ORDER BY ori_idx,ori_reg_dt DESC ";
$result = sql_query($sql_it,1);
//$total_count = sql_num_rows($result);

for($i=0;$row=sql_fetch_array($result);$i++){
    $sql_ot = " INSERT into {$g5['order_out_table']} SET
					com_idx = '{$row['com_idx']}',
					com_idx_customer = '{$row['com_idx_customer']}',
					ord_idx = '{$ord_idx}',
					ori_idx = '{$row['ori_idx']}',
					oro_count = '{$row['ori_count']}',
					oro_date_plan = '',
					oro_date = '',
					oro_memo = '',
                    com_idx_shipto = '{$row['com_idx_customer']}',
					oro_status = 'pending',
					oro_reg_dt = '".G5_TIME_YMDHIS."',
					oro_update_dt = '".G5_TIME_YMDHIS."',
					oro_1 = '{$row['ori_count']}'
    ";
    //print_r2($sql_ot);
    sql_query($sql_ot,1);
}

goto_url('./order_list.php?'.$qstr, false);