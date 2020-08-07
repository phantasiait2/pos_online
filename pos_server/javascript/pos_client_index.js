// JavaScript Document
var pIndex = 0
function findProduct(id,type){
	
	if(type==1){
		if(enter()==false) return;
	}



}


function checkoutTable(data)
{
	
var discount = 100;
				discount = $('#allDisCount').val();
				if(data.minDiscount!=0 && parseInt(data.minDiscount) > parseInt(discount)) discount = data.minDiscount;
				$('.check_btn').show();
				$('#barcode').val('');
				$('#sellTable').show().append(
				'<tr id="tr_'+pIndex+'">'+
				  '<td>'+data.barcode+'</td>'+
				  '<td id="name_'+pIndex+'">'+data.ZHName+'</td>'+
				  '<td>'+data.price+'</td>'+
				
				  '<input type="hidden" name="productID_'+pIndex+'" class="short_text" value="'+data.productID+'" />'+
				  '<input type="hidden" name="p_'+pIndex+'" id="p_'+pIndex+'" class="short_text" value="'+data.price+'" />'+
				  '<input type="hidden" name="min_'+pIndex+'"  id="min_'+pIndex+'" class="short_text" value="'+data.minDiscount+'" />'+
				  '<td><input type="text" name="n_'+pIndex+'" id="n_'+pIndex+'" class="short_text" value="1" onblur="changeNum()"/></td>'+
				  '<td><input type="text" name="c_'+pIndex+'"   id="c_'+pIndex+'" class="short_text disCount" value="'+discount+'" onblur="checkDiscount('+pIndex+')"/></td>'+
				  '<td><input type="button"  value="取消"  onclick="cancelProduct('+pIndex+')"/></td>'+
				'</tr>');
				pIndex= pIndex+1;
				countTotal();	
	
	
}



function cancelProduct(id)
{
	
	$('#tr_'+id).detach();
	pIndex = pIndex-1;
}

function changeNum()
{
	for(i=0;i<pIndex;i++)
	{
		
		checkDiscount(i)
		
	}
}
function changeAllDiscount(discount)
{
	$('.disCount').val(discount);
	
	for(i=0;i<pIndex;i++)
	{
		
		checkDiscount(i)
		
	}
}


function checkDiscount(id)
{


	var discount= $('#c_'+id).val();
	if(fucCheckNUM(discount)==0)discount = 0 
	if(discount=='')discount = 0 ;

	if(parseInt($('#min_'+id).val()) > parseInt(discount))	
	{
		
		alert($('#name_'+id).html()+' 最低折數為 '+ ($('#min_'+id).val()));
			$('#c_'+id).val($('#min_'+id).val());
	
		
	}
	countTotal();
	
}





function countTotal()
{
	var total = 0;
	
	for(i=0;i<pIndex;i++)
	{
			
		total +=Math.round($('#p_'+i).val()*$('#n_'+i).val()*$('#c_'+i).val()/100);
		
	}

	
	
	$('#totalSell').html(total);
	
	
}




function payBill()
{
	countTotal();
	

if(confirm('本次消費金額：'+$('#totalSell').html()+'元\n是否結帳？'))
{
	pIndex = 0;
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/product/pay",
	   data: $("#sellProduct").serialize(),
	   success: function(data){
		 
		   if(data.result!=0)
		   {
			 alert('共：'+data.total+'元，謝謝惠顧');
			 resetSellTable();
			 memberNameClear();
		   }
		   else alert('錯誤，請重新輸入');
	   }
	 });
}	
}

function checkOut(id)
{
	$.post('/check/check_out',{id:id},function(data){
		
			if(data.result ==true)
			{
				
				if(confirm('編號：'+data.checkData.memberID+'\n姓名：'+data.checkData.name+'\n 入場時間：'+data.checkData.diff+'\n是否離場？'))
				{
					reallCheckOut(id);
				}
			}
		
		},'json')	
	
}

function reallCheckOut(id)
{
	$.post('/check/real_check_out',{id:id},function(data){
		
			if(data.result ==true)
			{
				$('#c_'+id).detach();
				
			}
		
	},'json')	
	
	
}

function memberNameClear()
{
	 $('#pay_memberID').val('');
	 $('.memberName').html('');
	 $('.memberLevel').html('');	
	 $('#timerMemberNeme').val('');
	
}



function setCheckInList(data)
{
	  $('.check_btn').show();
	  $('#memberID').val('');
	  $('#checkIN').show().append(
	  '<tr id="c_'+data.id+'">'+
		'<td>'+data.memberID+'</td>'+
		'<td>'+data.name+'</td>'+
		'<td>'+data.checkIN+'</td>'+
		'<td><input type="button" " value="出場" onclick="checkOut('+data.id+')" /></td>'+
	  '</tr>');	
	  memberNameClear();	
	 
	  $('#search_memberID').focus();
	
	
}

function placeMember(data)
{
		if(data.levelID==1)$('#allDisCount').val(90);
		else $('#allDisCount').val(85);
		changeAllDiscount($('#allDisCount').val());
		$('#pay_memberID').val(data.memberID);
		$('#timerMemberNeme').val(data.name);
		$('.memberName').html('姓名：'+data.name);
		$('.memberLevel').html(data.levelName);
		$('#checkout_findBarcode').focus();
		$('#memberData').html('');
}


