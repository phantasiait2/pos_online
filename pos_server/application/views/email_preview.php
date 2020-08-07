<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>信件預覽</title>
</head>

<body>
<table>
	    <tr><td>目標：</td>
        	<td>本店會員</td>	
        </tr>
    	<tr>
        	<td>性別：</td>
            <td>
            	<?php switch($emailData['sex'])
					{
						case 0:
						echo '不限';
						break;	
						case 1:
						echo '男性';
						break;	
						case 2:
						echo '女性';
						break;		
							
							
						}
						
				
				?>
                
             </td>
         </tr>
         <tr>    
             <td>年齡：</td>
             <td><?=$emailData['fromAge']?>~<?=$emailData['toAge']?></td>
        </tr>
    	<tr><td>主旨：</td><td><?=$emailData['subject']?></td></tr>
	 	<tr><td></td><td colspan="1"></td><tr>
        <tr><td></td><td><?=$emailData['content']?></td></tr>
        <tr><td></td><td colspan="1"></td><tr>
        <tr><td></td>
        	<td>
            </td>
       </tr>        

    

</table>
</body>
</html>