<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);    
    
     function drawChart() {
          
          $.post("/sale/get_year_chart/<?=$date?>/<?=$assignShopID?>",{},function(r)
            {
              alert('ss');
                console.log(r)
                var data = google.visualization.arrayToDataTable(r.out);

            var options = {
              title: r.title,
              curveType: 'none',
              legend: { position: 'bottom' }
            };

            var chart = new google.visualization.LineChart(document.getElementById('yearChart'));

            chart.draw(data, options);

    
    
    
    
        },'json')
    
     }
</script>
<script type="text/javascript">
    /*
swfobject.embedSWF("/javascript/open-flash-chart.swf", "yearAllChart", "580", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/get_year_chart/<?=$date?>/0"} );
*/
</script>



<!--
<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "chart", "580", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/get_chart/<?=$date?>/<?=$assignShopID?>"} );
</script>
<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "allChart", "580", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/get_chart/<?=$date?>/0"} );
</script>



<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "bar", "580", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/get_bar/<?=$date?>/<?=$assignShopID?>"} );
</script>

<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "allBar", "580", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/get_bar/<?=$date?>/0"} );
</script>

<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "customerBar", "580", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/get_customer_bar/<?=$date?>/<?=$assignShopID?>"} );
</script>

<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "customerAllBar", "580", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/get_customer_bar/<?=$date?>/0"} );
</script>



<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "customerFullBar", "580", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/get_customer_bar/<?=substr($date,0,5).'0'?>/<?=$assignShopID?>"} );
</script>

<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "customerFullAllBar", "580", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/get_customer_bar/<?=substr($date,0,5).'0'?>/0"} );
</script>

<script type="text/javascript" src="http://shipment.phantasia.com.tw/javascript/geo.js"></script>
-->
<script type="text/javascript" src="http://shipment.phantasia.com.tw/javascript/pos_card_sleeve.js"></script>

<script type="text/javascript" >
getInshopWithoutCs();


</script>





	
<link rel="stylesheet" type="text/css" href="/style/pos.css" />


<title>銷售分析</title>
</head>

<body onload="">


<div id="analyze">

    <div id="yearChart"> 
        </div>
   <!--     
    <div id="yearAllChart">  
     
        </div>
     
    <div id="chart">  若你看見此段文字，而無法看見圖表，請點下列連結<br/>
       請複製此段文字，然後貼到網址列 chrome://settings/content <br>
        並且下移至flash的將設定改為『允許網站執行flash』<br/>   
        然後重新啟動瀏覽器<br/>    
        <img src="/images/flash.jpg">
        </div>
    <div id="allChart">  若你看見此段文字，而無法看見圖表，請點下列連結<br/>
       請複製此段文字，然後貼到網址列 chrome://settings/content <br>
        並且下移至flash的將設定改為『允許網站執行flash』<br/>   
        然後重新啟動瀏覽器<br/>    
        <img src="/images/flash.jpg">
        </div>
    
    
    <div id="bar">  若你看見此段文字，而無法看見圖表，請點下列連結<br/>
       請複製此段文字，然後貼到網址列 chrome://settings/content <br>
        並且下移至flash的將設定改為『允許網站執行flash』<br/>   
        然後重新啟動瀏覽器<br/>    
        <img src="/images/flash.jpg">
        </div>
    <div id="allBar">  若你看見此段文字，而無法看見圖表，請點下列連結<br/>
       請複製此段文字，然後貼到網址列 chrome://settings/content <br>
        並且下移至flash的將設定改為『允許網站執行flash』<br/>   
        然後重新啟動瀏覽器<br/>    
        <img src="/images/flash.jpg">
        </div>
    
    <div id="customerBar">  若你看見此段文字，而無法看見圖表，請點下列連結<br/>
       請複製此段文字，然後貼到網址列 chrome://settings/content <br>
        並且下移至flash的將設定改為『允許網站執行flash』<br/>   
        然後重新啟動瀏覽器<br/>    
        <img src="/images/flash.jpg">
        </div>
    <div id="customerAllBar">  若你看見此段文字，而無法看見圖表，請點下列連結<br/>
       請複製此段文字，然後貼到網址列 chrome://settings/content <br>
        並且下移至flash的將設定改為『允許網站執行flash』<br/>   
        然後重新啟動瀏覽器<br/>    
        <img src="/images/flash.jpg">
        </div>
    
    <div id="customerFullBar">  若你看見此段文字，而無法看見圖表，請點下列連結<br/>
       請複製此段文字，然後貼到網址列 chrome://settings/content <br>
        並且下移至flash的將設定改為『允許網站執行flash』<br/>   
        然後重新啟動瀏覽器<br/>    
        <img src="/images/flash.jpg">
        </div>
    <div id="customerFullAllBar">  若你看見此段文字，而無法看見圖表，請點下列連結<br/>
       請複製此段文字，然後貼到網址列 chrome://settings/content <br>
        並且下移至flash的將設定改為『允許網站執行flash』<br/>   
        然後重新啟動瀏覽器<br/>    
        <img src="/images/flash.jpg">
        </div>
    
    -->

</div>

</body>
</html>