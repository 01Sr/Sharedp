<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-18 11:17:22
 * Last modified by:    01sr
 * Last Modified time:  2017-05-19 10:51:09
 */
header("Content_type:text/html;character=utf-8");
include 'config/conn.php';
require_once 'lib/Ucpaas.class.php';
require_once 'lib/RandChar.class.php';

$status="error";
$info="";
$identity_type=$_POST['identity_type'];
$identifier=$_POST['identifier'];
if($identity_type=='phone'){
	$options['accountsid'] = '08c54316db2881df3d7574bce79df815';
	$options['token'] = 'abf50699ffce2fbef2b80db0f957a536';
	$ucpaas = new Ucpaas($options);
	$ucpaas->getDevinfo('xml');
	$appId = "8e2ff936755544cb9d5334b7bffbbce8";
	if ($identifier) {
		//生成验证码
		$randCharObj = new RandChar();
		$verifycode=$randCharObj->getRandChar(4);
		$time=time();//Unix时间戳
		$code=$time.';'.$verifycode;

		$sql="select id from user_auths where identity_type='$identity_type' and identifier='$identifier'";
		$result=mysql_query($sql);
		if(!mysql_fetch_assoc($result)){
			//注册信息不存在，创建

			// 调用存储过程获取user_id,并发过程中可能会导致生成的id和取出的id 不是同一个，产生数据错误，目前想法是@user_id+随机数产生一个变量名，这样变量名在并发操作时是不同的，保证一次操作的唯一性
			$r=rand(0,1000);
			$value='@user_id'.$r;
			$key_name="user_id";
			$sql="call get_key('$key_name',$value)";
			mysql_query($sql);
			$result=mysql_query("select $value");
			if($result=mysql_fetch_assoc($result)){
				//插入数据
				mysql_query('begin');
			 	$user_id=$result[$value];
			 	$sql="insert into users values($user_id,'','')";
			 	$re1=mysql_query($sql);
			 	if(!$re1) $info="服务器错误 ".mysql_error();
			 	$sql="insert into user_auths values(0,$user_id,'$identity_type','$identifier','','','$code')";
			 	$re2=mysql_query($sql);
			 	if(!$re2) $info="服务器错误 ".mysql_error();
			 	if($re1&&$re2){
			 		mysql_query('commit');
			 		//发送短信验证码
			 		$templateId = "52282";
			 		$ucpaas->templateSMS($appId, $identifier, $templateId, $verifycode);
			 		$status="success";
			 		$info="验证码发送成功";
			 	}else{
			 		mysql_query('rollback');
			 	}
			 	mysql_query('end');
			}else{
				$info="服务器错误 ".mysql_error();
			}
		}else{
			//信息已存在更新
			$sql="update user_auths set verifycode='$code' where identity_type='$identity_type' and identifier='$identifier'";
			$result=mysql_query($sql);
			if($result){
				$templateId = "52282";
				$ucpaas->templateSMS($appId, $identifier, $templateId, $verifycode);
				$status="success";
				$info="验证码发送成功";
			}else{
				$info="服务器错误 ".mysql_error();
			}

		}
	}else{
		$info="手机号不能为空";
	}
}

$result=array('status'=>$status,'info'=>$info);
echo json_encode($result);

mysql_close();

?>