function findMember(type){
	
	if(type==1){
		if(enter()==false) return;
	}
	var id = $('#search_memberID').val();
	var name = $('#search_name').val();
	var phone = $('#search_phone').val();	
	$.post('/member/get_member',{memberID:id,name:name,phone:phone},function(data){

			$('#memberData').html('');
			if(data.result==false)
			{
				memberTable(false);
				 alert('查無資料');
			}
			else if(id=='')
			{
				
				memberTable(data.memberData);
			
			}
			else 
			{
				
				 placeMember(data.memberData[0])
				
			}			
		},'json')
	$('#search_phone').val('') ;
	$('#search_memberID').val('');
	$('#search_name').val('') ;
}
var memberData;
function memberTable(data)
{

	if(data == false) {
		$('#memberData').html('<h1 style="color:red">查無資料，請重新輸入</h1>');
		return;
	} 
	$('#memberData').html(
	'請選擇一名會員'+
	'<table id="memberTable" width="500" border="1" style=" text-align:center">'+
    '<tr>'+
      '<td>會員編號</td>'+
      '<td>會員名稱</td>'+
	  '<td>會員等級</td>'+
    '</tr>'+    
  	'</table>'+
	'<input type="button" value="清除"   class="big_button check_btn" onclick="clearSearch()"/>'
	);
	memberData = data;
	for(key in data)
	{
		if(data[key].phone=='')data[key].phone = "權限不足";
		$('#memberTable').append(
		 '<tr onclick="placeMember(memberData['+key+'])">'+
		  '<td>'+data[key].memberID+'</td>'+
		  '<td>'+data[key].name+'</td>'+
		  '<td>'+data[key].levelName+'</td>'+
		'</tr>');
	}
	
}








function checkOutMember(id)
{
	if(enter()==false) return;
	
	$.post('/member/get_member',{memberID:id},function(data){
			

		},'json')		
	
	
	
}




function memberCheckIN(id,type){
	
	if(type==1){
		if(enter()==false) return;
	}
	if(id=='')id = $('#timerMemberNeme').val();
	if(id=='') alert('請輸入一組名稱以供識別');
	else
	$.post('/check/check_in',{memberID:id},function(data){
		
			if(data.result==false) alert('此會員已在清單');
			else setCheckInList(data.memberData);
		},'json')
	

}


function getCheckin()
{
		$.post('/check/get_check_in',{},function(data){
		for(var key in data){
			setCheckInList(data[key])
		}
		
		},'json')
	
	
}





function resetSellTable()
{
	pIndex = 0;
	$('#sell').show().html(
	'<table id="sellTable" border="1">'+
	'<tr>'+
      '<td>商品編號</td>'+
      '<td>商品名稱</td>'+
      '<td>商品價格</td>'+
      '<td>販售數量</td>'+
      '<td>折扣</td>'+
	  '<td>確認</td>'+
    '</tr>'+
	'</table>'+
	'<div style="float:right">總價<span id="totalSell">0</span>元</div>'+
	'<div style="clear:both;float:right">'+
        '<label><input type="radio"  name="payType" value="1" checked="checked"/>現金付款</label>'+
        '<label><input type="radio"  name="payType" value="2"/>信用卡</label>'+
     '</div>'
	);	
	$('#allDisCount').val(100);
	$('#sellMember').val('');
	$('#pay_memberID').val('');
	$('#memberName').html('');
	$('#memberLevel').html('');	
}

function resetCheckInTable()
{
	if(confirm('這麼做會將所有出場記錄刪除！請確認'))
	{
		$.post('/check/clear',{},function(data){
		
				if(data.result==true)
				{
					$('#checkInTable').show().html(
					'<table id="checkIN" width="400" border="1" style=" text-align:center">'+
					'<tr>'+
					  '<td>會員編號</td>'+
					  '<td>會員名稱</td>'+
					  '<td>進場時間</td>'+
					  '<td>進出場</td>'+
					'</tr>'+
					'</table>');	
					
				}
			
			
			},'json')
		
	}
}

function quickNewMember()
{

 	if($('#new_memberID').val()==''||$('#new_name').val()==''){
		alert('資料不完全，請重新輸入');
		return;
		
	}
	$.post('/member/quick_new_member',{memberID:$('#new_memberID').val(),name:$('#new_name').val(),level:$('#member_level').val()},function(data){
	
		
			if(data.result!=0)
			{
				alert('會員編號：'+$('#new_memberID').val()+'\n會員姓名：'+$('#new_name').val()+'\n資料已經新增')
				$('.new_member').val('');
				placeMember(data.memberData);
				$('#quickNewMember').hide();
				
			}
			else alert('會員編號重複囉');
			if (data.result==-1)$('#msgColumn').html('連線錯誤，會員資料在恢復連線後會自動新增');
			
		
		
		},'json')	
}
function getMemberLevel()
{
   	$.post('/member/get_member_level',{},function(data)
	{
		if(data.result==true)
		{
			for(key in data.memberLevel)
			{
				$('#member_level').append('<option value="'+data.memberLevel[key].levelID+'">'+data.memberLevel[key].levelName+'</option>');
				
			}
			
		}
		
	},'json')
	
	
}


$(document).ready(function(){
	 getCheckin();	
	 getMemberLevel();
	  resetSellTable();
	  queryProduct('checkout','select');
})

