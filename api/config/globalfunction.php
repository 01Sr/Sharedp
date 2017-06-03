<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-20 10:45:22
 * Last modified by:    01Sr
 * Last Modified time:  2017-06-03 21:18:10
 */
header("Content_type:text/html;character=utf-8");
include_once 'conn.php';

/**
* 全局函数
*/
class Sharedp
{
	public static function checkToken($user_id,$token){
		$sql="select token from user_auths where user_id=$user_id";
		$result=mysql_query($sql);
		$info='';
		if($result===false){
			$info='服务器错误 '.mysql_error();
		}else{
			if($row=mysql_fetch_assoc($result)){
				if($token==$row['token']){
					$info='ok'; 
				}else{
					$info='用户未登录';
				}
			}else{
				$info='用户未注册';
			}
		}
		return $info;
	}

	public static function generateOrderId($time,$type,$user_id){
		$t=date('YmdHis',$time);
		$uid=sprintf('%04s',$user_id);
		return $t.$type.$uid;
	}
}
?>