<!doctype html>
<html>
<head>
<title><?php echo WindSecurity::escapeHTML(Wind::getApp()->getResponse()->getData('G','title'));?></title>
<style>
body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,form,p,blockquote,th,td {
	margin: 0;
	padding: 0;
}

ul,li {
	list-style: none;
}

table {
	border-collapse: collapse;
	border-spacing: 0;
}

th {
	text-align: left;
}

td,th,div {
	word-break: break-all;
	word-wrap: break-word;
}

form {
	display: inline;
}

* {
	outline: none
}

img {
	border: 0;
}

em,cite {
	font-style: normal;
}

blockquote {
	quotes: none;
}

html {
	font-size: 12px;
}

body {
	font: 12px/1.5 Verdana;
	color: #333;
	background: #f3f3f3;
}

.wrap {
	width: 80%;
	margin: 50px auto;
	border: 1px solid #ccc;
	background: #fff;
}

h1 {
	border-bottom: 1px solid #ccc;
	line-height: 40px;
	height: 40px;
	padding: 0 20px;
	margin: 0;
	font-size: 16px;
	background: #f7f7f7;
	background: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#f3f3f3) );
	background: -moz-linear-gradient(top, #ffffff, #f3f3f3);
	filter: progid :     DXImageTransform.Microsoft.gradient (     startColorstr =     '#ffffff',
		endColorstr =  
		  '#f3f3f3' );
}

h1 span {
	color: #dd5925;
}

.main {
	padding: 20px;
	line-height: 1.8;
}

h2 {
	color: #dd5925;
	font-size: 14px;
	background: #f9eeea;
	margin-bottom: 10px;
	line-height: 25px;
	padding: 0 10px;
}

h3 {
	padding-left: 24px;
	background: #f7f7f7;
	font-weight: 100;
	font-size: 12px;
	margin-bottom: 10px;
}

ul {
	padding-left: 48px;
	margin-bottom: 10px;
}

ul li:hover {
	background: #f3f3f3;
}

.err {
	color: #ff0000;
	background: #faf6f4;
}

.tips {
	padding: 5px 10px;
	background: #ffffe3;
	border: 1px solid #cccccc;
	line-height: 20px;
	color: #333;
}
</style>

</head>
<body>
<div class="wrap">
<h1><?php echo WindSecurity::escapeHTML($errorHeader);?>:</h1>
<div class="main">
<ul>
	<?php foreach ($errors as $key => $error):
		$key = $key + 1;
		if(!WIND_DEBUG)
			$error = str_replace(Wind::getRootPath(Wind::getAppName()), '~/', $error);?>
	<li><?php echo WindSecurity::escapeHTML($key);?>. <?php echo WindSecurity::escapeHTML($error);?></li>
	<?php endforeach;?>
</ul>
<h2 class="f14 b">You Can Get Help In:</h2>
<p><?php echo "The server encountered an internal error and failed to process your request. 
Please try again later. If this error is temporary, reloading the page might resolve the problem.
\nIf you are able to contact the administrator report this error message.
(" . Wind::getApp()->getConfig('siteInfo', '', "http://www.windframework.com/") . ")" ; ?></p>
</div>
</div>
</body>
</html>