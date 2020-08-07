<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/style/prototype.css" />
<link rel="stylesheet" type="text/css" href="/style/pos.css" />

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
 <style>
.datagrid table { border-collapse: collapse; text-align: left; width: 1000px; margin:0 auto; } .datagrid {font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; border: 1px solid #991821; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; }.datagrid table td, .datagrid table th { padding: 3px 10px; }.datagrid table thead th {background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #991821), color-stop(1, #80141C) );background:-moz-linear-gradient( center top, #991821 5%, #80141C 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#991821', endColorstr='#80141C');background-color:#991821; color:#FFFFFF; font-size: 15px; font-weight: bold; border-left: 1px solid #B01C26; } .datagrid table thead th:first-child { border: none; }.datagrid table tbody td { color: #80141C; border-left: 1px solid #F7CDCD;font-size: 12px;font-weight: normal; }.datagrid table tbody .alt td { background: #F7CDCD; color: #80141C; }.datagrid table tbody td:first-child { border-left: none; }.datagrid table tbody tr:last-child td { border-bottom: none; }.datagrid table tfoot td div { border-top: 1px solid #991821;background: #F7CDCD;} .datagrid table tfoot td { padding: 0; font-size: 12px } .datagrid table tfoot td div{ padding: 2px; }.datagrid table tfoot td ul { margin: 0; padding:0; list-style: none; text-align: right; }.datagrid table tfoot  li { display: inline; }.datagrid table tfoot li a { text-decoration: none; display: inline-block;  padding: 2px 8px; margin: 1px;color: #FFFFFF;border: 1px solid #991821;-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #991821), color-stop(1, #80141C) );background:-moz-linear-gradient( center top, #991821 5%, #80141C 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#991821', endColorstr='#80141C');background-color:#991821; }.datagrid table tfoot ul.active, .datagrid table tfoot ul a:hover { text-decoration: none;border-color: #80141C; color: #FFFFFF; background: none; background-color:#991821;}div.dhtmlx_window_active, div.dhx_modal_cover_dv { position: fixed !important; }  


.dateBox{
	width:100px;
	height:100px;
	text-align:left;
	vertical-align: text-top;
}
#dateTable td:hover{
	background-color:#F00;
	
}

#dateTable tr:hover{
	background:#800;
	color:#FFF;
	cursor:pointer;
		
}

.saturday{
	background-color:#3F9
	
}
.sunday{
	background-color:#F66
	
	
}

 </style>
<?php if(isset($css))echo $css;?>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<script type="text/javascript" src="/javascript/pos.js"></script>
<script type="text/javascript" src="/javascript/pos_order.js?v20130724"></script>
<script type="text/javascript" src="/javascript/pos_product_query.js"></script>
<script type="text/javascript" src="/javascript/pop_up_box.js"></script>
<script type="text/javascript" src="/javascript/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/javascript/date_format.js"></script>
<script type="text/javascript" src="/javascript/pos_phantri.js"></script>

