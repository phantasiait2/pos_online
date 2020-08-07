
<div class="header" style="height:68px; position:relative; margin-bottom:2px; text-align:center;">
	
    	 <div id="headerMenu">
            	<div style="color:#FFF; float:left"><a><?=$account?>,您好</a> | <a href="/welcome/logout">登出</a></div>
				
      	</div>
        <div class="clearfix"></div>
           <div id="ajaxLoader" style="float:left"></div>
		<div class="titleList" style="margin-top:5px">
      
                 <?php if($level==-1):?>
                    <a href="/supplier/product">
                     <input type="button" class="titleBtn" value="商品管理">
                    </a>
                    <a href="/account">
                       <input type="button" class="titleBtn" value="使用者管理">
                    </a>
                  <?php else:?>	
                 
                 
                     <?php if($shopID==0&&$level>=80):?><input type="button" class="titleBtn" value="商品管理" onclick="location.href='/product'"><?php endif;?>
                   <?php if($shopID!=0):?> <input type="button" class="titleBtn" value="訂購商品" onclick="location.href='/order/online'"><?php endif;?>
                     <?php if($shopID<=1000 && $shopID!=0):?> <input type="button" class="titleBtn" value="客戶預訂管理" onclick="location.href='/csorder/webview'"><?php endif;?>

                    <?php if($shopID<=500 && $shopID!=0):?> <input type="button" class="titleBtn" value="線上訂位管理" onclick="location.href='/check/reserve_seat/'"><?php endif;?>


                    <?php if($shopID==0):?><input type="button" class="titleBtn" value="訂單管理" onclick="location.href='/order/view'"><?php endif;?>

                    <?php if($shopID==0&&$level>50 &&$account!='stock'):?><input type="button" class="titleBtn" value="會員管理" onclick="location.href='/member/'"><?php endif;?>
                      <?php  if($shopID==0 &&$account!='stock' ):?><input type="button" class="titleBtn" value="商品流向" onclick="location.href='/product_flow'"><?php endif;?>

                    <?php  if(($level>50)&&$account!='stock'):?><input type="button" class="titleBtn" value="帳務管理" onclick="location.href='/accounting'"><?php endif;?>




                   <?php  if(($shopID!=0&&$level>50)||($level>50&&$account!='stock')):?> <input type="button" class="titleBtn" value="銷售資訊分析" onclick="location.href='/sale/online'"><?php endif;?>
                    <input type="button" class="titleBtn" value="使用者管理" onclick="location.href='/account'">

                   <a href="/phantri" target="_new"><input type="button" class="titleBtn" value="問題解決中心" ></a>
                   <?php if($shopID==0&&$level>80 &&$account!='stock'):?><input type="button" class="titleBtn" value="訊息發布" onclick="location.href='/msg'"><?php endif;?>
               	 <?php endif;?>	
          
                
        </div>
          
       
   
    <div class="clearfix"></div>
    
</div>
<div  style="background-image:url(/images/index_banner-bottom.png); height:17px;"></div>