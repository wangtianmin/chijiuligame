<?php
	require_once "common.php";
	
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
	<title>Document</title>
</head>
<body>
	<table border="1">
		<tr>
			<td>名次</td>
			<td>用户名</td>
			<td>头像</td>
			<td>分数</td>
		</tr>
		<?php
			$sql = "SELECT * FROM wang_userinfo ORDER BY score DESC";
			$result = mysql_query($sql);
			$num=0;
			while($arr=mysql_fetch_assoc($result)){
				$num++;
		?>
		<tr>
			<td><?php echo $num; ?></td>
			<td><?php echo $arr["username"]; ?></td>
			<td><img width="50" src="<?php echo $arr["headimg"] ?>" alt=""></td>
			<td><?php echo $arr["score"]; ?></td>
		</tr>
		<?php
			}
		 ?>
	</table>
</body>
</html>