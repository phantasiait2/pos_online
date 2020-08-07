<div class="product">
<h1>STEP1.請先至官方網站下載更新檔<h1>
<a href="http://shipment.phantasia.com.tw/branch/update/<?=$this->uri->segment(3)?>">官方載點</a>
<div class="divider"></div>
<h1>STEP2.請選擇更新檔案</h1>
<form enctype="multipart/form-data" action="/system/upload" method="post">
<input type="file" name="data"/>
<input  type="submit"  value="更新"  class="big_button"/>


</form>
</div>