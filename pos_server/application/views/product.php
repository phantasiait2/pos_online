<div class="product">

     <a onclick="$('#classSelect').show('fast')">選擇貨品清單選項</a>
     <select  onchange="selectMyClass()" id="myClassSet">
     <option value="1">預設</option>
     <option value="2">出貨</option>
     <option value="3">盤點</option>
     </select>
     <div id="classSelect"></div>
<div style="height:75px">

<select id="product_time"></select>

<input type="button" class="big_button"  id="warroomBtn" value="查詢庫存" onClick="queryProduct('product','auto'); $('#warroomBtn').hide();$('#productIOBtn').show()" />
<input type="button" class="big_button"  id="productIOBtn"  value="進出貨查詢" onclick="productIOType();$('#productIOBtn').hide();$('#warroomBtn').show()"/>

<input type="button" class="big_button"  value="進貨" onClick="wareroomIO('purchase')"  />
<input type="button" class="big_button"  value="進貨清單" onClick="showRoomTable('purchase')"  />

<?php if($account!='stock'):?>
<input type="button" class="big_button"   value="新增商品" onclick=" newProductForm()"/>
<input type="button" class="big_button"   value="新增修改供應商" onclick="editSupplier()"/>
<input type="button" class="big_button"   value="新增採購單" onclick=" wareroomIO('purchaseOrder')"/>
<?php endif;?>
<input type="button" class="big_button"   value="採購清單" onclick=" showRoomTable('purchaseOrder')"/>
<!--
<input type="button" class="big_button"   value="新增商品通告" onclick="newProductAnnounce()"/>
-->
<?php if($account!='stock'):?>
<input type="button" class="big_button"   value="卡套編輯" onclick="editCardSleeve()"/>
<input type="button" class="big_button"   value="商品庫存精靈" onclick="getTopProduct()"/>
<input type="button" class="big_button"   value="快速採購" onclick="quickPurchase()"/>
<?php endif?>
<input type="button" class="big_button"   value="盤點模式" onclick="getChkList()"/>
<input type="button" class="big_button"   value="櫃號編輯" onclick="editCabinet()"/>
<?php if($account!='stock'):?>
<input type="button" class="big_button"   value="盒進包出編輯" onclick="editPackage()"/>
<input type="button" class="big_button"   value="消耗品編輯" onclick="editConsume()"/>

<?php $t = getdate();?>


<input type="button" class="big_button"   value="廠商寄賣表" onclick="showProductConsignment()" />


<input type="button" class="big_button"   value="魔法風雲會設定" onclick="magicSet()"/>
<input type="button" class="big_button"   value="寶可夢設定" onclick="pokemonSet()"/>
<?php endif?>
<input type="button" class="big_button"   value="歷年出貨退貨率" onclick="stockBack()"/>
<input type="button" class="big_button"   value="退貨原因編輯" onclick="stockBackAll()"/>

<?php if($level>=100):?>
<!--<input type="button" class="big_button"   value="庫存歸零" onclick=" turnZeroConfirm()"/>-->
<?php endif?>
<input type="hidden" id="shopID" value="<?=$shopID?>">
<input type="hidden" id="level" value="<?=$level?>">



<div style="float:right; " id="classifier">
<table  border="1" style=" text-align:center" width="100px">
    <thead>
    <tr><th style="font-weight:bold">遊戲分類對照</th></tr>
    </thead>
    <tbody>
    <tr><td>A.親子益智</td></tr>	
    <tr><td>B.派對歡樂</td></tr>				
    <tr><td>C.輕度策略</td></tr>					
    <tr><td>D.重度策略</td></tr>					
    <tr><td>E.團隊合作</td></tr>					
    <tr><td>F.兩人對戰</td></tr>					
    </tbody>
    </table>
</div>    
</div>

<div id="productQuery" style="width:1150px"></div>
     <div class="divider"></div>

     
     
	<div id="product_list"></div>
</div>