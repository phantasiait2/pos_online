<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    
<link rel="shortcut icon" href="/phantasia/images/favicon.ico"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="/javascript/jquery.js"></script>
    </head> 
<div style="margin-left:10px; margin-bottom:10px;">
                                您可以將進銷存系統上的品項，與商城上的品項跟網站做成三方連結，如此一來庫存及各項數據便能達到最佳化。
                                </div>
 
<table>
    <tr>
        <td>　</td>
        <td>　</td>
        <td>pos<?=(isset($product['ZHName']))?$product['ZHName']:''?></td>
        <td>　</td>
        <td>　</td>
    </tr>
    <tr>
        <td>　</td>
    <?php if($connect[0]==1):?>    
        <td style="font-weight:bold">/</td>     
        <?php else:?>   
        <td>　</td>    
        <?php endif;?>    
        <td>　</td>
     <?php if($connect[2]==1):?>       
        <td style="font-weight:bold">\</td>     
    <?php else:?>   
        <td>　</td>
    <?php endif;?>    
        <td>　</td>
    </tr>
     <tr>
        <td>web<?=(isset($website['cha_name']))?$website['cha_name']:''?></td>
      <?php if($connect[1]==1):?>     
        <td></td>
        <td style="border-bottom:solid"></td>
        <td></td>
     <?php else:?>   
        <td>　</td>
        <td >尚未建立連結<br/>
        <?php if(isset($allMartProduct)):?>
            <select id="martLink">   
            <?php foreach($allMartProduct as $row):
            ?>
                <option value="<?=$row['pID']?>"><?=$row['pID'].$row['ZHName']?></option>
            <?php endforeach;?> 
            </select>
        <?php endif;?>   
        
        
        <input type="button" value="建立連結" onclick="location.href='/product/three_way_merage?productID=<?=$productID?>&phaBid=<?=$phaBid?>&pID='+$('#martLink').val()">　</td>
        <td>　</td>
            
       <?php endif;?>
        <td>mart<?=(isset($mart['ZHName']))?$mart['ZHName']:''?></td>
    </tr>




</table>