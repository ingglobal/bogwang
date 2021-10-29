<?php
$sub_menu = "930180";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '완제품재고관리';
include_once('./_head.php');
echo $g5['container_sub_title'];


?>





<?php
include_once ('./_tail.php');
?>