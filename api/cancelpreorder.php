<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-21 15:26:17
 * Last modified by:    01sr
 * Last Modified time:  2017-05-21 15:37:45
 */
header("Content_type:text/html;character=utf-8");
include_once 'config/conn.php';
include 'config/globalfunction.php';

$status='error';
$info='';
$user_id=$_POST['user_id'];
$token=$_POST['token'];
$order_id=$_POST['order_id'];

$info=Sharedp::checkToken($user_id,$token);
if($info=='ok'){
	$unix_now=time();
	$now=date("Y-m-d H:i:s",$unix_now);
	$sql="update preorders set end_time='$now' where order_id='$order_id'";
	$result=mysql_query($sql);
	echo var_dump($result);
	if($result){
		$status='success';
		$info='取消成功';
	}else{
		$info='取消失败';
	}
}

$result=array('status'=>$status,'info'=>$info);
echo json_encode($result);
mysql_close();
?>