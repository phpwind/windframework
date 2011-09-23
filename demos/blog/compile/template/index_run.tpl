<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>BLOG</title>
</head>
<body>
<h1>博客</h1>

<br />
<?php  if($info){ ?>
提示：
<span style="color: red"><?php echo WindSecurity::escapeHTML($info);?></span><br />

<?php  }if($status){ ?>
Hello, <?php echo WindSecurity::escapeHTML($username);?> 
|<a href="index.php?a=logOut">注销</a>
<?php  }else{
 ?>

<form name="<?php echo WindSecurity::escapeHTML($param['formName']);?>" action="<?php echo WindSecurity::escapeHTML($param['action']);?>" method="post">
<table>
<tr>
<td>
用户名：
</td>
<td>
<input type="text" name="username" />
</td>
</tr>
<tr>
<td>
密码：
</td>
<td>
<input type="password" name="password" /> 
</td>
</tr>
<tr align="center">
<td>
<input type="submit" value="<?php echo WindSecurity::escapeHTML($param['button']);?>" />
</td>
<td>
<?php  if(!$register){ ?>
<a href="index.php?register=1">我要去注册！</a>
<?php  }else{ ?>
<a href="index.php">我要去登录！</a>
<?php }?>
</td>
</tr>
</table>
</form>

<?php  } ?>
</body>
</html>