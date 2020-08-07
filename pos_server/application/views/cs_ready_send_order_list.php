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
  
   
    <h1>以下商品已經通知客人取貨，請備妥商品不要將商品混入販售區</h1>
   
<table id="csorderTable" border="1"  class="tablesorter">
    <thead>   
    <tr>
        
        <th> 預訂時間 </th>
        <th> 訂單編號   </th>
       
        <th> 訂單來源 </th>
        <th> 預訂人   </th>
        <th> 會員編號 </th>
        <th> 預訂總額 </th>
       
        <th> 出貨狀態 </th>
        <th> 付款狀態 </th>
        <th> 備註</th>
        <th> 貨品內容</th>
      
        
    </tr>
    </thead>
    <tbody>
    
    <?php $en = 0; foreach($csOrderList as $row):?>
    <tr id="csorderlist_<?=$row['csOrderID']?>">
      
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
                    <tr>
                    <td
                     <?php if($each['stockNum']<=$each['num']):?>
                    style="background-color:red"
                    <?php endif;?>
                    
                    ><?=$each['ZHName']?></td>
                    <td  style="text-align:right;
                     <?php if($each['stockNum']<=$each['num']):?>
                    background-color:red
                    <?php endif;?>
                      "><?=$each['num']?></td><td  style="text-align:right; <?php if($each['stockNum']<=$each['num']):?>
                    background-color:red
                    <?php endif;?>"><?=$each['stockNum']?></td></tr>
                
                <?php endforeach;?>
            
            
            
             </table>
            
            
            
        </td>
       
        
        
    </tr>
    <?php endforeach?>
    </tbody>






</table>


</body>
</html>