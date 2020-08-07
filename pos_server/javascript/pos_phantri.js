// JavaScript Document

var i = 0;
function arriveBtn(productID,con) 
{
	i++;
	return '<input type="button" value="追蹤到貨日" title="點此按鈕將會提醒產品部人員，告知您到貨日期" class="big_button" onclick="arriveTracking('+productID+','+i+','+con+');" id="trackingBtn_'+i+'">';
}


function arriveTracking(productID,i,con)
{
	
	if(con)
	{
		if(!confirm('按此將會向產品部提出到貨時間的詢問，是否確定?'))	return;
		
	}
	var w = prompt('貨品追蹤查詢，請輸入要說的話，或是不輸入。點選確定');
	$.post('/phantri/arrive_track',{productID:productID,w:w},function(data)
	{
		if(data.result==true)
		{
				
			alert('已通知產品部人員，請等候產品部人員回覆,或可到問題解決中心查詢');
			$('#trackingBtn_'+i).parent().html('已加入到貨追蹤清單，<br/>請耐心等待回覆，<br/>或直接來電洽詢(02)2263-0120')
			//$('#trackingBtn_'+i).hide();
			
		}
		
	},'json');	
	
	
}
function loadShortStatus(parentID)
{
	
		$.post('/phantri/get_short_product_status',{parentID:parentID},function(data)
		{
			if(data.result==true)
			{
				
				for(key in data.statusArray)
				{
					if(data.statusArray[key]!=0)$('#status_'+parentID+'_'+data.statusArray[key]).attr('checked', true);
					
					
				}	
				
			}
			
			
		},'json')	
	
	
}

function shortStatus(parentID)
{
	$.ajax({
		   type: "POST",
		   dataType:"json",
		   url: "/phantri/short_product_status",
		   data: $("#shortForm"+parentID).serialize(),
		   success: function(data){
			 
			   if(data.result==true)closeTask(parentID,0);
			  
			  
		   }	
		 });


	
	}

function talkingTask(parentID)
{
		$('#msg_'+parentID).val('目前正與廠商洽談中，到貨時間會再另外說明，若仍有疑問請再告知我們，謝謝！');
		reply(parentID,0);		
	
	
}

function processTask(parentID)
{
		$('#msg_'+parentID).val('請確認是否幫您處理完成，完成後請點選結案，或回報我們喔，希望您滿意，若仍有疑問請再告知我們，謝謝！');
		reply(parentID,0);		
	
	
}
function closeTask(parentID,r)
{
	
	if(confirm('這個case確定結案了嗎？'))
	{
			t = 0 ;

				$('#msg_'+parentID).val('此問題已結案');
				reply(parentID,0);		
				t= 1000;
			
			
	}
	
}

function closeTaskSend(parentID)
{
	$.post('/phantri/close_task',{parentID:parentID},function(data)
			{
				
				if(data.result==true)
				{
					$('#closeTask_'+parentID).html('<td colspan="5">已結案</td>');
				
					$('#msg_'+parentID).detach();
		
					$('#replyBtn_'+parentID).detach();
					$('#pic_'+parentID).detach();
					$('#replyLoading').detach();
					
					if(data.feedback==1)getFeedBack(parentID);
					
				}
				
			},'json')	
	
}


function getFeedBack(parentID)
{
	
	content='<h1>感謝您的提問，希望您還滿意這次的處理</h1>';
	content+='<h2>我們很在乎您的感受</h2>';
	content+='<h2>請為此次的案子處裡留下評價</h2>';
	content+='<h2>您的鼓勵與支持是我們前進的原動力</h2>';
	content+='<form id="feedBack"><input type="hidden" name="parentID" value="'+parentID+'"><label><input type="radio" value="0" name="rank">0分</label>'+
				'<label><input type="radio" value="1" name="rank">1分</label>'+
				'<label><input type="radio" value="2" name="rank">2分</label>'+
				'<label><input type="radio" value="3" name="rank">3分</label>'+
				'<label><input type="radio" value="4"  name="rank">4分</label>'+
				'<label><input type="radio" value="5" checked="checked" name="rank">5分</label><br/>';
	
	content+= '<textarea id="comment" name="comment" style="width:400px; height:200px"></textarea></form>';

	 openPopUpBox(content,700,500,'feedBackSend');	
	
}

