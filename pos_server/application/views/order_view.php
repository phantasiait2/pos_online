<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">

<!--<script type="application/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>-->

<script type="text/javascript">
$(document).ready(function()
{
		//showOrderList(<?=($shopID>0)?'"watch"':'"staff"'?>,0);
		
		setDateSelector()
		
})


function setDateSelector()
{
		var dates = $( "#fromDate").datepicker({
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
                                    showShipmentList('<?=($shopID>0)?'watch':'staff'?>',$('#selectOrderStatus').val())
									
								}
							
						});		  	
			var datesto = $( "#toDate").datepicker({
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
									 showShipmentList('<?=($shopID>0)?'watch':'staff'?>',$('#selectOrderStatus').val())
								}
						});		  	
	
	
}


function shopSearch(option,searchID,target,backfunction)
{

	
	var minMatch = 99;
	var chageValue = 0;
	$('.'+option).each(function(){
		
		var judge = $(this).html().indexOf($('#'+searchID).val());
		if(judge!=-1)
		{
			if(judge<minMatch) 
			{
				chageValue = $(this).val();	
				minMatch = judge;
			}
			
			
			
			
		}
		 	
		
	})
	$('#'+target).val(chageValue);
	

	if(chageValue>0 &&backfunction==true)showShipmentList('<?=($shopID>0)?'watch':'staff'?>',$('#selectOrderStatus').val());
	
	
}


function  goInvoice()
{
	
	content='<h1>請輸入發票區間</h1>'+
	        '<input type="text"  id="invoiceFrom" value="0000-00-00"> 至 <input type="text"  id="invoiceTo" value="0000-00-00">';
	 openPopUpBox(content,800,200,"invoiceGo");	
	
	var dates = $('#invoiceFrom').datepicker({
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
								}
							});			
		var dateWSs = $('#invoiceTo').datepicker({
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
								}
							});			
		
	
	
	
}

function invoiceGo()
{
	
	window.open('http://shipment.phantasia.com.tw/order/get_invoice_list/'+$('#invoiceFrom').val()+'/'+$('#invoiceTo').val(), "about:blank")
	
	
	
}




</script>

 <style>
  .custom-combobox {
    position: relative;
    display: inline-block;
  }
  .custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
    /* support: IE7 */
    *height: 1.7em;
    *top: 0.1em;
  }
  .custom-combobox-input {
    margin: 0;
    padding: 0.3em;
  }
  
  
  </style>

<div class="product">
<?php if($account!='stock'):?>
<input type="button"  class="big_button"  value="新增訂單" onclick="createOrder()"/ >
<input type="button"  class="big_button"  value="訂貨清單" onclick="$('#selectOrderStatus').val(0);$('#product_list').html('');showOrderList('<?=($shopID>0)?'watch':'staff'?>','0')"/ >
<?php endif;?>
<input type="button"  class="big_button"  value="寶可夢訂單" onclick="$('#selectOrderStatus').val(6);$('#product_list').html('');showOrderList('<?=($shopID>0)?'watch':'staff'?>','6')" / >
<input type="button"  class="big_button"  value="出貨清單" onclick="shipmentView('staff')" / >
<input type="button"  class="big_button"  value="出貨紀錄" onclick="$('#selectOrderStatus').val(5);showShipmentList('<?=($shopID>0)?'watch':'staff'?>',5)"  id="shipOutBtn"/ >

<?php if($account!='stock'):?>
<input type="button"  class="big_button"  value="查看寄賣清單" onclick="$('#product_list').html('');showConsignment('<?=($shopID>0)?'watch':'staff'?>')"/ >
<input type="button"  class="big_button"  value="查看退貨紀錄" onclick="$('#product_list').html('');showBOAList('Back','<?=($shopID>0)?'watch':'staff'?>')"/ >
<input type="button"  class="big_button"  value="店家點數管理" onclick="pointFrame()"/ >

<input type="button"  class="big_button"  value="已開發票清單" onclick="goInvoice()" / >
<input type="button"  class="big_button"  value="商品集單區" onclick="getCollect()" / >
<?php endif;?>

<div class="divider"></div>
<?php if($shopID==0):?>

<?php if($account!='stock'):?>
<input type="button"  class="big_button"  value="電子商務出貨" onclick="$('#selectOrderStatus').val(-3);ecView();"/ >
<input type="button"  class="big_button"  value="產生寄賣補貨" onclick="consignmentOrder()"/ >
<input type="button"  class="big_button"  value="月結" onclick="monthCheck()"/ >
<input type="button"  class="big_button"  value="月結總表" onclick="monthProfit()"/ >

<input type="button"  class="big_button"  value="寄賣結帳表" onclick="consignmentCheck()"/ >
<input type="button"  class="big_button"  value="已收款項輸入" onclick="haveMoney()"/ >
<input type="button"  class="big_button"  value="其他金額輸入" onclick="otherMoney()"/ >

<input type="button"  class="big_button"  value="出貨地點" onclick="getOtherShop()" / >

<input type="button"  class="big_button"  value="出貨等級" onclick="getDistribute()" / >

<?php endif;?>


<input type="button"  class="big_button"  value="拉密大賽" onclick="getRumiFrame()" / >


<?php endif;?>

<input type="hidden" id="myshopID" value="<?=$shopID?>">

<div class="divider"></div>
<select name="selectOrderStatus" id="selectOrderStatus" style="float:left:100px;"  onchange="showShipmentList('<?=($shopID>0)?'watch':'staff'?>',this.value)">
		<option value="0">訂貨清單</option>
       <option value="6">寶可夢清單</option>
        <option value="5">出貨紀錄</option>
		<option value="4">訂單已完成</option>
		<option value="2">已送達物流</option>
		<option value="3">已到貨</option>
		<option value="-1">點數兌換專區</option>
		<option value="-2">出貨地點</option>
		<option value="-3">電子商務出貨</option>
</select>


<select name="selectOrderType" id="selectOrderType"  onchange="showOrderList('<?=($shopID>0)?'watch':'staff'?>',$('#selectOrderStatus').val())">
		<option value="2">買斷+寄賣</option>
		<option value="0">買斷</option>
        <option value="1">寄賣</option>
</select>





<input type="text" id="shop_search" placeholder="搜尋出貨地點"   onkeyup="shopSearch('select_shopID_op','shop_search','select_shopID',true)">
<select name="shopID" id="select_shopID" onchange="showShipmentList('<?=($shopID>0)?'watch':'staff'?>',$('#selectOrderStatus').val());editShipment(focusID);focusID=0">
		<option class="select_shopID_op" value="0">全部</option>
        <option class="select_shopID_op" value="-2">加盟</option>
        <option class="select_shopID_op" value="-1">非加盟</option>
	<?php foreach($shop as $row):?>
		<?php if($shopID==0||$row['shopID']==$shopID):?>
        <option class="select_shopID_op"  value="<?=$row['shopID']?>"><?=$row['name']?></option>
        <?php endif;?>
    <?php endforeach;?>
   

</select>

<label><input type="checkbox" value="1" id="report_list">報表</label>

<label><input type="checkbox" value="1" id="report_invoice">隱藏已開</label>    

<?php $date = getdate();?>
查詢日期：從
<input type="text" name="fromDate" id="fromDate" value="<?=date("Y-m-d", mktime(0, 0, 0,$date['mon'], $date['mday']-10, $date['year']));?>">
到
<input type="text" name="toDate"  id="toDate" value="<?=date("Y-m-d")?>">

 
               
<div id="productQuery"></div>
    <div id="product_list"><h1>請點選上方選項開始操作</h1></div>
</div>

