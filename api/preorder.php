<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-19 12:24:03
 * Last modified by:    01Sr
 * Last Modified time:  2017-06-03 21:37:17
 */

/*
mysql 的datetime数据类型是以字符串方式存储日期的格式是"Y-m-d H:i:s"
PHP time()获取当前unix 时间戳，数据类型为整形
strtotime 将英文文本日期时间解析为 Unix 时间戳：
*/
header("Content_type:text/html;character=utf-8");
include_once 'config/conn.php';
include 'config/globalfunction.php';
include_once 'resource/var.php';

$status='error';
$info='';
$user_id=$_POST['user_id'];
$parking_id=$_POST['parking_id'];
$token=$_POST['token'];

//获取时间段内可用车位 $available=array_diff($all,$unavailable);
$all=array();
$used=array();
$available=null;
$num=0;
$carport_id=-1;

$info=Sharedp::checkToken($user_id,$token);
if($info=='ok'){
	$unix_now=time();
	$now=date("Y-m-d H:i:s",$unix_now);
	$sql="select id from carports where parking_id=$parking_id";
	$result=mysql_query($sql);
	if($result===false){
		$info='服务器错误 '.mysql_error();
	}else{
		while($row=mysql_fetch_assoc($result)){
			array_push($all, $row['id']);
		}
		$sql="select carport_id from preorders where end_time>='$now' and parking_id=$parking_id";
		$result=mysql_query($sql);
		if($result===false){
			$info='服务器错误 '.mysql_error();
		}else{
			while($row=mysql_fetch_assoc($result)){
				array_push($used, $row['carport_id']);
			}
			$sql="select carport_id from use_info where finished=0 and parking_id=$parking_id";
			$result=mysql_query($sql);
			if($result===false){
				$info='服务器错误 '.mysql_error();
			}else{
				while($row=mysql_fetch_assoc($result)){
					array_push($used, $row['carport_id']);
				}
				
					// array_diff()		比较数组，返回差集（只比较键值）。
					// array_diff_assoc()	比较数组，返回差集（比较键名和键值）。
					// array_diff_key()	比较数组，返回差集（只比较键名）。
				
				$available=array_diff($all, $used);
				$num=count($available);
				if($num>0){
					//array_diff后数组下标不一定从0开始连续，所以arr[0]索引第一个元素是错误的，需要使用current()
					$carport_id=current($available);
					$order_id=Sharedp::generateOrderId($unix_now,$order_type_pre,$user_id);
					$end_time=date('Y-m-d H:i:s',$unix_now+$preorder_duration*60*60);
					$sql="insert into preorders values('$order_id',$user_id,$parking_id,$carport_id,'$now','$end_time')";
					// echo $sql;
					$result=mysql_query($sql);
					if($result){
						$status='success';
						$info=array('order_id'=>$order_id,'user_id'=>$user_id,'parking_id'=>$parking_id,'carport_id'=>$carport_id,'start_time'=>$now,'duration'=>$preorder_duration);

					}else{
						$info='服务器错误 '.mysql_error();
					}
				}else{
					$info='无可用车位';
				}
			}
		}
	}
}

$result=array('status'=>$status,'info'=>$info);
echo json_encode($result);

mysql_close();

?>