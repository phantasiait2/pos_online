<?php if($isIframe):?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />
<link rel="stylesheet" type="text/css" href="/style/pos_product.css" />
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pos.js"></script>
<title>Untitled Document</title>
</head>
<body>
<?php endif;?>
<script type="application/javascript">
function sanguoshaCheck()
{
	sanguoshaID = $('#sanguoshaID').val();
	userID = $('#userID').val();
	userIDConfirm = $('#userIDConfirm').val();
	if(fucCheckNUM(sanguoshaID)==0)
	{
		alert('請輸入數字');
		$('#sanguoshaID').select();
		return;	
		
	}
	
	
	if(userID=="")
	{
		
		alert("請輸入身分證字號");
		$('#userID').select();
		return;
	}
	if(userID!=userIDConfirm) 
	{
		alert('兩次身分證字號不相同');
		$('#userIDConfirm').select();
		return;
	}
	$.post('/product/sanguosha_check_id',{sanguoshaID:sanguoshaID,userID:userID},function(data)
	{
		if(data.result==true)
		{
			sanguoshaSend({sanguoshaID:sanguoshaID,userID:userID,shopID:<?=$shopID?>})
		
			
		}
		else if(data.errCode==1) //表保證卡ID重覆
		{
			alert('此保證卡已經輸入過了，請重新輸入');
			$('#popUpBoxEnter').show();
			$('#sanguoshaID').select();
		}
		else if(data.errCode==2) //表身分證與網路重覆
		{
			if(confirm('此身分證已經在網路上使用過，請詢問客人是否在網路上購買過，若無，點選確定將資料送出，否則點選取消'))
			{
				sanguoshaSend({sanguoshaID:sanguoshaID,userID:userID,shopID:<?=$shopID?>})
				
			}
			$('#popUpBoxEnter').show();
			$('#userIDConfirm').select();
			
		}
		else if(data.errCode==3) //表身分證與其他店重覆
		{
			alert('此身分證已經使用過365方案，不能再使用了');	
			$('#popUpBoxEnter').show();
			$('#userIDConfirm').select();
		}
		
		else if(data.errCode==4) //表身分證字號有誤
		{
			alert('此身分證字號有誤，請重新檢查');	
			$('#popUpBoxEnter').show();
			$('#userIDConfirm').select();
		}

		
		
		
	},'json')
}

function sanguoshaSend(sanguoshaArray)
{
	
	$.post('/product/sanguosha_send',sanguoshaArray,function(data)
	{
		if(data.result==true)
		{
			alert("已新增");
			location.reload();
		}
		else
		{
			alert(data.errMsg);
			
		}
		
	},'json')
	
}

function sanguoshaDelete(sanguoshaID,shopID)
{
	if(confirm('你確定要刪除？'))
	{
		$.post('/product/sanguosha_delete',{sanguoshaID:sanguoshaID,shopID:shopID},function(data)
		{
			if(data.result==true)
			{
				alert("已刪除");
				location.reload();
			}
			else
			{
				alert(data.errMsg);
				
			}
			
		},'json')
	}
		
	
	
}

function getSanguoshaOnline()
{
	
	$.post('/sale/get_sanguosha_online',{},function(data)
	{
		
		if(data.result==true)
		{
			
			$('#main_container').html('<table id="orderList" border="1" style=" background-color:white"></table>')
			$('#orderList').append('<tr>'+
										'<td>序號</td>'+
										'<td>姓名</td>'+
										'<td>下單日期</td>'+
										'<td>匯款資訊</td>'+
										'<td>匯款日期</td>'+
										'<td>確認匯款</td>'+
										'<td>出貨地點</td>'+
										'<td>收件人姓名</td>'+
										'<td>收件人電話</td>'+
										'<td>出貨</td>'+										
								   '</tr>')
								   i= 1;
			for(key in data.orderData)					   
			{
					content='<tr>'+
										'<td>'+data.orderData[key]['id']+'</td>'+
										'<td>'+data.orderData[key]['buyName']+'</td>'+
										'<td>'+data.orderData[key]['orderTime']+'</td>'+
										'<td>'+data.orderData[key]['remitCode']+'</td>'+
										'<td>'+data.orderData[key]['remitTime']+'</td>';
										
					if(data.orderData[key]['remitCheck']==0) content+= '<td><input type="button" value="點此確認匯款"class="big_button" onclick=" remitCheck('+data.orderData[key]['id']+')"></td>';
					else content+='<td>匯款已完成</td>';
								
						content+=		
										'<td id="address_'+data.orderData[key]['id']+'">'+data.orderData[key]['address']+'</td>'+
										'<td id="receiver_'+data.orderData[key]['id']+'">'+data.orderData[key]['receiver']+'</td>'+
										'<td id="phone_'+data.orderData[key]['id']+'">'+data.orderData[key]['phone']+'</td>';
						if(data.orderData[key]['LogisticsCode']!='')content+='<td>'+data.orderData[key]['LogisticsCode']+'</td>';
						else content+= '<td><input type="button" value="點此出貨"  class="big_button"onclick="shipmentCheck('+data.orderData[key]['id']+')"></td>';
						content+= '</tr>';
				
						$('#orderList').append(content)	;
				
				
				
			}
								   
								   
			
		}
		else
		{
			alert(data.errMsg);
			
		}
		
	},'json')	
	
}


