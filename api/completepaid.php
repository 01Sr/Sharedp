<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-21 15:43:01
 * Last modified by:    01sr
 * Last Modified time:  2017-05-21 16:07:36
 */
header("Content_type:text/html;character=utf-8");
include_once 'config/conn.php';
include 'config/globalfunction.php';

$status='error';
$info='';
$user_id=$_POST['user_id'];
$token=$_POST['token'];
//json_decode(string,[bool])，默认解析为对象,第二个参数为true时解析为数组
$orders=json_decode($_POST['orders'],true);
$info=Sharedp::checkToken($user_id,$token);
if($info=='ok'){
	$preorders=implode(",", $orders['preorders']);
	$sql="delete from preorders where find_in_set(order_id,$preorders)";
	$result=mysql_query($sql);
	if($result){
		$uses=implode(",", $orders['uses']);
		$sql="delete from use_info where find_in_set(order_id,$preorders)";
		$result=mysql_query($sql);
		if($result){
			$status='success';
			$info='付款成功';
		}else{
			$info='付款失败';
		}
	}else{
		$info='付款失败';
	}
}

$result=array('status'=>$status,'$info'=>$info);
echo json_encode($result);
mysql_close();
?>