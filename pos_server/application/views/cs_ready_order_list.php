<!doctype html>
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>商品預定管理系統</title>
    <link rel="stylesheet" type="text/css" href="/style/pos.css">
    <script type="text/javascript" src="/javascript/jquery.js"></script>
    <script type="text/javascript" src="/javascript/jquery.tablesorter.js"></script>
     <script type="text/javascript" src="/javascript/pop_up_box.js"></script>
    <script type="text/javascript">
            $(document).ready(function()
                  {
                
                variable = '<?=strstr($_SERVER['REQUEST_URI'],'?')?>';
               
           
                    
              
                
            })
            function sendAll()
            {
                
                 $('.emailNotifyB').each(function()
                        {
                            $(this).trigger('click');
                            
                        
                    })
                    
                 $('#alreadyNoti').trigger('click');
                orderListSendEmail();
                
            }
         
            function showOrder(id)
            {
                
                var content = '<iframe style="width:1000px;height:800px" src="/csorder/order_view/'+id+variable+'"></ifame>';
                openPopUpBox(content,1000,800,'closePopUpBox',true);
                $('#popUpBoxEnter').hide();
                
                
            }
        
            function createCsorder(source)
            {
                $.post('/csorder/create',{source:source},function(data)
                 {
                    if(data.result==true)
                        {
                            
                            
                            showOrder(data.csOrderID);
                            
                            
                        }
                    
                   
                   
                   
               },'json')
                
                
                
            }
         
           function orderEmailNotify(csOrderID)
          {
              $('#email_'+csOrderID).html('<img src="/images/ajax-loader.gif"/>');
              $.post('/csorder/web_order_ready',{csOrderID:csOrderID},function(data)
                {
                 
                     if(data.result==true)$('#email_'+csOrderID).html(data.time);
                  
                  
              },'json')
              
              
              
              
          }
         
        function orderListSendEmail()
        {
            
            var url = "/csorder/orderlist_send_ready<?=strstr($_SERVER['REQUEST_URI'],'?')?>";
            $.post('csorder/orderlist_send_email',{url:url},function(data)
                  {
                
                alert(data);
                
            },'html')
            
            
            
            
            
        }
          function orderPhoneNotify(csOrderID)
        {
            
            
              $('#phone_'+csOrderID).html('<img src="/images/ajax-loader.gif"/>');
              $.post('/csorder/phone_notify',{csOrderID:csOrderID},function(data)
                {
                 
                     if(data.result==true)$('#phone_'+csOrderID).html(data.time);
                  
                  
              },'json')
              
            
            
            
        }
        
        
        
    </script>
    
</head>
<body>
  
    <a href="/csorder/orderlist/0<?=strstr($_SERVER['REQUEST_URI'],'?')?>"><input type="button"  class="big_button" value="未完成的訂單"></a>
    <a href="/csorder/orderlist/1<?=strstr($_SERVER['REQUEST_URI'],'?')?>"><input type="button" class="big_button" value="已完成的訂單"></a>

 <a href="/csorder/orderlist_ready<?=strstr($_SERVER['REQUEST_URI'],'?')?>"><input type="button"  class="big_button" value="可出貨的訂單"></a>
   
    <a  target="_blank"  href="/csorder/orderlist_send_ready<?=strstr($_SERVER['REQUEST_URI'],'?')?>"><input id="alreadyNoti" type="button"  class="big_button" value="已通知客人的訂單"></a>
   
    <h1>目前可出貨清單</h1>
    <input type="button" class="big_button" value="一鍵通知可取貨訂單" onclick="sendAll()">
