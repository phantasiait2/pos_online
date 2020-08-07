<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title id="printTitle"></title>
<head>
<style>
    @page{margin:1.2cm 0 0 0;size:A4;}
    html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,font,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td{margin:0;padding:0;font-size:100%;;}

    </style>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pos_order.js"></script>
<script type="text/javascript" src="/javascript/pos.js"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
		
		print_out(<?=$orderID?>,0,1 ,'shipment');
		
		
})
</script>
</head>
<body style="width: 19.8cm;">
<div class="product" style="width:100% ">

</div>

<div style="clear:both"></div>

<div id="invoiceFrame">沒有發票資訊，請與本公司會計聯繫</div>
<div id="invoiceFrame_c"></div>






</body>
</html>