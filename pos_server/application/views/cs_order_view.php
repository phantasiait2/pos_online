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
    <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
   <script>
$(document).ready(function()
                 {
    
    getCsorderData(<?=$csOrder['csOrderID']?>)
    
})       
       
  $(function() {
    $( "#cargoStatus").buttonset();
    $( "#usage" ).buttonset();
       queryProduct('cs_product','select')  ;
    
    		//var dates = $( "#outDate").datepicker();
      
      
      
    		var dates = $( "#outDate").datepicker({
							dateFormat: 'yy-mm-dd' ,
                            dayNamesMin: [ "日", "一", "二", "三", "四", "五", "六" ],
							 monthNames: ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月']

						});	  
      

      
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

       function getCsorderData(csOrderID)
       {
           $.post('/csorder/cs_order_data',{csOrderID:csOrderID},function(data)
            {
                if(data.result==true)
                {
                    
                    for(var key in data.csOrderData)
                    {
                         cs_productTable(data.csOrderData[key])
                        
                        
                    }
                    
                    
                }
               
               
           },'json')
       }
       
       
    function cs_productTable(data)
       {
          if(typeof data.discount =='undefined') data.discount = parseInt($('#discount').val());
           if(typeof data.num =='undefined') data.num = 1;
          if(typeof data.sellPrice =='undefined') data.sellPrice = Math.round(data.price *parseInt($('#discount').val()) /100);
          if(typeof data.stockNum =='undefined'){
            data.stockNum = data.nowNum;
            stockString=data.stockNum ;
          }
          else if(data.stockNum==0 ||data.stockNum<0) stockString="已無庫存";
          else stockString =data.stockNum;
          if(data.num < 0) data.num = 0;
          //data.sellPrice = Math.round(data.price * data.discount/100);
           if($('#tr_'+data.productID).length>0)
               {
                   
                   
                        confirm('商品重複！！！！！');
                　　　　　return;
               }
          $('#productTable').append(
              '<tr id="tr_'+data.productID+'">'+
                '<td>'+data.ZHName+'</td>'+
                '<td>'+data.ENGName+'</td>'+
                '<td>'+data.language+'</td>'+
                '<td>'+data.price+'</td>'+
                '<input type="hidden" id="Price_'+data.productID+'" name="Price[]" value="'+data.price+'">'+
              '<td><input class="medium_text" type="text" onchange="subDiscountChange()" id="discount_'+data.productID+'" value="'+data.discount+'"></td>'+  
                 '<td><input class="medium_text" type="text" onchange="sellPriceChange()" id="sellPrice_'+data.productID+'" name="sellPrice[]" value="'+data.sellPrice+'"></td>'+ 

                '<td>'+
               '<div class="btn_minus" onclick="calcuteNum('+data.productID+',-1)"></div>'+ 
                '<input  class="short_text" type="text" onchange="countCsorderTotal()" id="product_num_'+data.productID+'" name="num[]" style="float:left" value="'+data.num+'">'+
                '<div class="btn_plus" onclick="calcuteNum('+data.productID+',1)"></div>'+
                '</td>'+
                 '<td id="subtotal_'+data.productID+'">計</td>'+
                '<td id="stock_num_'+data.productID+'">'+stockString+'</td>'+
                '<td>'+
              '<input type="hidden" id="cancel_item_'+data.productID+'" name="cancel[]" value="0">'+
              '<input type="hidden" class="csorderProductID" id="send_productID'+data.productID+'" name="productID[]" value="'+data.productID+'">'+
              '<span id="delete_container_'+data.productID+'">'+
                  '<input type="button" value="刪除" onclick="rowDeleteHandler(1,'+data.productID+')">'+
              '</span>'+
           '<span style="display:none" id="cancel_container_'+data.productID+'">'+
                  '<input type="button" value="取消刪除" onclick="rowDeleteHandler(0,'+data.productID+')">'+
              '</span>'+
              '</td>'+
			  '<td id="comNum_'+data.productID+'"></td>'+
                '</tr>'
          
          )
		   getComNum(data.productID);
           sellPriceChange();
           
       }
	   
	  function getComNum(productID) 
	   {
		   $.post('/csorder/get_com_num',{productID:productID},function(data)
		   {
				if(data.result==true)	  
		   		{
					$('#comNum_'+productID).html(data.comNum);
					
					
					
				}
				  
				  
			},'json')
				  
		   
		   
		   
		   
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
            showMsg('資料儲存中，請稍候....');

           $.ajax({
				type: 'post',
				data:  $('#csOrderForm').serialize(),
				url: "/csorder/order_update",
				dataType: 'json',
				success: function(data){
                    if(data.result==true)showMsg('儲存完畢');
         
				  //location.reload();  //
        }
			});
       }
       
       
       
       
     
  </script>
</head>
<body >
    <div>系統提示：<span id ="msg" style="color:red"></span></div>

    
    
    <form id="csOrderForm">
    
<table  style="width:850px">
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
         <div id="usage">
            <input type="radio" id="usage1" value="1" name="usage"
            <?=($csOrder['usage']==1)?'checked="checked"':'';?>  >
            <label for="usage1"  onmouseout="showMsg('')" onmouseover=" showMsg('遠端庫存顯示扣除')">客戶預訂</label>
            <input type="radio" id="usage2" value="2" name="usage"
             <?=($csOrder['usage']==2)?'checked="checked"':'';?>  >
            <label for="usage2"   onmouseout="showMsg('')" onmouseover=" showMsg('遠端庫存顯示不扣除')">估價使用</label>
       </div>
        </td>
        <td>
        <a onclick="formSendOut()" href="/csorder/estimate<?=(strstr($_SERVER['REQUEST_URI'],'?')!='')?strstr($_SERVER['REQUEST_URI'],'?'):'?'?>&csOrderID=<?=$csOrder['csOrderID']?>&shopID=<?=$csOrder['shopID']?>" target="_new">
        <input type="button" value="列印估價單" class="big_button">
        </a>
        </td>
    </tr>
    <tr>
        <input type="hidden" name="csOrderID" value="<?=$csOrder['csOrderID']?>">
        <input type="hidden" name="shopID" value="<?=$csOrder['shopID']?>">
        <td>訂購人：<br/><input  class="medium_text" type="text"  name="name" value="<?=$csOrder['name']?>"></td>
        <td>會員編號：<br/><input class="medium_text" type="text"  name="memberID" value="<?=$csOrder['memberID']?>"></td>
        <td>電話：<br/><input class="medium_text" type="text"  name="phone" value="<?=$csOrder['phone']?>"></td>
        <td>e-mail：<br/><input class="big_text" type="text"  name="email" value="<?=$csOrder['email']?>"></td>
        <td>
         <a  href="/csorder/getproduct?csOrderID=<?=$csOrder['csOrderID']?>&shopID=<?=$csOrder['shopID']?>" target="_new">
        <input type="button" value="點此列印客人簽收單" class="big_button">
        </a>

        </td>
    </tr>
        <tr>
          <td>通用折數：<br/><input class="medium_text" type="text" id="discount" onchange="discountChange()" name="discount" value="<?=$csOrder['discount']?>"></td>
        <td>報價日期：<input class="medium_text" type="text" id="outDate"  name="outDate" value="<?=$csOrder['outDate']?>"></td>
        <td>統一編號：<br/><input class="medium_text" type="text"  name="IDNumber" value="<?=$csOrder['IDNumber']?>"></td>
       
        <td>抬頭：<br/><input  class="big_text" type="text"  name="title" value="<?=$csOrder['title']?>"></td>
        <td> 
        <input type="button" value="儲存現有資料" class="big_button" onclick="formSendOut()">
        </td>
    </tr>
     <tr>
        <td colspan="2" id="cargoStatus">出貨狀態：
        
           <?php if($csOrder['source']==1):?>
                <?php if($csOrder['cargoStatus']==0):?><span onmouseover=" showMsg('網路訂單請至結帳區過帳')" >未出貨</span>
                <?php else:?>
                    已出貨
                <?php endif;?>
           
           <?php else:?>
            <input type="radio" id="cargoStatusRadio1" value="0" name="shipStatus"
             <?=($csOrder['cargoStatus']==0)?'checked="checked"':'';?>  >
            <label for="cargoStatusRadio1"  onmouseout="showMsg('')" onmouseover=" showMsg('點此設定為未交貨給客人')">未交貨</label>
            <input type="radio" id="cargoStatusRadio2" value="1" name="shipStatus"
              <?=($csOrder['cargoStatus']==1)?'checked="checked"':'';?>  >
            <label for="cargoStatusRadio2"  onmouseout="showMsg('')" onmouseover=" showMsg('點此設定為已交貨')">已交貨</label>
            
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
            <textarea name="comment" style="width:100%;height:100px;">
                <?=$csOrder['comment']?>
            </textarea>
        
  
        
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
        <td>店內庫存</td>
        <td>操作</td>
        <td>公司庫存</td>
    </tr>

</table>
    </form>
<!--input type="button" value="確定" class="big_button" onclick="formSendOut()"-->



</body>
</html>