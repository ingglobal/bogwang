<?php
include_once('./_common.php');
// 인스타 로그인후 인스타쪽 피드 배열을 받아서 올스타에 업데이트하는 폼을 임시로 구현한 페이지입니다.
// 실제로는 member_log.php가 크롤링 서버에서 정보를 받아서 저장합니다.
// 파일명이 실제로는 member_feed_log.php가 더 적합할 수도..

// $com_idx_array = array(9999,67,66,65,64,10000);
$com_idx_array = array(1,1);
$group_array = array('err','err','err','err','err','pre');
// $dta_code_array = array('M1100','M1009','M179F');
$dta_code_array = array('M1031','M1031');
// $mms_idx_array = array(7,8,9,10);
$mms_idx_array = array(4,4);
?>
<style>
	form {padding:10px 100px 100px;}
	section {border:solid 1px #ddd;font-size:0.8em;}
	section ul {margin-left:-11px;}
	table {border:solid 1px #666;margin-top:10px;}
	table caption {text-align:left;background:#222;color:white;padding:7px;}
	tr td:first-child {width:100px;font-size:0.9em;}
	tr td input {border:solid 1px #ddd;padding:4px;}
    button {background:#37a7ff;padding:10px 113px;font-size:1.5em;border:none;border-radius:4px;cursor:pointer;}
</style>

<form id="form01" action="./form2.php">
<h1>생산시작 API</h1>
<section>
	<ul>
		<li>바코드 출력과 동시에 통신하는 API입니다.</li>
		<li>g5_1_item 테이블에 새로운 record가 생성됩니다.(상태값=ing) / 관련자재(g5_1_meterial)들의 상태값들도 함께 변경됩니다.(상태값=ing) </li>
		<li>재발행인 경우도 같은 값으로 던져주시면 됩니다.</li>
		<li>반환(return)값: itm_idx, itm_status</li>
	</ul>
</section>

<table>
	<tr><td>공통 토큰</td><td><input type="text" name="token" value="1099de5drf09"></td></tr>
</table>

<table>
	<caption>생산시작</caption>
	<tr><td>업체 idx1</td><td><input type="text" name="com_idx[]" value="11"></td></tr>
	<tr><td>IMP idx1</td><td><input type="text" name="imp_idx[]" value="<?=rand(1,16)?>"></td></tr>
	<tr><td>MMS idx1</td><td><input type="text" name="mms_idx[]" value="<?=$mms_idx_array[rand(0,sizeof($mms_idx_array)-1)]?>"></td></tr>
	<tr><td>파트넘버</td><td><input type="text" name="bom_part_no[]" value="<?=rand(1,200)?>"></td></tr>
	<tr><td>교대번호1</td><td><input type="text" name="dta_shf_no[]" value="<?=rand(1,2)?>"></td></tr>
	<tr><td>총교대수1</td><td><input type="text" name="dta_shf_max[]" value="<?=rand(2,3)?>"></td></tr>
	<tr><td>데이터그룹1</td><td><input type="text" name="dta_group[]" value="<?=$group_array[rand(0,sizeof($group_array)-1)]?>"></td></tr>
	<tr><td>코드값1</td><td><input type="text" name="dta_code[]" value="<?=$dta_code_array[rand(0,sizeof($dta_code_array)-1)]?>"></td></tr>
	<tr><td>날짜1</td><td><input type="text" name="dta_date[]" value="<?=date("y.m.d",time())?>"></td></tr>
	<tr><td>시간1</td><td><input type="text" name="dta_time[]" value="<?=date("H:i:s",time()-rand(0,86400))?>"></td></tr>
	<tr><td>메시지1</td><td><input type="text" name="dta_message[]" value="에러코드입니다."></td></tr>
</table>

<hr>
<button type="submit">확인</button>
</form>


