<?php
$sub_menu = "945110";
include_once('./_common.php');

if( auth_check($auth[$sub_menu],"w",1) ) {
    alert('메뉴 접근 권한이 없습니다.');
}

$demo = 0;  // 데모모드 = 1
//$xls = G5_USER_ADMIN_SQL_PATH.'/xls/material_input.xlsx';

// ref: https://github.com/PHPOffice/PHPExcel
require_once G5_LIB_PATH."/PHPExcel-1.8/Classes/PHPExcel.php"; // PHPExcel.php을 불러옴.
$objPHPExcel = new PHPExcel();
require_once G5_LIB_PATH."/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php"; // IOFactory.php을 불러옴.
$filename = $_FILES['file_excel']['tmp_name'];//$xls
PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);

// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
$filename = iconv("UTF-8", "EUC-KR", $filename);
//echo $filename;exit;
$up_date = G5_TIME_YMD;
$conArr = array();
try {
    // 업로드한 PHP 파일을 읽어온다.
	$objPHPExcel = PHPExcel_IOFactory::load($filename);
	$sheetsCount = $objPHPExcel -> getSheetCount();
	// 시트Sheet별로 읽기
	for($i = 0; $i < $sheetsCount; $i++) { //시트갯수만큼 루프
        $objPHPExcel -> setActiveSheetIndex($i);
        $sheet = $objPHPExcel -> getActiveSheet();
        $highestRow = $sheet -> getHighestRow();          // 마지막 행
        $highestColumn = $sheet -> getHighestColumn();    // 마지막 컬럼
        // 한줄씩 읽기
        for($row = 1; $row <= $highestRow; $row++) { //첫줄부터 루프
            if($row < 5) continue;
            // $rowData가 한줄의 데이터를 셀별로 배열처리 된다.
            $rowData = $sheet -> rangeToArray("A" . $row . ":" . $highestColumn . $row, NULL, TRUE, FALSE);
            //날짜가 있는 셀에서 날짜만 추출한다.
            if($i == 0 && $row == 5) $up_date = PHPExcel_Style_NumberFormat :: toFormattedString ($rowData[0][1], PHPExcel_Style_NumberFormat :: FORMAT_DATE_YYYYMMDD2);
            //P/NO가 없는 줄은 루프는 건터뛴다.
            if(!$rowData[0][2] || $rowData[0][2] == '' || $rowData[0][2] == 'P/NO') continue; 
            
            // $rowData에 들어가는 값은 계속 초기화 되기때문에 값을 담을 새로운 배열을 선안하고 담는다.
            $conArr[$rowData[0][2]] = array(
                'com_name' => $rowData[0][1]
                ,'bom_name' => $rowData[0][3]
                ,'bom_price' => ceil($rowData[0][4])
                ,'times' => array(
                    1 => ($rowData[0][5]) ? $rowData[0][5] : 0
                    ,2 => ($rowData[0][6]) ? $rowData[0][6] : 0
                    ,3 => ($rowData[0][7]) ? $rowData[0][7] : 0
                    ,4 => ($rowData[0][8]) ? $rowData[0][8] : 0
                    ,5 => ($rowData[0][9]) ? $rowData[0][9] : 0
                    ,6 => ($rowData[0][10]) ? $rowData[0][10] : 0
                    ,7 => ($rowData[0][11]) ? $rowData[0][11] : 0
                    ,8 => ($rowData[0][12]) ? $rowData[0][12] : 0
                    ,9 => ($rowData[0][13]) ? $rowData[0][13] : 0
                    ,10 => ($rowData[0][14]) ? $rowData[0][14] : 0
                )
            );
        }
	}
} catch(exception $e) {
	echo $e;
}
// echo $up_date."<br>";
// print_r2($conArr);
// exit;
$g5['title'] = '엑셀 업로드';
include_once('./_head.php');
echo $g5['container_sub_title'];
?>
<div class="" style="padding:10px;">
	<span>
		작업 시작~~ <font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전 중간에 중지하지 마세요.
	</span><br><br>
	<span id="cont"></span>
</div>
<?php
include_once ('./_tail.php');
?>

<?php
$countgap = 10; // 몇건씩 보낼지 설정
$sleepsec = 20000;  // 백만분의 몇초간 쉴지 설정
$maxscreen = 50; // 몇건씩 화면에 보여줄건지?

flush();
ob_flush();

// print_r3($conArr);
$i=0;
//$up_date
foreach($conArr as $k => $v){


    echo "<script> document.all.cont.innerHTML += '[".$i."] (".$k.") - ".$v['bom_name']." - (".$v['bom_price'].") ---->> 완료<br>' </script>\n";

    flush();
    ob_flush();
    ob_end_flush();
    usleep($sleepsec);

    //보기 쉽게 묶음 단위로 구분 (단락으로 구분해서 보임)
    if($i % $countgap == 0)
        echo "<script> document.all.cont.innerHTML += '<br>'; </script>\n";
    
    //화면 정리! 부하를 줄임 (화면 싹 지움)
    if($i % $maxscreen == 0)
        echo "<script> document.all.cont.innerHTML += ''; </script>\n";
    
    $i++;
}





// 관리자 디버깅 메시지
if( is_array($g5['debug_msg']) ) {
    for($i=0;$i<sizeof($g5['debug_msg']);$i++) {
        echo '<div class="debug_msg">'.$g5['debug_msg'][$i].'</div>';
    }
?>
    <script>
    $(function(){
        $("#container").prepend( $('.debug_msg') );
    });
    </script>
<?php
}
?>


<script>
	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($i) ?>건 완료<br><br><font color=crimson><b>[끝]</b></font>";
</script>