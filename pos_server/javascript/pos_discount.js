// JavaScript Document

var discountIndex = 0;

function discountTurn(id,concessions)
{
	
		var content='';
			content+='<div style="float:left" id="'+id+'_'+discountIndex+'">';
		for(each in concessions)
		{
		
				content+=discountForm(discountIndex,concessions[each].num,concessions[each].discount);	
		}
		content +='</div>'
		content +='<input type="button" value="新增折扣" onclick="discountDefine(\''+id+'_'+discountIndex+'\')"/>';

	discountIndex++;
	
	return content
}




function discountForm(index,num,discount)
{
	
	
	var content = '<input type="text" class="short_text distributeNum_'+index+'" value="'+num+'"/>個以上，折扣：'+
    			'<input type="text" class="short_text distributeDiscount_'+index+'" value="'+discount+'"/> 　';
	return content;


}


function discountDefine(id)
{
	var separate  = id.split('_');
	var	index = separate [1];
	$('#'+id).append(discountForm(index,'',''))

}

function discountDefineDel(index)
{
	$('#discountDefine_'+index).detach();
	
}