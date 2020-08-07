<!doctype html>
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>商品預定管理系統</title>
    <link rel="stylesheet" type="text/css" href="/style/pos.css">
 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

    <script type="text/javascript" src="/javascript/jquery.tablesorter.js"></script>
    <script type="text/javascript" src="/javascript/pos.js"></script>
     <script type="text/javascript" src="/javascript/pop_up_box.js"></script>
      <script type="text/javascript" src="/javascript/pos_product.js"></script>
    <script type="text/javascript" src="/javascript/pos_product_query.js"></script>
   <script>
  $(function() {
    $( "#cargoStatus").buttonset();
    $( "#usage" ).buttonset();
     
  });
    $(document).ready(function()
                     {
        
         showMsg('來自網路商城的店面取貨單')
        checkAllShipStatus()
          
        
    })   
       
    function showMsg(t)
       {
           
           
           $('#msg').html(t);
           
       }
       
  
       function changeShipWay(productID)
       {
           
          
           
            var min = minBack(productID);
           if(parseInt($('#product_num_'+productID).val())<min)$('#product_num_'+productID).val(min);
         checkAllShipStatus();
                                                      
       }
       
       function checkAllShipStatus()
       {
           
            $('#okToGet').show();
              $('#notOkToGet').hide();
            $('.shipWay').each(function(data)
            {
              
                var id = this.id;
                var shipWay = $('#'+id).attr('name') ;
                if($('input[name='+shipWay+']:checked').val()==1)  
                {
                    $('#okToGet').hide();
                      $('#notOkToGet').show();
                }
           })        
           
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
          else if(data.stockNum==0 ||data.stockNum<0) stockString="扣除預定商品後，已無庫存";
          else 
          {
              stockString =data.stockNum;
             
          }
            if(data.stockNum > data.num ||data.ZHName.indexOf('會員')>=0)
                  {
                      //可使用店內庫存先行交貨
                      shipString =
                      '<label>'+
                      '<input type="radio" onchange=" changeShipWay('+data.productID+')"  checked="checked" name="ship_'+data.productID+'" value="0" >使用店內庫存先行交貨'+
                      '</label><br/>'+
                      '<label>';
                      
                      if(data.ZHName.indexOf('會員')==-1)
                      shipString+=
                        '<input   class="shipWay"  onchange=" changeShipWay('+data.productID+')"  id="shipWay_'+data.productID+'" type="radio" name="ship_'+data.productID+'" value="1" >待公司補貨後交貨'+
                        '</label>'  
                          
                  }
              else
                 {
                     //可使用店內庫存先行交貨
                      shipString =
                      '<label>'+      
                        '<input type="radio" class="shipWay"  onchange=" changeShipWay('+data.productID+')"   id="shipWay_'+data.productID+'" checked="checked" name="ship_'+data.productID+'" value="1" >待公司補貨後交貨'+
                        '</label>'  
                  
                  
                 }
          if(data.num < 0) data.num = 0;
          //data.sellPrice = Math.round(data.price * data.discount/100);
          $('#productTable').append(
              '<tr>'+
                '<td>'+data.ZHName+'</td>'+
                '<td>'+data.ENGName+'</td>'+
                '<td>'+data.language+'</td>'+
                '<td>'+data.price+'</td>'+
                '<input type="hidden" id="Price_'+data.productID+'" name="Price[]" value="'+data.price+'">'+
         
                 '<td>'+data.sellPrice+'</td>'+ 

                '<td id="orderNum_'+data.productID+'">'+
              data.num+
                '</td>'+
                
                '<td id="stock_num_'+data.productID+'"><input type="hidden" id="stock_num_v_'+data.productID+'" value="'+data.stockNum+'">'+stockString+'</td>'+
                '<td>'+
             
              '<input type="hidden" class="csorderProductID" id="send_productID'+data.productID+'" name="productID[]" value="'+data.productID+'">'+
              '<span id="delete_container_'+data.productID+'">'+
                 shipString+
              '</span>'+
           
              '</td>'+
             '<td>'+
               '<div class="btn_minus" onclick="calcuteNum('+data.productID+',-1)"></div>'+ 
                '<input  class="short_text" type="text" onchange="countCsorderTotal()" id="product_num_'+data.productID+'" name="num_'+data.productID+'" style="float:left" value="'+data.num+'">'+
                '<div class="btn_plus" onclick="calcuteNum('+data.productID+',1)"></div>'+
                
              '</td>'+
                '</tr>'
          
          )
          
       }
       
       function minBack(productID)
       {
           
             shipWay = $('#shipWay_'+productID).attr('name') 
          if($('#stock_num_v_'+productID).val() > parseInt($('#orderNum_'+productID).html())) min = parseInt($('#orderNum_'+productID).html());
          else  min = (parseInt($('#orderNum_'+productID).html())-$('#stock_num_v_'+productID).val());
          min = $('input[name='+shipWay+']:checked').val() *min;
           return min;
           
           
       }
      function calcuteNum(productID,num){
          
          max = parseInt($('#orderNum_'+productID).html());
          min =  minBack(productID);
          num = parseInt($('#product_num_'+productID).val())+num;
          if(num>max) showMsg('補貨數量不可超過訂購上限');
          else if(num<min)showMsg('補貨數量不可低於訂購量-庫存');
          else if(num>=0) $('#product_num_'+productID).val(num);
          else $('#product_num_'+productID).val(0);
          countCsorderTotal();
       }
     
       
       function formSendOut()
       {
         //  alert('ajaxSendOut:'+ decodeURIComponent($('#webOrderForm').serialize()));
		     
            $('.okbtn').hide();    
           $('#loading').show();
           $.ajax({
				type: 'post',
				data:  $('#webOrderForm').serialize(),
				url: "/csorder/web_order_feedback",
				dataType: 'json',
				success: function(data){
                    
           
                    if(data.result==true)
                    {
                         $('#mainContent').hide() ;
                        if(data.shipWay==0)  $('#resultOK').show() ;
                        else  $('#resultNotOK').show() ;
                        
                            
                    }
				  //location.reload();  //
        }
			});
       }
       
       
       
       
     
  </script>
