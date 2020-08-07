<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<?php if(isset($css))echo $css;?>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/reject_enter_key.js"></script>
<script type="text/javascript" src="/javascript/pos.js"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script type="application/javascript" src="/javascript/jquery.progressbar.js"/></script>

<?php if($shopID==0):?>
<script type="text/javascript">
	$(document).ready(function()
	{

		$('tr td').click(function(){
			if(this.id!='')
			{
				if($('.edit_input').length>0)
				{
				
				oldID =$('.edit_input').attr('id').substr(9);
				editSend(oldID);
				
				}
				//
				
				var id = this.id;
				$('#'+id).unbind();
				$('#'+id).html('<input type="text" class="edit_input" id="edit_val_'+id+'" onblur="editSend(\''+id+'\')" value="'+$('#'+id).html()+'">')
			
			}
				
			
			
		});
	})

function editSend(id)
{


	$.ajax({
        type: "post",
        url: '/race/edit_send',
		dataType:'json',
        async: false,
		data: 'key='+id+'&val='+$('.edit_input').val(),
        success : function(data) {
          
			if(data.result==true)location.reload();
        }
    });

	
	
	
	
	
	
}
</script>

<?php endif;?>
</head>
<body>
<div class="webFrame" style="background:#FFF; padding:20px;">
<h1>瘋桌遊益智遊戲專賣店公告：</h1>
<h1 onclick="$('#report_frame').toggle()" style="cursor:pointer; color:#F00">各店每周回報(點開輸入)</h1>
<iframe id="report_frame"  style="display:none" src="https://docs.google.com/forms/d/1QVhzsvbYxdMMrgdM-A3f4NGnYRuRn9W8wXYx8X9n2to/viewform" width="1000px" ; height="900px">
</iframe>



