<?php
class dbQuery{
private $DbConnection = false;	
private function DbConnection(){
  try {
	  $this->DbConnection = new PDO("mysql:host=". Db_Host .";dbname=". Db_Name ."", Db_User, Db_Password);
	  return true;
	  }
	  catch(PDOException $e)
	  {			  
	  $this->errors[] = $e->getMessage();
	  return false;
	  }
}  // end of db_connect

// insert function
private function insert($args){
	     if(array_key_exists('table',$args) && array_key_exists('insert',$args)){
	       $pre1 = '';
		   $pre2 = '';
		   $table = $args['table'];
		   $args = $args['insert'];		   
		   foreach($args as $key => $value){
			          $pre1 .=  '`'. $key .'`,';
					  $pre2 .=  ':'. $key .',';				  
			        }			
		   if($this->DbConnection()){
	       $sql = 'INSERT INTO '. $table . '('. rtrim($pre1,',') .') VALUES ('. rtrim($pre2,',') .')';
		   $query = $this->DbConnection->prepare($sql);
		   foreach($args as $key => $value){
			   $query->bindValue(':'.$key , trim(strip_tags($value)), PDO::PARAM_STR);
		   } 	
			  $query->execute();
			  $id = $this->DbConnection->lastInsertId();
			  if($query) {
				return $id;
			  }else{
				  return null;			  
				  }	
		   }
		 }
		 else{ return null;}
}  //end of insert function
public function db_insert($args){
	return $this->insert($args);
}


public function check_value($args){
	  if(array_key_exists('table',$args) && array_key_exists('check',$args) && array_key_exists('value',$args)){
		 $table = $args['table'];
		 $check = $args['check'];
		 $value = $args['value'];		 
		$sql = "SELECT `".trim($check)."` FROM ".trim($table)." WHERE `".trim($check)."` = :value";
		  if($this->DbConnection()){
			  $query = $this->DbConnection->prepare($sql);
			  $query->bindParam(':value', $value, PDO::PARAM_STR);
			  $query->execute();			  
			  if($query->rowCount() > 0){
				  return false;
			  }else{
				  return true;
				  }
		  }
	  }else{return false;}
}

public function linksCount($args){
	  if(array_key_exists('table',$args) && array_key_exists('check',$args) && array_key_exists('value',$args)){
		 $table = $args['table'];
		 $check = $args['check'];
		 $value = $args['value'];
		 if(array_key_exists('or',$args)){
		    $or = $args['or'];
		    $or_check = $or['or_check'];
			$or_value = $or['or_value'];
		 }
		 if(array_key_exists('group_by',$args)){
			  $order = " GROUP BY `".trim($args['group_by']."`");
			 }else{
				 $order = "";
				 }
	     $sql = "SELECT `".trim($check)."` FROM ".trim($table)." WHERE `".trim($check)."` = :value ".$order;
		 if(isset($_COOKIE['start_date'])){ $sql .= ' AND `date`>="'.trim($_COOKIE['start_date']).'"';}
		 if(isset($_COOKIE['end_date'])){   $sql .= ' AND `date`<="'.trim($_COOKIE['end_date']).'"';}
		 if(array_key_exists('or',$args)){
		 $sql .= " OR `".trim($or_check)."` = :orvalue";
		 }
		 //echo $sql;
		  if($this->DbConnection()){
			  $query = $this->DbConnection->prepare($sql);
			  $query->bindParam(':value', $value, PDO::PARAM_STR);
			  if(array_key_exists('or',$args)){
				 $query->bindParam(':orvalue', $or_value, PDO::PARAM_STR);
			  }
			  $query->execute();
			  return $query->rowCount();
		  }
	  }else{return false;}
}

/*........................... SELECT ....................................................................*/
private function select($args){
	if(array_key_exists('select',$args)){
		   if(empty($args['select'])){
			     $select = '* ';
			   }
		   else{
			     $select_data = '';
			     foreach($args['select'] as $v){
					   $select_data .= ' `'. trim($v) .'`, ';					 
					 }
				 $select = rtrim($select_data,', ').' ';
			   }   
		}
	else {
		   $select = '* ';
		 }
	if(array_key_exists('condition',$args)){ $con = $args['condition'];}else{$con = '=';}
	if(array_key_exists('condition-for',$args)){ $forcon = $args['condition-for'];}else{$forcon = '';}
	if(array_key_exists('datefrom',$args)){$datefrom = $args['datefrom'];}else{$datefrom = '';}
	if(array_key_exists('dateto',$args)){$dateto = $args['dateto'];}else{$dateto = '';}
	
	// Table to select	 
	if(array_key_exists('table',$args)){ $table = '`'.$args['table'].'`';}
	// Oprater in Where
	if(array_key_exists('oprater',$args)){ $oprater = $args['oprater'];}else{ $oprater = 'AND';}	 
    // Where Statmment
    if(array_key_exists('where',$args)){
		if(empty($args['where'])){ $where = "";}else{
		$where_data = "";
		foreach($args['where'] as $k => $v){
			           if($k == $forcon){
					   $where_data .= "`". $k ."` ".$con." :". $k ." ". $oprater ." ";
					   }
					   elseif($k == 'datefrom'){
							 if($v != ''){
								$where_data .= "`date` >= :". $k ." ". $oprater ." ";
							 }
						   }
					   elseif($k == 'dateto'){
							 if($v !=''){
							   $where_data .= "`date` <= :". $k ." ". $oprater ." ";
							 }
						   }
					   elseif(is_array($v)){
							   $i = 0;
							   foreach($args['where'][$k] as $v1){
							      $where_data .= "`". $k ."` = :". $k.'_'.$i ." ". $oprater ." ";
								  $i++;
						        }
					   }else{
						      $where_data .= "`". $k ."` = :". $k ." ". $oprater ." ";
						    
						   }
					 }
		  $where = rtrim($where_data," ". $oprater." ");
		  $where = " WHERE ".$where;		 
		}					 
	 }else{ $where = "";}
	 
	 if(array_key_exists('search',$args)){
		 $search = '';
		foreach($args['search'] as $key=>$value){
			$search .= "`".$key."` LIKE '%".$value."%' OR ";
		}
		$search = rtrim($search," OR ");
	   $where = $where." AND ".$search;
	 }
	 
	 // Set the limit
	 if(array_key_exists('limit',$args)){
		 $lim = $args['limit'];
		 $limit = " LIMIT ". $lim[0].", ". $lim[1] ;
	 }else{ $limit = ' '; }
	 // Set the order
	 if(array_key_exists('order_by',$args)){		    
			if(array_key_exists('order',$args)){
				$order = " ORDER BY ". $args['order_by'] ." ". $args['order'];
			}else{
				$order = " ORDER BY ". $args['order_by'] ." ASC";				
				}					 
		 }else{ $order = "";}
	 

$sql = "SELECT ". $select . " FROM ". $table . $where  ;
if(array_key_exists('group_by',$args)){
$sql .= " GROUP BY `".trim($args['group_by']."`");
}
$sql .= $order. $limit;
//echo $sql;
if($this->DbConnection()){
	 $query = $this->DbConnection->prepare($sql);
	 if(array_key_exists('where',$args)){
	 foreach($args['where'] as $key => $value){		
		  if(is_int($value)){ $param = PDO::PARAM_INT;}
		  elseif(is_bool($value)){ $param = PDO::PARAM_BOOL;}
		  elseif(is_null($value)){ $param = PDO::PARAM_NULL;}
		  elseif(is_string($value)){ $param = PDO::PARAM_STR;}
		  else{ $param = FALSE;}
		//  $query->bindValue(':'. $key , trim(strip_tags(mysql_real_escape_string($value))), $param);
		if($key=='datefrom'){
			if($value!=''){
		      $query->bindValue(':'. $key , $value, $param);
			}
		}elseif($key=='dateto'){
			if($value!=''){
		      $query->bindValue(':'. $key , $value, $param);
			}
		}else{
			if(is_array($value)){
				$i = 0;
				foreach($args['where'][$key] as $value1){
				  $query->bindValue(':'. $key.'_'.$i , $value1, $param);
				  $i++;
				}
			}else{
			   $query->bindValue(':'. $key , $value, $param);
			 }
			}
	 }
	 }
	 $query->execute();
	 $result_row = $query->fetchAll();
	// $data = $query->debugDumpParams();
}
return 	$result_row;
}
function dbcustom($sql){
	if($this->DbConnection()){
	$query = $this->DbConnection->prepare($sql);	
	$query->execute();
	$result_row = $query->fetchAll();
	return 	$result_row;
	}else{
		return false;
	}
}

function dbSelect($args){
	return $this->select($args);
}
/*........................... END SELECT ....................................................................*/
private function update($args){
	 $table  = '';
	 $update = '';
	 $where  = '';
	 if(array_key_exists('table',$args)){ $table = $args['table'];}
	 if(array_key_exists('update',$args)){ foreach($args['update'] as $k => $v){ $update .= '`'. $k .'` = :'. $k .', '; } }
	 if(array_key_exists('where',$args)){ foreach($args['where'] as $nk => $nv){ $where .= '`'. $nk .'` = :'. $nk .', AND '; } }
	// trim(strip_tags(mysql_real_escape_string($k)))
	 
	 $sql = 'UPDATE `'. $table .'` SET '. rtrim($update,', ') .' WHERE '. rtrim($where,', AND ');
	 if($this->DbConnection()){
	 $query = $this->DbConnection->prepare($sql);
	 foreach($args['update'] as $key => $value){
		 $query->bindValue(':'.$key , $value, PDO::PARAM_STR);		 
	 }
	 foreach($args['where'] as $key => $value){
		 $query->bindValue(':'.$key , trim(strip_tags($value)), PDO::PARAM_STR);
	 }
	 $query->execute();
	 if($query) {
				  return true;
				}else{
					return false;			  
					}
	 }else{
	 return null;
	 }
	 
}
function db_update($args){
	return $this->update($args);
	}
// Delete	
private function delete($args){
	$table  = '';
	$where = '';
	if(array_key_exists('table',$args)){ $table = $args['table'];}
	if(array_key_exists('where',$args)){ foreach($args['where'] as $nk => $nv){ $where .= '`'. $nk .'` = :'. $nk .' AND '; } }
	$sql = 'DELETE FROM `'. $table .'` WHERE '. rtrim($where,' AND ');
	if($this->DbConnection()){
		$query = $this->DbConnection->prepare($sql);
		foreach($args['where'] as $key => $value){
		 $query->bindValue(':'.$key , trim(strip_tags($value)), PDO::PARAM_STR);
	    }
	 $query->execute();
	 if($query) {  return true; }else{ return false; }
	}else{
	 return null;
	 }
}
function db_delete($args){
	return $this->delete($args);
	}
}

function get_date($days){
	$today = date("Y-m-d"); 
	$date = date_create($today);
	date_sub($date, date_interval_create_from_date_string($days.' days'));
	return date_format($date, 'Y-m-d');
}
?>