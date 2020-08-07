var onEditing = false;
function openPopUpBox(content,width,height,backFunction,closehint)
{
	closePopUpBox();
	$('body').append('<div class="popUpCover" style="display:none"></div>')
	$('body').append('<div class="popUpBox popBox" style="display:none" onclick=" popUpBoxHeight(0)"><div id="pupContent"></div>'+
	'<div class="divider"></div>'+
	'<input type="button" id="popUpBoxEnter" value="確定" class="big_button" onclick="$(\'#popUpBoxEnter\').hide();'+backFunction+'();">'+
	'<input type="button" id="popUpBoxCancel" value="取消"class="big_button"  onclick="closePopUpBox('+closehint+')">'+
	'</div>');	
	$('#pupContent').html(content)
	$('.popUpBox').css('width',width);
	$('.popUpBox').css('height',height);	
	$('.popUpBox').css('left',(650-width/2));
	$('.popUpBox').css('top',($(document).scrollTop()+100));
	$('.popUpBox').fadeIn('slow');
	$('.popUpCover').css('height',$(document).scrollTop()+$(window).height());	
	$('.popUpCover').css('width',$(window).width());	
	$('.popUpCover').fadeIn('fast');
	$('.popUpCover').bind('click',function(){
		$('#popUpBoxCancel').click();
		
	})
	onEditing = true;
	
	
}

function closePopUpBox(token)
{
	
	if(token)
	{
		if(!confirm('是否關閉?'))	return;
		
	}
	onEditing = false;
	$('.popUpCover').fadeOut();
	$('.popUpBox').fadeOut('slow');
	$('.popUpBox').detach();	
	$('.popUpCover').detach();
	
	
}
function adjustHeight(id,addHeight,baseHeight)
{
	
	addHeight = addHeight || 100;
    baseHeight = baseHeight || 300;
	height =parseInt($('.popUpBox').css('height').substr(0,$('.popUpBox').css('height').length-2))
	if(parseInt($(id).css('height').substr(0,$(id).css('height').length-2))+baseHeight>height) 
	{
		
		popUpBoxHeight(height+addHeight);
		
	}
	
	
}



function popUpBoxHeight(height)
{

	height = $('#pupContent').height()+100;

	 $('.popUpBox').css('height',height);
	 if(height-100>$(window).height())	$('.popUpCover').css('height',height+150);	
	 else $('.popUpCover').css('height',$(document).scrollTop()+$(window).height());	
}
function alertBoxHeight(height)
{

	height = $('#pupContent').height()+100;

	 $('.popUpBox').css('height',height);
	 if(height-100>$(window).height())	$('.popUpCover').css('height',height+150);	
	 else $('.popUpCover').css('height',$(document).scrollTop()+$(window).height());	
}


var err ='';
function errPopUpBox(content)
{
	
	err = content;
	content = '<h1>OOPs!系統出現錯誤</h1>出現不可預期的錯誤，<br/>將自動回報系統管理員，<br/>造成您的不便敬請見諒<br/>';
	width= 300;
	height = 150;
	$('body').append('<div class="popUpBox" id="errPopUpBox">'+content+
	'<div class="divider"></div>'+
	'<input type="button" id="popUpBoxCancel" value="確定"class="big_button"  onclick="errReport()">'+
	'</div>');	
	$('#errPopUpBox').css('width',width);
	$('#errPopUpBox').css('height',height);	
	$('#errPopUpBox').css('left',(parseInt($(window).width())-width)/2);
	$('#errPopUpBox').css('top',($(document).scrollTop()+100));
	
}


function closeErrPopUpBox()
{
	
	
	
	$('#errPopUpBox').detach();	
  
	
	
}




$(window).bind('beforeunload',function()
{
	if(onEditing==true) return '正在編輯中';
})





function errReport()
{
	$.post('/report/err_report',{err:err},function(data){
		 if(data.result==true)
		 {
			 alert('錯誤已回報');
			 closeErrPopUpBox()
		 }
		
	},'json')
	
	
	
	
}

var alertBoxToken =false;
function alertBox(content,width,height,backFunction,closeFunction)
{

	if(alertBoxToken ==false)
	{
		alertBoxToken = true;
		content='<h3 style=" font-weight:bold">系統貼心提示窗，請遵照流程進行</h3><div id="alertContent">'+content+'</div>';
		$('body').append('<div class="popUpCover alertCover" style="display:none; z-index:9999" onclick=" popUpBoxHeight(0)"></div>')
		$('body').append('<div class="popUpBox alertBox" style="display:none;z-index:9999">'+content+
		'<div class="divider"></div>'+
		'<input type="button" id="popUpBoxEnter" value="確定" class="big_button" onclick="closeAlertBox()">'+
		'</div>');	
		$('.alertBox').css('width',width);
		$('.alertBox').css('height',height);	
		$('.alertBox').css('left',((parseInt($(window).width())-width)/2));
		$('.alertBox').css('top',($(document).scrollTop()+100));
		$('.alertBox').fadeIn('slow');
		$('.alertCover').css('height',$(document).scrollTop()+$(window).height());	
		$('.alertCover').css('width',$(window).width());	
		$('.alertCover').fadeIn('fast');
		$('.alertCover').bind('click',function(){
			closeAlertBox();
		})
	}
	else 
	{
			$('#alertContent').append(content);
			
			$('.alertBox').css('height',$('#alertContent').height()+100);
	}
	
	
	alertBoxHeight()

	
	
}
function closeAlertBox()
{
	$('.alertCover').fadeOut();
	$('.alertBox').fadeOut('slow');
	$('.alertBox').detach();	
	$('.alertCover').detach();	
	alertBoxToken =false;
	
}

