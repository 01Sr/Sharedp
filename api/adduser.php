<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-18 17:31:40
 * Last modified by:    01sr
 * Last Modified time:  2017-05-22 12:50:04
 */
header("Content_type:text/html;character=utf-8");
include 'config/conn.php';

$status='error';
$info='';
$nickname=$_POST['nickname'];
$identity_type=$_POST['identity_type'];
$identifier=$_POST['identifier'];
$credential=$_POST['credential'];
$verifycode=$_POST['verifycode'];

$sql="select id,user_id,credential,verifycode from user_auths where identity_type='$identity_type' and identifier='$identifier'";
$result=mysql_query($sql);
if($arr=mysql_fetch_assoc($result)){
	$sqlcd=$arr['credential'];
	if($sqlcd==''){
		$sqltvc=$arr['verifycode'];
		//split支持正则，explode不支持所以更快
		$tmp=explode(';', $sqltvc);
		$before=$tmp[0];
		$sqlvc=$tmp[1];
		$now=time();
		if($now-(int)$before<=180){
			if($sqlvc==$verifycode){
				$id=$arr['id'];
				$user_id=$arr['user_id'];
				mysql_query('begin');
				$avatar=$avatar_path."default.png";
				$sql="update users set nickname='$nickname',avatar='$avatar' where id=$user_id";
				$re1=mysql_query($sql);
				if(!$re1) $info="服务器错误 ".mysql_error();
				$sql="update user_auths set credential='$credential' where id=$id";
				$re2=mysql_query($sql);
				if(!$re2) $info="服务器错误 ".mysql_error();
				if($re1&&$re2){
					$status='success';
					$info='注册成功';
					mysql_query('commit');
				}else{
					mysql_query('rollback');
				}
				mysql_query('end');
			}
		}else{
			$info='验证码已过期';
		}
	}else{
		$info='用户已注册';
	}

}else{
	$info='验证码未获取';
}

$result = array('status' =>$status ,'info'=>$info );
echo json_encode($result);
mysql_close();
?>