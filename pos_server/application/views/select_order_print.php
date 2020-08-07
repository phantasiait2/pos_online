<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
     @page{margin:1.2cm 0 0 0.5cm;size:A4;}
   
    html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,font,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td{margin:0;padding:0;font-size:100%;;}

    </style>
<head>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pos_order.js?t=4"></script>

<script type="text/javascript">
$(document).ready(function()
{
	
		selectOrder('<?=$IDList?>','<?=$showType?>')
		
})
</script>
</head>
<body style="width: 19.8cm;">

<div class="mainContent" style="width:100%; ">
<input type="button" value="go" onclick="selectOrder('<?=$IDList?>');">
</div>
<div style="clear:both"></div>
</body>
</html>