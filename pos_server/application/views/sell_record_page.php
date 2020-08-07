
<input type="button" value="開始" onclick=" confirm('d');sellRecord()"> 

<h1><span id="progress"></span>/811583</h1>
<script type="text/javascript">
 $(document).ready(function()
                  {
     
   
     sellRecord(109035);
     
 })
function sellRecord(mypage)
    {
       
        $.post('/product/sell_record',{page:mypage},function(data)
              {
         
                if(data.result==true)
                    {
                        
                        $('#progress').html(mypage);
                        sellRecord(mypage+1);
                        
                    }
                else alert('finish');
                    
            
            
            
            
            
        },'json')
       
        
        
        
    }
  
    
</script>