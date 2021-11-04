<?php
$sub_menu = "945115";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '완제품재고관리';
include_once('./_head.php');
echo $g5['container_sub_title'];


$sql_common = " FROM {$g5['item_table']} AS itm
                    LEFT JOIN {$g5['bom_table']} AS bom USING(bom_idx)
"; 

$where = array();
// 디폴트 검색조건 (used 제외)
$where[] = " itm_status NOT IN ('delete','trash','used') AND itm.com_idx = '".$_SESSION['ss_com_idx']."' ";

// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'bom_idx' || $sfl == 'itm_idx' || $sfl == 'itm_borcode' || $sfl == 'itm_lot' || $sfl == 'itm_defect_type' || $sfl == 'trm_idx_location' ) :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
		case ( $sfl == 'bct_id' ) :
			$where[] = " {$sfl} LIKE '".trim($stx)."%' ";
            break;
        default :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
    }
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

if (!$sst) {
    $sst = "itm_idx";
    $sod = "desc";
}

$sql_order = " ORDER BY {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql,1);
$total_count = $row['cnt'];
// echo $total_count.'<br>';

$rows = $config['cf_page_rows'];
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
.td_itm_name {text-align:left !important;}
.td_itm_part_no, .td_com_name, .td_itm_maker
,.td_itm_items, .td_itm_items_title {text-align:left !important;}
.span_itm_price {margin-left:20px;}
.span_itm_price b, .span_bit_count b {color:#737132;font-weight:normal;}
#modal01 table ol {padding-right: 20px;text-indent: -12px;padding-left: 12px;}
#modal01 form {overflow:hidden;}
.ui-dialog .ui-dialog-titlebar-close span {
    display: unset;
    margin: -8px 0 0 -8px;
}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="itm_name"<?php echo get_selected($_GET['sfl'], "itm_name"); ?>>품명</option>
    <option value="itm_barcode"<?php echo get_selected($_GET['sfl'], "itm_borcode"); ?>>바코드</option>
    <option value="itm_lot"<?php echo get_selected($_GET['sfl'], "itm_borcode"); ?>>LOT</option>
    <option value="com_idx_customer"<?php echo get_selected($_GET['sfl'], "com_idx_customer"); ?>>거래처번호</option>
    <option value="itm_maker"<?php echo get_selected($_GET['sfl'], "itm_maker"); ?>>메이커</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit" value="검색">

</form>

<div class="local_desc01 local_desc" style="display:none;">
    <p>새로운 고객을 등록</p>
</div>


<form name="form01" id="form01" action="./item_list_update.php" onsubmit="return form01_submit(this);" method="post">
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
        <th scope="col" id="itm_list_chk">
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">생산일</th>
        <th scope="col"><?php echo subject_sort_link('itm_name') ?>품명</a></th>
        <th scope="col">파트넘버</th>
        <th scope="col">바코드</th>
        <th scope="col">외부라벨</th>
        <th scope="col">LOT</th>
        <th scope="col">PLT</th>
        <th scope="col">품질</th>
        <th scope="col">위치</th>
        <th scope="col">히스토리</th>
        <th scope="col">상태</th>
        <th scope="col">관리</th>
    </tr>
    <tr>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        // print_r2($row);

        $s_mod = '<a href="./item_form.php?'.$qstr.'&amp;w=u&amp;itm_idx='.$row['itm_idx'].'" class="btn btn_03">수정</a>';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['itm_idx'] ?>">
        <td class="td_chk">
            <input type="hidden" name="itm_idx[<?php echo $i ?>]" value="<?php echo $row['itm_idx'] ?>" id="itm_idx_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['itm_name']); ?> <?php echo get_text($row['itm_nick']); ?>님</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_itm_reg_dt"><?=substr($row['itm_reg_dt'],0,19)?></td><!-- 생산일 -->
        <td class="td_itm_name"><?=$row['itm_name']?></td><!-- 품명 -->
        <td class="td_itm_part_no"><?=$row['bom_part_no']?></td><!-- 파트넘버 -->
        <td class="td_itm_barcode"><?=$row['itm_barcode']?></td><!-- 바코드 -->
        <td class="td_itm_com_barcode"><?=$row['itm_com_barcode']?></td><!-- 외부라벨 -->
        <td class="td_itm_lot"><?=$row['itm_lot']?></td><!-- LOT -->
        <td class="td_itm_plt"><?=$row['itm_plt']?></td><!-- PLT -->
        <td class="td_itm_defect"><?=($row['itm_defect'])?'불량품':'양품'?></td><!-- 품질 -->
        <td class="td_itm_location"><?=$g5['location_name'][$row['trm_idx_location']]?></td><!-- 위치 -->
        <td class="td_itm_history"><?=$row['itm_history']?></td><!-- 히스토리 -->
        <td class="td_itm_status"><?=$g5['set_itm_status_value'][$row['itm_status']]?></td><!-- 상태 -->
        <td class="td_mng">
            <?=($row['itm_type']!='material')?$s_bom:''?><!-- 자재가 아닌 경우만 BOM 버튼 -->
			<?=$s_mod?>
		</td>
    </tr>
    <?php
    }
    if ($i == 0)
        echo "<tr><td colspan='20' class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <?php if (!auth_check($auth[$sub_menu],'d')) { ?>
       <a href="<?=G5_URL?>/device/itm_ing/form.php" target="_blank" class="btn btn_02">생산시작</a>
       <a href="<?=G5_URL?>/device/itm_error/form.php" target="_blank" class="btn btn_02">검수</a>
       <a href="<?=G5_URL?>/device/itm_error/form.php" target="_blank" class="btn btn_02">완제품코드매칭</a>
       <a href="<?=G5_URL?>/device/itm_error/form.php" target="_blank" class="btn btn_02" style="margin-right:200px;">출하</a>
    <?php } ?>
    <?php if (!auth_check($auth[$sub_menu],'w')) { ?>
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <a href="./item_form.php" id="member_add" class="btn btn_01">추가하기</a>
    <?php } ?>

</div>


</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<div id="modal01" title="엑셀 파일 업로드" style="display:none;">
    <form name="form02" id="form02" action="./item_excel_upload.php" onsubmit="return form02_submit(this);" method="post" enctype="multipart/form-data">
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
$(document).on( 'keyup','input[name^=itm_price], input[name^=itm_count], input[name^=itm_lead_time]',function(e) {
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

</script>

<?php
include_once ('./_tail.php');
