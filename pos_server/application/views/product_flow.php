<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="/javascript/jquery.table2excel.min.js"></script>


<script type="application/javascript">

	
	
	
function paser_product(shopID)
    {

        
        $.post('/product_flow/get_all_list',{shopID:shopID},function(data){
            
            if(data.result==true)
            {
                $('#product_list_'+shopID).append('<h1><span id="progress_'+shopID+'"></span>/'+data.product.length+'</h1>');
                progress(shopID,0,data.product)
            }
            
        },'json')
    }

function progress(shopID,index,product)
{
	
	
	    $.post('/product_flow/set_avarage_num',{shopID:shopID,productID:product[index].productID},function(data){
            
            if(data.result==true)
            {
              
                $('#progress_'+shopID).html(index);
				if(index+1<product.length)progress(shopID,++index,product);
            }
            
        },'json')
	
	
	
}





</script>
<div class="product" style="width:1400px">
 <div id="classSelect"></div>
 	<form name="flow_form"  id="flow_form"action="/product_flow/flow_report" method="post" target="_blank">
    <div>
        <select name="from_year" id="from_year">
            <?php for($i = $time['year'] ;$i>2010;$i--):?>
                <option value="<?=$i?>"><?=$i?></option>
            <?php endfor;?>
        </select>
        年
        <select name="from_mon" id="from_mon">
            <?php for($i = 1 ;$i<=12;$i++):?>
                <option value="<?=$i?>" <?=($i==$time['mon'])?'selected="selected"':''?>><?=$i?></option>
            <?php endfor;?>
        
        </select>
        月
        <select name="from_day" id="from_day">
            <?php for($i = 1 ;$i<=31;$i++):?>
                <option value="<?=$i?>" <?=($i==1)?'selected="selected"':''?>><?=$i?></option>
            <?php endfor;?>
        </select>
        日~
        <select name="to_year" id="to_year">
            <?php for($i = $time['year'] ;$i>2010;$i--):?>
                <option value="<?=$i?>"><?=$i?></option>
            <?php endfor;?>
        </select>
        年
        <select name="to_mon" id="to_mon">
            <?php for($i = 1 ;$i<=12;$i++):?>
                <option value="<?=$i?>" <?=($i==$time['mon'])?'selected="selected"':''?>><?=$i?></option>
            <?php endfor;?>
        
        </select>
        月
        <select name="to_day" id="to_day">
            <?php for($i = 1 ;$i<=31;$i++):?>
                <option value="<?=$i?>" <?=($i==$maxDay)?'selected="selected"':''?>><?=$i?></option>
            <?php endfor;?>
        </select>
        日
        
        <div>
        <h1>請選擇要觀看的店家</h1>
                <label>
                <input type="checkbox" name="shop_0" value="1"  checked="checked" >幻遊天下
                </label>
                <?php foreach($shopList as $row):?>
                        <label>
                        <input type="checkbox"  name="shop_<?=$row['shopID']?>" value="1" checked="checked"><?=$row['name']?>
                        </label>
                <?php endforeach;?>
                <label>
                <input type="checkbox"  name="purchase" value="1" checked="checked">進貨
                </label> 
                 <label>
                <input type="checkbox"  name="customerBack" value="1" >客人退貨
                </label> 
                <label>
                <input type="checkbox"  name="amount" value="1" checked="checked">存貨
                </label> 
                <label>
                <input type="checkbox"  name="sell" value="1" checked="checked">出貨
                </label>
                  <label>
                <input type="checkbox"  name="back" value="1" >退公司
                </label> 
                  <label>
                <input type="checkbox"  name="adjust" value="1" >調他店
                </label>                                         
    </div>

     <div class="divider"></div>

     <input type="button" class="big_button"  value="查詢" onclick="productFormSend()">
     <input type="button" class="big_button"  value="貨物財報" onclick="getProductAccounting(0)">
     <input type="button" class="big_button"  value="近三月營業額登入" onclick="update_sale()">
     <input type="button" class="big_button"  value="壓貨查詢" onclick="getInventory()">
     <input type="button" class="big_button"  value="商品流速" onclick="getFlowRate()">
     <input type="button" class="big_button"  value="進銷存查詢" onclick="getIO()">
    <input type="button" class="big_button"  value="進出明細查詢" onclick="getOrderIO()">
     <input type="submit" class="big_button"  value="列印">
       </div>
     </form>
     	 <div id="productIOQuery"></div>
<div id="product_list">

	<div id="product_list_1"><input type="button" value="板橋" onclick="paser_product(1)"></div>

	</div>
</div>


