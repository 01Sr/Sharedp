<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-22 12:41:58
 * Last modified by:    01sr
 * Last Modified time:  2017-05-22 13:45:17
 */
header("Content_type:text/html;character=utf-8");
// include_once 'config/conn.php';
include 'config/globalfunction.php';
include_once 'resource/var.php';

$status='error';
$info='';
$user_id=$_POST['user_id'];
$token=$_POST['token'];
$avatar=$_FILES["avatar"]["tmp_name"];
$info=Sharedp::checkToken($user_id,$token);
if($info=='ok'){
echo var_dump($_FILES);
	$error=$_FILES['avatar']['error'];
	if($error>0){
		$info='上传错误 '.$error;
	}else{
		$des=$avatar_path.'1'.'.png';
		$result=move_uploaded_file($_FILES['avatar']['tmp_name'], $des);
		if($result){
			$sql="update users set avatar='$des' where id=$user_id";
			$result=mysql_query($sql);
			if($result){
				$status='success';
				$info=$des;
			}else{
				unlink($des);
				$info='服务器错误 '.mysql_error();
			}

		}else{
			$info='上传错误';
		}

	}
}
$result=array('status'=>$status,'info'=>$info);
echo json_encode($result);
mysql_close();
?>