function remitCheck(id)
{
	if(confirm('匯款資料已經核對無誤？'))
	{
		$.post('/sale/remit_check',{id:id,remitCheck:1},function(data)
		{
			
			if(data.result==true) 
			{
				alert('完成匯款');
				getSanguoshaOnline();
			}
			else alert('糟了，哪裡出錯了');
			
			
		},'json')
		
	
	
	}
	
	
	
}
function shipmentCheck(id)
{
	content='<h1>出貨資訊</h1>';
	content+='收件人姓名：'+$('#receiver_'+id).html()+'<br/>';
	content+='收件人電話：'+$('#phone_'+id).html()+'<br/>';
	content+='收件人地址：'+$('#address_'+id).html()+'<br/>';
	content+='物流單號：<input type="text" id="LogisticsCode" placeholder="ex:日通 13076582">';
	content+='<input type="hidden" id="this_id" value="'+id+'">';
	 openPopUpBox(content,600,280,'shipmentSend');
	
}


function shipmentSend()
{
	if($('#LogisticsCode').val()=='') 
	{
		
		alert('請輸入配送單號');
		$('#popUpBoxEnter').show()
		return;
		
	}	
	
	else
	{
		$.post('/sale/sanguosha_shipment',{id:$('#this_id').val(),LogisticsCode:$('#LogisticsCode').val()},function(data)
		{
			
			if(data.result==true)
			{
				
							

				$.post('/order/shipment_update',{shipmentID:data.shipmentID,shipmentCode:$('#LogisticsCode').val(),shipment_type:0,shipment_status:2,shipmentComment:''},function(data)
				{
						if(data.result==true)
						{
							 alert('完成出貨');
							 getSanguoshaOnline();
							closePopUpBox();
						}
				},'json')
			}
			else alert('糟了，哪裡出錯了');
			
			
		},'json')
		
		
	}
	
	
}

		
</script>
<input type="button" value="新增"  class="big_button" onclick="$('#newBlock').slideDown()" style="text-align:left">
<?php if($shopID==0):?>
<input type="button" value="網路訂單"  class="big_button" onclick="getSanguoshaOnline()" style="text-align:left">
<?php endif;?>
<div id="main_container">
    
    <div style="display:none; text-align:left; background:#FFF; margin-bottom:10px;" id="newBlock">
        <h1>請輸入保證卡號碼</h1>
        <input type="text" class="big_text" id="sanguoshaID" placeholder="000000">
        
        <h1>請購買者提供證件，並輸入身分證字號</h1>
        <input type="text"  class="big_text"  id="userID" placeholder="提示:可用條碼直接刷身分證">			
        <h1>請再一次輸入身分證字號</h1>
        <input type="text" class="big_text"  id="userIDConfirm">
        <div style="clear:both"></div>
    <input type="button" value="確認"  class="big_button" onclick="sanguoshaCheck()"><input type="button" value="取消"  class="big_button" onclick="$('#newBlock').slideUp()">
    
    </div>
    
    
    
    
            <table border="1" style="background:#FFF">
                <tr>
                    <td>項次</td>
                    <td>保證卡號碼</td>
                    <td>購買時間</td>
                    <td>購買地點</td>
                    <td>身分證字號</td>
                     <td></td>
                </tr>       	
        <?php $i=1; foreach($sanguosha as $row):?>
                <tr>
                    <td><?=$i++?></td>
                    <td><?=$row['sanguoshaID']?></td>
                    <td><?=$row['time']?></td>
                    <td><?=$row['name']?></td>
                    <td><?=substr($row['userID'],0,3).'*******'?></td>
                    <td>
                        <?php if($row['shopID']==$shopID||$shopID==0):?>
                            <input type="button" value="刪除"  class="big_button" onclick="sanguoshaDelete(<?=$row['sanguoshaID']?>,<?=$row['shopID']?>)">
    
                        <?php endif;?>
                    
                    
                    </td>
                    
                    
                </tr>
        <?php endforeach;?>
            </table>
</div>
<?php if($isIframe):?>
</body>
</html>
<?php endif;?>