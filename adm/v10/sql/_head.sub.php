<?php
include_once('./_common.php');
include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_SQL_URL.'/css/sql.css">', 0);
add_javascript('<script src="'.G5_USER_ADMIN_SQL_URL.'/js/sql.js"></script>', 0);
?>
<div id="sql_head">
    <a class="<?=(($g5['file_name'] == 'index')?'focus':'')?>" href="<?=G5_USER_ADMIN_SQL_URL?>">SQL홈</a>
    <a class="" href="<?=G5_USER_ADMIN_URL?>">관리자홈</a>
    <a class="<?=(($g5['file_name'] == 'bom_insert')?'focus':'')?>" href="<?=G5_USER_ADMIN_SQL_URL?>/bom_insert.php">BOM업데이트</a>
    <a class="<?=(($g5['file_name'] == 'bom_product_exlabel')?'focus':'')?>" href="<?=G5_USER_ADMIN_SQL_URL?>/bom_product_exlabel.php">완제품 외부라벨</a>
    <a class="<?=(($g5['file_name'] == 'company_insert')?'focus':'')?>" href="<?=G5_USER_ADMIN_SQL_URL?>/company_insert.php">회사등록</a>
    <a class="<?=(($g5['file_name'] == 'material_input')?'focus':'')?>" href="<?=G5_USER_ADMIN_SQL_URL?>/material_input.php">자재등록</a>
</div>
<div id="sql_container">
