<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>
<link rel="stylesheet" type="text/css" href="/style/pos.css?data=20180709" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

<script type="text/javascript" src="/javascript/pos_order.js"></script>
<script type="text/javascript" src="/javascript/pos.js?date=20160414"></script>



<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
	if(confirm('是否匯出託運表格?'))$('#exportOut').click();
		
		
})
    </script>

</head>
<body>

<div class="mainContent" style="width:1100px; ">
<input type="button" id="exportOut" value="匯出excel" onclick="location.href='<?php echo $download;?>'" >
<table border="1">
    <?php foreach ($excelData as $row  ):?>
   
        <?php foreach ($row['sheet'] as $col):?>
            <tr>
            <?php foreach ($col as $item):?>
            <td><?php echo  $item;?></td>
            <?php endforeach;?>
             </tr>
        <?php endforeach;?>
   
    
    <?php endforeach;?>
</table>
</div>
<div style="clear:both"></div>
</body>
</html>


