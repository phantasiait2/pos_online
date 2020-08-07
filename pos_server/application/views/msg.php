

<script type="text/javascript" src="http://www.phantasia.tw/libs/ckeditor/ckeditor.js"></script>

<h1>店家公告訊息</h1>
<h2  style=" cursor:pointer; color:#FFF" onClick="$('#report_work_list').show('slow')">我要發布新公告！</h2>
<div id="report_work_list" style="display:none; background-color:#6F6">
<div style=" background:#930">


</div>
	<form id="workForm">
      <table width="1024">
   			<tr><td>標題</td><td><input type="text" class="big_text" name="title"  style="width:500px;"></td></tr>
            <tr><td>內文</td><td><textarea style="width:800px; height:500px" id="content" name="content" ></textarea></td></tr>
           
						<script type="text/javascript">
								mainContent = CKEDITOR.replace( 'content', { customConfig : 'config_memberemail.js'} );
							</script>
             <tr>
            	<td colspan="2">
                	公告店家<br/>
                   	<input type="button" value="全部選取" class="big_button" onclick="$('.shop_check').attr('checked',true)">
		   			<input type="button" value="全部取消" class="big_button" onclick="$('.shop_check').attr('checked',false)">

                    <?php foreach($shopList as $row):if($row['shopID']<800):?>
            		<label><input type="checkbox" name="shop[<?=$row['shopID']?>]"  class="shop_check" value="1"><?=$row['name']?></label>
                    <?php  endif;endforeach; ?>
            	</td>
            </tr>
        </table>
        		<label><input type="checkbox" name="email" value="1">同步發送email！</label>
        		
        		<input type="button" class="big_button"value="送出"  onClick="announceSend()"/>
               	<input type="button" class="big_button"value="取消"  onClick="$('#report_work_list').hide('slow');"/>
    </form>
</div>

<div id="msgColomn"></div>
<div id="work_list" style="background-color:#FF6">
	<table border="1" width="1024">
    	<tr><td>序號</td><td>公告日</td><td>主題</td><td>狀況</td></tr>

    	
    
    </table>



</div>

<h1>店家螢幕跑馬燈</h1>
<h2  style=" cursor:pointer; color:#FFF" onClick="$('#report_problem').show('slow')">我要發布新訊息！</h2>
<div id="report_problem" style="display:none; background-color:#6F6">
<div style=" background:#930">


</div>
	<form id="msgForm">
      <table width="1024">
   			<tr><td>標題</td><td><input type="text" name="title"  style="width:500px;"></td></tr>
            <tr><td>內文</td><td><textarea    name="content" style="width:600px; height:30px"></textarea></td></tr>
            <tr>
            	<td colspan="2">
            		<label><input type="radio" name="show" value="1" checked="checked" >顯示</label>
                    <label><input type="radio" name="show" value="0">隱藏</label>
            	</td>
            </tr>
        </table>
        		<label><input type="checkbox" name="email" value="1">同步發送email！</label>
        		<input type="button" class="big_button"value="送出"  onClick="msgSend()"/>
               	<input type="button" class="big_button"value="取消"  onClick="$('#report_problem').hide('slow');"/>
    </form>
</div>

<div id="msgColomn"></div>
<div id="problem_list" style="background-color:#FF6">
	<table border="1" width="1024">
    	<tr><td>狀態</td><td>內容</td><td></td></tr>

    	<?php foreach($msg as $row):?>
    	<tr id="msg_<?=$row['id']?>"><td>
    		    <label><input type="radio" name="show_<?=$row['id']?>" value="1" <?=($row['show']==1)?'checked="checked"':''?> onchange="updateMsg(<?=$row['id']?>)" >顯示</label>
	            <label><input type="radio" name="show_<?=$row['id']?>" value="0" <?=($row['show']==0)?'checked="checked"':''?>  onchange="updateMsg(<?=$row['id']?>)" >隱藏</label>
        
        </td>
        <td><textarea style="width:600px; height:30px"  name="content" id="content_<?=$row['id']?>" onblur="updateMsg(<?=$row['id']?>)"><?=$row['msg']?></textarea></td>
        <td><input type="text" id="order_<?=$row['id']?>" value="<?=$row['order']?>" onblur="updateMsg(<?=$row['id']?>)"></td>
        <td><input type="button" value="刪除訊息" onclick="deleteMsg(<?=$row['id']?>)"></td></tr>
        <?php endforeach;?>
    
    
    </table>



