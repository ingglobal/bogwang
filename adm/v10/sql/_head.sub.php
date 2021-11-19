<?php
include_once('./_common.php');
include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_SQL_URL.'/css/sql.css">', 0);
add_javascript('<script src="'.G5_USER_ADMIN_SQL_URL.'/js/sql.js"></script>', 0);
?>
<div id="sql_head">
    <a class="<?=(($g5['file_name'] == 'index')?'focus':'')?>" href="<?=G5_USER_ADMIN_SQL_URL?>">SQL_HOME</a>
    <a class="" href="<?=G5_USER_ADMIN_URL?>">ADM_HOME</a>
    <a class="<?=(($g5['file_name'] == 'company_insert')?'focus':'')?>" href="<?=G5_USER_ADMIN_SQL_URL?>/company_insert.php">COMPANY_INSERT</a>
</div>
<div id="sql_container">
