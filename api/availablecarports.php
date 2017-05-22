<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-21 14:49:24
 * Last modified by:    01sr
 * Last Modified time:  2017-05-21 15:10:56
 */
header("Content_type:text/html;character=utf-8");
include_once 'config/conn.php';
include 'config/globalfunction.php';

$status='';
$info='';
$user_id=$_POST['user_id'];
$token=$_POST['token'];
$parking_id=$_POST['parking_id'];
$all=array();
$used=array();
$available=array();

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
				if($num<0){
					$num=0;
				}
				$statu='success';
				$info=array('num'=>$num,'available'=>$available);
			}
		}
	}
}

$result=array('status'=>$status,'info'=>$info);
echo json_encode($result);

mysql_close();
?>