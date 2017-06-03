<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-21 14:23:39
 * Last modified by:    01Sr
 * Last Modified time:  2017-05-31 17:40:48
 */
header("Content_type:text/html;character=utf-8");
include_once 'config/conn.php';
include 'config/globalfunction.php';

$status='error';
$info='';
$user_id=$_POST['user_id'];
$token=$_POST['token'];

$info=Sharedp::checkToken($user_id,$token);
if($info=='ok'){
	$sql="select *from parking_lots";
	$result=mysql_query($sql);
	if($result===false){
		$info='服务器错误 '.mysql_error();
	}else{
		$status='success';
		$info=array();
		while($row=mysql_fetch_assoc($result)){
			array_push($info, $row);
		}
	}
}

$result=array('status'=>$status,'info'=>$info);
echo json_encode($result);
mysql_close();
?>