function feedBackSend()
{
	
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/phantri/problem_solve",
	   data: $("#feedBack").serialize(),
	   success: function(data){
		 
		  		if(data.result==true)
				{
					problemConclusion(data.parentID);
					closePopUpBox();
					 
					alert('您的寶貴意見，將作為本我們改進的依據。謝謝您的配合與協助');
					
				}

	   }
	 });	
	
	
}


function problemConclusion(parentID)
{
	
	$.post('/phantri/problem_conclusion',{parentID:parentID},function(data)
	{
		
		//alert(data);
		
		
		
	},'html')
	
	
}








function reply(parentID,enterCode)
{
	
		if(enterCode==1)
		if(enter()==false)return;
	
	

	if(replyImage)
	{
		if(replyImgObj.isReady()) 
		{
			file = 	replyImgObj.getFiles();   
		}
		else
		{
			alert('圖檔上傳中，請稍後再試');	
			return;
		}
		
	}
	else
	{
		 file = '';
		 if($('#msg_'+parentID).val()=='')return;
	}
	

	var msg = $('#msg_'+parentID).val();
	$('#msg_'+parentID).val('');
		$('#msg_'+parentID).after('<img src="http://shipment.phantasia.com.tw/images/loading.gif" class="replyLoading" id="replyLoading">');
		$('#msg_'+parentID).hide('');
	$('#replyBtn_'+parentID).hide();
	
	
	
	$.post('/phantri/reply',{parentID:parentID,msg:msg,file:file},function(data)
	{
		
		if(data.result==true)
		{
			
			
			   content = '';

			if(data.reply['img'])
			{
				content='<div class="divider"></div>'+
                    '<div class="img">';
					
				for(key in data.reply['img'])
				{
        			content+='<a href="'+data.reply['img'][key]+'">'+
							'<img src="'+data.reply['img'][key]+'"   style="max-width:100px; max-height:100px" >'+
				            '</a>'
				}
				content+='<div>';
				
			
			}
			
			
		
			$('#replyTr_'+parentID).before(
				   '<tr  id="parent_'+data.reply['id']+'" class="reply_parent_'+parentID+' allTopic "style="background-color:#CFF; color:#000">'+
				        '<td style=" width:180px">->'+data.reply['time'].substr(0,16)+'</th>'+
      					'<td style=" width:300px;">['+data.reply['name']+']'+data.reply['account']+' '+data.reply['type']+'</th>'+
       					'<td>'+data.reply['content']+content+'</td>'+
    				    '<td>'+data.reply['status']+'</td>'+
					'</tr>'
    
			
			
			
			
			)
			$('.img a').lightBox();
			
			$('#parent_'+data.reply['id']).show();
			$('#msg_'+parentID).show('');
			$('.replyLoading').hide();
			$('#replyBtn_'+parentID).show();
			if(msg=='此問題已結案') closeTaskSend(parentID);
			if(replyImage)$('.img a').lightBox();
			$('#reply_image_canvas_'+parentID).slideUp();

		}
		
	},'json')
	
	replyImage  = false;
}

function transferTask(parentID)
{
	content='<input type="hidden" id="transferParentID" value="'+parentID+'">';
	 content+= '<select id="transferPart">';
	 content+='<option value="到貨詢問" >到貨詢問</option>'+
	 		  '<option value="缺件回報" >缺件回報</option>'+	
	          '<option value="產品相關問題" >產品相關問題</option>'+
			  '<option value="活動執行" >活動執行</option>'+
			  '<option value="海報DM名片等印刷品" >海報DM名片等印刷品</option>'+
			  '<option value="系統問題">系統問題</option>'+
			   '<option value="經營相關" >經營相關</option>'+
			    '<option value="會計問題" >會計問題</option>';
			  
	 content += '</select>';	  
	

	openPopUpBox(content,700,100,'transferSend');
		
	
}

