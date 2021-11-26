<?php
// 크롬 요소검사 열고 확인하면 되겠습니다. 
// print_r2 안 쓰고 print_r로 확인하는 게 좋습니다.
header('Content-Type: application/json; charset=UTF-8');
include_once('./_common.php');

//print_r2($_REQUEST);exit;
//echo $_REQUEST['shf_type'][0];
$rawBody = file_get_contents("php://input"); // 본문을 불러옴
$getData = array(json_decode($rawBody,true)); // 데이터를 변수에 넣고
// print_r2($getData);
// echo $getData[0]['imp_idx'];
// exit;

// 토큰 비교
if(!check_token1($getData[0]['token'])) {
	$result_arr = array("code"=>499,"message"=>"token error");
}
else if($getData[0]['bom_part_no']) {

    $arr = $getData[0];

    $arr['itm_status'] = 'ing';
    $arr['itm_dt'] = strtotime(preg_replace('/\./','-',$arr['itm_date'])." ".$arr['itm_time']);
    $arr['itm_date1'] = date("Y-m-d",$arr['itm_dt']);   // 2 or 4 digit format(20 or 2020) no problem.
    $arr['st_time'] = strtotime($arr['itm_date1']." 00:00:00"); // 해당 날짜의 시작
    $arr['en_time'] = strtotime($arr['itm_date1']." 23:59:59"); // 해당 날짜의 끝
    $arr['itm_dt2'] = strtotime(preg_replace('/\./','-',$arr['itm_date2'])." 00:00:00");    // statistics date
    $arr['itm_date_stat'] = date("Y-m-d",$arr['itm_dt2']);   // 2 or 4 digit format(20 or 2020) no problem.
    // $table_name = 'g5_1_item_'.$arr['mms_idx'];  // 향후 테이블 분리가 필요하면..
    $table_name = 'g5_1_item';

    // checkout db table exists and create if not exists.
    $sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables
                WHERE TABLE_SCHEMA = '".G5_MYSQL_DB."'
                AND TABLE_NAME = '".$table_name."'
            ) AS flag
    ";
    // echo $sql.'<br>';
    $tb1 = sql_fetch($sql,1);
    if(!$tb1['flag']) {
        $file = file('./sql_write.sql');
        $file = get_db_create_replace($file);
        $sql = implode("\n", $file);
        $source = array('/__TABLE_NAME__/', '/;/');
        $target = array($table_name, '');
        $sql = preg_replace($source, $target, $sql);
        sql_query($sql, FALSE);
    }

    $oop = get_table_meta('order_out_practice','oop_idx',$arr['oop_idx']);
    $bom = get_table_meta('bom','bom_idx',$oop['bom_idx']);

    // 외부 라벨 추출
    if(strlen($arr['itm_barcode'])>40) {
        $arr['itm_barcodes'] = explode("_",$arr['itm_barcode']);
        // print_r2($arr['itm_barcodes']);
        $arr['itm_com_barcode'] = $arr['itm_barcodes'][3];
    }

    // 히스토리
    $arr['itm_history'] = $arr['itm_status'].'|'.G5_TIME_YMDHIS;

    // 공통요소
    $sql_common = " com_idx = '".$g5['setting']['set_com_idx']."'
                    , bom_idx = '".$oop['bom_idx']."'
                    , orp_idx = '".$oop['orp_idx']."'
                    , bom_part_no = '".$arr['bom_part_no']."'
                    , itm_name = '".addslashes($bom['bom_name'])."'
                    , itm_barcode = '".$arr['itm_barcode']."'
                    , itm_com_barcode = '".$arr['itm_com_barcode']."'
                    , itm_lot = '".$arr['itm_lot']."'
                    , trm_idx_location = '".$arr['trm_idx_location']."'
                    , itm_shift = '".$arr['itm_shift']."'
                    , itm_history = '".$arr['itm_history']."'
                    , itm_status = '".$arr['itm_status']."'
    ";

    // 중복체크
    $sql_dta = "   SELECT itm_idx FROM {$table_name}
                    WHERE itm_barcode = '".$arr['itm_barcode']."'
    ";
    //echo $sql_dta.'<br>';
    $itm = sql_fetch($sql_dta,1);
    // 정보 업데이트
    if($itm['itm_idx']) {
        $sql = "UPDATE {$table_name} SET 
                    {$sql_common}
                    , itm_update_dt = '".G5_TIME_YMDHIS."'
                WHERE itm_idx = '".$itm['itm_idx']."'
        ";
        sql_query($sql,1);
        $result_arr['code'] = 200;
        $result_arr['message'] = "Updated OK!";

    }
    // 정보 입력
    else{
        $sql = "INSERT INTO {$table_name} SET 
                    {$sql_common}
                    , itm_reg_dt = '".G5_TIME_YMDHIS."'
                    , itm_update_dt = '".G5_TIME_YMDHIS."'
        ";
        sql_query($sql,1);
        $itm['itm_idx'] = sql_insert_id();
        $result_arr['code'] = 200;
        $result_arr['message'] = "Inserted OK!";        
    }
    // echo $sql.'<br>';
    $result_arr['itm_idx'] = $itm['itm_idx'];   // 고유번호
    $result_arr['itm_status'] = $arr['itm_status'];   // 상태값


    // 자재 리스트 (재고포함)
    $sql = "SELECT bom.bom_idx, com_idx_customer, bom.bom_name, bom_part_no, bom_price, bom_status, bom_min_cnt
                , bit1.bit_idx, bit1.bom_idx_child, bit1.bit_reply, bit1.bit_count
                , COUNT(bit2.bit_idx) AS group_count
            FROM {$g5['bom_item_table']} AS bit1
                JOIN {$g5['bom_item_table']} AS bit2
                LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = bit2.bom_idx_child
            WHERE bit1.bom_idx = '".$oop['bom_idx']."' AND bit2.bom_idx = '".$oop['bom_idx']."'
                AND bit1.bit_num = bit2.bit_num
                AND bit2.bit_reply LIKE CONCAT(bit1.bit_reply,'%')
            GROUP BY bit1.bit_num, bit1.bit_reply
            ORDER BY bit1.bit_num DESC, bit1.bit_reply
    ";
    // print_r2($sql);
    $result = sql_query($sql,1);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        // print_r2($row);
        $ar['bom_name'] = $row['bom_name'];
        $ar['bom_part_no'] = $row['bom_part_no'];
        $ar['bom_min_cnt'] = $row['bom_min_cnt'];

        // 현재고
        $sql1 = "   SELECT COUNT(mtr_idx) AS cnt FROM {$g5['material_table']}
                    WHERE bom_part_no = '".$row['bom_part_no']."'
                        AND mtr_status IN ('pending','stock','ready')
        ";
        $row1 = sql_fetch($sql1,1);
        $ar['itm_stock'] = $row1['cnt'];

        $list[] = $ar;
    }
    $result_arr['list'] = $list;
	
}
else {
	$result_arr = array("code"=>599,"message"=>"error");
}

//exit;
//echo json_encode($arr);
echo json_encode( array('meta'=>$result_arr) );
?>