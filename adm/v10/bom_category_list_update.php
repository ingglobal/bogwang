<?php
$sub_menu = '915125';
include_once('./_common.php');

check_demo();

auth_check_menu($auth, $sub_menu, "w");

check_admin_token();

$post_bct_id_count = (isset($_POST['bct_id']) && is_array($_POST['bct_id'])) ? count($_POST['bct_id']) : 0;

for ($i=0; $i<$post_bct_id_count; $i++)
{
    $sql = " update {$g5['bom_category_table']}
                set bct_name    = '".$_POST['bct_name'][$i]."',
                    bct_order   = '".sql_real_escape_string(strip_tags($_POST['bct_order'][$i]))."'
              where bct_id = '".sql_real_escape_string($_POST['bct_id'][$i])."'
                AND com_idx = '".$_SESSION['ss_com_idx']."'
    ";
    sql_query($sql,1);
}

goto_url("./bom_category_list.php?$qstr");