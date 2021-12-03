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

		$split_arr = str_split($this->bct_id, 2);
		$cat1 = (count($split_arr) >= 1) ? $split_arr[0] : '';
		$cat2 = (count($split_arr) >= 2) ? $split_arr[0].$split_arr[1] : '';
		$cat3 = (count($split_arr) >= 3) ? $split_arr[0].$split_arr[1].$split_arr[2] : '';
		$cat4 = (count($split_arr) >= 4) ? $split_arr[0].$split_arr[1].$split_arr[2].$split_arr[3] : '';

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
