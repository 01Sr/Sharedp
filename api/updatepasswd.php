<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-19 09:27:29
 * Last modified by:    01sr
 * Last Modified time:  2017-05-19 09:44:01
 */
header("Content_type:text/html;character=utf-8");
include 'config/conn.php';

$status='error';
$info='';
$identity_type=$_POST['identity_type'];
$identifier=$_POST['identifier'];
$credential=$_POST['credential'];
$verifycode=$_POST['verifycode'];

$sql="select id,user_id,credential,verifycode from user_auths where identity_type='$identity_type' and identifier='$identifier'";
$result=mysql_query($sql);
if($arr=mysql_fetch_assoc($result)){
	$sqlcd=$arr['credential'];
	if($sqlcd!=''){
		$sqltvc=$arr['verifycode'];
		//split支持正则，explode不支持所以更快
		$tmp=explode(';', $sqltvc);
		$before=$tmp[0];
		$sqlvc=$tmp[1];
		$now=time();
		if($now-(int)$before<=180){
			if($sqlvc==$verifycode){
				$user_id=$arr['user_id'];
				$sql="update user_auths set credential='$credential' where user_id=$user_id and (identity_type='phone' or identity_type='email') ";
				$re=mysql_query($sql);
				if($re){
					$status='success';
					$info='密码更新成功';
				}else{
					$info='服务器错误 '.mysql_error();
				}
			}
		}else{
			$info='验证码已过期';
		}
	}else{
		$info='用户未注册';
	}

}else{
	$info='验证码未获取';
}

$result = array('status' =>$status ,'info'=>$info );
echo json_encode($result);
mysql_close();
?>