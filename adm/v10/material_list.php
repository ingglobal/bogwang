<?php
$sub_menu = "945110";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '재고관리';
// include_once('./_top_menu_mtr.php');
include_once('./_head.php');
// echo $g5['container_sub_title'];

$sql_common = " FROM {$g5['material_table']} AS mtr
                    LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = mtr.bom_idx
                    LEFT JOIN {$g5['company_table']} AS com ON bom.com_idx_provider = com.com_idx
"; 

$where = array();
// 디폴트 검색조건 (used 제외)
$where[] = " mtr_status NOT IN ('delete','trash','used') AND mtr.com_idx = '".$_SESSION['ss_com_idx']."' ";

// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'mtr.bom_part_no' ) :
			$where[] = " {$sfl} LIKE '%".trim($stx)."%' ";
            break;
        default :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
    }
}

if($times) $where[] = " mtr_times = '".$times."' ";
if($mtr_input2_date) $where[] = " mtr_input_date = '".$mtr_input2_date."' ";

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

if (!$sst) {
    $sst = "mtr_idx";
    $sod = "desc";
}

$sql_order = " ORDER BY {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 1000;//$config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "SELECT *
        {$sql_common} {$sql_search} {$sql_order}
        LIMIT {$from_record}, {$rows}
";
// print_r3($sql);
$result = sql_query($sql,1);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
$qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들
?>
<style>
#top_form:after{display:block;visibility:hidden;clear:both;content:'';}
#top_form #fsearch{float:left;}
#top_form #finput{float:right;margin:10px 0;}
.tbl_head01 thead tr th{position:sticky;top:100px;z-index:100;}
.td_mtr_name {text-align:left !important;}
.td_mtr_part_no, .td_com_name, .td_mtr_maker
,.td_mtr_items, .td_mtr_items_title {text-align:left !important;}
.span_mtr_price {margin-left:20px;}
.span_mtr_price b, .span_bit_count b {color:#737132;font-weight:normal;}
#modal01 table ol {padding-right: 20px;text-indent: -12px;padding-left: 12px;}
#modal01 form {overflow:hidden;}
.ui-dialog .ui-dialog-titlebar-close span {
    display: unset;
    margin: -8px 0 0 -8px;
}
label[for="mtr_input2_date"]{position:relative;}
label[for="mtr_input2_date"] i{position:absolute;top:-10px;right:0px;z-index:2;cursor:pointer;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
</div>

<div id="top_form">
    <form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
        <label for="sfl" class="sound_only">검색대상</label>
        <select name="sfl" id="sfl">
            <option value="mtr_name"<?php echo get_selected($_GET['sfl'], "mtr_name"); ?>>품명</option>
            <option value="mtr.bom_part_no"<?php echo get_selected($_GET['sfl'], "bom_part_no"); ?>>품번</option>
        </select>
        <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
        <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
        <select name="times" id="times">
            <option value="">::입고차수::</option>
            <?=$g5['set_mtr_times_value_options']?>
        </select>
        <?php
        $mtr_input2_date = ($mtr_input2_date) ? $mtr_input2_date : G5_TIME_YMD;
        ?>
        <label for="mtr_input2_date"><strong class="sound_only">입고일 필수</strong>
        <i class="fa fa-times" aria-hidden="true"></i>
        <input type="text" name="mtr_input2_date" value="<?php echo $mtr_input2_date ?>" placeholder="입고일" id="mtr_input_date" readonly class="frm_input readonly" style="width:80px;">
        </label>
        <input type="submit" class="btn_submit" value="검색">
    </form>

    <form name="finput" id="finput" action="./material_input_update.php" onsubmit="return input_form(this);" method="post">
        <label for="bom_name">
            <input type="hidden" name="bom_idx" value="">
            <input type="hidden" name="bom_part_no" value="">
            <input type="hidden" name="bom_type" value="">
            <input type="hidden" name="bom_price" value="">
            <input type="text" id="bom_name" name="bom_name" link="./material_select.php" readonly class="frm_input readonly" placeholder="입고자재상품선택(클릭!)" value="" style="width:200px;">            
        </label>
        <label for="mtr_input_date">
            <input type="text" name="mtr_input_date" id="mtr_input_date" readonly required class="frm_input readonly required" value="<?=G5_TIME_YMD?>" style="width:80px;">
        </label>
        <select name="mtr_times" required id="mtr_times">
            <?=$g5['set_mtr_times_value_options']?>
        </select>
        <label for="counts" id="counts">
            <input type="text" name="counts" required class="frm_input required" placeholder="자재입고갯수" value="" style="text-align:right;width:100px;" onclick="javascript:chk_Number(this)">
        </label>
        <input type="submit" name="act_button" class="btn_input btn btn_01" onclick="document.pressed=this.value" value="자재입고">
        <input type="submit" name="act_button" class="btn_input btn btn_04" onclick="document.pressed=this.value" value="자재삭제">
    </form>
</div>
<div class="local_desc01 local_desc" style="display:no ne;">
    <p>새로운 자재를 등록하는 페이지입니다.</p>
    <p><b style="color:skyblue;">엑셀파일</b>로 등록할때는 <b style="color:red;">최초에 한 번만 등록할 수 있으니</b> 신중하게 작성해서 등록해 주시기 바랍니다.</p>
    <p>엑셀파일에 의한 최초 등록후 재고품목의 <b style="color:skyblue">추가등록</b> 및 <b style="color:red">삭제작업</b>은 [<b style="color:orange">자재재고관리</b>] 페이지상에서 진행해 주세요.</p>
</div>


<form name="form01" id="form01" action="./material_list_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" id="mtr_list_chk">
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">ID</th>
        <th scope="col"><?php echo subject_sort_link('mtr_name') ?>품명</a></th>
        <th scope="col">파트넘버</th>
        <th scope="col">공급처</th>
        <th scope="col">입고일</th>
        <th scope="col">차수</th>
        <th scope="col">상태</th>
        <th scope="col">관리</th>
    </tr>
    <tr>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {

        $s_mod = '<a href="./material_form.php?'.$qstr.'&amp;w=u&amp;mtr_idx='.$row['mtr_idx'].'" class="btn btn_03">수정</a>';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['mtr_idx'] ?>">
        <td class="td_chk">
            <input type="hidden" name="mtr_idx[<?php echo $i ?>]" value="<?php echo $row['mtr_idx'] ?>" id="mtr_idx_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['mtr_name']); ?> <?php echo get_text($row['mtr_nick']); ?>님</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_mtr_name"><?=$row['mtr_idx']?></td><!-- ID -->
        <td class="td_mtr_name"><?=$row['mtr_name']?></td><!-- 품명 -->
        <td class="td_mtr_part_no"><?=$row['bom_part_no']?></td><!-- 파트넘버 -->
        <td class="td_mtr_provider"><?=$row['com_name']?></td><!-- 공급처명 -->
        <td class="td_mtr_input_date"><?=$row['mtr_input_date']?></td><!-- 입고일 -->
        <td class="td_mtr_times"><?=$row['mtr_times']?></td><!-- 차수 -->
        <td class="td_mtr_status"><?=$g5['set_mtr_status_value'][$row['mtr_status']]?></td><!-- 상태 -->
        <td class="td_mng">
            <?=($row['mtr_type']!='material')?$s_bom:''?><!-- 자재가 아닌 경우만 BOM 버튼 -->
			<?=$s_mod?>
		</td>
    </tr>
    <?php
    }
    if ($i == 0)
        echo "<tr><td colspan='9' class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <?php if (!auth_check($auth[$sub_menu],'d')) { ?>
       <a href="javascript:" id="btn_excel_upload" class="btn btn_02" style="margin-right:50px;">엑셀등록</a>
    <?php } ?>
    <?php if (!auth_check($auth[$sub_menu],'w')) { ?>
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <!--
    <a href="./material_form.php" id="member_add" class="btn btn_01">추가하기</a>
    -->
    <?php } ?>

</div>


</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<div id="modal01" title="엑셀 파일 업로드" style="display:none;">
    <form name="form02" id="form02" action="./material_excel_upload.php" onsubmit="return form02_submit(this);" method="post" enctype="multipart/form-data">
        <table>
        <tbody>
        <tr>
            <td style="line-height:130%;padding:10px 0;">
                <ol>
                    <li>엑셀은 97-2003통합문서만 등록가능합니다. (*.xls파일로 저장)</li>
                    <li>엑셀은 하단에 탭으로 여러개 있으면 등록 안 됩니다. (한개의 독립 문서이어야 합니다.)</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="padding:15px 0;">
                <input type="file" name="file_excel" onfocus="this.blur()">
            </td>
        </tr>
        <tr>
            <td style="padding:15px 0;">
                <button type="submit" class="btn btn_01">확인</button>
            </td>
        </tr>
        </tbody>
        </table>
    </form>
</div>


<script>
$("input[name*=_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99" });

