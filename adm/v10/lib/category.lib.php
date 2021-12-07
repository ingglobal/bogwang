<?php
/*

*/
class category_list {

	protected $com_idx = "";
	protected $value = "";
	protected $name = "";
	protected $list_flag = false;
	protected $list_id = "";
	protected $count = 0;
	protected $id = "";
	protected $id1 = "";
	protected $id2 = "";
	protected $id3 = "";
	protected $id4 = "";

	function __construct($com_idx='',$value='',$list_flag=false,$list_id='') {
        $this->com_idx = $com_idx;
        $this->value = $value;
		$this->name = 'bct_id';
		$this->name .= ($list_flag)?$this->name.'['.$list_id.']':'';
		$this->list_flag = $list_flag;
		$this->list_id = $list_id;
		$this->id = "bct";
		$this->id1 = $this->id."_1";
		$this->id2 = $this->id."_2";
		$this->id3 = $this->id."_3";
		$this->id4 = $this->id."_4";
        $this->count++;
    }

	function set_value($value) {
		$this->value = $value;
	}

	function set_name($name) {
		$this->name = $name;
		if($this->list_flag){
			$this->name .= '['.$this->list_id.']';
		}
	}

	function set_id($id) {
		$this->id = $id;
		if($id) {
			$this->id1 = $id.'_1';
			$this->id2 = $id.'_2';
			$this->id3 = $id.'_3';
			$this->id4 = $id.'_4';
		}
	}

	function run(){
        //global $g5, $config, $member, $default;
        global $g5;

		$cats = category_tree_array($this->value);
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
			$csql = " SELECT bct_id,bct_name FROM {$g5['bom_category_table']} WHERE com_idx = '{$_SESSION['ss_com_idx']}' AND bct_id REGEXP '^.{".(($i==0)?2:strlen($cats[$i]))."}$' ";
			$csql .= ($i == 0) ? "" : " AND bct_id LIKE '{$cats[$i-1]}%' ";
			//echo $csql;
			$cres = sql_query($csql,1);
			if($cres->num_rows){
				//${'cats'.($i+1)}
				for($j=0;$crow=sql_fetch_array($cres);$j++){
					${'cats'.($i+1)}[$crow['bct_id']] = $crow['bct_name'];
				}
			}
		}
		// echo $this->bct_id."<br>";
		// print_r2($cats1);
		// echo $this->bct_id."<br>";
		// print_r2($cats2);
		// echo $this->bct_id."<br>";
		// print_r2($cats3);
		// echo $this->bct_id."<br>";
		// print_r2($cats4);
		
		$file = G5_USER_ADMIN_SKIN_PATH.'/category/category.skin.php';
		$category_list_call_url = G5_USER_ADMIN_SKIN_URL.'/category/ajax/category_call.php';

		if (!file_exists($file)) {
            return $file." 파일을 찾을 수 없습니다.";
        } else {
            ob_start();
			$this->com_idx;
			$this->value;
            include($file);
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
	}
}
