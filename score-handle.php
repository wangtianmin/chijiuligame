<?php
	require_once"common.php";
	$score = $_GET["score"];
	$openid = $_GET["openid"];
	$username = $_GET["username"];
	$headimg = $_GET["headimg"];
	$sql = "SELECT * FROM wang_userinfo WHERE openid = '{$openid}'";
	$result = mysql_query($sql);
	if(mysql_num_rows($result)>0){
		//有
		$row = mysql_fetch_assoc($result);
		if($row["score"]<$score){
			//更新
			$sql = "UPDATE wang_userinfo set score='{$score}' WHERE openid = '{$openid}'";
			$result = mysql_query($sql);
			if(mysql_affected_rows()>0){
				echo '{"err":"0","msg":"更新成功"}';
			}else{
				echo '{"err":"1","msg":"更新失败"}';
			}
		}else{
			//不更新
			echo '{"err":"0","msg":"没超过分数"}';
		}
	}else{
		//没有
		$sql = "INSERT INTO wang_userinfo (id,openid,username,headimg,score) VALUE (NULL,'{$openid}','{$username}','{$headimg}','{$score}')";
		$result = mysql_query($sql);
		if(mysql_insert_id()>0){
			echo '{"err":"0","msg":"插入成功"}';
		}else{
			echo '{"err":"0","msg":"插入失败"}';
		}
	}
?>