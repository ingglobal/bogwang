<?php
$sub_menu = "930100";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'order_out_practice';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form/","",$g5['file_name']); // _form을 제외한 파일명
//$qstr .= '&ser_mms_idx='.$ser_mms_idx; // 추가로 확장해서 넘겨야 할 변수들

if($w == ''){
    $sound_only = '<strong class="sound_only">필수</strong>';
    $w_display_none = ';display:none;'; //쓰기에서 숨김
    ${$pre}['com_idx'] = $_SESSION['ss_com_idx'];

}
else if($w == 'u' || $w == 'c'){
    $u_display_none = ';display:none;'; //수정에서 숨김

    ${$pre} = get_table_meta($table_name, $pre.'_idx', ${$pre."_idx"});
    //print_r2(${$pre."_idx"});
    if(!${$pre}[$pre.'_idx'])
    alert('존재하지 않는 자료입니다.');
    
}
else
    alert('제대로 된 값이 값이 넘어오지 않았습니다.');

${$pre}['com_idx'] = $_SESSION['ss_com_idx'];


/*
array(
    'type' => 'text/password/url/radio/checkbox/textarea/select/hidden/none'
    'ttl' => '타이틀명',
    'required' => true or false,
    'readonly' => true or false,
    'width' => 60,
    'unit' => '단위명(개,개월,시,분,...)',
    'desc' => '설명',
    'colspan' => 0,
    'value' => ,
    'radio' => ex) array(1=>"예",0=>"아이오"),
    'select' => ex) array(1=>"예",0=>"아이오"),
    'checkbox' => ex) '예,맞습니다.',
    'textarea' => '속성 관련 배열',
    'id'=>'',
    'class'=>'',
    'tr_s' => true or false,
    'tr_e' => true or false,
    'th' => true or false,
    'td' => true or false,
    'td_s' => true or false,
    'td_e' => true or false
)
*/
if($w == ''){
    $f1 = array(
        'oop_idx' => array('type'=>'hidden','value'=>${$pre."_idx"})
        ,'com_idx' => array('type'=>'hidden','value'=>${$pre}['com_idx'])
        ,'ord_idx' => array('type'=>'text','ttl'=>'수주선택','value'=>$$pre['ord_idx'],'required'=>true,'readonly'=>true,'tr_s'=>true,'th'=>true,'td'=>true,'width'=>100)
        ,'ori_idx' => array('type'=>'text','ttl'=>'제품선택','value'=>$$pre['ori_idx'],'required'=>true,'readonly'=>true,'tr_e'=>true,'th'=>true,'td'=>true,'width'=>100)
        ,'oro_idx' => array('type'=>'text','ttl'=>'출하선택','value'=>$$pre['oro_idx'],'required'=>true,'readonly'=>true,'tr_s'=>true,'th'=>true,'td'=>true,'width'=>100)
        ,'bom_idx' => array('type'=>'none','value'=>$$pre['bom_idx'])
        ,'orp_idx' => array('type'=>'text','ttl'=>'생산계획ID','value'=>$$pre['orp_idx'],'required'=>true,'readonly'=>true,'tr_e'=>true,'th'=>true,'td'=>true)
        ,'oop_count' => array('type'=>'text','ttl'=>'지시수량','value'=>$$pre['oop_count'],'required'=>true,'readonly'=>true,'colspan'=>3,'tr_s'=>true,'tr_e'=>true,'th'=>true,'td'=>true,'width'=>80,'class'=>'align_right','id'=>'oop_count')
        ,'oop_1' => array('type'=>'text','ttl'=>'시간별수량','value'=>$$pre['oop_1'],'colspan'=>3,'tr_s'=>true,'tr_e'=>true,'th'=>true,'td'=>true)
        ,'oop_2' => array('type'=>'none','value'=>$$pre['oop_2'])
        ,'oop_3' => array('type'=>'none','value'=>$$pre['oop_3'])
        ,'oop_4' => array('type'=>'none','value'=>$$pre['oop_4'])
        ,'oop_5' => array('type'=>'none','value'=>$$pre['oop_5'])
        ,'oop_6' => array('type'=>'none','value'=>$$pre['oop_6'])
        ,'oop_7' => array('type'=>'none','value'=>$$pre['oop_7'])
        ,'oop_8' => array('type'=>'none','value'=>$$pre['oop_8'])
        ,'oop_memo' => array('type'=>'textarea','ttl'=>'메모','value'=>$$pre['oop_memo'],'colspan'=>3,'tr_s'=>true,'tr_e'=>true,'th'=>true,'td'=>true)
        ,'oop_history' => array('type'=>'text','ttl'=>'로그내용','value'=>$$pre['oop_history'],'colspan'=>3,'tr_s'=>true,'tr_e'=>true,'th'=>true,'td'=>true)
        ,'oop_status' => array('type'=>'select','ttl'=>'상태','value'=>$$pre['oop_status'],'width'=>'auto','select'=>$g5['set_oop_status_value'],'colspan'=>3,'tr_s'=>true,'tr_e'=>true,'th'=>true,'td'=>true)
    );
}
else if($w == 'u' || $w == 'c'){
    $f1 = array(
        'oop_idx' => array('type'=>'hidden','value'=>${$pre."_idx"})
        ,'com_idx' => array('type'=>'hidden','value'=>${$pre}['com_idx'])
        ,'ord_idx' => array('type'=>'hidden','value'=>$$pre['ord_idx'])
        ,'ori_idx' => array('type'=>'hidden','value'=>$$pre['ori_idx'])
        ,'oro_idx' => array('type'=>'hidden','value'=>$$pre['oro_idx'])
        ,'orp_idx' => array('type'=>'text','value'=>$$pre['orp_idx'],'ttl'=>'생산계획ID','required'=>true,'readonly'=>true,'tr_s'=>true,'th'=>true,'td'=>true)
        ,'bom_idx' => array('type'=>'hidden','value'=>$$pre['bom_idx'])
        ,'oop_count' => array('type'=>'text','ttl'=>'지시수량','value'=>$$pre['oop_count'],'required'=>true,'readonly'=>true,'tr_e'=>true,'th'=>true,'td'=>true,'width'=>80,'class'=>'align_right','id'=>'oop_count')
        ,'oop_1' => array('type'=>'text','ttl'=>'시간별수량','value'=>$$pre['oop_1'],'colspan'=>3,'tr_s'=>true,'tr_e'=>true,'th'=>true,'td'=>true)
        ,'oop_2' => array('type'=>'none','value'=>$$pre['oop_2'])
        ,'oop_3' => array('type'=>'none','value'=>$$pre['oop_3'])
        ,'oop_4' => array('type'=>'none','value'=>$$pre['oop_4'])
        ,'oop_5' => array('type'=>'none','value'=>$$pre['oop_5'])
        ,'oop_6' => array('type'=>'none','value'=>$$pre['oop_6'])
        ,'oop_7' => array('type'=>'none','value'=>$$pre['oop_7'])
        ,'oop_8' => array('type'=>'none','value'=>$$pre['oop_8'])
        ,'oop_memo' => array('type'=>'textarea','ttl'=>'메모','value'=>$$pre['oop_memo'],'colspan'=>3,'tr_s'=>true,'tr_e'=>true,'th'=>true,'td'=>true)
        ,'oop_history' => array('type'=>'text','ttl'=>'로그내용','value'=>$$pre['oop_history'],'colspan'=>3,'tr_s'=>true,'tr_e'=>true,'th'=>true,'td'=>true)
        ,'oop_status' => array('type'=>'select','ttl'=>'상태','value'=>$$pre['oop_status'],'width'=>'auto','select'=>$g5['set_oop_status_value'],'colspan'=>3,'tr_s'=>true,'tr_e'=>true,'th'=>true,'td'=>true)
    );
}
//print_r3($g5['set_oop_status_value']);

