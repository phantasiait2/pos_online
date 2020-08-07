

// JavaScript Document
function formatFloat(num, pos)
{
  var size = Math.pow(10, pos);
  return Math.round(num * size) / size;
}


var msgList = Array();



function putMsg(msg)
{
	msgList = Array();
	var len = msg.length;
	var msgListLen = msgList.length;
	for(i=0;i<len;i++)
	{
		msgList[msgListLen++]=msg[i];
		
	}
	$('#setmsg').html('<h1>更新完成</h1>');
	return;
}

function showMsg(msgIndex)
{
	$('#msgColumn').html(msgList[msgIndex++]);
	if(msgIndex>=msgList.length) msgIndex=0;
	setTimeout(function(){showMsg(msgIndex)},5000);
}



function clearSearch()
{
	$('.search').val('');
	$('#memberData').html('');
}


function enter()
{
	
 if (event.keyCode == 13 && !event.shiftKey) {
        return true;
         }
		return false;
	
}


function versionChk()
{
	$('#msgColumn').html('更新資料中，請稍候...');
	$.ajax({
	   type: "post",
	   dataType: "json",
	   url: "/system/version_chk",
	   success: function(data){
		
		   if(data.result==true)
		   {
				if(data.licence==false)
				{
					 alert('授權碼錯誤');
					 location.href='/initial';
				}
				else if(data.needUpdate==true)
				{
					
					location.href='/system/version_update/'+data.version;
				}
				putMsg(data.msg)
				
		   }
		   else
		   {
				putMsg(Array('與主機連線失敗，部分功能待連線恢復正常後方可使用'));
				
					   
			   
		   }
	   }
	 });	
	
	
	
}


function memberExpired()
{
	$.post('/member/member_expired',{},function(data){
		if(data.result == true)
		{
			for(key in data.expired)	
			{
				$('#sideMenu').append('<li>'+
				'['+data.expired[key].levelName+']'+data.expired[key].name+'將於'+data.expired[key].dueDay+'天後到期'+
				'</li>')	
				
			}
			
			
		}
		
		
	},'json')
	
	
}

function fucCheckNUM(NUM)
{
 var i,j,strTemp;
	strTemp="0123456789";
	 if ( NUM.length== 0)
	  return 0
	 for (i=0;i<NUM.length;i++)
	 {
	  j=strTemp.indexOf(NUM.charAt(i)); 
	  if (j==-1&&i==0) j='-'.indexOf(NUM.charAt(i)); 
	  if (j==-1)
	  {
	  //说明有字符不是数字
	   return 0;
	  }
	 }
	 //说明是数字
	 return 1;
}


function productMiss()
{
	$('#product_list').html('<div style=" float:left"><h1>請在此填寫商品缺件清單</h1><iframe  width="500px" height="800px" id="report" src="https://docs.google.com/forms/d/14S7-oG1fREkvcpbLpq-tTxi5yEpWbORNIzp6inFwzhk/viewform"></iframe></div>'+
	
				'<div style=" float:left"><a href="https://docs.google.com/spreadsheet/ccc?key=0Apw7yi5GjygAdE1rR1kxTk04Tk13NTg3RzBfbE1BbHc&usp=drive_web#gid=1" target="_blank"><h1>商品缺件進度(點此開新視窗)</h1></a><iframe width="700px" height="800px"  id="situation" src="https://docs.google.com/spreadsheet/ccc?key=0Apw7yi5GjygAdE1rR1kxTk04Tk13NTg3RzBfbE1BbHc&usp=drive_web#gid=1"></iframe></div>');

	
	
}
function loginChk()
{
    
    	$.ajax({
	   type: "post",
	   dataType: "json",
	   url: "/welcome/login_status",
       async :false,    
	   success: function(data){
           if(data.result==false) 
		{
			//alert('登入逾時，請重新登入')
			location.href='/welcome/login';
		}
       }})
		
  
	
	
}

function preTimeSubmit(preTime,list)
{
	
		for(key in list)
		{
			
			var parentID = list[key].parentID;
			
	
			$.post('/phantri/reply',{parentID:parentID,msg:preTime},function(data){
			},'json')
		}
}

function longFun(url,t)
{
	if(url!='/welcome/longfun')	
	$.post('/welcome/longfun',{url:url,t:t},function()
	{
		
	},'json')	
	
	
	
	
}



$(document).ready(function(){
	//versionChk();
	
	var runtTime = 0;
	window.alert = function(data){
	

		alertBox('<h2 style="color:red">'+data+'</h2>',400,150);
	//***你的代码
	
	 }
	
	
	 $('.webFrame').ajaxStart(function(){
	$('#msgColumn').html('connecting...');
	$('#ajaxLoader').html('<img src="/images/ajax-loader.gif"/>');
         
         if($('#ajaxloader').length==0)
             {
                  $('body').append('<div class="popUpCover" id="ajaxloader"><img src="/images/ajax-loader.gif"/></div>');             
                          $('#ajaxloader').show();
                $('.popUpCover').css('height',$(document).scrollTop()+$(window).height());	
	$('.popUpCover').css('width',$(window).width());	
             }
    
         
	loginChk();
	runTime = Math.floor(Date.now() / 1000);
	//console.log(runTime);
	})
	/*
	
	 $('.webFrame').ajaxError(function(){
		$('#msgColumn').html('connect error');	
	})
	*/
	 $('.webFrame').ajaxSuccess(function(event, request, settings){
         $('#ajaxloader').detach();
		$('#ajaxLoader').html('');
		if(settings.url!='/welcome/longfun')	
		{
			t = Math.floor(Date.now() / 1000) -runTime;
			//console.log(t);
			if(t >5 )longFun( settings.url,t);
		}
	});
	
	$('.webFrame').ajaxError(function(event, request, settings,thrownError){
         $('#ajaxloader').detach();
			 errPopUpBox( settings.url+'<br/>'+thrownError);
});	
});