</head>
<body >
<div id="resultOK" style="display:none">
    <h1>已經以email通知客人取貨</h1>
    <h2>請事先將貨品準備妥當</h2>
    <h2>若客人逾三天未領，請電話聯絡</h2>
     <a  href="/csorder/getproduct?csOrderID=<?=$csOrder['csOrderID']?>&shopID=<?=$csOrder['shopID']?>" target="_new">
        <input type="button" value="點此列印客人簽收單" class="big_button">
        </a>

    <h3>事後亦可到，商品預定管理，找到該筆訂單列印客戶簽收單做為取貨憑證</h3>
      <h4>點選下方關閉按鈕可以關閉視窗</h4>
</div>

<div id="resultNotOK" style="display:none">
    <h1>已經通知總公司盡速補貨</h1>
    <h2>請注意商品到貨時間</h2>
     <a  href="/csorder/getproduct?csOrderID=<?=$csOrder['csOrderID']?>&shopID=<?=$csOrder['shopID']?>" target="_new">
        <input type="button" value="點此列印客人簽收單" class="big_button">
        </a>

    <h3>事後亦可到，商品預定管理，找到該筆訂單列印客戶簽收單做為取貨憑證</h3>
    <h4>點選下方關閉按鈕可以關閉視窗</h4>
</div>


<div id="mainContent">    
            
    <div>系統提示：<span id ="msg" style="color:red"></span></div>


    <form id="webOrderForm">  

    <table  style="width:850px">
        <tr>
            <td>預定時間：<br/><?=$csOrder['orderTime']?></td>
            <td>訂單編號：<br/><h2><?=$csOrder['csOrderNum']?></h2></td>
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

            </a>
            </td>
        </tr>
        <tr>
            <input type="hidden" name="announceID" value="<?=$announceID?>">
            <input type="hidden" name="csOrderID" value="<?=$csOrder['csOrderID']?>">
            <input type="hidden" name="shopID" value="<?=$csOrder['shopID']?>">
            <td>訂購人：<h2><?=$csOrder['name']?></h2></td>
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


    <table id="productTable" border="1" width="900">
        <tr>
            <td>中文</td>
            <td>英文</td>
            <td>語言</td>
            <td>原價</td>
            <td>售價</td>
            <td>訂購數量</td>
            <td>店內庫存</td>
            <td>交貨方式</td>
            <td>補貨數量</td>
        </tr>
        <?php
        if(count($csOrderData) >0)
         foreach($csOrderData as $row):?>
              <?php $row['discount'] = $csOrder['discount'];?>
              <script type="text/javascript">
                  <?php 
                  
                 $str =  json_encode($row,true);
				  $search = array('\\', "\n", "\r", "\f", "\t", "\b", "'") ;
  					  $replace = array('\\\\', "\\n", "\\r","\\f","\\t","\\b", "\'");
				$str =  str_replace($search, $replace, $str);
							  
				  
				  
                  ?>
                  var str = JSON.parse('<?=$str;?>');
             
                  cs_productTable(str)</script>
               
        <?php endforeach;?>
    </table>
        </form>

        <div id="okToGet" style="display:none">
        <h1>目前設定的狀態可以直接交貨給客人，<br/>按下下方按鈕『通知客人取貨』</h1>
        <input  class="okbtn big_button" type="button" value="通知客人取貨" class="big_button" onclick="formSendOut()">
        </div>

        <div id="notOkToGet" style="display:none">
        <h1>目前設定的狀態尚須等待補貨，<br/>按下設定完成通知總部盡速補貨，待到貨後再通知客人取貨即可</h1>
        <input class="okbtn big_button" type="button" value="請總部盡速補貨" class="big_button" onclick="formSendOut()">
        </div>
      <img id="loading" style="display:none" src="/images/ajax-loader.gif"/>
      
      
    <h2>商城取貨教學影片</h2>
      
      <iframe width="640" height="360" src="https://www.youtube.com/embed/0TaD0-zYCc8" frameborder="0" allowfullscreen></iframe>
</div>

</body>
</html>