<table border="1" style=" text-align:right" width="1000px" >
	<tr><td colspan="7" style="font-weight:bold; text-align:center">粉絲團人數成長百分比計算30%</td></tr>
    <tr><td>店名</td><td>板橋店</td><td>大安店</td><td>新竹店</td><td>新店大坪林店</td><td>桃園店</td><td>台中店</td></tr>
	<tr><td>本來人數(6/30 23:00)</td>
    
    <?php for($i=1;$i<=$shop_num;$i++):?>
    <td id="f_0_<?=$i?>"><?=$raceDatail['f_0_'.$i]?></td>
    <?php endfor;?>
   	</tr>
	<tr><td>現在人數</td>
    <?php for($i=1;$i<=$shop_num;$i++):?>
    <td id="f_1_<?=$i?>"><?=$raceDatail['f_1_'.$i]?></td>
    <?php endfor;?>
    <tr><td>粉絲團成長比例(滿分100%佔總分10%)</td>
    	<?php foreach($runOut['fans_base'] as $row):?>
	    	<td><?=$row?>%</td>
        <?php endforeach;?>
  
	</tr>
     <tr style="background-color:#FFC; font-weight:bold" ><td>獲得積分</td>
       	<?php foreach($runOut['fans_grade'] as $row):?>
	    	<td><?=$row?></td>
        <?php endforeach;?></tr>
    
    
     <tr><td>每日是否po文(滿分31佔10%)</td>
     <?php for($i=1;$i<=$shop_num;$i++):?>
    <td id="f_2_<?=$i?>"><?=$raceDatail['f_2_'.$i]?></td>
    <?php endfor;?>
    </tr>
    <tr  style="background-color:#FFC; font-weight:bold" ><td>獲得積分</td>
		<?php foreach($runOut['post_grade'] as $row):?>
	    	<td><?=$row?></td>
        <?php endforeach;?></tr>
    
     <tr><td>和粉絲互動性(滿分100佔10%)</td>
     <?php for($i=1;$i<=$shop_num;$i++):?>
    <td id="f_3_<?=$i?>"><?=$raceDatail['f_3_'.$i]?></td>
    <?php endfor;?>
    </tr>
    <tr  style="background-color:#FFC; font-weight:bold" ><td>獲得積分</td>
    
    <?php foreach($runOut['inter_grade'] as $row):?>
	    	<td><?=$row?></td>
        <?php endforeach;?></tr>
    
    
     <tr  style="background-color:#FF9; font-weight:bold" ><td>本項合計</td>  
          <?php foreach($runOut['facebook'] as $row):?>
	    	<td><?=$row?></td>
        <?php endforeach;?></tr>
        
    
    <tr><td colspan="7">統計到止</td></tr>
	<tr><td></td></tr>
	<tr><td colspan="7" style="font-weight:bold;text-align:center">有效問卷填寫份數30%</td></tr>
    <tr><td>店名</td><td>板橋店</td><td>大安店</td><td>新竹店</td><td>新店大坪林店</td><td>桃園店</td><td>台中店</td></tr>
    <tr><td>份數</td>
     <?php for($i=1;$i<=$shop_num;$i++):?>
    <td id="q_<?=$i?>"><?=$raceDatail['q_'.$i]?></td>
    <?php endfor;?>
    </tr>
    <tr  style="background-color:#FFC; font-weight:bold"><td>獲得積分</td>    
	<?php foreach($runOut['ask_grade'] as $row):?>
	    	<td><?=$row?></td>
        <?php endforeach;?></tr>
    

    
     <tr><td>消費者評價</td>
     <?php for($i=1;$i<=$shop_num;$i++):?>
    <td id="q_2_<?=$i?>"><?=$raceDatail['q_2_'.$i]?></td>
    <?php endfor;?>
    </tr>
    <tr  style="background-color:#FFC; font-weight:bold"><td>獲得積分</td>
    	<?php foreach($runOut['askR_grade'] as $row):?>
	    	<td><?=$row?></td>
        <?php endforeach;?></tr>

   <tr  style="background-color:#FF9; font-weight:bold" ><td>本項合計</td>  
          <?php foreach($runOut['ask'] as $row):?>
	    	<td><?=$row?></td>
        <?php endforeach;?></tr> 
    <tr><td colspan="7">統計到止</td></tr>
    <tr><td></td></tr>
	<tr><td colspan="7" style="font-weight:bold;text-align:center">活動配合評分(活動宣傳，照片，活動參與人數)20%</td></tr>
    <tr><td>店名</td><td>板橋店</td><td>大安店</td><td>新竹店</td><td>新店大坪林店</td><td>桃園店</td><td>台中店</td></tr>
    <tr><td>活動宣傳</td>
         <?php for($i=1;$i<=$shop_num;$i++):?>
    <td id="a_1_<?=$i?>"><?=$raceDatail['a_1_'.$i]?></td>
    <?php endfor;?>
	</tr>
    <tr><td>活動結束照片張貼</td>
         <?php for($i=1;$i<=$shop_num;$i++):?>
   		 <td id="a_2_<?=$i?>"><?=$raceDatail['a_2_'.$i]?></td>
   		 <?php endfor;?>    
	</tr>
    <tr><td>活動參與人數</td>
		 <?php for($i=1;$i<=$shop_num;$i++):?>
        <td id="a_3_<?=$i?>"><?=$raceDatail['a_3_'.$i]?></td>
        <?php endfor;?>
    </tr>
    <tr><td>小計</td>
   	<?php foreach($runOut['active_base'] as $row):?>
	    	<td><?=$row?></td>
        <?php endforeach;?></tr>

    
    
    <td></td><td></td><td></td><td></td><td></td><td></td></tr>	
    <tr style="min-height:50px; text-align:left"><td>簡評</td>
     <?php for($i=1;$i<=$shop_num;$i++):?>
    <td id="a_4_<?=$i?>"><?=$raceDatail['a_4_'.$i]?></td>
    <?php endfor;?>
    </tr>
   <tr  style="background-color:#FFC; font-weight:bold"><td>獲得積分</td>
    	<?php foreach($runOut['active_grade'] as $row):?>
	    	<td><?=$row?></td>
        <?php endforeach;?></tr>

    

    <tr><td colspan="7">統計到止</td></tr>
	<tr><td></td></tr>
  	<tr><td colspan="7" style="font-weight:bold;text-align:center">自有商品販售20%</td></tr>
    <tr><td>店名</td><td>板橋店</td><td>大安店</td><td>新竹店</td><td>新店大坪林店</td><td>桃園店</td><td>台中店</td></tr>
    <?php foreach($productList as $row):?>
    <tr><td><?=$row['name']?></td>
    	<?php for($i =1; $i<=$shop_num;$i++):?>
        	 <td><?=(isset($product[$i][$row['productID']]))? $product[$i][$row['productID']]:0?></td>
         <?php endfor;?>    
    </tr>
    <?php endforeach;?>
    
    
  
<tr><td>小計</td>
   	<?php foreach($runOut['product_base'] as $row):?>
	    	<td><?=$row?></td>
        <?php endforeach;?></tr>
     <tr  style="background-color:#FF9; font-weight:bold" ><td>本項合計</td>  
          <?php foreach($runOut['product_grade'] as $row):?>
	    	<td><?=$row?></td>
        <?php endforeach;?></tr>
    <tr><td colspan="7">統計到止</td></tr>
<tr><td></td></tr>
  	<tr><td colspan="7" style="font-weight:bold;text-align:center">總積分</td></tr>

    <tr><td>店名</td><td>板橋店</td><td>大安店</td><td>新竹店</td><td>新店大坪林店</td><td>桃園店</td><td>台中店</td></tr>
     <tr  style="background-color:#FF9" ><td>分數</td> <?php foreach($runOut['result_grade'] as $row):?>
	    	<td><?=$row?></td>
        <?php endforeach;?></tr>

</table>

</div>
</body>
</html>



