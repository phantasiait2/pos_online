<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php if(isset($css))echo $css;?>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/reject_enter_key.js"></script>
<script type="text/javascript" src="/javascript/pos.js"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script type="application/javascript" src="/javascript/jquery.progressbar.js"/></script>
<script type="application/javascript">
$(document).ready(function(){
//	$("#rentbar").progressBar(0, {showText: true });	
	 caculate()
})


function caculate()
{

	var result = parseInt($('#profit').html());
	result -= parseInt($('#rent').val());
	
	result -= parseInt($('#waterPower').val());
	result -= parseInt($('#salary1').val());
	result -= parseInt($('#salary2').val());
	result -= parseInt($('#salary3').val());
	result -= parseInt($('#salary4').val());
$('.monExpenses').each(
	function()
	{
		
		if($('#'+this.id).is(":checked"))
		{
		
			result -= parseInt($('#'+this.id.substr(6)).html());
			
		}
		
	}
	
	
	
)

$('#net').html(result);
$('#divide').html(result*0.4);



}
</script>


</head>
<body >
<div class="webFrame" style="background:#FFF; padding:20px;">
<h1>瘋桌遊益智遊戲專賣店分紅試算表：</h1>
<table border="1px" >
    <tr><td >本月毛利</td></tr>
    <tr><td id="profit"><?=$monVerify?></td></tr>
    <tr><td>房租</td></tr>
    <tr><td><input type="text"  width="150px" id="rent" onchange="caculate()"></td></tr>
    <tr><td>水電</td></tr>
    <tr><td><input type="text"  width="150px" id="waterPower" onchange="caculate()"></td></tr>
    <tr><td>薪水(含勞健保1人3234)</td></tr>
    <tr><td><input type="text"  width="150px" id="salary1"  value="0"onchange="caculate()"></td></tr>    
    <tr><td><input type="text"  width="150px" id="salary2" value="0"onchange="caculate()"></td></tr>    
    <tr><td><input type="text"  width="150px" id="salary3" value="0"onchange="caculate()"></td></tr>    
    <tr><td><input type="text"  width="150px" id="salary4" value="0"onchange="caculate()"></td></tr>    

    <tr><td>支出</td></tr>
    <?php $i = 0 ; foreach($monExpenses as $row):?>
    <tr><td><label><input type="checkbox" checked="checked" onclick="caculate()" class="monExpenses" id="check_monExpenses_<?=$i?>"> <?=substr($row['time'],5,5)?> <?=$row['note']?>:<span id="monExpenses_<?=$i++?>"><?=$row['MOUT']?></span></label></td></tr>   
    <?php endforeach;?> 
    <tr><td >淨利</td></tr>
    <tr><td id="net"></td></tr>
	<tr><td>分紅40%</td></tr> 
	<tr><td id="divide"></td></tr> 

</table>
</div>
</body>
</html>