$bom = sql_fetch(" SELECT bom_name FROM {$g5['bom_table']} WHERE bom_idx = '".${$pre}['bom_idx']."' ");

$html_title = ($w=='')?'추가':'수정';
$html_title = ($w=='c')?'복제':$html_title;
$g5['title'] = '출하생산계획 '.$html_title.' - '.$bom['bom_name'];

include_once('./_head.php');
?>
<style>
.align_right{text-align:right;}
#cnt_per_time{}
#cnt_per_time:after{display:block;visibility:hidden;clear:both;content:'';}
#cnt_per_time li{float:left;margin-right:10px;text-align:center;}
</style>
<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post" enctype="multipart/form-data">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="<?=$pre?>_idx" value="<?php echo ${$pre."_idx"} ?>">
<input type="hidden" name="ser_mms_idx" value="<?php echo $ser_mms_idx ?>">
<?php
$hskip = array();
foreach($f1 as $hk=>$hv){
    if($hv['type'] != 'hidden' || $hv['type'] == 'none' || in_array($hk,$hskip)) continue;
    echo input_hidden($hk,$hv);
}

//print_r3($g5);
?>
<div class="local_desc01 local_desc" style="display:no ne;">
    <p>각종 고유번호(설비번호, IMP번호..)들은 내부적으로 다른 데이타베이스 연동을 통해서 정보를 가지고 오게 됩니다.</p>
	<p class="txt_redblink" style="display:no ne;">설비idx=0 인 경우는 전체설비(설비 비선택 추가해라!!!)<br>설비idx 가 있으면 특정설비의 작업구간</p>
