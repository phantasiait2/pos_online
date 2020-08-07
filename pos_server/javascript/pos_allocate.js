// JavaScript Document

function autoAllocate(nowNum)
{
	$.post('/product/auto_allocate',{nowNum:nowNum,productID:$('#allocateProductID').val()},function(data)
	{
		
		if(data.result==true)
		{
			for(key in data.allocate)
			{
				
				$('#allocate_'+data.allocate[key].rowID).val(data.allocate[key].sellNum);
			}
			
			countRemain();
			
		}
		
		
		
	},'json')
;
	
	
}

function countRemain()
{
	var nowNum = parseInt($('#nowNum').html());
    var orderNum = 0  ; 
	$('.allocateNum').each(function(){
		nowNum -= parseInt($('#'+this.id).val());
	})
    $('.buyNum').each(function(){
		orderNum += parseInt($('#'+this.id).html());
	})
	$('#remain').html(nowNum);
    $('#need').html(orderNum);
    
    
  
  
    
}



function reAllocateSend()
{
	$.ajax({
	   type: "POST",
	   dataType:"json",
	   url: "/order/re_allocate_send",
	   data: $("#allocateForm").serialize(),
	   success: function(data){
		   
		   if(data.result==true)
		   {
			   if($('#reAddressID').val())   shipmentView('staff',$('#reAddressID').val());
				closePopUpBox();  
		   }
	   }
	   });

	
	
}

function orderRowDelete(orderID,id)
{
	if(confirm('你確定要刪除？'))
	{
		$.post('/order/product_delete',{orderID:orderID,id:id},function(data){
			
			if(data.result==true){
					$('#orderRow_'+id).detach();
					$('#allocateOrderRow_'+id).detach();
				
				
				}
			else alert('魔風直送單無法刪除')	
				
		},'json')	
	}
	
}



function magicCheck(m)
{
	if(m>0) img = '<img style=" width:20px;" src="/images/Hasbro.png">';
	else img ='';	
	return img;
	
}