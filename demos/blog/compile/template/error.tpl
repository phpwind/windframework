<!doctype html>
<html>
<head>
<meta charset="utf8" />
<title>首页</title>
<link href="http://t4.pw.com:8080/wind/demos/blog/static/images/core.css" rel="stylesheet" />
<link href="http://t4.pw.com:8080/wind/demos/blog/static/images/css.css" rel="stylesheet" />
</head>
<body>
	<div class="wrap">
		<div id="header" class="mb10">
			<div class="header">
				<table width="100%"><tr>
					<td><h2 class="fl logo"><a href="http://t4.pw.com:8080/wind/demos/blog/index.php?m=error&amp;c=ErrorController&amp;a=run&amp;"><img src="http://t4.pw.com:8080/wind/demos/blog/static/images/logo.png" width="198" height="80" class="fl" /></a></h2></td>
					<td align="right">
						<div class="login_header fr">
							<dl class="cc login_dlA">
								<dt></dt>
								<dd></dd>
								<dd>&nbsp;</dd>
							</dl>
							<dl class="cc login_dlA">
								<dt></dt>
							</dl>
						</div>
					</td>
				</tr></table>
				<div id="navA">
					<div class="navA">
						<ul class="cc">
							<li class="current"><a href="http://t4.pw.com:8080/wind/demos/blog/index.php?m=error&amp;c=ErrorController&amp;a=run&amp;">首页</a></li>
							<li><a href="http://t4.pw.com:8080/wind/demos/blog/index.php?m=error&amp;c=ErrorController&amp;a=run&amp;">关于本demo</a></li>
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
<ul>
	<?php foreach ($errors as $key => $error):
		$key = $key + 1;
		if(!WIND_DEBUG)
			$error = str_replace(Wind::getRootPath(Wind::getAppName()), '~/', $error);?>
	<li><?php echo WindSecurity::escapeHTML($key);?>. <?php echo WindSecurity::escapeHTML($error);?></li>
	<?php endforeach;?>
</ul>
<br>
<div class="mb10 f14"><a href='<?php echo WindSecurity::escapeHTML($baseUrl);?>'>&rsaquo;&rsaquo;返回首页</a></div>
<h2 class="f14 b">You Can Get Help In:</h2>
<p><?php echo "The server encountered an internal error and failed to process your request. 
Please try again later. If this error is temporary, reloading the page might resolve the problem.
\nIf you are able to contact the administrator report this error message.
(" . Wind::getApp()->getConfig('siteInfo', '', "http://www.windframework.com/") . ")" ; ?></p>

		</div>
	</div>
</div>		</div>
<div id="footer">
			<div class="footer">
				<p class="f10">Powered by phpwind windframework group &copy;2003-2103 http://www.windframework.com</p>
			</div>
		</div>
	</div>
</body>
</html>	