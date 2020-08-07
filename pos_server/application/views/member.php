
<input type="button"  class="big_button"  value="查詢會員" onclick="createOrder()"/ >
<input type="button"  class="big_button"  value="email發送審核" onclick="getEmailAudit(0)"/ >  

 <a href="http://shipment.phantasia.com.tw/member/get_email_send" target="_blank"> <input type="button" value="總部發信"   class="big_button check_btn" />	</a>
<div class="divider"></div>

<div class="product" style="width:1200px">
<h1>會員資料查詢</h1>
 會員編號：<input type="text"  class="search big_text" id="search_memberID"  onkeyup="findMember(1)"   />
 會員姓名：<input type="text"  class="search big_text"  id="search_name"  onkeyup="findMember(1)" />
 會員電話：<input type="text"  class="search big_text"  id="search_phone"  onkeyup="findMember(1)" />
 <input type="button" value="查詢"   class="big_button check_btn" onclick="findMember(2)"/>	

  <input type="button" value="清除"   class="big_button check_btn" onclick="clearSearch()"/>	
  <form name="memberDataForm" id="memberDataForm">
  <div id="memberData" style="margin-top:20px;"></div>
    <div class="divider"> </div>

   <input type="button" value="查詢所有會員"   class="big_button check_btn" onclick="findAllMember()"/>	

	</form>
</div>

