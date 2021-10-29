<?php
$sub_menu = "945110";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'material';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form/","",$g5['file_name']); // _form을 제외한 파일명
$qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들

if ($w == '') {
    $sound_only = '<strong class="sound_only">필수</strong>';
    $w_display_none = ';display:none';  // 쓰기에서 숨김

    ${$pre}[$pre.'_start_date'] = G5_TIME_YMD;
    ${$pre}[$pre.'_status'] = 'ok';
}
else if ($w == 'u') {
    $u_display_none = ';display:none;';  // 수정에서 숨김

	${$pre} = get_table_meta($table_name, $pre.'_idx', ${$pre."_idx"});
    if (!${$pre}[$pre.'_idx'])
		alert('존재하지 않는 자료입니다.');
    // print_r3(${$pre});

}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');


// 라디오&체크박스 선택상태 자동 설정 (필드명 배열 선언!)
$check_array=array('mb_field');
for ($i=0;$i<sizeof($check_array);$i++) {
	${$check_array[$i].'_'.${$pre}[$check_array[$i]]} = ' checked';
}

$html_title = ($w=='')?'추가':'수정'; 
$g5['title'] = '자재정보 '.$html_title;
include_once ('./_head.php');
?>
<style>
    .bop_price {font-size:0.8em;color:#a9a9a9;margin-left:10px;}
    .btn_bop_delete {color:#0c55a0;cursor:pointer;margin-left:20px;}
    a.btn_price_add {color:#3a88d8 !important;cursor:pointer;}
</style>

<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="<?=$pre?>_idx" value="<?php echo ${$pre."_idx"} ?>">
<input type="hidden" name="sca" value="<?php echo $sca ?>">

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>가격 변경 이력을 관리합니다. (가격 변동 날짜 및 가격을 지속적으로 기록하고 관리합니다.)</p>
    <p>가격이 변경될 미래 날짜를 지정해 두면 해당 날짜부터 변경될 가격이 적용됩니다.</p>
</div>

<div class="tbl_frm01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4" style="width:15%;">
		<col style="width:35%;">
		<col class="grid_4" style="width:15%;">
		<col style="width:35%;">
    </colgroup>
	<tbody>
    <tr>
        <?php
        $ar['id'] = 'mtr_name';
        $ar['name'] = '품명';
        $ar['type'] = 'input';
        $ar['width'] = '100%';
        $ar['value'] = ${$pre}[$ar['id']];
        $ar['required'] = 'required';
        echo create_td_input($ar);
        unset($ar);
        ?>
        <?php
        $ar['id'] = 'mtr_price';
        $ar['name'] = '단가';
        $ar['type'] = 'input';
        $ar['width'] = '80px';
        $ar['value'] = ${$pre}[$ar['id']];
        $ar['unit'] = '원';
        $ar['value_type'] = 'number';
        $ar['form_script'] = 'onClick="javascript:chk_Number(this)"';
        echo create_td_input($ar);
        unset($ar);
        ?>
    </tr>
    <tr>
        <?php
        $ar['id'] = 'mtr_barcode';
        $ar['name'] = '바코드';
        $ar['type'] = 'input';
        $ar['value'] = ${$pre}[$ar['id']];
        $ar['width'] = '150px';
        echo create_td_input($ar);
        unset($ar);
        ?>
        <?php
        $ar['id'] = 'mtr_lot';
        $ar['name'] = 'LOT';
        $ar['type'] = 'input';
        $ar['width'] = '120px';
        $ar['value'] = ${$pre}[$ar['id']];
        echo create_td_input($ar);
        unset($ar);
        ?>
    </tr>
	<tr>
		<th scope="row">품질</th>
		<td>
        	<input type="radio" name="mtr_defect" value="0" id="mtr_defect_0" <?=(${$pre}['mtr_defect'])?'':'checked'?>>
            <label for="mtr_defect_0">양품</label>
            <input type="radio" name="mtr_defect" value="1" id="mtr_defect_1" <?=(${$pre}['mtr_defect'])?'checked':''?>>
            <label for="mtr_defect_1">불량품</label>

            <select name="mtr_defect_type" id="mtr_defect_type" style="margin-left:20px;display:<?=(!${$pre}['mtr_defect'])?'none':''?>;">
                <option value="">선택하세요</option>
                <?=$g5['set_mtr_defect_type_options']?>
            </select>
            <script>$('select[name="mtr_defect_type"]').val('<?=${$pre}['mtr_defect_type']?>');</script>
		</td>
		<th scope="row">자재위치</th>
		<td>
            <select name="trm_idx_location" id="trm_idx_location">
                <option value="">선택하세요</option>
                <?=$location_form_options?>
                <script>$('select[name="trm_idx_location"]').val('<?=${$pre}['trm_idx_location']?>');</script>
            </select>
		</td>
    </tr>
    <?php
    $ar['id'] = 'mtr_memo';
    $ar['name'] = '메모';
    $ar['type'] = 'textarea';
    $ar['value'] = ${$pre}['mtr_memo'];
    $ar['colspan'] = 3;
    echo create_tr_input($ar);
    unset($ar);
    ?>
    <tr>
        <th scope="row">히스토리</th>
        <td colspan="3">
            <?=${$pre}[$pre.'_history']?>
        </td>
    </tr>
    <tr>
        <th scope="row">상태</th>
        <td colspan="3">
            <select name="<?=$pre?>_status" id="<?=$pre?>_status"
                <?php if (auth_check($auth[$sub_menu],"d",1)) { ?>onFocus='this.initialSelect=this.selectedIndex;' onChange='this.selectedIndex=this.initialSelect;'<?php } ?>>
                <?=$g5['set_mtr_status_options']?>
            </select>
            <script>$('select[name="<?=$pre?>_status"]').val('<?=${$pre}[$pre.'_status']?>');</script>
        </td>
    </tr>
	</tbody>
	</table>
</div>

<div class="btn_fixed_top">
    <a href="./<?=$fname?>_list.php?<?php echo $qstr ?>" class="btn btn_02">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
</div>
</form>

<script>
$(function() {
    $("input[name$=_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99" });

    // 불량타입 숨김,보임
	$("input[name=mtr_defect]").click(function(e) {
        if( $(this).val() == 1 ) {
            $('#mtr_defect_type').show();
        }
        else
           $('#mtr_defect_type').hide();
	});

    // 가격 입력 쉼표 처리
	$(document).on( 'keyup','input[name$=_price], #bom_moq, #bom_lead_time',function(e) {
//        console.log( $(this).val() )
//		console.log( $(this).val().replace(/,/g,'') );
        if(!isNaN($(this).val().replace(/,/g,'')))
            $(this).val( thousand_comma( $(this).val().replace(/,/g,'') ) );
	});

});

// 숫자만 입력
function chk_Number(object){
    $(object).keyup(function(){
        $(this).val($(this).val().replace(/[^0-9|-]/g,""));
    });
}

function form01_submit(f) {

    return true;
}

</script>

<?php
include_once ('./_tail.php');
?>
