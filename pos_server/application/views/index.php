<div class="timer">
<h1>進出場管理</h1>
 <input type="text"  name="memberID" id="memberID" class="big_text" onkeyup="memberCheckIN(this.value,1)" />
<input type="button" value="進場"  class="big_button" onclick="memberCheckIN($('#memberID').val(),2)"/>
		

 <div class="divider"></div>
 <div id = "checkInTable" style="margin-bottom:20px;">
  <table id="checkIN" width="400" border="1" >
    <tr>
      <td>會員編號</td>
      <td>會員名稱</td>
      <td>進場時間</td>
      <td>進出場</td>
    </tr>
  </table>
  </div>
   <input type="button" value="清除所有記錄"    style=" margin-left:250px"class="big_button" onclick="resetCheckInTable()"/>
</div>




<div class="member">
<h1>會員資料查詢</h1>
 編號：<input type="text"  class="search medium_text" id="search_memberID"  onkeyup="findMember(1)" />
 姓名：<input type="text"  class="search medium_text"  id="search_name"  onkeyup="findMember(1)" />
 電話：<input type="text"  class="search medium_text"  id="search_phone"  onkeyup="findMember(1)" />
 <input type="button" value="查詢"   class="big_button check_btn" onclick="findMember(2)"/>	
 
  <div id="memberData"></div>

 <div class="divider"></div>
 <h1>會員快速新增</h1>
 編號：<input type="text" id="new_memberID"  class="new_member medium_text"  />
 姓名：<input type="text" id="new_name" class="new_member medium_text"  />
 等級：
 <select id="member_level" name="member_level">
 
 </select>
 <input type="button" value="新增"   class="big_button check_btn" onclick="quickNewMember()"/>	

</div>

 <div class="divider"></div>
<div class="checkout">

<h1>結帳</h1>
 <div class="divider"></div>
  會員編號：<input type="text"  name ="sellMember" id="sellMember" class="big_text" onkeyup="checkOutMember(this.value)"/>
   <span id="memberName" class="checkOutInfo"></span>
    <span id="memberLevel" class="checkOutInfo"></span>
   <span class="checkOutInfo">折扣：<input type="text" value="1" style="width:30px"  id="allDisCount" onblur="changeAllDiscount(this.value)"/></span>
 
 <div >
 商品條碼：<input type="text"  name="barcode" id="barcode"class="big_text"  onkeyup="findProduct(this.value,1)" />
	<input type="button" value="確認"  class="big_button" onclick="findProduct($('#barcode').val(),2)"/>
  </div> 
    <form name="sellProduct" id="sellProduct"> 
     
    <div id="sell" style="margin:20px 0 20px 0;">   
  	</div>
 		<input type="hidden" id="pay_memberID" name="pay_memberID"/>
        <div style="clear:both" ></div>
      <input type="button" value="結帳"    style=" display:none; margin-left:800px"class="big_button check_btn" onclick="payBill()"/>		
      <input type="button" value="取消"    style=" display:none; margin-left:5px"class="big_button check_btn" onclick="resetSellTable()"/>		

 
    <!--<input type="button" value="租借"  class="big_button"/>	-->
	</form>

</div>