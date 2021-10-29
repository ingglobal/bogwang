<?php
// 호출 페이지들
// /adm/v10/bom_form.php: 제품(BOM)수정: 거래처찾기
include_once('./_common.php');

if($member['mb_level']<4)
	alert_close('접근할 수 없는 메뉴입니다.');

$where = array();




$g5['title'] = '생산계획 검색'.$total_count_display;
include_once('./_head.sub.php');

$qstr1 = 'frm='.$frm.'&d='.$d.'&sch_field='.$sch_field.'&sch_word='.urlencode($sch_word).'&file_name='.$file_name;

add_stylesheet('<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/ui-darkness/jquery-ui.css">', 1);
add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_JS_URL.'/jquery-ui-1.12.1/jquery-ui.structure.min.css">', 1);
add_javascript('<script src="'.G5_USER_ADMIN_JS_URL.'/jquery-ui-1.12.1/jquery-ui.min.js"></script>',1);
add_javascript('<script src="'.G5_USER_ADMIN_JS_URL.'/bwg_datepicker-ko.js"></script>',1);
add_javascript('<script src="'.G5_USER_ADMIN_JS_URL.'/bwg_datepicker.js"></script>',1);
?>
<style>

</style>



<script>
schFieldDate();

$('#sch_field').on('change',function(){
    schFieldDate();
});


function schFieldDate(){
    var slt_val = $('#sch_field').val();
    if(slt_val == 'orp_start_date'){
        $('#sch_word').addClass('orp_start_date').attr('readonly',true).val('');
        $(".orp_start_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99" });
    }else{
        if($(".orp_start_date").length){
            $(".orp_start_date").datepicker("destroy");
            $('#sch_word').removeClass('orp_start_date').removeAttr('readonly',false).val('');
        }
    }
}

$('.btn_select').click(function(e){
    e.preventDefault();
    <?php
    // 이전 파일의 폼에 따라 전달 내용 변경
    if($file_name=='order_out_practice_form') {
    ?>
        // 폼이 존재하면
        if( $("form[name=<?php echo $frm;?>]", opener.document).length > 0 ) {
            $("input[name=orp_idx]", opener.document).val( $(this).closest('td').attr('orp_idx') );
            $("input[name=line_name]", opener.document).val( $(this).closest('td').attr('line_name') );
        }
        else {
            alert('값을 전달할 폼이 존재하지 않습니다.');
        }
    <?php
    }

    // ajax 호출이 있을 때는 너무 빨리 창을 닫으면 안 됨
    if($file_name!='company_list') {
    ?>
    window.close();
    <?php
    }
    ?>
});
</script>
<?php
include_once('./_tail.sub.php');