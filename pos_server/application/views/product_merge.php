<script type="application/javascript">
    
    function findProduct(id,p)
    {
        
        content='<div id="productInQuery"></div>';
        content+='<input  type="hidden" id="InID" value ="'+id+'">';
        openPopUpBox(content,600,400)  ;
         queryProduct('productIn','select');
                
        $('#productIn_findProcutQuery').val(p)
        $('#productIn_start').val(0);findProductStock('productIn',0,'0');
    //   
        
    }
    
    function productInTable(data)
    {
        
      
         $('#result_'+$('#InID').val()).html(data['ZHName']+'<input type="hidden" value="'+data['productID']+'" id="productID_'+  $('#InID').val()+'">');
        productIDmerge($('#InID').val())
        closePopUpBox();
        
        
    }
    
    function productIDmerge(id)
    {
        if(confirm('確定要合併？'))
        $.post('/order/product_merge',{pID:id,productID:$('#productID_'+id).val()},function(data)
              {
            
         
            if(data.result==true) $('#tr_'+id).slideUp();
            
            
        },'json')
        
        
    }
    
    
</script>
<table>   
    <tr><td>商城資料</td><td>進銷存品名</td><td></td></tr>
<?php foreach($product as $row):?>
    

 <tr id="tr_<?=$row['pID']?>"><td><?=$row['ZHName']?>
     <img src="http://mart.phantasia.tw/images/product/400/p_<?=$row['pID']?>_1.jpg">
 
 
 </td><td>
 <input type="button"  value="查找" onclick="findProduct(<?=$row['pID']?>,'<?=$row['ZHName']?>')">
  <div id="result_<?=$row['pID']?>">
     </div>
   
 
 
 </td>
 
 
 
 <td><input type="button"  value="合併" onclick="productIDmerge(<?=$row['pID']?>)"></td></tr>


<?php endforeach;?>
</table>