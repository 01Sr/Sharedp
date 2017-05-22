<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-19 09:55:28
 * Last modified by:    01sr
 * Last Modified time:  2017-05-19 11:25:25
 */
header("Content_type:text/html;character=utf-8");
include 'config/conn.php';
require_once 'lib/RandChar.class.php';

$status='error';
$info='';
$identity_type=$_POST['identity_type'];
$identifier=$_POST['identifier'];
$credential=$_POST['credential'];

$sql="select id,user_id from user_auths where identity_type='$identity_type' and identifier='$identifier' and credential='$credential'";
$result=mysql_query($sql);
if($result===false){
	$info='服务器错误 '.mysql_error();
}else{
	if($result=mysql_fetch_assoc($result)){
		$id=$result['id'];
		$user_id=$result['user_id'];
		$sql="select nickname,avatar from users where id=$user_id";
		$re=mysql_query($sql);
		if($re===false){
			$info='服务器错误 '.mysql_error();
		}else{
			if($re=mysql_fetch_assoc($re)){
				$nickname=$re['nickname'];
				$avatar=$re['avatar'];

				$rand=new RandChar();
				//token 10位
				$token=$rand->getRandChar(10);
				$sql="update user_auths set token='$token' where id=$id";
				$re1=mysql_query($sql);
				if($re1){
					$status='success';
					$info=array('user_id'=>$user_id,'nickname'=>$nickname,'avatar'=>$avatar,'identity_type'=>$identity_type,'identifier'=>$identifier,'token'=>$token);
				}else{
					$info='服务器错误 '.mysql_error();
				}

			}else{
				$info='用户未注册';
			}
		}
	}else{
		$info='账户或密码错误';
	}
}

$result=array('status'=>$status,'info'=>$info);
echo json_encode($result);
mysql_close();

?>