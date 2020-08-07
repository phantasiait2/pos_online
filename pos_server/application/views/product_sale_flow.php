<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="/javascript/jquery.js"></script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
          
          $.post('/product_flow/latest_product_sell',{productID:<?=$productID?>},function(r)
            {
    
                var data = google.visualization.arrayToDataTable(r.out);

            var options = {
              title: r.title,
              curveType: 'none',
              legend: { position: 'bottom' }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            chart.draw(data, options);

    
    
    
    
        },'json')
          
        
      }
    </script>




<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<title>銷售分析</title>
</head>


 


<body>
 <div id="productChart">
      <div id="curve_chart" style="width: 700px; height: 400px"></div>
  <table border="1">
  
      <tr>
          <td>店家名稱</td>
          
          <td>銷售數量</td>
          
      </tr>
 <?php $total = 0;foreach($sell as $row):$total+=$row['num']?>
 
      <tr>
          <td><?=$row['name']?></td>
          
          <td><?=$row['num']?></td>
          
      </tr>
      
     
 

   
    
<?php endforeach;?>
 
      <tr>
          <td>總量</td>
          
          <td><?=$total?></td>
          
      </tr>
  </table>
 
 
</body>
</html>