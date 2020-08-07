<!doctype html>
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>商品預定管理系統</title>
    <link rel="stylesheet" type="text/css" href="/style/pos.css">
 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
   <SCRIPT type="text/javascript" src="http://www.google.com/jsapi"></SCRIPT>
    <script type="text/javascript" src="/javascript/jquery.tablesorter.js"></script>
    <script type="text/javascript" src="/javascript/pos.js"></script>
     <script type="text/javascript" src="/javascript/pop_up_box.js"></script>
      <script type="text/javascript" src="/javascript/pos_product.js"></script>
    <script type="text/javascript" src="/javascript/pos_product_query.js"></script>
   <script>
  $(function() {
    $( "#cargoStatus").buttonset();
    $( "#usage" ).buttonset();
       queryProduct('cs_product','select')  ;
    
  });
       
    function showMsg(t)
       {
           
           
           $('#msg').html(t);
           
       }
       
    function rowDeleteHandler(deleteToken,productID)
       {
           if(deleteToken==1)
               {
                   
                  $('#cancel_container_'+productID).show(); 
                   $('#delete_container_'+productID).hide(); 
                  $('#cancel_item_'+productID).val(1);
                   
               }
           else
               {

                   $('#delete_container_'+productID).show(); 
                   $('#cancel_container_'+productID).hide(); 
                   $('#cancel_item_'+productID).val(0);
                   



               }

           
           
       }

       
    function cs_productTable(data)
       {
          if(typeof data.discount =='undefined') data.discount = parseInt($('#discount').val());
           if(typeof data.num =='undefined') data.num = 0;
          if(typeof data.sellPrice =='undefined') data.sellPrice = Math.round(data.price *parseInt($('#discount').val()) /100);
          if(typeof data.stockNum =='undefined'){
            data.stockNum = 0;
            stockString="資料建立中...";
          }
          else if(data.stockNum==0 ||data.stockNum<0) stockString="已無庫存";
          else stockString =data.stockNum;
          if(data.num < 0) data.num = 0;
          //data.sellPrice = Math.round(data.price * data.discount/100);
          $('#productTable').append(
              '<tr>'+
                '<td>'+data.ZHName+'</td>'+
                '<td>'+data.ENGName+'</td>'+
                '<td>'+data.language+'</td>'+
                '<td>'+data.price+'</td>'+
                '<input type="hidden" id="Price_'+data.productID+'" name="Price[]" value="'+data.price+'">'+
              '<td>'+data.discount+'</td>'+  
                 '<td>'+data.sellPrice+'</td>'+ 

                '<td>'+
              data.num+
                '</td>'+
                 '<td id="subtotal_'+data.productID+'">計</td>'+
                '<td id="stock_num_'+data.productID+'">'+stockString+'</td>'+
                '<td>'+
              '<input type="hidden" id="cancel_item_'+data.productID+'" name="cancel[]" value="0">'+
              '<input type="hidden" class="csorderProductID" id="send_productID'+data.productID+'" name="productID[]" value="'+data.productID+'">'+
              '<span id="delete_container_'+data.productID+'">'+
                 
              '</span>'+
           
              '</td>'+
                '</tr>'
          
          )
           sellPriceChange();
           
       }
      function calcuteNum(productID,num){
          num = parseInt($('#product_num_'+productID).val())+num;
          if(num>=0) $('#product_num_'+productID).val(num);
          else $('#product_num_'+productID).val(0);
          countCsorderTotal();
       }
       function discountChange(){
          $('.csorderProductID').each(
              function() {
                  
                var productID = $('#'+this.id).val();
                
                discount = parseInt($('#discount').val());
                $('#discount_'+productID).val(discount);
                 
                subDiscountChange();
           })
       }
       function subDiscountChange()
       {
           $('.csorderProductID').each(
              function() {
                var productID = $('#'+this.id).val();
                sellPrice = parseInt($('#Price_'+productID).val())*parseInt($('#discount_'+productID).val())/100;
                sellPrice = Math.round(sellPrice);
                $('#sellPrice_'+productID).val(sellPrice);
           })
           countCsorderTotal();
       }
       function sellPriceChange()
       {
           $('.csorderProductID').each(
            function() {
                var productID = $('#'+this.id).val();
                discount = (parseInt($('#sellPrice_'+productID).val())/parseInt($('#Price_'+productID).val()))*100;
                discount = Math.round(discount);
                $('#discount_'+productID).val(discount);


                
           })
           countCsorderTotal();
       }
       function countCsorderTotal()
       {

       
           var total = 0;
           $('.csorderProductID').each(
          function() {
         
                var productID = $('#'+this.id).val();
                var subTotal = parseInt($('#sellPrice_'+productID).val())*parseInt($('#product_num_'+productID).val())
                subTotal = Math.round(subTotal);
               $('#subtotal_'+productID).html(subTotal);
               
                total += subTotal;  
           })
            
           $('#total').html('總額：'+total);
           
       }
       
       
       function formSendOut()
       {
           //alert('ajaxSendOut:'+ decodeURIComponent($('#csOrderForm').serialize()));


           $.ajax({
				type: 'post',
				data:  $('#csOrderForm').serialize(),
				url: "/csorder/order_update",
				dataType: 'json',
				success: function(data){
          //alert(data);
				  //location.reload();  //
        }
			});
       }
       
       
       
       
     
  </script>
