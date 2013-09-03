<?php

require_once('tools.php');

class MobileAction extends Action{
	
	public function index()
	{
		$sql = "select CAR_ID,VIHICLE_TYPE,BUY_TIME,CAR_STATE from T_CAR_INFO;";
		$M = new Model();
		$list = $M->query($sql);
		$this->assign('carList',$list);
		$this->display();
	}
	public function start_upload_location()
	{
		$carID = Tools::request('carid');
		$this->assign('carID', $carID);
		$this->assign('ip', C('LOCAL_IP'));
		$this->assign('port', C('LOCAL_PORT'));
		$this->assign('interval', C('INTERVAL'));

		$this->display();		
	}
	public function index_mobile_map()
	{
		$this->display();
		
	}
	public function index_realtime_camera()
	{
		$id = Tools::request("id");
		$camera_text = $this->get_camera_text_by_id($id);

		$this->assign("pic_name","rt");
		$this->assign("camera_id", $id);
		$this->assign("camera_text", $camera_text);
		// $this->assign("pic_name","Blender");
		$this->display();
	}
	//视频更新完全版，包括标题栏、时间提示
	public function index_realtime_camera_full()
	{
		$id = Tools::request("id");
		$camera_text = $this->get_camera_text_by_id($id);

		$this->assign("pic_name","rt");
		$this->assign("camera_id", $id);
		$this->assign("camera_text", $camera_text);
		// $this->assign("pic_name","Blender");
		$this->display();
	}
	public function get_new_pic()
	{
		$id = Tools::request("id");
		$finded = false;
		// echo stristr("1001-rt.png", $id); return;
		// echo $id;return;
		foreach(glob("Public/realTimePic/*") as $d)
		{
			//echo $d;echo "<br>";
			///*
		    if(!is_dir($d))
		    {
		    	$name = basename($d);
		        if($name != 'rt.png')
		        {
					//echo $name;echo "<br>";
					///*
		        	// $bfind = stripos($name, $id, 0);
					if(false === strpos($name, $id)){
		        	//if(false == stristr($name, $id, 0)){
						//echo $name.' dose not contin id '.$id.'<br>';
		        		//没找到
		        		continue;
		        	}
		        	else{
		        		//比较图片的更新时间是否时间过长，过长的话需要返回默认图片
		        		$index_of_gap = strpos($name, "-");
		        		$index_of_dot = strpos($name, ".");
						$stamp = substr($name, $index_of_gap+1, $index_of_dot-$index_of_gap-1);
						if( (mktime()-$stamp) < 10 * 60){
			        		$finded = true;
					        echo $name;
					        break;
						}
		        	}
					//*/
		        }
		    }
			//*/
		}
		if($finded == false){
			echo "rt.png";
		}
		// echo "Blender";
		// echo "rt2";
	}
	function get_camera_text_by_id($id)
	{
		$camera_list = $this->camera_list();
        if (!empty($id)) {
            foreach ($camera_list as $key => $value) {
                if($value['id'] == $id)
                {
                    return $value['text'];
                }
            }
        }
        else
            return false;
	}
	function camera_list()
	{
		$list = array();
		$list[] = array('id'=>'10001', 'text'=>'环境科学楼');
		$list[] = array('id'=>'10002', 'text'=>'科技楼');
		$list[] = array('id'=>'10003', 'text'=>'学一宿舍楼');
		return $list;
	}
}

?>
