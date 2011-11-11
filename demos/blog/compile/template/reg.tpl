<!doctype html>
<html>
<head>
<meta charset="utf8" />
<title>首页</title>
<link href="<?php echo WindSecurity::escapeHTML($_theme);?>/images/core.css" rel="stylesheet" />
<link href="<?php echo WindSecurity::escapeHTML($_theme);?>/images/css.css" rel="stylesheet" />
</head>
<body>
	<div class="wrap">
		<div id="header" class="mb10">
			<div class="header">
				<table width="100%"><tr>
					<td><h2 class="fl logo"><a href="<?php echo WindSecurity::escapeHTML(WindUrlHelper::createUrl('default/index/run'));?>"><img src="<?php echo WindSecurity::escapeHTML($_theme);?>/images/logo.png" width="198" height="80" class="fl" /></a></h2></td>
					<td align="right">
						<div class="login_header fr">
							<dl class="cc login_dlA">
								<dt></dt>
								<dd></dd>
								<dd>&nbsp;</dd>
							</dl>
							<dl class="cc login_dlA">
								<dt><?php echo WindSecurity::escapeHTML(Wind::getApp()->getResponse()->getData('index_run','userInfo','username'));?></dt>
							</dl>
						</div>
					</td>
				</tr></table>
				<div id="navA">
					<div class="navA">
						<ul class="cc">
							<li class="current"><a href="<?php echo WindSecurity::escapeHTML(WindUrlHelper::createUrl('default/index/run'));?>">首页</a></li>
							<li><a href="<?php echo WindSecurity::escapeHTML(WindUrlHelper::createUrl('default/index/run'));?>">关于本demo</a></li>
							<li class="tail">&nbsp;</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="main">
			<div class="grid cc mb10">
	<div class="bA">
		<div class="content">
<?php if(isset($userInfo)):?>
			<div class="mb10 f16 b">恭喜您,注册成功!</div>
			<div class="mb10 f14">&rsaquo;&rsaquo;<a href="<?php echo WindSecurity::escapeHTML(WindUrlHelper::createUrl('run'));?>">返回登录</a></div>
<?php else:?>
			<div class="mb10 f16 b">用户注册</div>
			<form name="reg" action="<?php echo WindSecurity::escapeHTML(WindUrlHelper::createUrl('dreg'));?>" method="post">
			<dl class="mb10">
				<dt>姓名<span class="s1">*</span></dt>
				<dd><input name="username" type="text" class="input" ></dd>
			</dl>
			<dl class="mb10">
				<dt>密码<span class="s1">*</span></dt>
				<dd><input name="password" type="text" class="input"></dd>
			</dl>
			<span class="btn" style="margin:0 0;"><span><button type="submit">提 交</button></span></span>
			<span class="bt" style="margin:0 0;"><span><button type="button"  onclick="javascript:document.location.href='<?php echo WindSecurity::escapeHTML(WindUrlHelper::createUrl('run'));?>';">返回登录</button></span></span>
			</form>
<?php endif;?>
		</div>
	</div>
</div>
		</div>
<div id="footer">
			<div class="footer">
				<p class="f10">Powered by phpwind windframework group &copy;2003-2103 http://www.windframework.com</p>
			</div>
		</div>
	</div>
</body>
</html>	