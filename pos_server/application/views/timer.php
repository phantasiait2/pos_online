<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
.column{
	width:150px;
	float:left;
	font-size:16pt;
	
	
	
}


</style>
<script>
	function newThingSend()
	{
		 $.ajax({
		   type: "POST",
		   dataType:"json",
		   url: "/timer/new_thing_send",
		   data: $("#newThingForm").serialize(),
		   success: function(data){
			 
		
			   if(data.result==true)
			   {  
			   		
					$('#new_thing_cavas').slideUp('slow');
					
					getToDo(0);
			   }
		   	}
		   })
		
		
		
	}
	function changeStatus(id,status)
	{
		$.post('/timer/change_status',{id:id,status:status},function(data){
			if(data.result)
			{
				$('#thing_'+id).slideUp('slow',function(){getToDo(status);$('#thing_'+id).detach();});
				
				
				
				
				
			}
			
			
		},'json')

		
		
		
	}
	  var countdownid=0;
	function misson_start(id)
	{
		
			$.post('/timer/missoion_start',{id:id},function(data)
			{
				
				if(data.result==true)
				{
					var sStr = prompt("請輸入預計做的時間?");
			
					content ='<div style="top:100px;background-color:white; Z-index:25;position:absolute " >'+
							'<h1>'+data.thing.name+'</h1>'+
							'<h2>'+data.thing.content+'</h2>'+
							'<h1>倒數計時</h1>';
					content+='<span id="timerCountMin">'+sStr+'</span>：<span id="timerCountSec">00</span>';
					
					content+=
							'<input type="button" class="big_button" value="完成"  onclick=$(\'.popUpCover\').detach();"changeStatus('+data.thing.id+',-1)">'+
							'<input type="button" class="big_button" value="取消排程" onclick="$(\'.popUpCover\').detach();changeStatus('+data.thing.id+',0)">';
					content+='</div>'
					$('body').append('<div class="popUpCover" style="display:none"></div>')
					$('.popUpCover').css('height',$(document).scrollTop()+$(window).height());	
					$('.popUpCover').css('width',$(window).width());	
					$('.popUpCover').fadeIn('fast');
					$('.popUpCover').html(content);
					  countdownid=setTimeout(countdownfunc,1000);;
			
					
					
					
				}
				
				
				
				
				
			},'json')
			
		
	}
	
	function getToDo(status)
	{
		
		$.post('/timer/get_to_do',{status:status},function(data)
		{
			
			if(data.result==true)
			{
				switch(status)
					{
						case 1:
							
						$('#onSchedule').html(
							'<div style="background-color:#06F; float:left; width:inherit;">'+
					        '<span class="column">建立時間</span>'+
        					'<span class="column">事項名稱</span>'+
        					'<span class="column">預計時間</span>'+
        					'<span class="column">開始時間</span>'+
        					'<span class="column">結束時間</span>'+
        					'<span class="column">完成</span>'+  
    						'<span class="column">取消排程</span>'+   
 							'</div>'+   
    						'<div  style="clear:both"></div>'
						)
						for(key in data.thing)
						{
							
								content =
								'<div style=" float:left;" id="thing_'+data.thing[key].id+'">'+
									'<div style="background-color:#9F0; float:left; width:inherit; cursor:pointer;" onclick="$(\'#content_'+data.thing[key].id+'\').slideToggle();">'+
										'<span class="column">'+data.thing[key].creatTime+'</span>'+
										'<span class="column">'+data.thing[key].name+'</span>'+
										'<span class="column">'+data.thing[key].estimateTime+'分</span>'+
										'<span class="column">'+data.thing[key].startTime+'</span>'+
										'<span class="column">'+data.thing[key].endTime+'</span>'+
										'<span class="column"><input type="button" class="big_button" value="完成"  onclick="changeStatus('+data.thing[key].id+',-1)"></span>'+
										'<span class="column"><input type="button" class="big_button" value="取消排程" onclick="changeStatus('+data.thing[key].id+',0)"></span>'+
									'</div>'+   
								'<div  style="clear:both"></div>'+
									'<div id="content_'+data.thing[key].id+'" style="border:solid; background-color:#FFC;display:none">'+
										'<h3>排定內容</h3>'+
										'<textarea name="content" style="width:800px; height:200px">'+data.thing[key].content+'</textarea>'+
										'<div  style="clear:both"></div>'+
										'<input type="button" class="big_button" value="開始進行" onclick="misson_start('+data.thing[key].id+')">'+
										'<input type="button" class="big_button" value="取消排程"  onclick="changeStatus('+data.thing[key].id+',0)">'+
									'</div>'+
								'</div>';
								$('#onSchedule').append(content);
								
								
						}
						break;
						case 0:
							
						$('#onStandBy').html(
							'<div style="background-color:#06F; float:left; width:inherit;">'+
					        '<span class="column">建立時間</span>'+
        					'<span class="column">事項名稱</span>'+
        					'<span class="column">預計時間</span>'+
        					'<span class="column">開始時間</span>'+
        					'<span class="column">結束時間</span>'+
        					'<span class="column">加入排程</span>'+  
    						'<span class="column">刪除任務</span>'+   
 							'</div>'+   
    						'<div  style="clear:both"></div>'
						)
						for(key in data.thing)
						{
							
								content =
								'<div style=" float:left;" id="thing_'+data.thing[key].id+'">'+
									'<div style="background-color:#9F0; float:left; width:inherit; cursor:pointer;" onclick="$(\'#content_'+data.thing[key].id+'\').slideToggle();">'+
										'<span class="column">'+data.thing[key].creatTime+'</span>'+
										'<span class="column">'+data.thing[key].name+'</span>'+
										'<span class="column">'+data.thing[key].estimateTime+'分</span>'+
										'<span class="column">'+data.thing[key].startTime+'</span>'+
										'<span class="column">'+data.thing[key].endTime+'</span>'+
										'<span class="column"><input type="button" class="big_button" value="加入排程" onclick="changeStatus('+data.thing[key].id+',1)"></span>'+
										'<span class="column"><input type="button" class="big_button" value="刪除任務" onclick="changeStatus('+data.thing[key].id+',-2)"></span>'+
									'</div>'+   
								'<div  style="clear:both"></div>'+
									'<div id="content_'+data.thing[key].id+'" style="border:solid; background-color:#FFC;display:none">'+
										'<h3>排定內容</h3>'+
										'<textarea name="content" style="width:800px; height:200px">'+data.thing[key].content+'</textarea>'+
										'<div  style="clear:both"></div>'+
									'</div>'+
								'</div>';
								$('#onStandBy').append(content);
								
								
						}
						break;
						case -1:
							
						$('#ready').html(
							'<div style="background-color:#06F; float:left; width:inherit;">'+
					        '<span class="column">建立時間</span>'+
        					'<span class="column">事項名稱</span>'+
        					'<span class="column">預計時間</span>'+
        					'<span class="column">開始時間</span>'+
        					'<span class="column">結束時間</span>'+
        					'<span class="column">進行時間</span>'+  
    						'<span class="column">刪除任務</span>'+   
 							'</div>'+   
    						'<div  style="clear:both"></div>'
						)
						for(key in data.thing)
						{
							
								content =
								'<div style=" float:left;" id="thing_'+data.thing[key].id+'">'+
									'<div style="background-color:#9F0; float:left; width:inherit; cursor:pointer;" onclick="$(\'#content_'+data.thing[key].id+'\').slideToggle();">'+
										'<span class="column">'+data.thing[key].creatTime+'</span>'+
										'<span class="column">'+data.thing[key].name+'</span>'+
										'<span class="column">'+data.thing[key].estimateTime+'分</span>'+
										'<span class="column">'+data.thing[key].startTime+'</span>'+
										'<span class="column">'+data.thing[key].endTime+'</span>'+
										'<span class="column">'+data.thing[key].endTime+'</span>'+
										'<span class="column"><input type="button" class="big_button" value="刪除任務" onclick="onclick="changeStatus('+data.thing[key].id+',-2)""></span>'+
									'</div>'+   
								'<div  style="clear:both"></div>'+
									'<div id="content_'+data.thing[key].id+'" style="border:solid; background-color:#FFC;display:none">'+
										'<h3>排定內容</h3>'+
										'<textarea name="content" style="width:800px; height:200px">'+data.thing[key].content+'</textarea>'+
										'<div  style="clear:both"></div>'+
									'</div>'+
								'</div>';
								$('#ready').append(content);
								
								
						}
						break;
					
					}
			}
			
			
			
		},'json')
		
		
	}
	
	function countdownfunc(){
		 countdownnumberMin = parseInt($('#timerCountMin').html());
		  countdownnumberSec = parseInt($('#timerCountSec').html());
		 
		 if (countdownnumberMin==0){ 
		  alert("倒數結束");
		  clearTimeout(countdownid);
		 }else{
			 countdownnumberSec--;
			 if(countdownnumberSec==-1)
			 {
				 countdownnumberSec = 59;
				 countdownnumberMin--;
			 }
			 if(countdownnumberMin<10) $('#timerCountMin').html('0'+countdownnumberMin)
			 else $('#timerCountMin').html(countdownnumberMin)
			if(countdownnumberSec<10) $('#timerCountSec').html('0'+countdownnumberSec)
			 else $('#timerCountSec').html(countdownnumberSec)
			 
		  if(countdownid){
		   clearTimeout(countdownid);
		  }
		  countdownid=setTimeout(countdownfunc,1000);
		 }
}
	
	 getToDo(0);
	 getToDo(-1);
	 getToDo(1);


</script>
<input type="button" class="big_button" value="新增項目" onClick="$('#new_thing_cavas').slideToggle('slow')">
<div style="background-color:#9CC;display:none" id="new_thing_cavas" >
	<form  id="newThingForm">
	<div>事項名稱：<input type="text" name="name" style="width:800px; height:50px;"></div>
    <div>事項內容：<textarea name="content" style="width:800px; height:200px"></textarea></div>
    <div>預計時間：<input type="text" name="time" value=""  style="width:50px; height:50px">分鐘</div>
    <input type="button" class="big_button" value="確定送出" onClick="newThingSend()">
    <input type="button" class="big_button" value="取消" onClick="$('#new_thing_cavas').slideUp('slow')">
	</form>
</div>


<h2>排定事項</h2>
<div id="onSchedule"></div>
<div  style="clear:both"></div>
<h2>待徘事項</h2>
<div id="onStandBy"></div>
<div  style="clear:both"></div>

<h2>完成事項</h2>
<div id="ready"></div>
<div  style="clear:both"></div>