<script type="text/javascript" src="/javascript/jquery.tablesorter.js"></script>
<link rel="stylesheet" type="text/css" href="http://www.phantasia.tw/libs/dtaetimepicker/jquery.datetimepicker.css"/ >
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css">
<script src="http://www.phantasia.tw/libs/dtaetimepicker/jquery.datetimepicker.js"></script>
<script type="text/javascript">
	function tableSelect(id)
	{
		content = '<select name="table" id="table_'+id+'"  onchange="bookingEdit('+id+')" form="bookingRow_'+id+'">';
		for(i=1;i<=parseInt($('#useTable').html());i++)
		{
				content +=	'<option value="'+i+'">'+i+'</option>';
		}
		content += '</select>';
		return content;
		
	}
	
	function newReserve()
	{
	
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/member/booking",
	   data: $("#bookingForm").serialize(),
	   success: function(data){
		   if(data.result==true)
		   {  
			
				changeMonth(0,false);
				 getReserveByDay('',data.date);
				 $('#newReserve').slideUp();
		   }
		  
	   }
	 });
		
	}


	function reserveRow(data)
	{
		content=
				'<tr><td><input type="hidden" class="big_text" id="id_'+data.id+'"  name="id" value="'+data.id+'"  form="bookingRow_'+data.id+'">'+
				'<input type="text" class="medium_text" id="name_'+data.id+'"  name="name" value="'+data.name+'" onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'"></td>'+
        		'<td><input type="text" class="big_text" id="time_'+data.id+'"   name="time" value="'+data.time+'"  onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'"></td>'+
				'<td>'+
					'<select id="people_'+data.id+'"  name="people"  onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'">'+
					<?php for($i=1; $i<=50 ; $i++):?>
                    　	'<option value="<?=$i?>"><?=$i ?></option>'+
                    <?php  endfor; ?>        
	                '</select>'+
				'<td><input type="text" class="medium_text" id="phone_'+data.id+'" name="phone" value="'+data.phone+'"  onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'"></td>'+
		        '<td><input type="text" class="big_text" id="email_'+data.id+'" name="email" value="'+data.email+'"  onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'"></td>'+
	        	'<td><select  id="confirm_'+data.id+'" name="confirm"   onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'">'+
            		'<option value="1">已確認定位</option>'+
		            '<option value="0">待回復客人</option>'+
            		'<option value="-1">取消訂位</option>'+                  
            	'</select></td>'+
	        	'<td>'+tableSelect(data.id)+'</td>'+
	        	'<td><input type="text" name="comment" class="big_text" id="name_'+data.id+'" value="'+data.comment+'"  onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'"></td></tr>';
				   			;
		$('#formWay').append('<form id="bookingRow_'+data.id+'"></form>');$
		('#reserve_body').append(content);	
		$('#people_'+data.id).val(data.people);
		$('#table_'+data.id).val(data.table);
		$('#confirm_'+data.id).val(data['confirm']);
		
		jQuery('#time_'+data.id).datetimepicker(
		{
			lang:'zh-TW',
			minTime:'10:00',// now
			maxTime:'21:00',	
			allowTimes:[  '10:00',  '10:30',  '11:00',  '11:30', '12:00',  '12:30',  '13:00',  '13:30', '14:00',  '14:30',  '15:00',  '15:30', '16:00',  '16:30',  '17:00',  '17:30', '18:00',  '18:30',  '19:00',  '19:30', '20:00' ,'20:30', '21:00'],
			minDate:'+1970/01/01', // today+1
		//	maxDate:'+1970/01/14', // 兩個禮拜		
			//startDate:mydate.getFullYear()+'/'+mydate.getMonth()+'/'+mydate.getDate(),
		   onChangeDateTime:function(dp,$input){
				//checkTime($input.val())
				
				// bookingEdit(data.id)
		  }
			}	
			);
		
	}


	function getReserveByDay(id,date)
	{
		
		if(date===undefined)date = $('#selectDate_'+id).val();
		$('#reserve_body').html('<img src="/images/ajax-loader.gif"/>');
		
		$.post('/check/get_reserve',{date:date},function(data)
		{
			
			if(data.result==true)
			{
				
				remainTable = (parseInt($('#useTable').html())-data['table'])
				$('#dataHeader').html(date+'的定位，已安排桌次：'+data['table']+'桌；當日剩餘桌次：'+remainTable+'桌')
				$('#reserve_body').html('');	
				if(data.reserve.length==0)$('#reserve_body').html('<h1 style="margin:10px 0 10px 0 ">今日無訂位</h1>');
				for(key in data.reserve)reserveRow(data.reserve[key]);
				
			}
			
		},'json')	
	
		
	}
	
	
	function wdChange(wd)
	{
		switch(wd)
		{
			case 1: return '【一】';
			case 2: return '【二】';
			case 3: return '【三】';
			case 4: return '【四】';
			case 5: return '【五】';
			case 6: return '【六】';
			case 7: return '【日】';
		}	
		
	}
	var today = new Date();	
	var selectYear = today.getFullYear();
	var selectMonth = today.getMonth()+1;
	
	function getCalander(date,getToday)
	{
        $('#dateBoxTr_6').hide();
		$('.dateBox').html('<img src="/images/ajax-loader.gif"/>');
		$.post('/check/get_brief_reserve',{date:date},function(data)
		{
		
			ym= date.split('-');
			var d = new Date(ym[0], ym[1]-1, 1, 0, 0, 0, 0);	
			selectYear = d.getFullYear();
			selectMonth = d.getMonth()+1;
			var row = 1; 
			$('#nowSelect').html(selectYear+' '+selectMonth+'月')
			wd = d.getDay();
			if(wd==0) wd =7;
			$('.dateBox').unbind('click');
			$('.dateBox').css('background-color','');
			for(col=1;col<wd;col++) $('#dateBox_'+row+'_'+col).html('');
			for(var i=1;i<=31;i++)	
			{
				var d = new Date(selectYear, selectMonth-1, i, 0, 0, 0, 0);	
				var wd = d.getDay();
		
				if(wd ==0)wd = 7 ; 
				var selectDate = selectYear+'-'+selectMonth+'-'+d.getDate();
				
				var id = 'content_'+row+'_'+wd;
				if(d.getMonth()==parseInt(selectMonth)-1)
				{
					
					$('#dateBox_'+row+'_'+wd).html('<input type="hidden" id="selectDate_dateBox_'+row+'_'+wd+'"  value="'+selectDate+'">'+d.getDate()+wdChange(wd)+'<div id="content_'+row+'_'+wd+'"></div>');
					
					if(getToday==true&&today.getDate()==i &&today.getFullYear()==selectYear &&today.getMonth()==selectMonth-1)
					{
						$('#dateBox_'+row+'_'+wd).css('background-color','#FFFF00');
						getReserveByDay('dateBox_'+row+'_'+wd);
					}
					if(data.reserve[selectDate].holiday==false)
					{
						//公休
						$('#dateBox_'+row+'_'+wd).css('background-color','gray');
					
						
							
					}
					if(data.reserve[selectDate].people!=0)
					{
						
						$('#'+id).html('訂位：<br/>'+data.reserve[selectDate]['people']+'人，共'+data.reserve[selectDate]['table']+'桌<br/>');
					
						remainTable = (parseInt($('#useTable').html())-data.reserve[selectDate]['table'])
						if($('#safeTable').html()>=remainTable)$('#'+id).append('<span style=" font-weight:bold">剩餘：'+remainTable+'桌<br/></span>');
						else $('#'+id).append('剩餘：'+remainTable+'桌<br/>');
						
					}
					else $('#'+id).html('');
				
				}
				else $('#dateBox_'+row+'_'+wd).html('');
				
				
				
				 $('#dateBox_'+row+'_'+wd).bind('click',function(){$('#dateConves').slideUp(); getReserveByDay(this.id)});
                $('#dateBoxTr_'+row).show();
              
				 if(wd==7) row++;;
			}
			for(col=wd+1;col<=7;col++) $('#dateBox_'+row+'_'+col).html('');
		},'json')
	}
	
	function bookingEdit(id)
	{
		
		
		$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/check/booking_edit",
	   data: $("#bookingRow_"+id).serialize(),
	   success: function(data){
		 
		
		   if(data.result==true)
		   {  
			
				changeMonth(0,false);
				
		   }
		  
	   }
	 });
	}
	
	
	function changeMonth(t,getToday)
	{
		if(t=='+1')
		{
			if((parseInt(selectMonth)+1)>12) getCalander( (parseInt(selectYear)+1)+'-'+'1',false)
			else  getCalander(selectYear+'-'+(parseInt(selectMonth)+1),false)
		}
		if(t=='-1')
		{
				if((parseInt(selectMonth)-1)<=0) getCalander( (parseInt(selectYear)-1)+'-'+'12',false)
			 	else getCalander(selectYear+'-'+(parseInt(selectMonth)-1),false)
		}
		if(t=='0') getCalander(selectYear+'-'+selectMonth,getToday)	
		
	}
	$(document).ready(function(){
			
		changeMonth(0,true);
			var today=new Date();
	    var mydate=new Date(today.getFullYear(),today.getMonth()+1,today.getDate()+1,0,0,0);
	//alert(today.getDate()+1);
	
	//alert(mydate.getFullYear()+'/'+mydate.getMonth()+'/'+mydate.getDate());
	jQuery('#datetimepicker').datetimepicker(
	{
	lang:'zh-TW',
	minTime:'10:00',// now
	maxTime:'21:00',	
	allowTimes:[  '10:00',  '10:30',  '11:00',  '11:30', '12:00',  '12:30',  '13:00',  '13:30', '14:00',  '14:30',  '15:00',  '15:30', '16:00',  '16:30',  '17:00',  '17:30', '18:00',  '18:30',  '19:00',  '19:30', '20:00' ,'20:30', '21:00'],
	minDate:'+1970/01/01', // today+1
//	maxDate:'+1970/01/14', // 兩個禮拜		
	//startDate:mydate.getFullYear()+'/'+mydate.getMonth()+'/'+mydate.getDate(),
   onChangeDateTime:function(dp,$input){
    	//checkTime($input.val())
  }
	}	
	);

	})

