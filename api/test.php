<?php
include 'resource/var.php';
function unlock($ip,$port,$type,$id){
	$bin=pack("CN",$type,1);
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
if(unlock($ip,$port,$UNLOCK,1)==0x01){
	echo 'true';
}else{
	echo 'false';
}
?>