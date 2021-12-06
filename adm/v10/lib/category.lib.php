<?php
/*

*/
class category_list {

	protected $com_idx = "";
	protected $bct_id = "";
	protected $count = 0;

	function __construct($com_idx='',$bct_id='') {
        $this->com_idx  = $com_idx;
        $this->bct_id   = $bct_id;
        $this->count++;
    }

	function run(){
        //global $g5, $config, $member, $default;
        global $g5;

		$cats = category_tree_array($this->bct_id);
		/*
		[0] => 1c
		[1] => 1c10
		[2] => 1c103m
		[3] => 1c103m14
		*/
		$cats1 = array();
		$cats2 = array();
		$cats3 = array();
		$cats4 = array();
		for($i=0;$i<4;$i++){
			if($cats[$i]){
				$csql = " SELECT  ";
			}
			else{
				echo 'no';
			}
		}

		$file = G5_USER_ADMIN_SKIN_PATH.'/category/category.skin.php';
		$category_list_call_url = G5_USER_ADMIN_SKIN_URL.'/category/ajax/category_call.php';

		if (!file_exists($file)) {
            return $file." 파일을 찾을 수 없습니다.";
        } else {
            ob_start();
			$this->com_idx;
			$this->bct_id;
            include($file);
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
	}
}