</script>

<script>
	



</script>

</head>
<body>
<h1 style="color:#FFF">訂位管理系統</h1>
<div style="color:#FFF">
<input type="button" value="設定公休日" class="big_button" onclick="$('#holidayReserve').slideToggle()"> 
<input type="button" value="新增訂位資訊" class="big_button" onclick="$('#newReserve').slideToggle()">  <input type="button" value="打開日期表" class="big_button" onclick="$('#dateConves').slideToggle()">   可用桌次:<span id="useTable"><?=$totalTable?></span>，安全桌次:<span id="safeTable"><?=$saveTable?></span></div> 

<iframe id="holidayReserve" src="/check/seat_holiday/" style="width:600px; display:none; height:300px">
</iframe>
<div id="newReserve" style="margin:0 auto; background-color:#FFF; border:double; width:600px; display:none">
	     <table style="width:80%; margin-left:10%; text-align:left; font-size:16px; line-height:150%;">
        <form method="post"  id="bookingForm" >
     	<tr>
        	<th>訂位人姓名： </th>
            <td>   <input   type="hidden" name="select_shop"  id="select_shop" ><input class="big_text" name="bookingPeople" id="bookingPeople" type="text" /></td>
        </tr>
        
     	<tr>
        	<th>訂位日期： </th>
            <td>
            <input class="big_text" style="text-align:center; margin:1%;"  name="datetimepicker"  id="datetimepicker" value="請選擇日期及時間" type="text" />
           </td>
        </tr>
        
     	<tr>
        	<th>訂位人數： </th>
            <td>
            	<select name="bookingNumber" id="bookingNumber" onchange="$('#table').val((Math.ceil($('#bookingNumber').val()/6)))" >
					<?php for($i=1; $i<=50 ; $i++):?>
                    　<option value="<?=$i?>"><?=$i ?></option>
                    <? endfor; ?>        
                </select>
            </td>
            <td></td>
        </tr>
        <tr>
        	<th>安排桌數： </th>
            <td>
            	<select name="table"  id="table" onchange="" >
					<?php for($i=1; $i<=50 ; $i++){?>
                    　<option value="<?=$i?>"><?=$i ?></option>
                    <? } ?>        
                </select>
            </td>
            <td></td>
        </tr>
        
     	<tr>
        	<th>連絡電話： </th>
            <td><input class="big_text" name="bookingPhone" id="bookingPhone" type="text" /></td>
        </tr>
        
        <tr>
        	<th>訂位狀況： </th>
            <td><select  name="confirm" />
            		 <option value="1">已確認定位</option>
		            <option value="0">待回復客人</option>
            		<option value="-1">取消訂位</option>
            
            
            	</select>
            </td>
        </tr>
        
        
     	<tr>
        	<th>連絡Email(無則空白)： </th>
            <td class="cont"><input class="big_text" name="bookingEmail" id="bookingEmail" type="text" /></td>
        </tr> 
             	<tr>
        	<th>備註欄(無則空白)： </th>
            <td class="cont"><input class="big_text" name="comment" id="comment" type="text" /></td>
        </tr> 
     
        
      
     	   
         
        </form> 
     </table>    
		<input type="button" value="確認送出" class="big_button" onclick="newReserve()"> <input type="button" class="big_button" value="取消" onclick="$('#newReserve').slideToggle()">

