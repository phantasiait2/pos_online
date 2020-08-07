<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/jquery.tablesorter.js"></script>
<script type="text/javascript">
$(document).ready(function()
                 {
    
    $('#sort_table').tablesorter({widgets: ['zebra'],sortList: [[3,1]]});
        
    
    
})

    
    
</script>
<script type="text/javascript" >
	function changeUrl()
	{
		
		location.href = '/product_flow/ship_out/'+$('#year').val()+'/'+$('#month').val()
		
		
	}


</script>
<title>出貨</title>
<style>
    /* tables */
table.tablesorter {
	font-family:arial;
	background-color: #CDCDCD;
	margin:10px 0pt 15px;
	font-size: 14pt;
	width: 100%;
	text-align: left;
}
table.tablesorter thead tr th, table.tablesorter tfoot tr th {
	background-color: #e6EEEE;
	border: 1px solid #FFF;
	font-size: 8pt;
	padding: 4px;
    height:40px;
}
table.tablesorter thead tr .theader {
	background-image: url(/images/bg.gif);
	background-repeat: no-repeat;
	background-position: center right;
	cursor: pointer;
}
table.tablesorter tbody td {
	color: #3D3D3D;
	padding: 4px;
  height:40px;
	background-color: #FFF;
	vertical-align: top;
}
table.tablesorter tbody tr.odd td {
	background-color:#F0F0F6;
}
table.tablesorter thead tr .headerSortUp {
	background-image: url(/images/asc.gif);
}
table.tablesorter thead tr .headerSortDown {
	background-image: url(/images/desc.gif);
}
table.tablesorter thead tr .headerSortDown, table.tablesorter thead tr .headerSortUp {
background-color: #8dbdd8;
}

    </style>
</head>

<body>
    <h1>
	從
	
	<select name="year" id="year" >
    	<?php	$t = getdate();
		
		 for($y=2015;$y<=$t['year'];$y++):?>
         	<option value="<?=$y?>"  <?=($y==$year)?'selected="selected"':''?>><?=$y?></option>
         <?php endfor;?>
    </select>
    	<select name="month" id="month" >
    	<?php	
		
		 for($m=1;$m<=12;$m++):?>
         	<option value="<?=$m?>" <?=($m==$month)?'selected="selected"':''?>><?=$m?></option>
         <?php endfor;?>
    </select>
         
       
    往後算6個月
     <input type="button" value="重新查詢" onclick="changeUrl()">
    </h1>
    <div>
        買賣超比解釋 <br/>
        40%以上 表示庫存正在減少。<br/>
        10~40% 表示正常銷貨，且合理利潤。<br/>
        0~10%  可能為庫存增加，也可能為利潤偏低。<br/>
        小於 0%   表庫存增加，或賠錢出售。<br/>
    
    </div>
    
    
    
<table  border="1"; style="float:left; width:50px" class="tablesorter">
    <thead><tr><th>排序</th></tr></thead>
    
    <tbody>
    <?php for($i=1;$i<=50;$i++):?>
    <tr class="<?=($i%2==0)?'odd':'even'?>"><td><?=$i?></td></tr>
    <?php endfor;?>
    </tbody>

</table>




<?php if (count($s) > 0): ?>
<table id="sort_table" class="tablesorter" border="1" style="width:1000px; float:left">
  <thead>
    <tr>
      <th><?php echo implode('</th><th>', array_keys(current($s))); ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($s as $row): array_map('htmlentities', $row); ?>
    <tr>
      <td><?php echo implode('</td><td>', $row); ?></td>
    </tr>
<?php endforeach; ?>
  <tbody>
</table>
<?php endif; ?>
</body>
</html>