$('label[for="mtr_input_date"] i').on('click',function(){
    $(this).siblings('input').val('');
});

// 제품찾기 버튼 클릭
$("#bom_name").click(function(e) {
    e.preventDefault();
    var href = $(this).attr('link');
    winBomSelect = window.open(href, "winBomSelect", "left=300,top=150,width=650,height=600,scrollbars=1");
    winBomSelect.focus();
});

// 엑셀등록 버튼
$( "#btn_excel_upload" ).on( "click", function() {
    $( "#modal01" ).dialog( "open" );
});
$( "#modal01" ).dialog({
    autoOpen: false
    , position: { my: "right-10 top-10", of: "#btn_excel_upload"}
});


// 마우스 hover 설정
$(".tbl_head01 tbody tr").on({
    mouseenter: function () {
        $('tr[tr_id='+$(this).attr('tr_id')+']').find('td').css('background','#0b1938');
        
    },
    mouseleave: function () {
        $('tr[tr_id='+$(this).attr('tr_id')+']').find('td').css('background','unset');
    }    
});

// 가격 입력 쉼표 처리
$(document).on( 'keyup','input[name^=mtr_price], input[name^=mtr_count], input[name^=mtr_lead_time]',function(e) {
    if(!isNaN($(this).val().replace(/,/g,'')))
        $(this).val( thousand_comma( $(this).val().replace(/,/g,'') ) );
});

// 숫자만 입력
function chk_Number(object){
    $(object).keyup(function(){
        $(this).val($(this).val().replace(/[^0-9|-]/g,""));
    });
}
    

function form01_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }

    return true;
}

function form02_submit(f) {
    if (!f.file_excel.value) {
        alert('엑셀 파일(.xls)을 입력하세요.');
        return false;
    }
    else if (!f.file_excel.value.match(/\.xls$|\.xlsx$/i) && f.file_excel.value) {
        alert('엑셀 파일만 업로드 가능합니다.');
        return false;
    }

    return true;
}


function input_form(f){
    if(!f.bom_name.value){
        alert('입고할 상품을 선택해 주세요.');
        f.bom_name.focus();
        return false;
    }

    if(!f.mtr_input_date.value){
        alert('입고일을 선택해 주세요.');
        f.mtr_input_date.focus();
        return false;
    }
    
    if(!f.mtr_times.value){
        alert('입고차수를 선택해 주세요.');
        f.mtr_times.focus();
        return false;
    }
    
    if(!f.counts.value){
        alert('입고갯수를 설정해 주세요.');
        f.counts.focus();
        return false;
    }

    if(document.pressed == "자재삭제") {
        if(!confirm("등록된 자재를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }
    
    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>
