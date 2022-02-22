<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>系统错误提示!</title>
</head>
<body>
<div style="border:1px solid #9CF; margin:20px auto; width:800px;">
  <div style="border:1px solid #fff; padding:15px; background:#f0f6f9;">
    <div style="border-bottom:1px #9CC solid; font-size:26px;font-family: "Microsoft Yahei", Verdana, arial, sans-serif; line-height:40px; height:40px; font-weight:bold">系统错误提示!</div>
    <div style="height:20px; border-top:1px solid #fff"></div>
    <div style="border:1px dotted #F90; border-left:6px solid #F60; padding:15px; background:#FFC"> 出错信息：<?=$row["message"]?></div>
    <div style="border:1px dotted #F90; border-left:6px solid #F60; padding:15px; background:#FFC"> 出错文件：<?=$row["file"]?> </div>
    <div style="border:1px dotted #F90; border-left:6px solid #F60; padding:15px; background:#FFC"> 错误行：<?=$row["line"]?> </div>
    <div style="border:1px dotted #F90; border-left:6px solid #F60; padding:15px; background:#FFC"> 错误级别：<?=$row["level"]?> </div>
    <div style="border:1px dotted #F90; border-left:6px solid #F60; padding:15px; background:#FFC;line-height:20px;"> Trace信息：<br> <?=print_r($row["trace"],true)?>
    </div>
    <div style="height:20px;"></div>
    <div style=" font-size:15px;">您可以选择 &nbsp;&nbsp;<a href="" title="重试">重试</a> &nbsp;&nbsp;<a href="javascript:history.back()" title="返回">返回</a> </div>
  </div>
</div>
</body>
</html>