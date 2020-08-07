<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="/javascript/swfobject.js"></script>





<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "peopleChart", "580", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/accounting/people/<?=$date?>/<?=$shopID?>"} );
</script>









	
<link rel="stylesheet" type="text/css" href="/style/pos.css" />


<title>銷售分析</title>
</head>

<body onload="">


<div id="analyze">

    <div id="peopleChart" style="float:left">
       若你看見此段文字，而無法看見圖表，請點下列連結<br/>
       請複製此段文字，然後貼到網址列 chrome://settings/content <br>
        並且下移至flash的將設定改為『允許網站執行flash』<br/>   
        然後重新啟動瀏覽器<br/>    
        <img src="/images/flash.jpg">
        
        
    </div>
    <div style="float:left">
        <table  border="solid">
            <tr><td>日</td><td>人數</td></tr>
            <?php $p=0;foreach($people as $index=>$row): if($index==0)continue;$p+=$row?>
                <tr><td><?=$index?></td><td><?=$row?></td></tr>
            <?php endforeach;?>
                 <tr><td>總人數：</td><td><?=$p?></td></tr>
        </table>
    </div>
    
    

</div>

</body>
</html>