</head>
<body >
    <div>系統提示：<span id ="msg" style="color:red"></span></div>
    <form id="csOrderForm">
    
<table  style="width:750px">
    <tr>
        <td>預定時間：<br/><?=$csOrder['orderTime']?></td>
        <td>訂單編號：<br/><?=$csOrder['csOrderNum']?></td>
        <td>訂單來源：<br/> <?php 
                switch($csOrder['source'])
                {
                case 1: echo '網路商城';break;
                case 2: echo '現場訂購';break;
                case 3: echo '臉書訂購';break;
                case 4: echo '電話訂購';break;
                }
              ?></td>
        <td>訂單用途：
        
        </td>
        <td>
        <a href="/csorder/estimate<?=strstr($_SERVER['REQUEST_URI'],'?')?>&csOrderID=<?=$csOrder['csOrderID']?>&shopID=<?=$csOrder['shopID']?>" target="_new">
        <input type="button" value="列印估價單" class="big_button">
        </a>
        </td>
    </tr>
    <tr>
        <input type="hidden" name="csOrderID" value="<?=$csOrder['csOrderID']?>">
        <input type="hidden" name="shopID" value="<?=$csOrder['shopID']?>">
        <td>訂購人：<?=$csOrder['name']?></td>
        <td>會員編號：<?=$csOrder['memberID']?></td>
        <td>電話：<?=$csOrder['phone']?></td>
        <td>e-mail：<?=$csOrder['email']?></td>
    </tr>
        <tr>
          <td></td>
        <td></td>
        <td>統一編號：<?=$csOrder['IDNumber']?></td>
       
        <td>抬頭：<?=$csOrder['title']?></td>
        <td> 
       
        </td>
    </tr>
     <tr>
        <td colspan="2" id="cargoStatus">出貨狀態：
        
           <?php if($csOrder['source']==1):?>
                <?php if($csOrder['cargoStatus']==0):?><span onmouseover=" showMsg('網路訂單請至結帳區過帳')" >未出貨</span>
                <?php else:?>
                    已出貨
                <?php endif;?>
        
            
             <?php endif;?>
       </td>
       <?php if($csOrder['cashStatus']==0):?>
        <td colspan="2"  onmouseout="showMsg('')"  onmouseover=" showMsg('若須結清請至結帳頁')">付款狀態：
         未付款</td>
        <?php else:?>
        <td colspan="2"  onmouseout="showMsg('')"  onmouseover=" showMsg('此筆已登錄帳務系統中，交貨後訂單即完成')">付款狀態：
         已付款</td>
         <?php endif;?>
    </tr>
    <tr>
        <td colspan="4">備註：
                       <?=$csOrder['comment']?>
            
        
        </td>
       
    </tr>
    <tr>
        <td id="total">總額：<?=$csOrder['total']?></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    </table>
    
    <div id="cs_productQuery"></div>
    
    
<table id="productTable" border="1">
    <tr>
        <td>中文</td>
        <td>英文</td>
        <td>語言</td>
        <td>原價</td>
        <td>折扣</td>
        <td>售價</td>
        <td>數量</td>
        <td>小計</td>
        <td>庫存</td>
    </tr>
    <?php
    if(count($csOrderData) >0)
     foreach($csOrderData as $row):?>
          <?php $row['discount'] = $csOrder['discount'];?>
          <script type="text/javascript">cs_productTable(JSON.parse('<?=json_encode($row)?>'))</script>
    <?php endforeach;?>
</table>
    </form>
<!--input type="button" value="確定" class="big_button" onclick="formSendOut()"-->



</body>
</html>