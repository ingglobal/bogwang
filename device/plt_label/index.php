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
else if($getData[0]['plt_barcode']) {

    $arr = $getData[0];

    $arr['plt_status'] = 'ok';
    $arr['plt_dt'] = strtotime(preg_replace('/\./','-',$arr['plt_date'])." ".$arr['plt_time']);
    $arr['plt_date1'] = date("Y-m-d",$arr['plt_dt']);   // 2 or 4 digit format(20 or 2020) no problem.
    $arr['st_time'] = strtotime($arr['plt_date1']." 00:00:00"); // 해당 날짜의 시작
    $arr['en_time'] = strtotime($arr['plt_date1']." 23:59:59"); // 해당 날짜의 끝
    $arr['plt_dt2'] = strtotime(preg_replace('/\./','-',$arr['plt_date2'])." 00:00:00");    // statistics date
    $arr['plt_date_stat'] = date("Y-m-d",$arr['plt_dt2']);   // 2 or 4 digit format(20 or 2020) no problem.
    $table_name = 'g5_1_pallet';

    // 바코드 분리
    $arr['plt_barcodes'] = explode("_",$arr['plt_barcode']);
    // print_r2($arr['plt_barcodes']);
    $arr['plt_barcode_count'] = $arr['plt_barcodes'][0].'_'.$arr['plt_barcodes'][1].'_';
    $arr['plt_barcode_part_no'] = $arr['plt_barcodes'][0].'_'.$arr['plt_barcodes'][1].'_'.$arr['plt_barcodes'][2].'_';
    $arr['plt_part_no'] = $arr['plt_barcodes'][2];
    $arr['plt_count'] = $arr['plt_barcodes'][3];

    $sql = " SELECT * FROM {$g5['bom_table']} WHERE bom_part_no = '".$arr['plt_part_no']."' ";
    $bom = sql_fetch($sql);

    // 공통요소
    $sql_common = " com_idx = '".$g5['setting']['set_com_idx']."'
                    , bom_idx = '".$bom['bom_idx']."'
                    , bom_part_no = '".$arr['plt_part_no']."'
                    , plt_barcode = '".$arr['plt_barcode']."'
                    , plt_count = '".$arr['plt_count']."'
                    , plt_status = '".$arr['plt_status']."'
                    , plt_update_dt = '".G5_TIME_YMDHIS."'
    ";

    // 중복체크
    $sql = "SELECT plt_idx FROM {$table_name}
            WHERE plt_barcode LIKE '".$arr['plt_barcode_count']."%'
            ORDER BY plt_idx LIMIT 1
    ";
    //echo $sql_dta.'<br>';
    $plt = sql_fetch($sql,1);
    // 정보 업데이트(same pallet)
    if($plt['plt_idx']) {

        // 중복체크(same part_no), if part_no also is same, it is the same pallet. All you need is the update the db.
        $sql = "SELECT plt_idx FROM {$table_name}
                WHERE plt_barcode LIKE '".$arr['plt_barcode_part_no']."%'
        ";
        //echo $sql_dta.'<br>';
        $plt2 = sql_fetch($sql,1);
        // 정보 업데이트
        if($plt2['plt_idx']) {
            $sql = "UPDATE {$table_name} SET 
                        {$sql_common}
                        , plt_history = CONCAT(plt_history,'\n".$arr['plt_status']."|".G5_TIME_YMDHIS."')
                    WHERE plt_idx = '".$plt2['plt_idx']."'
            ";
            sql_query($sql,1);
            $result_arr['code'] = 200;
            $result_arr['message'] = "Updated OK!";

        }
        // 정보 입력
        else {
            // 부모 idx 
            $arr['plt_idx_parent'] = $plt['plt_idx'];

            $sql = "INSERT INTO {$table_name} SET 
                    {$sql_common}
                    , plt_idx_parent = '".$arr['plt_idx_parent']."'
                    , plt_history = ".$arr['plt_status']."|".G5_TIME_YMDHIS."'
                    , plt_reg_dt = '".G5_TIME_YMDHIS."'
            ";
            sql_query($sql,1);
            $plt_idx = sql_insert_id();
            $result_arr['code'] = 200;
            $result_arr['message'] = "Inserted OK!";        
        }
        // echo $sql.'<br>';
        $result_arr['plt_idx'] = $plt_idx;   // 고유번호
        $result_arr['plt_status'] = $arr['plt_status'];   // 상태값

    }
    // 정보 입력 (new pallet)
    else {
        
        $sql = "INSERT INTO {$table_name} SET 
                    {$sql_common}
                    , plt_history = '".$arr['plt_status']."|".G5_TIME_YMDHIS."'
                    , plt_reg_dt = '".G5_TIME_YMDHIS."'
        ";
        sql_query($sql,1);
        $plt_idx = sql_insert_id();

        // 부모 idx update
        sql_query(" UPDATE {$table_name} SET plt_idx_parent = '".$plt_idx."' WHERE plt_idx = '".$plt_idx."' ");

        $result_arr['code'] = 200;
        $result_arr['message'] = "Inserted OK!";        
    }
    // echo $sql.'<br>';
    $result_arr['plt_idx'] = $plt_idx;   // 고유번호
    $result_arr['plt_status'] = $arr['plt_status'];   // 상태값



    // 제품(item) 처리, 앞에서부터 차례대로 처리
    $sql = "SELECT * FROM {$g5['item_table']}
            WHERE bom_part_no = '".$arr['plt_part_no']."' AND itm_status = 'finish' ORDER BY itm_idx LIMIT ".$arr['plt_count'];
    $rs = sql_query($sql,1);
    // echo $sql.'<br>';
    for ($i=0; $row=sql_fetch_array($rs); $i++) {

        // 상태값 = 출고완료
        $row['itm_status'] = 'delivery';

        $sql1 = "UPDATE {$g5['item_table']} SET
                    itm_history = CONCAT(itm_history,'\n".$row['itm_status']."|".G5_TIME_YMDHIS."')
                    , itm_status = '".$row['itm_status']."'
                    , itm_update_dt = '".G5_TIME_YMDHIS."'
                WHERE itm_idx = '".$row['itm_idx']."'
        ";
        // echo $sql1.'<br>';
        sql_query($sql1,1);

        // 연결된 자재의 모든 상태값을 변경
        $sql1 = "UPDATE {$g5['material_table']} SET
                    mtr_status = '".$row['itm_status']."'
                    , mtr_history = CONCAT(mtr_history,'\n".$row['itm_status']."|".G5_TIME_YMDHIS."')
                    , mtr_update_dt = '".G5_TIME_YMDHIS."'
                WHERE itm_idx = '".$row['itm_idx']."'
        ";
        // echo $sql1.'<br>';
        sql_query($sql1,1);
        
    }

}
else {
	$result_arr = array("code"=>599,"message"=>"error");
}

//exit;
//echo json_encode($arr);
echo json_encode( array('meta'=>$result_arr) );
?>