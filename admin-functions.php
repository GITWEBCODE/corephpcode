<?php
function user_status(){
	$result = array();
	$result['status'] = false;
	$result['user'] = 0;
	$db = new dbQuery;
	if(isset($_POST['status']) && $_POST['status']== 'true'){
		$status = 1;
	}else{
		$status = 0;
	}
	
	if(isset($_POST['id']) && $_POST['id'] != ''){
	$args = array(
		  'table'=>'users',
		  'update'=>array('status' => $status),	
		  'where' => array('id' => $_POST['id']) 
		);
	if($db->db_update($args)){
		$result['status'] = true;
		$args = array(
		  'table'=>'users',	
		  'where' => array('id' => $_POST['id'])
		);
		$table = $db->dbSelect($args);
		$table = $table[0];
	    
		$result['user'] = $table['status'];
		}else{$result['status'] = false;}	
	}
	echo json_encode($result);
	exit;
}

function change_select_level(){
	$db = new dbQuery;
	$result = array();
	$result['status'] = false;
	if(isset($_POST['id']) && $_POST['id'] != ''){
	  $args = array(
		  'table'=>'users',
		  'update'=>array('level' => $_POST['level']),	
		  'where' => array('id' => $_POST['id']) 
		);
		if($db->db_update($args)){
		$result['status'] = true;
		}
	}
    echo json_encode($result);
	exit;
}

function remove_user(){
	$db = new dbQuery;
	$result = array();
	$result['status'] = false;
	if(isset($_POST['id']) && $_POST['id'] != ''){
	  $args = array(
		  'table'=>'users',	
		  'where' => array('id' => $_POST['id']) 
		);
		if($db->db_delete($args)){
		 $result['status'] = true;
		}
	}
    echo json_encode($result);
	exit;
}	

function get_website_access_list(){
	$db = new dbQuery;
	$result = array();
	$result['status'] = false;
	 if(isset($_POST['id'])){
		$args_website = array(
		  'table'=>'websites',	
		  'select'=>array()
		);		
		$website_data = $db->dbSelect($args_website);
		$str = '';
		$str .= '<input type="hidden" id="for_user" name="user_id" value="'.$_POST['id'].'">';
		foreach($website_data as $w_data ){
		 $args_access = array(
		  'table'=>'term_relation',	
		  'select'=>array(),
		  'where'=>array('term_name'=>'website', 'user_id'=>$_POST['id'], 'term_id'=>$w_data['id'])
		 );	
		 $website_access_data = $db->dbSelect($args_access);
		 if(count($website_access_data) > 0){
		 $str .= '<li><a class="active" href="#"><i class="fa fa-circle "></i>  '.$w_data['website_name'].'</a><input type="checkbox" value="'.$w_data['id'].'" name="websites[]" checked="checked" /></li>';	
		 }else{
		 $str .= '<li><a class="" href="#"><i class="fa fa-circle-o "></i>  '.$w_data['website_name'].'</a><input type="checkbox" value="'.$w_data['id'].'" name="websites[]" /></li>';}		 
		 $result['status'] = true;
		}		
	 }
   $result['list'] = $str;
   echo json_encode($result);
   exit;
}	


function set_user_access(){
   $db = new dbQuery;
   $result = array();
   $result['status'] = false;
	 if(isset($_POST['status']) && isset($_POST['term']) && isset($_POST['user'])){
		 
		 $args_access = array(
		  'table'=>'term_relation',	
		  'select'=>array(),
		  'where'=>array('term_name'=>'website', 'user_id'=>$_POST['user'], 'term_id'=>$_POST['term'])
		);
		$website_access_data = $db->dbSelect($args_access);
		if($_POST['status']=='true'){
		  if(count($website_access_data) > 0){ 
		    $result['status'] = true;
		  }else{
			  $args_insert = array(
		        'table'=>'term_relation',
		        'insert'=>array('term_name'=>'website', 'user_id'=>$_POST['user'], 'term_id'=>$_POST['term']),
		      ); 
			  $db->db_insert($args_insert);
			  $result['status'] = true;
		  }
		}elseif($_POST['status']=='false'){
			$args_delete = array(
						   'table'=>'term_relation',
						   'where'=>array('term_name'=>'website', 'user_id'=>$_POST['user'], 'term_id'=>$_POST['term'])
					   );
			$db->db_delete($args_delete);
			$result['status'] = true;
	    }
		   
	 }
   echo json_encode($result);
   exit;
}
?>