</div>
<div class="tbl_frm01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?></caption>
	<colgroup>
		<col class="grid_4" style="width:7%;">
		<col style="width:43%;">
		<col class="grid_4" style="width:7%;">
		<col style="width:43%;">
	</colgroup>
	<tbody>
    <?php
    $fskip = array('oop_history');//제외되는 필드명
    $fcust = array('ord_idx','ori_idx','oro_idx','orp_idx','oop_1');//array('mms_idx','shf_start_time','shf_end_time');//커스터마이징해야 하는 필드명
    
    foreach($f1 as $fk=>$fv){
        if($fv['type'] == 'hidden' || $fv['type'] == 'none' || in_array($fk,$fskip)) continue;
        if($fk == 'shf_start_dt' || $fk == 'shf_end_dt'){
            $fv['value'] = substr($fv['value'],0,10);
        }

        //필드 폼을 커스터마이징 해야 할 경우
        if(in_array($fk,$fcust)){
            $fctag = '';//form customize tag 편집태그
            $colspan = ($fv['colspan']) ? ' colspan="'.$fv['colspan'].'"' : '';
            $fctag .= ($fv['tr_s']) ? '<tr>'.PHP_EOL : '';
            $fctag .= ($fv['th']) ? '<th>'.$fv['ttl'].'</th>'.PHP_EOL : '';
            $fctag .= ($fv['td']) ? '<td'.$colspan.'>'.PHP_EOL : '';
            $fctag .= ($fv['td_e']) ? '<td>'.PHP_EOL : '';

            $id_str = ($fv['id']) ? ' id="'.$fv['id'].'"' : '';
            $class_nm = ($fv['class']) ? $fv['class'] : '';
            $wd_style = ($fv['width']) ? 'width:'.((preg_match('/[0-9]{1,3}%$/',$fv['width']) || preg_match('/auto/',$fv['width'])) ? $fv['width'] : $fv['width'].'px') : 'width:100%;';
            $style_str = ($wd_style) ? ' style="'.$wd_style.'"' : '';
            $required = ($fv['required']) ? ' required' : '';
            $readonly = ($fv['readonly']) ? ' readonly' : '';

            $tag .= ($fv['desc']) ? '<p>'.$fv['desc'].'</p>' : '';
            //######################### 커스터마이징 필드별 소스 추가 : 시작 #################################
            if($fk == 'ord_idx'){
                $fctag .= '<input type="hidden" name="'.$fk.'" value="">'.PHP_EOL;
                $fctag .= '<input type="text" name="ord_date_" value=""'.$required.$readonly.' class="frm_input'.$required.$readonly.'">'.PHP_EOL;
                $fctag .= '<a href="./order_select.php?frm=form01&file_name='.$g5['file_name'].'" class="btn btn_02" id="btn_ord">수주선택</a>'.PHP_EOL;
            }
            if($fk == 'ori_idx'){
                $fctag .= '<input type="hidden" name="bom_idx" value="">'.PHP_EOL;
                $fctag .= '<input type="hidden" name="'.$fk.'" value="">'.PHP_EOL;
                $fctag .= '<input type="text" name="bom_name" value=""'.$required.$readonly.' class="frm_input'.$required.$readonly.'" style="width:300px;">'.PHP_EOL;
                $fctag .= '<a href="./order_item_select.php?frm=form01&file_name='.$g5['file_name'].'&ord_idx=" class="btn btn_02" id="btn_ori">수주상품선택</a>'.PHP_EOL;
            }
            if($fk == 'oro_idx'){
                $fctag .= '<input type="hidden" name="'.$fk.'" value="">'.PHP_EOL;
                $fctag .= '<input type="text" name="oro_date_plan" value=""'.$required.$readonly.' class="frm_input'.$required.$readonly.'">'.PHP_EOL;
                $fctag .= '<a href="./order_out_select.php?frm=form01&file_name='.$g5['file_name'].'&ord_idx=" class="btn btn_02" id="btn_oro">출하선택</a>'.PHP_EOL;
            }
            if($fk == 'orp_idx'){
                //라인명 추출
                $ln_sql = sql_fetch(" SELECT trm_idx_line FROM {$g5['order_practice_table']} WHERE orp_idx = '{$fv['value']}' ");
                $line_idx = $ln_sql['trm_idx_line'];
                $line_name = ($line_idx) ? $fv['value'].'-('.$g5['line_name'][$line_idx].')' : '';

                $fctag .= '<input type="hidden" name="'.$fk.'" value="'.$fv['value'].'"'.$required.$readonly.' class="frm_input'.$required.$readonly.'">'.PHP_EOL;
                $fctag .= '<input type="text" name="line_name" id="line_name" value="'.$line_name.'"'.$required.$readonly.' class="frm_input'.$required.$readonly.'">'.PHP_EOL;
                $fctag .= '<a href="./order_practice_select.php?frm=form01&file_name='.$g5['file_name'].'&w='.$w.'&orp_idx='.$fv['value'].'" class="btn btn_02" id="btn_orp">생산계획ID(라인설비별)찾기</a>'.PHP_EOL;
            }
            if($fk == 'oop_1'){
                $time_arr = array('시간1<br>08:00~10:00','시간2<br>10:10~12:00','시간3<br>13:00~15:00','시간4<br>15:10~17:00','시간5<br>17:10~19:00','시간6<br>19:10~21:00','시간7<br>21:10~23:00','시간8<br>23:10~01:00');
                $fctag .= '<ul id="cnt_per_time">'.PHP_EOL;
                for($i=1;$i<=sizeof($time_arr);$i++){
                    $tkey = 'oop_'.$i;
                    $fctag .= '<li>'.PHP_EOL;
                    $fctag .= $time_arr[$i-1].'<br>'.PHP_EOL;
                    $fctag .= '<input type="text" name="'.$tkey.'" value="'.$$pre[$tkey].'" class="frm_input oop_ex" style="width:80px;text-align:right;margin-top:5px;" onclick="javascript:chk_Number(this)">'.PHP_EOL;
                    $fctag .= '</li>'.PHP_EOL;
                }
                $fctag .= '</ul>'.PHP_EOL;
            }
            // if($fk == 'oop_history'){
            //     ;//echo conv_content($od['oop_history'], 0);
            // }
            //######################### 커스터마이징 필드별 소스 추가 : 종료 #################################
            $fctag .= ($fv['td_e'])?'</td>'.PHP_EOL:'';
            $fctag .= ($fv['td'])?'</td>'.PHP_EOL:'';
            $fctag .= ($fv['tr_e'])?'</tr>'.PHP_EOL:'';
            echo $fctag;
        }
        //기본 디폴트로 사용할 경우
        else{
            echo form_tag($fk,$fv);
        }
    }
    ?>
	</tbody>
	</table>
