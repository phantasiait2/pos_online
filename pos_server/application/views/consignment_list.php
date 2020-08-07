<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pos_order.js"></script>
<script type="text/javascript" src="/javascript/pos.js"></script>
<script>
    
    function query()
    {
        
        $.post('get_consignment_link',{year:$('#year').val(),month:$('#month').val()},function(data)
              {
            
                    if(data.result)
                        {
                            
                            location.href=data.url;
                        }
                    else 
                        {
                            alert('登入逾時，請嘗試重新查詢')
                            loaction.reload();
                            
                            
                        }
            
        },'json')
        
    }
</script>
<style>
	
.fancytable{border:1px solid #cccccc; width:100%;border-collapse:collapse;}
.fancytable td{border:1px solid #cccccc; color:#555555;text-align:center;line-height:28px;}
.headerrow{ background-color:#0066cc;}
.headerrow td{ color:#ffffff; text-align:center;}
.datarowodd{background-color:#ffffff;}
.dataroweven{ background-color:#efefef;}
.datarowodd td{background-color:#ffffff;}
.dataroweven td{ background-color:#efefef;}

.headerrow td{ color:#ffffff; text-align:center;}
</style>
<title><?=$product[0]['inf']['supplierName']?> 寄售期間：<?=$from?>~<?=$to?></title>
</head>
<body>
<div class="product" style="width:1100px; ">

</div>
    <h1><?=$product[0]['inf']['supplierName']?>  寄售期間：<?=$from?>~<?=$to?> 
    <select id="year">
        <?php for($i=date('Y');$i>2015;$i--):?>
        <option  <?=($year==$i)?'selected="selected"':'';?>><?=$i?></option>
        <?php endfor;?>
    </select>
        <select id="month">
        <?php for($i=1;$i<=12;$i++):?>
        <option  <?=($month==$i)?'selected="selected"':'';?>><?=$i?></option>
        <?php endfor;?>
    </select> 
      <input type="button" value="查詢" onclick="query()"> 
       </h1>
<div style="clear:both"></div>
<?php if(!empty($product)):?>
<table class="fancytable">
    <tr class="headerrow">
       <td>櫃號</td>
       <td>中文</td>
       <td>英文</td>
       <td>語言</td> 
       <?php if($product[0]['inf']['supplierName']==''):?> 
       <td>供應商</td> 
       <?php endif;?>  
        <td>期初寄售數量</td>
        <td>寄售單價</td>
        <td>本月增加寄售數量</td>
        <td>本月寄售單價</td>
        <td>本月售出數量</td>
        <td>本月售出小計</td>
        <td>期末寄售數量</td>
    
    </tr>
<?php $i = 0;$total = 0;foreach($product as $row):?>
 <tr <?=($i%2==0)?'class="datarowodd"':'class="dataroweven"'?>>
        <td><?=$row['inf']['cabinet']?></td>
       <td><?=$row['inf']['ZHName']?></td>
       <td><?=$row['inf']['ENGName']?></td>
       <td><?=$row['inf']['language']?></td>
        <?php if($product[0]['inf']['supplierName']==''):?>  
       <td><?=$row['inf']['supplierName']?></td>
          <?php endif;?>    
        <td><?=$row['open']['num']?></td>
        <td><?=$row['open']['purchasePrice']?></td>
        <td><?=$row['purchase']['num']?></td>
        <td><?=$row['purchase']['purchasePrice']?></td>
        <td><?=$row['sell']?>
            <?php if($row['sell'] > 0): $total+=$row['sell']*$row['purchasePrice']?>
                <br/>出貨賣斷：<?=$row['sellNum']?>
                <br/>直營售出：<?=$row['dirSell']?>
                <br/>加盟售出：<?=$row['consignmentSellNum']?>
            <?php endif;?>
        
        
        </td>
         <td><?=$row['sell']*$row['purchasePrice']?></td>
    
        <td><?=$row['finalNum']?></td>
    </tr>


<?php endforeach;?>


</table>
<h2>本期售出總金額為:
<?=$total?></h2>

<?php else:?>
    <h2>此供應商無寄賣商品</h2>
<?php endif;?>
</body>
</html>