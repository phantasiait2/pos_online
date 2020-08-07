<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<script type="text/javascript" src="/javascript/pos.js"></script>
<script type="text/javascript" src="/javascript/pos_order.js?v20130724"></script>

<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script type="text/javascript" src="/javascript/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/javascript/date_format.js"></script>
<script type="text/javascript" src="/javascript/pos_phantri.js"></script>
<script src="http://www.phantasia.tw/libs/dtaetimepicker/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="http://www.phantasia.tw/libs/dtaetimepicker/jquery.datetimepicker.css"/ >
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css">
<script>
$(document).ready(function()
{
		jQuery('#nweDate').datetimepicker(
		{
			timepicker:false,
			lang:'zh-TW',
			format:'Y-m-d',
			//minTime:'10:00',// now
			//maxTime:'21:00',	
			// allowTimes:[  '10:00',  '10:30',  '11:00',  '11:30', '12:00',  '12:30',  '13:00',  '13:30', '14:00',  '14:30',  '15:00',  '15:30', '16:00',  '16:30',  '17:00',  '17:30', '18:00',  '18:30',  '19:00',  '19:30', '20:00' ,'20:30', '21:00'],
			minDate:'+1970/01/01', // today+1
		//	maxDate:'+1970/01/14', // 兩個禮拜		
			//startDate:mydate.getFullYear()+'/'+mydate.getMonth()+'/'+mydate.getDate(),
		   onChangeDateTime:function(dp,$input){
				//checkTime($input.val())
		  }
			}	
			);
			
			
			
	
})


function holidaySendOut()
{
	$.post('/check/new_holiday',{date:$('#nweDate').val()},function(data)
	{
		if(data.result==true)
		{
			
			$('#nweDate').val('')
			location.reload();
			
		}
		
	},'json')	
	
	
}

function deleteholidaySendOut(date)
{
	if(confirm('你確定要刪除？'))
	$.post('/check/delete_holiday',{date:date},function(data)
	{
		if(data.result==true)
		{
			
			$('#nweDate').val('')
			location.reload();
			
		}
		
	},'json')	
	
	
}


    
    
    function setHoliday(h)
{
	
	$.post('/check/set_holiday',{holiday:h},function(data)
	{
		if(data.result==true)
		{
			
			
			alert('設定完成')
			
		}
		
	},'json')	
	
	
}

</script>
<title>Untitled Document</title>
 
</head>

<body style="background-color:white">
<h1>固定公休日設定：</h1>
<label><input type="radio" name="holiday" <?=($systemInf['holiday']==0)?'checked=checked':''?>value="0" onclick="setHoliday(this.value)">星期日</label>
<label><input type="radio" name="holiday" <?=($systemInf['holiday']==1)?'checked=checked':''?>value="1" onclick="setHoliday(this.value)">星期一</label>
<label><input type="radio" name="holiday" <?=($systemInf['holiday']==2)?'checked=checked':''?>value="2"  onclick="setHoliday(this.value)">星期二</label>
<label><input type="radio" name="holiday" <?=($systemInf['holiday']==3)?'checked=checked':''?>value="3" onclick="setHoliday(this.value)">星期三</label>
<label><input type="radio" name="holiday" <?=($systemInf['holiday']==4)?'checked=checked':''?>value="4" onclick="setHoliday(this.value)">星期四</label>
<label><input type="radio" name="holiday" <?=($systemInf['holiday']==5)?'checked=checked':''?>value="5" onclick="setHoliday(this.value)">星期五</label>
<label><input type="radio" name="holiday" <?=($systemInf['holiday']==6)?'checked=checked':''?>value="6" onclick="setHoliday(this.value)">星期六</label>
<label><input type="radio" name="holiday" <?=($systemInf['holiday']==-1)?'checked=checked':''?>value="-1" onclick="setHoliday(this.value)">不公休</label>


<h1>自行設定公休日如下</h1>
<input type="button" value="新增例外公休日" onclick="$('#newHoliday').slideToggle()">
<div id="newHoliday" style="display:none">
	<input type="text" value="" placeholder="請輸入日期" id="nweDate">
<input type="button" value="送出" onclick="holidaySendOut()">
</div>
<?php if(!empty($seatHoliday)):?>

<table>
	<tr><td>例外公休日</td><td></td></tr>
    <?php foreach($seatHoliday as $row):?>
  	  <tr><td><?=$row['date']?></td><td><input type="button" value="刪除" onclick="deleteholidaySendOut('<?=$row['date']?>')"></td></tr>
    <?php endforeach;?>
    
</table>
<?php endif;?>

</body>
</html>