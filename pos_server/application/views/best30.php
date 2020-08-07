<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="background-color:white ;margin-top:20px">
<head>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="/javascript/pos.js?date=20160414"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>


<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<style>
	
.fancytable{border:1px solid #cccccc; width:100%;border-collapse:collapse;}
.fancytable td{border:1px solid #cccccc; color:#555555;text-align:center;line-height:28px;}
.headerrow{ background-color:#0066cc;}
.headerrow td{ color:#ffffff; text-align:center;}
.datarowodd{background-color:#ffffff;}
.dataroweven{ background-color:#efefef;}
.datarowodd td{background-color:#ffffff;}
.dataroweven td{ background-color:#efefef;}

.headerrow td{ color:#ffffff; text-align:center;}
</style>

<script>
	function showDetail(shopID,account)
	{
		
		$.post('/race/best30_detail',{shopID:shopID,account:account,year:$('#year').val(),month:$('#month').val()},function(data)
			  {
			openPopUpBox(data,1000,200,'closePopUpBox');
			setTimeout(function(){popUpBoxHeight(0)},500);
			 
			
		},'html')
		
		
		
		
		
	}
	function editInfo()
	{
		content='<h2>請輸入系統登入密碼</h2><input type="password" id="pw" value="">';
		openPopUpBox(content,
					 250,200,'editInfoGet');
		
		
		
		
	}
	function editInfoGet()
	{
		
		$.post('/race/edit_info_get',{pwCode:$('#pwCode').val(),pw:$('#pw').val(),shopID:$('#shopID').val(),account:$('#account').val()},function(data){
			if(data.result==true)
				{
					alert('OK');
					$('#name').val(data.info['name']);
					$('#account').val(data.info['account']);
					$('#pwCode').val(data.info['pwCode']);
					$('#address').val(data.info['address']);
					$('#IDNumber').val(data.info['IDNumber']);
					$('#phone').val(data.info['phone']);
					$('#bankCode').val(data.info['bankCode']);
					$('#bankAccount').val(data.info['bankAccount']);
					$('#infoBlock').slideToggle();
					$('#editBox').hide();
					closePopUpBox();					
				}
			else 
			{
				editInfo();
				alert('密碼錯誤');
				
			}
			
		},'json')
		
		
		
	}
	
	</script>
</head>

<body style=" background-color:none">

	
<?php if($shopID!=0&&$account!=''):?>
	<form  action="/race/best30_inf_send" method="post">
	<input type="hidden" class="big_text"   id="shopID" name="shopID" value="<?=$shopID?>">
	<input type="hidden" class="big_text" id="account" name="account" value="<?=$account?>">
	<input type="hidden" class="big_text" id="pwCode" name="pwCode" value="<?=$pw?>">
	<div style="display:none;background-color:#FFFFBB;padding:10px" id="infoBlock">
	<h2>為配合獎金發放，請填寫以下資訊</h2>
	姓　　　名：<input type="text" id="name" class="big_text" name="name"><br/>
	戶籍地址　：<input type="text"  id="address"  class="big_text" name="address"　s><br/>
	身分證字號：<input type="text"  id="IDNumber"  class="big_text" name="IDNumber"><br/>
	電　　　話：<input type="text"  id="phone"  class="big_text" name="phone"><br/>
	(中國信託佳，其它帳戶獎金扣除手續費後匯出)<br/>
	銀行代碼　：<input type="text" id="bankCode"  class="big_text" name="bankCode"><br/>
	銀行帳號　：<input type="text"  id="bankAccount" class="big_text" name="bankAccount"><br/>
	<input type="submit"  value="送出" class="big_button">
	</div>
	
	
<?php if(empty($info)):?>
		<script type="text/javascript"> $('#infoBlock').slideToggle();</script>
<?php else:?>
		<div id="editBox" style="background-color:#FFFFBB;padding:10px"><h2>您已經填過基本資料<input type="button"  value="修改" class="big_button" onclick="editInfo()"></h2> </div>

<?php endif;?>
	</form>	
<?php endif;?>
<div style="float:left; width:450px" >
	【瘋桌遊精選桌遊30】方案即日起正式啟動，<br/>
這將是瘋桌遊在未來一整年，主力推動的方案，<br/>
請各位夥伴務必配合推動!!<br/>

<h2>⭐瘋桌遊精選30 銷售獎金制度⭐</h2>

	<h2>✅一、凡銷售即可得 6%獎金回饋：</h2>
瘋桌遊精選30之商品，凡瘋桌遊門市人員成功銷售1件，<br/>
將由總公司提供該商品之，商品原價x 6%，<br/>
作為獎金給該位銷售人員<br/>

<h2>✅二、每月8款主打，10%獎金回饋：</h2>
為更能提高銷售的成效，總公司將從精選30當中，<br/>
每月挑選出8款為當月主打，設定銷售目標門檻為，<br/>
每間門市同款商品至少賣出3個，<br/>
完成將由總公司提供該商品之，商品原價x 10%，<br/>
作為獎金給該位銷售人員<br/>
※請各門市人員注意，6%獎金與每月8款主打之10%獎金不得重複領取<br/>
<h2>✅【<?=$month?>月之8款主打】: </h2>
<h3>
<?php $i = 0;
if(empty($best30Major)):?>
尚未設定
<?php else:
foreach($best30Major as $row):?>
	<?=($i++!=0)?',':''?>
	<?=$row['name']?>
<?php endforeach?>
<?php endif;?>
</h3>
-<br/>

目前市場上的桌遊品項眾多，<br/>
各店家所販售的品項超過上百樣，<br/>
且一直不斷的在推陳出新，但經過調查及數據，<br/>
熱賣的品項其實不超過30-50款，<br/>
在這樣的情況下，門市的夥伴在銷售上反而無法聚焦，<br/>
另一方面，在無法集中火力的情況下，<br/>
總公司更無法與代理商取得更好的方案以嘉惠各位<br/>

-<br/>

有鑑於此，選取的30款涵蓋各年齡層及族群的桌遊，<br/>
並於前幾個月不斷的與內外部開會討論及爭取。<br/>
初衷便是，希望能幫助各位聚焦，<br/>
把商品賣好!有更穩定的收入，<br/>
以在日益競爭的桌遊市場中，<br/>
佔有一席之地並實現未來的目標!<br/>

-<br/>

因此，<br/>
從即日起，【瘋桌遊精選桌遊30】系列方案將正式起跑，<br/>未來將陸續有，直接回饋各位的獎金制度、<br/>
各式相關活動、行銷方案、廣告露出、<br/>
課程及活動推廣置入、商品陳設專區建議、<br/>
銷售話術教學等等，<br/>
希望總公司及門市夥伴，大家有共同的目標，<br/>
就是把【瘋桌遊精選桌遊30】用力地賣好!認真地推廣出去!最重要的是，若只是規劃而不執行，<br/>
這項任務將滯礙難行，<br/>
所以，非常需要各位的配合與確實落實執行!!<br/>

-<br/>

</div>
<div style="float:left">
<form id="reportForm" name ="reportForm"action="/race/best30/<?=$shopID?>/<?=$account?>" method="get">
<select name="year" id ="year">
<?php $time = getdate();for($i=$time['year'];$i>=2018;$i--):?>
<option value="<?=$i?>"  <?=($i==$year)?'selected="selected"':''?>><?=$i?></option>
<?php endfor;?>
</select>
年
<select name="month" id ="month"  >
<?php for($i=1;$i<=12;$i++):?>
<option value="<?=$i?>" <?=($i==$month)?'selected="selected"':''?>><?=$i?></option>
<?php endfor;?>
</select>
月
<input type="submit" value="查詢"> 
	</form>




	<h1>精選30獎金排行龍虎榜！！</h1>
<table border="1"　class="fancytable" style="width:400px">
	<tr class="headerrow">
		<td>名次</td>
		<td>店家</td>
		<td>帳號</td>
		<td>獎金</td>
		<td>明細</td>
		
	</tr>

	<?php $i = 0;$k = 0;$t = 0 ; $total = 0 ;if(!empty($r))foreach($r as $row):$total+=round($row['bonusTotal']);
		$i++;
		if($i<=10 || $row['shopID']==$shopID||$shopID==0):$k++?>
		<?php if($k!=$i):$k = $i?>
	<tr　 class="<?=($t++%2==0)?'datarowodd':'dataroweven'?>"><td colspan="5">．．．．</td></tr>
		<?php endif?>
		
		
		<tr class="<?=($t++%2==0)?'datarowodd':'dataroweven'?>">
		<td><?=($i==1)?'<img src="/images/aword.jpg" style="height:50px">':''?><?=$i?></td>
		<td><?=$row['shopName']?></td>
		<td><?=$row['account']?></td>
		<td><?=round($row['bonusTotal'])?></td>
		<?php if($shopID==$row['shopID']||$shopID==0):?><td><input type="button" value="明細" onclick="showDetail(<?=$row['shopID']?>,'<?=$row['account']?>')">
		</td>
		 <?php if($this->data['account']=='phantasia00'):?>
		<td><?=$row['name']?></td>
	    <td><?=$row['IDNumber']?></td>
   		<td><?=$row['address']?></td>
   		<td>'<?=$row['phone']?></td>
   		<td>'<?=$row['bankCode']?></td>
   		<td>'<?=$row['bankAccount']?></td>
   			<?php endif;?>
		
		
		
		<?php endif;?>
		
		
		</tr>
		<?php endif;?>
	
	

	
	
	
	<?php endforeach?>



</table>
 <?php if($this->data['account']=='phantasia00'):?>
    <h1><?= $total?></h1>
 <?php endif;?>

	</div>
	計算方式如下：<br/>
1.本次獎金將會直接發到店員個人戶頭。
以「執行業務所得」做為獎金發放。列入個人所得。<br/>

2.所有銷售獎金計算以照店內登入系統之帳號來認定。<br/>

3.一個帳號必須建立一組銀行帳號(中國信託佳，其它帳戶獎金扣除手續費後匯出)，以及登記一組身份證字號。
(系統會在月底結算前請各帳號人員登入資料)<br/>

4.5/3以前之帳號因為系統尚未記錄完善，因此5/3號前的帳號會無法分辨，此部分請店家提出一名人員做為代表發放即可。<br/>
	<a href="http://mart.phantasia.tw/product/?functionID=7&domain=menuFunction" target="_blank">
<img style="width:600px" src="/images/best30.jpg">
	</a>
	</body>
</html>