</div>
<div id="errList" style="background-color:#FF6">
</div>


<script type="application/javascript">
$('.due').each(function()
{
		
	var WID = this.id.substr(5);

	var dates = $(this).datepicker({
							dateFormat: 'yy-mm-dd' ,
							
							yearRange: '1930',
							monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
							changeMonth: true,
							numberOfMonths: 1,
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
								
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates.not( this ).datepicker( "option", option, date );
							},
							onClose:function(data)
							{
								updateWork(WID)
							}
						});			
	
})



	var dates = $('#dueDay').datepicker({
							dateFormat: 'yy-mm-dd' ,
							
							yearRange: '1930',
							monthNamesShort:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
							changeMonth: true,
							numberOfMonths: 1,
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
								
									date = $.datepicker.parseDate(
										instance.settings.dateFormat ||
										$.datepicker._defaults.dateFormat,
										selectedDate, instance.settings );
								dates.not( this ).datepicker( "option", option, date );
							},
							onClose:function(data)
							{
							}
						});			
	

	
	function deleteMsg(id)
	{
		if(confirm('你確定要刪除？'))
		{
		$.post('/msg/delete_msg',{id:id},function(data){
			
				if(data.result==true)
				{
					$('#msg_'+id).detach();	
				}
			
			
		},'json')	
		
		}
	}
	function deleteWork(id)
	{
		if(confirm('你確定要刪除？'))
		{
		$.post('/msg/delete_work',{id:id},function(data){
			
				if(data.result==true)
				{
					$('#work_'+id).detach();	
				}
			
			
		},'json')	
		
		}
	}
	function updateMsg(id)
	{
	
		
		$.post('/msg/update_msg',{id:id,content:$('#content_'+id).val(),show:$('input[name=show_'+id+']:checked').val(),order:$('#order_'+id).val()},function(data){
				
					if(data.result==true)
					{
						
					}
				
				
			},'json')			
		
		
	}
	function updateWork(WID)
	{
	
		
		$.post('/msg/update_work',{WID:WID,content:$('#work_content_'+WID).val(),title:$('#work_title_'+WID).val(),dueDay:$('#time_'+WID).val()},function(data){
				
					if(data.result==true)
					{
						
					}
				
				
			},'json')			
		
		
	}
	function msgSend()
	{
        
        
        
	$.ajax({
		   type: "POST",
		   dataType:"json",
		   url: "/msg/insert_msg",
		   data: $("#msgForm").serialize(),
		   success: function(data){
			   
				if(data.result==true)
				{
						
					$('#report_problem').hide('slow');
					location.reload();
				}
			 
			   }
		   })	
		
		
		
	}
    
    function announceCheck()
    {
        content='<h1>公告內容</h1>';
        
        
        
        
        
        
        
    }
    
    
	function announceSend()
	{
        for ( instance in CKEDITOR.instances )
    CKEDITOR.instances[instance].updateElement();
        
		
		$.ajax({
			   type: "POST",
			   dataType:"json",
			   url: "/msg/insert_announce",
			   data: $("#workForm").serialize(),
			   success: function(data){
				  alert(data);
				   $('#report_problem').html(data);
					if(data.result==true)
					{
							
						$('#report_problem').hide('slow');
						location.reload();
					}
				 
				   }
			   })	
			
		
			
		}



</script>