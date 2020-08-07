<div class="product">

	<?php if($level>=100):?>
    	<input type="button" class="big_button" value="新增帳號" onclick="newAccountForm()"/>
        <input type="button" class="big_button" value="email管理" onclick="emailManger()"/>
    	
    <?php endif?>
    <table border="1" width="800px;" style="font-size:14pt">
    <tr>
        <th>帳號</th>
        <th>電子信箱</th>
        <th>權限</th>
        <th>店家</th>
        <th>操作</th>
    </tr>
   
    <?php foreach($accountList as $row):?>
    	<?php if(($level>=100&&$account!='stock')||$aid==$row['aid']):?>
    <tr>
   
        <th><?=$row['account']?></th>
        <th><input type="text" id="email_<?=$row['aid']?>" value="<?=$row['email']?>"></th>
        <th>
        	<select onchange="changeOp('power_<?=$row['aid']?>','shopID_<?=$row['aid']?>')" id="power_<?=$row['aid']?>">
	            <?php if($level>=150):?><option value="100" <?=$row['level']==150?'selected=selected':''?>>管理者</option><?php endif;?>
            	<?php if($level>=100):?><option value="100" <?=$row['level']==100?'selected=selected':''?>>商品管理</option><?php endif;?>
                <?php if($level>=80):?><option value="80" <?=$row['level']==80?'selected=selected':''?>>財務管理</option><?php endif;?>
                <?php if($level>=50):?><option value="50" <?=$row['level']==50?'selected=selected':''?>>店長</option><?php endif;?>
                <?php if($level>=10):?><option value="10" <?=$row['level']==10?'selected=selected':''?>>店員</option><?php endif;?>
                <?php if($level>=5):?><option value="5" <?=$row['level']==5?'selected=selected':''?>>訂貨廠商</option><?php endif;?>
                 <?php if($level>=100):?><option value="-1" <?=$row['level']==-1?'selected=selected':''?>>供應商</option><?php endif;?>
                
                
            </select>
        </th>
        <th>
   
        	<select id="shopID_<?=$row['aid']?>">
            
		                 <?php if($row['level']==-1):?>
		            <?php foreach($supplierList as $eachShop):?>
		            <?php if($level>=100||$shopID==$eachShop['supplierID']):?><option value="<?=$eachShop['supplierID']?>" <?=($eachShop['supplierID']==$row['shopID'])?'selected="selected"':''?>><?=$eachShop['name']?></option><?php endif;?>
                <?php endforeach;?>
                
		                		
		              <?php else:?>
		              <?php if($level>=100||$shopID==0):?><option value="0" <?=(0==$row['shopID'])?'selected="selected"':''?>>幻遊天下</option><?php endif;?>     		    		    		
            	<?php foreach($shopList as $eachShop):?>
		            <?php if($level>=100||$shopID==$eachShop['shopID']):?><option value="<?=$eachShop['shopID']?>" <?=($eachShop['shopID']==$row['shopID'])?'selected="selected"':''?>><?=$eachShop['name']?></option><?php endif;?>
                <?php endforeach;?>
                       	<?php endif;?>	
            	
                
            	
            </select>
           
        </th>
        <th>
        	 <input type="button" class="big_button" value="儲存" onclick="save(<?=$row['aid']?>)"/>
        	<?php if($aid==$row['aid']):?><input type="button" class="big_button" value="修改密碼" onclick="changePwForm();"/><?php endif;?>
			<?php if($aid==$row['aid']||$level>100):?><input type="button" class="big_button" value="刪除帳號" onclick="accountDelete(<?=$row['aid']?>)"/><?php endif;?>
			
        </th>
    </tr>
    	<?php endif;?>
    <?php endforeach?>




</table>



<div>


</div>
</div>