</div>




<div id="dateConves" style="margin:0px auto;">
	<table id="dateTable"  align="center" style="margin:0px auto; background-color:#FFF" border="1" width="840">
    	<tr style=" font-size:14pt"><td></td><td  onclick="changeMonth('-1')" >上個月</td><td colspan="3" id="nowSelect">2015 5月</td><td  onclick="changeMonth('+1')">下個月</td><td><input type="button" value="關閉月曆" style="float:right" class="big_button" onclick="$('#dateConves').slideToggle()"> </td></tr>
       	<?php for($j=1;$j<=6;$j++):?>
           <tr id="dateBoxTr_<?=$j?>" >
        	<?php for($i=1;$i<=7;$i++):?>
        	<td class="dateBox <?=($i==6)?'saturday':''?> <?=($i==7)?'sunday':''?>" id="dateBox_<?=$j.'_'.$i?>"></td>
            <?php endfor;?>
         </tr>
          <?php endfor;?>
    </table>


</div>




<div class="datagrid">
<h1 style="margin:10px 0 10px 0 " id="dataHeader"></h1>
<div id="formWay"></div>
<table border="1" width="800px">
	<thead>
	<tr>
    	<th>訂位人</th>
        <th>日期，時間</th>
        <th>人數</th>
        <th>電話</th>
        <th>email</th>
        <th>確認</th>
        <th>安排桌數</th>
        <th>備註</th>
        
	</tr>
    </thead>
    <tbody id="reserve_body">
    	
    </tbody>
</table>

</div>




</body>
</html>