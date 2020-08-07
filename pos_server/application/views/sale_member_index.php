<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<link rel="stylesheet" type="text/css" href="/style/sale.css" />
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/swfobject.js"></script>
<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "memberPile", "300", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/member_pile/<?=$memberID?>/"} );
</script>
<script type="text/javascript">
swfobject.embedSWF("/javascript/open-flash-chart.swf", "memberProduct", "300", "300", "9.0.0", "expressInstall.swf", 
{"data-file":"/sale/member_product/<?=$memberID?>/"} );
</script>
<body>
 <div id="memberPile"></div>
  <div id="memberProduct"></div>
</body>
</html>