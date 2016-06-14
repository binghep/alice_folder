<?php


class setting_manager{
	public $allowed_competitors;
	public $local_setting_file_path;
	function __construct($local_setting_file_path){
		// $this->allowed_competitors
		$this->local_setting_file_path=$local_setting_file_path;
		$this->allowed_competitors=$this->get_allowed_competitor_array();//remember it is not up to date. you should call get_allowed_competitor_array()
	}
	/*
	output: array(4) { [0]=> string(6) "amazon" [1]=> string(2) "jd" [2]=> string(6) "taobao" [3]=> string(5) "tmall" }
	*/
	public function get_allowed_competitor_array(){
		$competitor_array=file($this->local_setting_file_path);
		// var_dump($competitor_array);

		$competitor_array_new=array();
		foreach ($competitor_array as $competitor_raw) {//remove trailing PHP_EOL
			// array_push($competitor_array_new, rtrim($competitor_raw," "));
			array_push($competitor_array_new, substr($competitor_raw,0,-1));
		}
		return $competitor_array_new;
	}
	/*
		save unique values in $new_allowed_array to database/setting.txt 
	*/
	public function save_allowed_competitor_array($new_allowed_array){
		$new_allowed_array=array_unique($new_allowed_array);//remove duplicate items in array

		$output="";
		foreach ($new_allowed_array as $competitor) {
			// $output=$output.$competitor.'/n';
			$output=$output.$competitor.PHP_EOL;
		}
		$result=$this->write_allowed_competitors_txt($output);
		return $result;
	}
	/*
		helper function used in save_allowed_competitor_array($new_allowed_array)
	*/
	protected function write_allowed_competitors_txt($output_string){
		if (empty($output_string)){
			echo 'Are you sure you want to make setting.txt empty?';
			return;
		}
		// file_put_contents("database/setting.txt", $output_string);
		$result=file_put_contents($this->local_setting_file_path, $output_string);
		if ($result===false){
			return false;
		}else{
			return true;
		}
	}
	//function readFileLinesIntoArrayExample(){
	// print_r(file("database/setting2.txt"));
	//}
	/*
		An interface for outside world to add competitor. and save to setting.txt
		Output: true (if save/write to file successfully)
				false (if save/write to file failed)
	*/
	public function addCompetitor($new_competitor){
		$allowed_competitors=$this->get_allowed_competitor_array();
		array_push($allowed_competitors, $new_competitor);
		$result=$this->save_allowed_competitor_array($allowed_competitors);//will take care of duplicate values in param
		return $result;
	}
}

// $setting=new setting_manager("database/setting.txt");
// $allowed_competitors=$setting->get_allowed_competitor_array();
// if (is_null($allowed_competitors) || !is_array($allowed_competitors)){
// 	echo 'Cant read settings. or setting.txt is empty. no allowed competitors found. Exiting...';
// 	exit;
// }

// $result=$setting->addCompetitor("8pm");
// var_dump($result);//true
// var_dump($setting->get_allowed_competitor_array());


?>