<table id="csorderTable" border="1"  class="tablesorter">
    <thead>   
    <tr>
        <th> 變更</th>
        <th> 預訂時間 </th>
        <th> 訂單編號   </th>
       
        <th> 訂單來源 </th>
        <th> 預訂人   </th>
        <th> 會員編號 </th>
        <th> 預訂總額 </th>
        <th> 用途    </th>  
        <th> 出貨狀態 </th>
        <th> 付款狀態 </th>
        <th> 備註</th>
        <th> 貨品內容</th>
        <th> 信件通知</th>
        <th>電話通知</th>
         
        
    </tr>
    </thead>
    <tbody>
    
    <?php $en = 0; foreach($csOrderList as $row):?>
    <tr id="csorderlist_<?=$row['csOrderID']?>">
        <td><input type="button" value="查看" onclick="showOrder(<?=$row['csOrderID']?>)"></td>
        <td> <?=$row['orderTime']?> </td>
        <td>  <?=$row['csOrderNum']?></td>
        <td>  <?php 
                switch($row['source'])
                {
                case 1: echo '網路商城';break;
                case 2: echo '現場訂購';break;
                case 3: echo '社群網站訂購';break;
                case 4: echo '電話訂購';break;
                }
              ?></td>
        <td>  <?=$row['name']?>  </td>
        <td>  <?=$row['memberID']?>  </td>
        <td>  <?=$row['total']?>  </td>
        <td>  <?php 
                switch($row['usage'])
                {
                case 1: echo '客戶預訂';break;
                case 2: echo '僅估價用';break;
                } 
                ?>
                </td>
        <td>   <?php 
                switch($row['cargoStatus'])
                {
                case 0: echo '尚未交貨';break;
                case 1: echo '交貨完成';break;
                } 
                ?>
        </td>
        <td>   <?php 
                switch($row['cashStatus'])
                {
                case 0: echo '尚未付款';break;
                case 1: echo '付款完成';break;
                } 
                ?></td>
        <td>  <?=$row['comment']?>  </td>
         <td> 
            <table>
                <tr><td>商品名稱</td><td>訂購數量</td><td>現在庫存</td></tr>
                <?php foreach($row['detail'] as $each):?>
                    <tr><td><?=$each['ZHName']?></td><td  style="text-align:right"><?=$each['num']?></td><td  style="text-align:right"><?=$each['stockNum']?></td></tr>
                
                <?php endforeach;?>
            
            
            
             </table>
            
            
            
        </td>
        <td id="email_<?=$row['csOrderID']?>"><?php 
    if($row['source']==1&&strtotime(date('Y-m-d H:i:s'))-strtotime($row['emailNotify'])>3600*24*3):$en++;?>
       <input type="button" class="emailNotifyB" id="notify_<?=$row['csOrderID']?>"  onclick=" orderEmailNotify(<?=$row['csOrderID']?>)"  value="點此email通知客人">
       <?php endif;?>
       上次通知時間<br/><?=$row['emailNotify']?>
     
       </td>
                <td id="phone_<?=$row['csOrderID']?>"><?php 
    if(strtotime(date('Y-m-d H:i:s'))-strtotime($row['phoneNotify'])>3600*24*3):?>
       <input id="phoenBtn_<?=$row['csOrderID']?>"type="button"value="點此電話通知客人" onclick="$('#phoenBtn_<?=$row['csOrderID']?>').hide();;$('#phoneNotify_<?=$row['csOrderID']?>').show();"><br/>
           <?php endif;?>
           <div style="display:none" id="phoneNotify_<?=$row['csOrderID']?>">
            <?=$row['name']?>:<?=$row['phone']?>
              
           <input type="button"   onclick=" orderPhoneNotify(<?=$row['csOrderID']?>)"  value="通知完成點此紀錄時間">
          </div>
           
       上次通知時間<br/><?=$row['phoneNotify']?>

       
       </td>
        
        
    </tr>
    <?php endforeach?>
    </tbody>






</table>

<?php if($en>0):?>
    <script  type="application/javascript">
        $(document).ready(function()
                         {
            
            
            alert('你有多項商城訂單已經到貨，可點選一鍵通知，email通知客人取貨')
            
            
        })
        
    </script>    

<?php endif;?>
</body>
</html>