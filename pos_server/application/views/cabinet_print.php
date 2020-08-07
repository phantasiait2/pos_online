<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>

</head>
<body>
    
<table border="1" style="font-size:9pt;font-weight:bold">
<tr>
    <td style="text-align:center;font-size:14pt"  colspan="5"><?=$cabinet?>櫃</td>
</tr>
<tr>
    <th style="width:50px">層數</th>
    <th style="width:100px">編號</th>
    <th style="width:300px">中文品名</th>
    <th style="width:210px">英文品名</th>
    <th style="width:100px">備註</th>
</tr>

<?php $k = 0;$key=false;$i=0;
     foreach($product as $row):
    
  
     if($i < $line[$k]['num']): $i++;?>
   

    <tr>
    <?php if($i==1):?>
        <td  style="text-align:center" rowspan="<?=max($line[$k]['num'],8)?>"><?=$line[$k]['name']?></td>
 
    <?php endif;?>

    <td ><?=$row['productNum']?></td>
    <td><?=$row['ZHName']?></td>
    <td><?=$row['ENGName']?></td>
    <td></td>
    </tr>
    
           <?php  if($i>=$line[$k]['num']):
                $k++;
                for($i;$i<8;$i++):?>

                        <tr>

                        <td>　</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>
                <?php endfor;?>
                    <tr><td colspan="5">　</td></tr>    
            <?php $i=0; endif;?>


     <?php endif;?>
<?php  endforeach;?>

</table>

</body>
</html>