</div>

<div class="btn_fixed_top">
    <a href="./<?=$fname?>_list.php?<?php echo $qstr ?>" class="btn btn_02">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
</div>
</form>

<script>
$(function(){
    $("input[name$=_date], #oro_date_plan").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99" });

    // 수주선택 버튼 클릭
    $('#btn_ord').click(function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        var winOrderSelect = window.open(href,"winOrderSelect","left=300,top=150,width=550,height=600,scrollbars=1");
        winOrderSelect.focus();
        return false;
    });

    // 수주제품선택 버튼 클릭
    $('#btn_ori').click(function(e){
        e.preventDefault();
        //수주선택을 실행하지 않으면 선택불가
        if(!$('input[name="ord_idx"]').val()){
            alert('수주선택을 먼저 실행해 주세요.');
            $('input[name="ord_idx"]').siblings('input[type=text]').focus();
            return false;
        }
        var href = $(this).attr('href');
        var winOrderItemSelect = window.open(href, "winOrderItemSelect", "left=300,top=150,width=550,height=600,scrollbars=1");
        winOrderItemSelect.focus();
        return false;
    });

    // 출하계획선택 버튼 클릭
    $('#btn_oro').click(function(e){
        e.preventDefault();
        //수주선택을 실행하지 않으면 선택불가
        if(!$('input[name="ord_idx"]').val()){
            alert('수주선택을 먼저 실행해 주세요.');
            $('input[name="ord_idx"]').siblings('input[type=text]').focus();
            return false;
        }
        //수주제품선택을 실행하지 않으면 선택불가
        if(!$('input[name="ori_idx"]').val()){
            alert('수주제품선택을 먼저 실행해 주세요.');
            $('input[name="ori_idx"]').siblings('input[type=text]').focus();
            return false;
        }
        var href = $(this).attr('href');
        var winOrderOutSelect = window.open(href, "winOrderOutSelect", "left=300,top=150,width=550,height=600,scrollbars=1");
        winOrderOutSelect.focus();
        return false;
    });

    // 생산계회ID(라인설비)찾기 버튼 클릭
	$("#btn_orp").click(function(e) {
		e.preventDefault();
        //수주선택을 실행하지 않으면 선택불가
        if(!$('input[name="ord_idx"]').val()){
            alert('수주선택을 먼저 실행해 주세요.');
            $('input[name="ord_idx"]').siblings('input[type=text]').focus();
            return false;
        }
        //수주제품선택을 실행하지 않으면 선택불가
        if(!$('input[name="ori_idx"]').val()){
            alert('수주제품선택을 먼저 실행해 주세요.');
            $('input[name="ori_idx"]').siblings('input[type=text]').focus();
            return false;
        }
        //출하선택을 실행하지 않으면 선택불가
        if(!$('input[name="oro_idx"]').val()){
            alert('출하계획선택을 먼저 실행해 주세요.');
            $('input[name="oro_idx"]').siblings('input[type=text]').focus();
            return false;
        }
        var href = $(this).attr('href');
		var winOrderPracticeSelect = window.open(href, "winOrderPracticeSelect", "left=300,top=150,width=550,height=600,scrollbars=1");
        winOrderPracticeSelect.focus();
        return false;
	});
});

// 숫자만 입력, 합산계산입력
function chk_Number(object){
    $(object).keyup(function(){
        $(this).val($(this).val().replace(/[^0-9|-]/g,""));
        var oop_sum = 0;
        $('.oop_ex').each(function(){
            oop_sum += Number($(this).val());
        });
        var oop_sum_str = (oop_sum) ? oop_sum : '';
        $('#oop_count').val(oop_sum_str);
    });
}

// 숫자만 입력
function only_Number(object){
    $(object).keyup(function(){
        $(this).val($(this).val().replace(/[^0-9|-]/g,""));
    });
}
</script>

<?php
include_once ('./_tail.php');