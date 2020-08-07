<script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
<script>
    
$(document).ready(function(){
 
	$('#product_supplier').val(<?=$shopID?>);
	selectMyClass();
})
 function addbutton(name)
{
   var intId = ''; 
   for(var key in arr)
   {//取key值
      if(key == name) intId = arr[key];
   }
   var nameid = name+'_'+intId;
 
   var type = '';
   if(name == 'video') type = 1;
    
   $('.add_'+name+'').append('<div class="'+name+'" id="'+nameid+'_div"><input type="text" id="'+nameid+'" name="'+name+'[]" value="" size="50px"/> <input type="button" name="del_'+nameid+'" value="刪除" onclick="deltxt('+type+','+intId+')"/></div>');
    
   intId++;
   for(var key in arr)
   {//存回陣列中
      if(name == key) arr[key] = intId;
   }
   
}
       
    
    
</script>
     

     
<div class="product">

    <div style="display:none">
     <a onclick="$('#classSelect').show('fast')">選擇貨品清單選項</a>
     <select  onchange="selectMyClass()" id="myClassSet">
     <option value="4" select="select">廠商</option>
     </select>
     <div id="classSelect"></div>
    </div>
<div style="height:75px">

<select id="product_time"></select>

<input type="button" class="big_button"  id="warroomBtn" value="查詢庫存" onClick="queryProduct('product','auto'); $('#warroomBtn').hide();$('#productIOBtn').show()" />


<input type="button" class="big_button"   value="新增商品" onclick=" newProductForm()"/>

<!--
<input type="button" class="big_button"   value="新增商品通告" onclick="newProductAnnounce()"/>
-->

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