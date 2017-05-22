<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-20 14:43:47
 * Last modified by:    01sr
 * Last Modified time:  2017-05-21 15:24:04
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
	$pre_orders=array();
	$use_orders=array();
	$unix_now=time();
	$now=date("Y-m-d H:i:s",$unix_now);
	$sql="select preorders.order_id as order_id,"
	."preorders.parking_id as parking_id,"
	."preorders.carport_id as carport_id,"
	."preorders.start_time as start_time,"
	."preorders.end_time as end_time,"
	."parking_lots.preorder_price as preorder_price "
	."from preorders join parking_lots "
	."on preorders.parking_id=parking_lots.id "
	."where preorders.end_time<'$now'";
	$result=mysql_query($sql);
	if($result===false){
		$info='服务器错误 '.mysql_error();
	}else{
		while($row=mysql_fetch_assoc($result)){
			array_push($pre_orders, $row);
		}

		$sql="select use_info.order_id as order_id,"
		."use_info.parking_id as parking_id,"
		."use_info.carport_id as carport_id,"
		."use_info.preorder_start_time as preorder_start_time,"
		."use_info.use_start_time as use_start_time,"
		."use_info.end_time as end_time,"
		."parking_lots.preorder_price as preorder_price,"
		."parking_lots.use_price as use_price "
		."from use_info join parking_lots "
		."on use_info.parking_id=parking_lots.id "
		."where use_info.finished=0";
		$result=mysql_query($sql);
		if($result===false){
			$info='服务器错误 '.mysql_error();
		}else{
			while($row=mysql_fetch_assoc($result)){
				array_push($use_orders, $row);
			}

			$status='success';
			$info=array('preorders'=>$pre_orders,'uses'=>$use_orders);
		}
	}
}

$result=array('status'=>$status,'info'=>$info);
echo json_encode($result);
mysql_close();
?>