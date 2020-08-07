<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>標籤列印</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body style="margin:0;padding:0;width:57mm;height:30mm;">


     
      <?php foreach($product as $key=>$row):?>
       <div style="height:37mm;;width:55mm;padding:1mm 1mm 0 1mm ">
           <?php if($row['suppliers']==167||$row['suppliers']==201):?>
            <img src="/images/PH_logo_bk.jpg" style="height:25%;display:block;float:left;margin:auto;">
            <?php else:?>
            <img src="/images/2019logo-bw.png" style="height:25%;display:block;float:left;margin:auto;">
            <?php   endif;?>
            <div style="float:left;margin-top:5%;font-size:18px;"><?=$row['ENGName']?></div>
           <div style="clear:both"></div>
            <div style="font-family:Microsoft JhengHei;font-size:12px;font-weight:bold"><span style="font-size:14px"><?=$row['ZHName']?>
<?php $len = strlen($row['ZHName']);
               
                for($i=$len;$i<=38;$i++) echo '　';
                
                ?>



</div>
        
            <img style="margin-left:5%;width:90%;height:40%" src="https://mart.phantasia.tw/barcode?t=<?=$row['barcode']?>&show=1&h=100">
        </div>
    <?php endforeach;?>

</body>
</html>