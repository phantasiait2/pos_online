<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<script type="text/javascript" src="/javascript/jquery.js"></script>

<script>
    function tagOut(id,way)
    {
        
        $.post('/welcome/tagout',{id:id,way:way},function(data)
              {
                if(data.result==true)$('#tag_'+id).detach();
            
        },'json')
            
    }
    
    
</script>
</head>
<body >
  <?php $i  = 0; foreach($data as $row):?>
          <div style="width:100%">
              <div style="font-size:6vh;"><?=$row['userID']?><?=$row['time']?>:<?=$row['content']?></div> 
              <!--
             <input type="button" onclick="tagOut(<?=$row['id']?>,1)" value="多" style="font-size:6vh;width:100%;height:10vh;background-color:red">
             <input type="button"  onclick="tagOut(<?=$row['id']?>,-1)"value="空" style="font-size:6vh;width:100%;height:10vh;background-color:green">
             <input type="button"  onclick="tagOut(<?=$row['id']?>,0)"value="無" style="font-size:6vh;width:100%;height:10vh;">-->
             
    </div>      
  
  <?php endforeach;?>

</body>
</html>