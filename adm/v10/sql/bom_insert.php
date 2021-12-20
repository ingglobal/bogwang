<?php
include_once('./_head.sub.php');
include_once(G5_USER_ADMIN_SQL_PATH.'/lib/functions.php');
$xls = G5_USER_ADMIN_SQL_PATH.'/xls/bom_data_org.xlsx';
//$xls = G5_USER_ADMIN_SQL_PATH.'/xls/bom_product_exlabel.xlsx';
//echo $xls;exit;
?>
<div>
    <a id="btn_start" href="<?=G5_USER_ADMIN_SQL_URL?>/bom_insert.php?start=1">시작</a>
</div>
<?php
$demo = 0;  // 데모모드 = 1

$allData = array();
if($start){
    // ref: https://github.com/PHPOffice/PHPExcel
    require_once G5_LIB_PATH."/PHPExcel-1.8/Classes/PHPExcel.php"; // PHPExcel.php을 불러옴.
    $objPHPExcel = new PHPExcel();
    require_once G5_LIB_PATH."/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php"; // IOFactory.php을 불러옴.
    $filename = $xls; //$_FILES['file_excel']['tmp_name'];
    PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);

    // 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
    $filename = iconv("UTF-8", "EUC-KR", $filename);
    try {
        // 업로드한 PHP 파일을 읽어온다.
        $objPHPExcel = PHPExcel_IOFactory::load($filename);
        $sheetsCount = $objPHPExcel -> getSheetCount();

        // 시트Sheet별로 읽기
        //$allData = array();
        for($i = 0; $i < $sheetsCount; $i++) {

            $objPHPExcel -> setActiveSheetIndex($i);
            $sheet = $objPHPExcel -> getActiveSheet();
            $highestRow = $sheet -> getHighestRow();          // 마지막 행
            $highestColumn = $sheet -> getHighestColumn();    // 마지막 컬럼
            // 한줄씩 읽기
            for($row = 1; $row <= $highestRow; $row++) {
                // $rowData가 한줄의 데이터를 셀별로 배열처리 된다.
                $rowData = $sheet -> rangeToArray("A" . $row . ":" . $highestColumn . $row, NULL, TRUE, FALSE);
                // $rowData에 들어가는 값은 계속 초기화 되기때문에 값을 담을 새로운 배열을 선안하고 담는다.
                $allData[$i][$row] = $rowData[0];
            }
        }
    } catch(exception $e) {
        echo $e;
    }
    //print_r2($allData);
?>
<div class="" style="padding:10px;">
	<span>
		작업 시작~~ <font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전 중간에 중지하지 마세요.
	</span><br><br>
	<span id="cont"></span>
</div>
<?php
}
else{
    echo "[시작]버튼을 누르면 시작됩니다.";
}
include_once('./_tail.sub.php');



?>
<script>
	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($i) ?>건 완료<br><br><font color=crimson><b>[끝]</b></font>";
</script>