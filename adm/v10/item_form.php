<?php
$sub_menu = "945115";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

$html_title = ($w=='')?'추가':'수정'; 
$g5['title'] = '완제품재고 '.$html_title;
include_once ('./_head.php');
?>


<?php
include_once ('./_tail.php');
?>