<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title id="printTitle"></title>
<style>
    .header_ZHName{
        width: 200px
        
        
    }
      .header_ENGName{
        width: 200px;
     
    }
        .header_orderComment{
        width: 50px;
            font-size: 8pt
            
     
    }
    
    
    </style>
<head>
<style>
  @page{padding:1.2cm 0 0 1cm;size:A4;}
    html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,font,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td{margin:0;padding:0;font-size:100%;;}

    </style>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pos_order.js?t=9"></script>
<script type="text/javascript" src="/javascript/pos.js"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
		<?php if($type=="order"):?>
		 print_order(<?=$orderID?>);
		 <?php elseif($type=="boxCheck") :?>
            print_box_cehck()
    
        <?php else:?>
		print_out(<?=$orderID?>,<?=$showing?>,<?=$price?> ,'<?=$type?>');
		<?php endif?>
		
})
</script>
</head>
<body style="width: 19.8cm;padding-left:0.5cm;padding-top:0.5cm">
<div class="product" style="width:100% ">

</div>

<div style="clear:both"></div>






</body>
</html>