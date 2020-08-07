<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>行事曆系統</title>
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
	 //alert($("#detailForm").serialize());
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/check/insertCalendar",
	   data: $("#detailForm").serialize(),
	   success: function(data){
	   	//alert(data);
		 if(data.result==true)
		   {  
				 changeMonth(0,false);
				 getReserveByDay('',data.finishdate);
				 $('#newReserve').slideUp();
		   }
		  
	   }
	 });
		
	}


	function reserveRow(data)
	{
		if(data.status==0)
			str='<option value="0" selected>待處理</option>'+
		        '<option value="1" >已完成</option>'+
            	'<option value="-1">取消</option>';
		else if(data.status==1){
			str='<option value="0" >待處理</option>'+
		        '<option value="1" selected>已完成</option>'+
            	'<option value="-1">取消</option>';
         }
        else if(data.status==-1) 
			str='<option value="0" >待處理</option>'+
		        '<option value="1" >已完成</option>'+
            	'<option value="-1" selected>取消</option>';
		content=
				'<tr><td><input type="hidden" class="big_text" id="id_'+data.id+'"  name="id" value="'+data.id+'"  form="bookingRow_'+data.id+'">'+
				data.business+'</td>'+
				'<td><input type="text" readonly="readonly" class="medium_text" id="account_'+data.id+'"  name="account" value="'+data.account+'"></td>'+
				'<td><input type="text" readonly="readonly" class="medium_text" id="account_'+data.id+'"  name="date" value="'+data.date+'"></td>'+
				'<td><input type="text" class="medium_text" id="target_'+data.id+'"  name="target" value="'+data.target+'" onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'"></td>'+
				'<td><textarea class="big_text" style="height:150px" name="thing" id="thing_'+data.id+'" onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'">'+data.thing+'</textarea>'+
		        '<td><input type="text" class="big_text" id="reason_'+data.id+'" name="reason" value="'+data.reason+'"  onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'"></td>'+
		        '<td><input type="text" class="medium_text" id="place_'+data.id+'" name="place" value="'+data.place+'"  onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'"></td>'+
	        	'<td><select  id="from_'+data.id+'" name="from" onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'">'+
            		'<option value="1">Facebook</option>'+
		            '<option value="2">Line</option>'+
            		'<option value="3">電話</option>'+ 
            		'<option value="4">商店</option>'+ 
            		'<option value="5">其他</option>'+                  
            	'</select></td>'+
            	'<td><input type="text" class="big_text" id="how_'+data.id+'" name="how" value="'+data.how+'"  onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'"></td>'+
            	'<td><select  id="status_'+data.id+'" name="status" onchange="bookingEdit('+data.id+')" form="bookingRow_'+data.id+'">'+
            		str+          
            	'</select></td>'
            	 ;
		$('#formWay').append('<form id="bookingRow_'+data.id+'"></form>');
		$('#reserve_body').append(content);	
		//$('#people_'+data.id).val(data.people);
		//$('#table_'+data.id).val(data.table);
		$('#from_'+data.id).val(data['from']);
		
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
		
		$.post('/check/getThing',{date:date},function(data)
		{
			
			if(data.result==true)
			{
				$('#dataHeader').html(date+'所有應完成事項')
				$('#reserve_body').html('');	
				if(data.reserve.length==0)$('#reserve_body').html('<h1 style="margin:10px 0 10px 0 ">今日無待辦事項</h1>');
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
		$.post('/check/getThingNum',{date:date},function(data)///check/searchThingNum
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
					/*if(data.reserve[selectDate].holiday==false)
					{
						//公休
						$('#dateBox_'+row+'_'+wd).css('background-color','gray');
					
				    
							
					}*/
					if(data.reserve[selectDate].snum!=0||data.reserve[selectDate].anum!=0)
					{
						
						$('#'+id).html('待辦事項數：</br>店務：'+data.reserve[selectDate]['snum']+'</br>活動：'+data.reserve[selectDate]['anum']);
					/*if(data.reserve[selectDate].people!=0)
					{
						
						$('#'+id).html('訂位：<br/>'+data.reserve[selectDate]['people']+'人，共'+data.reserve[selectDate]['table']+'桌<br/>');
					
						remainTable = (parseInt($('#useTa1ble').html())-data.reserve[selectDate]['table'])
						if($('#safeTable').html()>=remainTable)$('#'+id).append('<span style=" font-weight:bold">剩餘：'+remainTable+'桌<br/></span>');
						else $('#'+id).append('剩餘：'+remainTable+'桌<br/>');*/
						
					}
					else $('#'+id).html('');
				
				}
				else $('#dateBox_'+row+'_'+wd).html('');
				
				
				
				 $('#dateBox_'+row+'_'+wd).bind('click',function(){/*$('#dateConves').slideUp();*/ getReserveByDay(this.id)});
                $('#dateBoxTr_'+row).show();
              
				 if(wd==7) row++;;
			}
			for(col=wd+1;col<=7;col++) $('#dateBox_'+row+'_'+col).html('');
		},'json')
	}
	function businessChange()
	{
		business = $('#business').val();
		if(business==1){
			$('#newtable1').show();
			$('#newtable2').hide();	
		}
		else if(business==2){
			$('#newtable2').show();
			$('#newtable1').hide();
		}
	}
	function bookingEdit(id)
	{
		
		$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/check/thingEdit",
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
	jQuery('#datetimepicker1').datetimepicker(
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
	jQuery('#datetimepicker2').datetimepicker(
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
<h1 style="color:#FFF">行事曆系統</h1>
<div style="color:#FFF">
<input type="button" value="新增事項" class="big_button" onclick="$('#newReserve').slideToggle()">  <input type="button" value="打開日期表" class="big_button" onclick="$('#dateConves').slideToggle()"> </div> 

<iframe id="holidayReserve" src="/check/seat_holiday/" style="width:600px; display:none; height:300px">
</iframe>
<div id="newReserve" style="margin:0 auto; background-color:#FFF; border:double; width:600px; display:none">
		<form method="post"  id="detailForm" >
	  	選擇新增項目 :    
	  	<select onchange="businessChange()" name="business" id="business">
	  				<option value="1">店務</option>
	  				<option value="2">活動</option>
	  		</select>
	  		</br>
     	<input name="account" id="acount" type="hidden" value="<?=$account?>" />
     	<input name="shopID" id="shopID" type="hidden" value="<?=$shopID?>" />
     	<table id="newtable1" style="width:80%; margin-left:10%; text-align:left; font-size:16px; line-height:150%;">
     	<tr>
     	<th>對象   ：</th>
     	<td><input class="big_text" name="target1" id="target1" type="text" /></td>
        </tr>
     	<tr>
			<th>預計完成時間  ：</th>
        	<td>
            <input class="big_text" style="text-align:center; margin:1%;"  name="datetimepicker1"  id="datetimepicker1" value="請選擇日期及時間" type="text" />
           </td>
        </tr>
        <tr>
        	<th>原因   ：</th>
            <td><input class="big_text" name="reason1" id="reason1" type="text" /></td>
        </tr>
        <tr>
        	<th>事務   ：</th>
            <td>
			<textarea class="big_text" style="height:150px" name="thing1" id="thing1" ></textarea>

            </td>
        </tr>
     	<tr>
        	<th>地點   ：</th>
            <td><input class="big_text" name="place1" id="place1" type="text" /></td>
        </tr>
        <tr>
        	<th>解決方法： </th>
            <td><input class="big_text" name="how1" id="how1" type="text" /></td>
        </tr>
        <tr>
        	<th>訊息來源</th>
            <td>
            <select name="from1" id="from1">
            <option value="1">Facebook</option>
			<option value="2">Line</option>
			<option value="3">電話</option>
			<option value="4">商店</option>
			<option value="5">其他</option>
			</select>
            </td>
        </tr>    
         </table>    
         <table id="newtable2" style="display:none; width:80%; margin-left:10%; text-align:left; font-size:16px; line-height:150%;">
     	<tr>
     	<th>對象   ：</th>
     	<td><input class="big_text" name="target2" id="target2" type="text" /></td>
        </tr>
     	<tr>
			<th>活動時間   ：</th>
        	<td>
            <input class="big_text" style="text-align:center; margin:1%;"  name="datetimepicker2"  id="datetimepicker2" value="請選擇日期及時間" type="text" />
           </td>
        </tr>
        <tr>
        	<th>活動核心   ：</th>
            <td><input class="big_text" name="reason2" id="reason2" type="text" /></td>
        </tr>
        <tr>
        	<th>活動內容   ：</th>
            <td><textarea class="big_text" style="height:150px" name="thing2" id="thing2" > </textarea></td>
        </tr>
     	<tr>
        	<th>活動地點   ：</th>
            <td><input class="big_text" name="place2" id="place2" type="text" /></td>
        </tr>
        <tr>
        	<th>如何參加(收不收費)   ： </th>
            <td><input class="big_text" name="how2" id="how2" type="text" /></td>
        </tr>    
        <tr>
        	<th>訊息來源</th>
            <td>
            <select name="from2" id="from2">
            <option value="1">Facebook</option>
			<option value="2">Line</option>
			<option value="3">電話</option>
			<option value="4">商店</option>
			<option value="5">其他</option>
			</select>
            </td>
        </tr>    
         </table>    
        </form> 
    
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
<table border="1" width="1000px">
	<thead>
	<tr>
		<th>類別</th>
		<th>填表人</th>
		<th>填表時間</th>
    	<th>客戶姓名</th>
        <th>客戶需求內容</th>
        <th>需求原因</th>
        <th>地點</th>
        <th>訊息來源</th>
        <th>預期解決辦法</th>
        <th>狀態</th>
       
        
	</tr>
    </thead>
    <tbody id="reserve_body">
    	
    </tbody>
</table>

</div>




</body>
</html>