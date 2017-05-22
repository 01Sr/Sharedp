<?php
/**
 * Author: 				01sr
 * Email:				chenbing8914@outlook.com
 * Date:   				2017-05-18 10:41:27
 * Last modified by:    01sr
 * Last Modified time:  2017-05-18 17:37:46
 */
header("Content_type:text/html;character=utf-8");
$conn=@mysql_connect("localhost","root","520520fzy");
if(!$conn) die("连接数据库失败".mysql_error());
mysql_select_db("sharedp");
mysql_query("set character set 'utf8'");
mysql_query("set names 'utf8'");

?>