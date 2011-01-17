@Rem: 本批命令用来计算某个路径下的总文件个数，及总行数。行数的计算不算空行
@Rem: author: 肖肖 xiaoxia.xuxx@alibaba-inc.com
@echo ****************************************************************
@echo off
@echo 使用说明：
@echo 1、更改要计算的路径：将filePath的路径更改为自己的需要的路径。
@echo 2、更改统计的文件后缀，如果需要统计所有文件则将fileExt设置为*,如果只需要统计php文件则将该变量指定为*.php
@echo ****************************************************************

@set fileExt=*.php
@set filePath=D:\PHPAPP\phpwindframework\_tests\core

@setlocal enabledelayedexpansion
@set filenum=0
@set totalnum=0
@for /r %filePath% %%i in (%fileExt%) do (
	@set linenum=0 & @set /a filenum+=1 & @echo %%i & (@for /f "usebackq" %%b in (%%i) do @set /a linenum+=1) & @echo 行数：!linenum! & @set /a totalnum+=linenum)

@echo 总行数: %totalnum%行
@echo 总文件数: %filenum%
@pause