function transferSend()
{
	$.post('/phantri/trans_part',{parentID:$('#transferParentID').val(),part:$('#transferPart').val()},function(data)
	 {
		 if(data.result==true)
		 {
			alert('轉介完成');
			closePopUpBox();
			location.reload();	 
			 
		 }
		 
		 
		 
	 },'json')		
	
}




var replyImage = false;
var replyImgObj;
function replyImageUpload(parentID)
{
	replyImage = true;
	$('#reply_upload_block').detach();
	content  = '<div id="reply_upload_block">'+  
				  '<h2>圖檔上傳區，可上傳 4 張圖，請勿超過3MB</h2>'+  
                  '<h3>圖檔過大請先上傳臉書後再下載下來可得到良好壓縮</h3>'+
				  '<div id="reply_preview_block"></div>'+  
			  	   '<div style="clear: both;">'+
					    '<span id="reply_image_input_block"></span>'+
 			 		'</div> ' +
				 '</div> ';
	$('#reply_image_canvas_'+parentID).html(content).slideDown();
				 			  
	replyImgObj = new JSG.imgUploader({
    fileLimits: 4,
    actionUrl: '/phantri/photo_upload',
    inputContainer: 'reply_image_input_block',
    previewContainer: 'reply_preview_block',
	loadingIcon: '/images/loading_indicator_big.gif',  
  	deleteIcon: '/images/icon_delete.gif'
  });
	
	
}







var myImageUploader1;	
function problemAsk(type)
{
		content = '<h1>問題回報：'+type+'</h1>';
		content += '<h2>請描述你遇到的問題，以及希望的答案</h2>';	
		content+='<input type="hidden" id="type" value="'+type+'">';
		content+= '<textarea id="w" style="width:400px; height:200px"></textarea>';
		content+= '<div id="upload_block">'+  
				  '<h2>圖檔上傳區，可上傳 4 張圖</h2>'+  
				  '<div id="preview_block"></div>'+  
			  	   '<div style="clear: both;">'+
					    '<span id="image_input_block"></span>'+
 			 		'</div> ' +
				  '</div> '
		 openPopUpBox(content,700,500,'problemAskSend');	
	 myImageUploader1 = new JSG.imgUploader({
    fileLimits: 4,
    actionUrl: '/phantri/photo_upload',
    inputContainer: 'image_input_block',
    previewContainer: 'preview_block',
	loadingIcon: '/images/loading_indicator_big.gif',  
  	deleteIcon: '/images/icon_delete.gif'
  });

}	

function problemAskSend(type)
{
	if(myImageUploader1.isReady()) 
	$.post('/phantri/problem_ask_send',{type:$('#type').val(),w:$('#w').val(),file:myImageUploader1.getFiles()},function(data)
	{
		if(data.result==true)
		{
			alert('問題已送出');
			closePopUpBox();
			location.reload();	
			
			
		}
		
	},'json')	
	else 
	{
		alert('圖檔上傳中，請稍後再試');
		$('#popUpBoxEnter').show();
	}
	
}

function comeProduct()
{			
			content='<h1>到貨查詢</h1>'+
			  '<h1>請輸入欲查詢的產品名稱</h1>'+
			  '<div id="comeProductQuery"></div>'+
			  '<div style=" float:left; height:400px;">'+
			  '<form id="comePtoductForm">'+
			  '<input type="hidden" id="comeProduct_status" value="1">'+
			  '<table id="comePtoductTable" border="1" width="1000px">'+
			  	'<tr>'+
					'<td>產品編號</td>'+
					'<td>中文名稱</td>'+
					'<td>英文名稱</td>'+
					'<td>語言</td>'+
					'<td>到貨時間</td>'+
				'</tr>'+
			  '</table>'+
			  '</form>'
			  '</div>';

	 openPopUpBox(content,1000,600,'closePopUpBox');	
		$('#popUpBoxCancel').hide();
	
		queryProduct('comeProduct','select');
	
}

