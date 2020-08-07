<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table border="1" id="product_flow_table" class="tablesorter">
    <thead>
    <tr>
    	<th>序號</th>
    	<th>商品中文</th>
        <th>商品英文</th>
        <th>語言</th>
        <th>定價</th>
        <th>流速(日/個)</th>
        <th>流量</th>
        <th>流比</th>
        <th>現在庫存</th>
       
        <th>安全備貨</th>
        <th>短缺狀況</th>
    </tr>
    </thead>
    <tbody>
    <?php $i=0;$total = 0 ;foreach($product as $row):
	  if($row['type']==1):
	?>
    <tr <?=($i++%2==0)?'style="background-color:#EEE"':''?>>
    			<td><?=$i?></td>
    			<td><?=$row['ZHName']?></td>
                <td><?=$row['ENGName']?></td>
                <td><?=$row['language']?></td>
                <td style="text-align:right"><?=$row['price']?></td>
                <td><?=$row['flowRate']?></td>
        	      <td style="text-align:right"><?=$row['flowNum']?></td>
                  <td style="text-align:right"><?=$row['flowNum']/5+3/$row['flowRate']?></td>
                <td style="text-align:right"><?=($shopNum>1)?$row['comNum']:$row['nowNum']?></td>
                <td style="text-align:right"><?=($row['flowRate']!=0)?$prepare = round($row['wait']/$row['flowRate'])*$shopNum:$prepare=0?></td>
                <?php if(($shopNum>1)) $short = ($row['comNum'] - $prepare);
					 else	 $short = ($row['nowNum'] - $prepare);
				
				?>
                <td style="text-align:right;color:<?=($short >0)?'green':'red'?>"><?=$short?></td>
    </tr>
    <?php 
	endif;
	endforeach;?>
	</tbody>
</table>

