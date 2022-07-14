<?php
include('./_common.php');

$menus = array(
    '910100'
    ,'915125'
    ,'915130'
    ,'915145'
    ,'915115'
    ,'920100'
    ,'920110'
    ,'925100'
    ,'925180'
    ,'925120'
    ,'925130'
    ,'925230'
    ,'930100'
    ,'930105'
    ,'945110'
    ,'945115'
    ,'945118'
    ,'950100'
    ,'950110'
    ,'955400'
    ,'955500'
    ,'960100'
);
// array_rand($menus,1);

$mbs = array(
    'kbw'
    ,'kyh'
    ,'rjs'
    ,'rhy'
    ,'khs'
    ,'hmj'
    ,'ldw'
    ,'lyc'
    ,'ktw'
    ,'kes'
);
// array_rand($mbs,1);

$types = array(
    '접속'
    ,'종료'
    ,'등록'
    ,'수정'
    ,'검색'
);
// $types[array_rand($types,1)];
/*
2022-01-05 00:00:00 부터  2022-07-14 00:00:00 까지
------------------------------------------------
FROM_UNIXTIME(FLOOR(unix_timestamp('2022-01-05 00:00:00')+(RAND()*(unix_timestamp('2022-07-14 00:00:00')-unix_timestamp('2022-01-05 00:00:00')))))

*/

//위의 정보를 다시 epcs DB서버 g5_5_user_log테이블에 저장한다.
/*
$sql = " INSERT INTO g5_tblname SET
        mb_id = 'aaa',
        usl_menu_cd = '1111',
        usl_type = 'ok',
        usl_reg_dt = FROM_UNIXTIME(FLOOR(unix_timestamp('2022-01-05 00:00:00')+(RAND()*(unix_timestamp('2022-07-14 00:00:00')-unix_timestamp('2022-01-05 00:00:00')))))
";
*/

$insert_strs = '';
for($i=0;$i<100;$i++){
    $insert_strs .= ($insert_strs === '') ? " ( '".$mbs[array_rand($mbs,1)]."','".$menus[array_rand($menus,1)]."','".$types[array_rand($types,1)]."', FROM_UNIXTIME(FLOOR(unix_timestamp('2022-01-05 00:00:00')+(RAND()*(unix_timestamp('2022-07-14 00:00:00')-unix_timestamp('2022-01-05 00:00:00'))))) ) " : " ,( '".$mbs[array_rand($mbs,1)]."','".$menus[array_rand($menus,1)]."','".$types[array_rand($types,1)]."', FROM_UNIXTIME(FLOOR(unix_timestamp('2022-01-05 00:00:00')+(RAND()*(unix_timestamp('2022-07-14 00:00:00')-unix_timestamp('2022-01-05 00:00:00'))))) ) ";
}

$sql = " INSERT INTO {$g5['user_log_table']} 
        ( mb_id, usl_menu_cd, usl_type, usl_reg_dt )
        VALUES
        {$insert_strs}
";

print_r2($sql);exit;
// sql_query($sql,1);