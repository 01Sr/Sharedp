<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-20 13:41:05
 * Last modified by:    01sr
 * Last Modified time:  2017-05-27 11:58:54
 */
header("Content_type:text/html;character=utf-8");
include_once 'config/conn.php';
include 'config/globalfunction.php';
include 'resource/var.php';

$status='error';
$info='';
$user_id=$_POST['user_id'];
$parking_id=$_POST['parking_id'];
$carport_id=$_POST['carport_id'];
$token=$_POST['token'];

$info=Sharedp::checkToken($user_id,$token);
if($info=='ok'){
	$unix_now=time();
	$now=date("Y-m-d H:i:s",$unix_now);
	$sql="select order_id,user_id,start_time from preorders where end_time>='$now' and  parking_id=$parking_id and carport_id=$carport_id";
	$result=mysql_query($sql);
	if($result===false){
		$info='服务器错误 '.mysql_error();
	}else{
		if($row=mysql_fetch_assoc($result)){
			//被预定
			if($user_id==$row['user_id']){


				/*
				向车位加锁设备发送开锁信息
				*/
				if(($re=unlock($ip,$port,$UNLOCK,$carport_id))==0x01){
					$unix_now=time();
					$now=date("Y-m-d H:i:s",$unix_now);
					$order_id=$row['order_id'];
					$order_id=substr_replace($order_id, $order_type_use, 8,1);
					$preorder_start_time=$row['start_time'];
					mysql_query("begin");
					$sql="delete from preorders where order_id='$order_id'";
					$re1=mysql_query($sql);
					if(!$re1) $info='服务器错误 '.mysql_error();
					$sql="insert into use_info values('$order_id',$user_id,$parking_id,$carport_id,'$preorder_start_time','$now','$now',0)";
					$re2=mysql_query($sql);
					if(!$re2) $info='服务器错误 '.mysql_error();
					if($re1&&$re2){
						mysql_query("commit");
						$status='success';
						$info=array('order_id'=>$order_id,'user_id'=>$user_id,'parking_id'=>$parking_id,'carport_id'=>$carport_id,'preorder_start_time'=>$preorder_start_time,'use_start_time'=>$now);
					}else{
						mysql_query("rollback");
					}
					mysql_query("end");
				}else{
					$info='服务器错误 '.$re;
				}

			}else{
				$info='车位已被预定';
			}
		}else{
			//未被预定
			/*
			向车位加锁设备发送开锁信息
			*/
			if(($re=unlock($ip,$port,$UNLOCK,$carport_id))==0x01){
				$unix_now=time();
				$start_time=date("Y-m-d H:i:s",$unix_now);
				$order_id=Sharedp::generateOrderId($unix_now,$order_type_use,$user_id);
				$sql="insert into use_info values('$order_id',$user_id,$parking_id,$carport_id,'$start_time','$start_time','$start_time',0)";
				$result=mysql_query($sql);
				if($result){
					$status='success';
					$info=array('order_id'=>$order_id,'user_id'=>$user_id,'parking_id'=>$parking_id,'carport_id'=>$carport_id,'preorder_start_time'=>$now,'use_start_time'=>$now);
				}else{
					$info='服务器错误 '.mysql_error();
				}
			}else{
				$info='服务器错误 '.$re;
			}
		}
	}
}

function unlock($ip,$port,$type,$id){
	$bin=pack("CN",$UNLOCK,$id);
	$socket=socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if($socket<0){
		return socket_strerror($socket);
	}
	$re=socket_connect($socket,$ip,$port);
	if($re<0){
		return $re;
	}
	if(!socket_write($socket,$bin,strlen($bin))){
		return socket_strerror($socket);
	}
	$re=socket_read($socket,1024);
	return unpack('C',$re)[1];
}

$result=array('status'=>$status,'info'=>$info);
echo json_encode($result);

mysql_close();
?>