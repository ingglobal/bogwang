<?php
$sub_menu = "945110";
include_once('./_common.php');

if( auth_check($auth[$sub_menu],"w",1) ) {
    alert('메뉴 접근 권한이 없습니다.');
}

if(!$bom_idx) alert('상품ID가 제대로 넘어오지 않았습니다.');
if(!$bom_part_no) alert('품번이 제대로 넘어오지 않았습니다.');
if(!$bom_name) alert('품명이 제대로 넘어오지 않았습니다.');
if(!$bom_type) alert('상품유형이 제대로 넘어오지 않았습니다.');
if(!$bom_price) alert('상품단가가 제대로 넘어오지 않았습니다.');
if(!$mtr_times) alert('입고차수가 제대로 넘어오지 않았습니다.');
if(!$mtr_input_date) alert('입고일이 제대로 넘어오지 않았습니다.');
if(!$counts) alert('입고갯수가 제대로 넘어오지 않았습니다.');

// print_r2($_POST);
// print_r2($counts);

if($act_button == '자재입고'){
    $sql = " INSERT INTO {$g5['material_table']} (`com_idx`,`bom_idx`,`bom_part_no`,`mtr_name`,`mtr_type`,`mtr_price`,`mtr_times`,`mtr_status`,`mtr_input_date`,`mtr_reg_dt`,`mtr_update_dt`) VALUES ";
    for($i=0;$i<$counts;$i++){
        $coma = ($i == 0) ? '' : ',';
        $sql .= $coma." ('{$_SESSION['ss_com_idx']}','{$bom_idx}','{$bom_part_no}','{$bom_name}','{$bom_type}','{$bom_price}','{$mtr_times}','stock','{$mtr_input_date}','".G5_TIME_YMDHIS."','".G5_TIME_YMDHIS."') ";
    }
    sql_query($sql,1);
}
else if($act_button == '자재삭제'){
    $sql = "SELECT * FROM {$g5['material_table']}
            WHERE com_idx = '{$_SESSION['ss_com_idx']}' AND bom_part_no = '{$bom_part_no}' AND mtr_input_date = '{$mtr_input_date}' AND mtr_type = '{$bom_type}' AND mtr_status = 'stock' ORDER BY mtr_idx LIMIT {$counts} ";
    $rs = sql_query($sql,1);
    
    for($i=0; $row=sql_fetch_array($rs);$i++){
        //자재들 상태 전체 변경
        $ar['mtr_status'] = 'trash';
        $ar['mtr_idx'] = $row['mtr_idx'];
        update_mtr_status($ar);
        unset($ar);
    }
}

$qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들
goto_url('./material_list.php?'.$qstr);