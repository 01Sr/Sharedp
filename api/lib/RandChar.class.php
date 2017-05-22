<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-19 10:44:05
 * Last modified by:    01sr
 * Last Modified time:  2017-05-19 10:49:26
 */
header("Content_type:text/html;character=utf-8");

// 生成任意位随机数
class RandChar {
	function getRandChar($length) {
		$str = null;
		$strPol="0123456789";
		$max = strlen($strPol) - 1;
		for ($i = 0; $i < $length; $i++) {
			$str .= $strPol[rand(0, $max)]; //rand($min,$max)生成介于min和max两个数之间的一个随机整数
		}
		return $str;
	}
}
?>