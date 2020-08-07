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
               
                $("#csorderTable").tablesorter({widgets: ['zebra']});
                    
              $('.detailClass').trigger('click');
                
            })
            
            function loadDetail(csorderID)
            {
               
                $.post('/csorder/get_csorder_detail',{csOrderID:csorderID},function(data)
                      {
                      if(data.result==true)
                            {
                                
                                for(var key in data.csOrderData)
                                    {
                                        
                                       $('#csorder_tr_'+csorderID).append(
                                         '<tr><td>'+data.csOrderData[key]['ZHName']+'</td>'+
                                            '<td style="text-align:right">'+data.csOrderData[key]['num']+'</td>'+
                                            '<td  style="text-align:right">'+data.csOrderData[key]['stockNum']+'</td></tr>'
                                        ) 
                                        
                                    }
                                
                                
                            }
                                                                                                                
                    
                    
                },'json')
         
                
                
            }
            function deleteCsorder(csOrderID)
            {
                
                confirm('你確定要刪除這一筆紀錄？');
                  $.post('/csorder/delete_csorder',{csOrderID:csOrderID},function(data)
                 {
                    if(data.result==true)
                        {
                            
                            
                          $('#csorderlist_'+csOrderID).fadeOut();
                            
                            
                        }
                    
                   
                   
                   
               },'json')
                
                
                
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
        
        
        
        
            function newCsorder()
            {
               
                content='<h1>請選擇預訂單的來源</h1>';
                
                content+='<input type="button" class="big_button" value="現場訂購" onclick="createCsorder(2)"><br/>';
                 content+='<input type="button" class="big_button" value="社群網站訂購" onclick="createCsorder(3)"><br/>';
                 content+='<input type="button" class="big_button" value="電話訂購" onclick="createCsorder(3)"><br/>';
                
                 openPopUpBox(content,500,300,'closePopUpBox',true);
                
                $('#popUpBoxEnter').hide();
            }
        
    </script>
    
</head>
<body>
  
    <a href="/csorder/orderlist/0<?=strstr($_SERVER['REQUEST_URI'],'?')?>"><input type="button" class="big_button"  value="未完成的訂單"></a>
    <a href="/csorder/orderlist/1<?=strstr($_SERVER['REQUEST_URI'],'?')?>"><input type="button" class="big_button"  value="已完成的訂單"></a>
<input type="button" class="big_button"  value="新增預訂單" onclick="newCsorder()">
 <a  href="/csorder/orderlist_ready<?=strstr($_SERVER['REQUEST_URI'],'?')?>"><input type="button"  class="big_button"  value="可出貨的訂單"></a>
 
   <h1><?=($this->uri->segment(3)==0)?'未':'已'?> 完成的訂單</h1>
 
<table id="csorderTable" border="1"  class="tablesorter">
    <thead>   
    <tr>
        <th> 變更</th>
        <th> 預訂時間 </th>
        <th> 訂單編號   </th>
       
        <th> 訂單來源 </th>
        <th> 預訂人   </th>
        <th> 會員編號 </th>
        <th> 電話   </th>
        <th> email   </th>
        <th> 預訂總額 </th>
        <th> 用途    </th>  
        <th> 出貨狀態 </th>
        <th> 付款狀態 </th>
        <th> 內容</th>
        <th> 備註</th>
        <th> 刪除</th>
        
         
        
    </tr>
    </thead>
    <tbody>
    
    <?php foreach($csOrderList as $row):?>
    <tr id="csorderlist_<?=$row['csOrderID']?>">
        <td><input type="button" value="變更" onclick="showOrder(<?=$row['csOrderID']?>)"></td>
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
        <td>  <?=$row['phone']?>     </td>  
        <td>  <?=$row['email']?>  </td>
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
        <td>         <table class="detailClass" id="csorder_tr_<?=$row['csOrderID']?>" onclick=" loadDetail(<?=$row['csOrderID']?>)">
                <tr><td>商品名稱</td><td>訂購數量</td><td>現在庫存</td></tr>
  
            
            
             </table>
            
             </td>
        <td>  <?=$row['comment']?>  </td>
         <td> 
             <?php if($this->uri->segment(3)!=1):?>
           <input type="button" value="刪除" onclick="deleteCsorder(<?=$row['csOrderID']?>)">  
            <?php  endif;?>
        </td>
        
        
        
    </tr>
    <?php endforeach?>
    </tbody>






</table>


</body>
</html>