function comeProductTable(data)
{
	
	  if($('#shortPtoduct_'+data.productID).length!=0){
		  alert('商品已在清單中');
		  return;
	  }	
	$('#comePtoductTable').append(
			'<tr id="comePtoduct_'+data.productID+'">'+
					'<td>'+data.productNum+'</td>'+
					'<td>'+data.ZHName+'</td>'+
					'<td>'+data.ENGName+'</td>'+
					'<td>'+data.language+'</td>'+
					'<td id="arrive_'+data.productID+'"><img src="/images/ajax-loader.gif"/></td>'+
			'</tr>'
	);


;	arriveTime(data.productID,data.wait);
	
}


	
function shortProduct()
{			
			content='<h1>缺件商品登記</h1>'+
			  '<h1>此缺件表僅供新品內容瑕疵，或短少使用。遺失恕無法補件</h1>'+	
			  '<h1>請輸入欲查詢的產品名稱</h1>'+
			  '<div id="shortProductQuery"></div>'+
			  '<div style=" float:left; height:400px;">'+
			  '<form id="shortPtoductForm">'+
			  '<table id="shortPtoductTable" border="1" width="1000px">'+
			  	'<tr>'+
					'<td>產品編號</td>'+
					'<td>中文名稱</td>'+
					'<td>英文名稱</td>'+
					'<td>語言</td>'+
					'<td>配件名稱<br/>顏色特徵</td>'+
					'<td>問題描述</td>'+
				'</tr>'+
			  '</table>'+
			  '</form>'
			  '</div>';

	 openPopUpBox(content,1000,600,'shortProductSend');	
	
	
		queryProduct('shortProduct','select');
	
}

function shortProductTable(data)
{
	
	  if($('#shortPtoduct_'+data.productID).length!=0){
		  alert('商品已在清單中');
		  return;
	  }	
	$('#shortPtoductTable').append(
			'<tr id="shortPtoduct_'+data.productID+'">'+
					'<td>'+data.productNum+'</td>'+
					'<td>'+data.ZHName+'</td>'+
					'<td>'+data.ENGName+'</td>'+
					'<td>'+data.language+'</td>'+
					'<td id="arrive_'+data.productID+'"><input type="text" name="short['+data.productID+'][0]" class="big_text" ></td>'+
					'<td><textarea  name="short['+data.productID+'][1]" style="width:200px; height:100px"></textarea></td>'+
			'</tr>'
	);


;	
}
function shortProductSend()
{

	
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/phantri/short_product_send",
	   data: $("#shortPtoductForm").serialize(),
	   success: function(data){
		 
		  		if(data.result==true)
				{
					closePopUpBox();
					alert('已通知產品部人員，請等候產品部人員回覆，查詢進度可至問題解決中心->缺件回報中查詢');
					
				}

	   }
	 });
	
}

function arriveTime(productID,wait)
{
		
	$.post('/product/already_order',{productID:productID},function(data){
	
			if(data.comNum>0)content = '現貨供應['+data.comNum+']';
			else if(data.result==true)
				{
						
						content = '';
						var waitDate =   data.pre.preTime
						if(waitDate=='7777-07-07')content+='廠商暫時缺貨中';
						else if(waitDate=='3333-03-03')content+='暫不進貨';
						else if(waitDate=='9999-09-09')content+='<span style="color:red">已斷貨，請取消訂購</span>';
						else if(waitDate!='0000-00-00')content+='到貨日'+waitDate;
						else content+=arriveBtn(productID) ;;
					
				}
				else
				{
					
						content='';
						if(wait!=0)content+='需等候約'+wait+'天以上的叫貨時間' ;
						else content+='進貨時間未定'
						content+=arriveBtn(productID,false) ;;
						
				}
				$('#arrive_'+productID).html(content);	
		
	},'json')
	
}