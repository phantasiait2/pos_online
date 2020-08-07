<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<table border="1">

    <tr><td>時間</td><td>內容</td></tr>
    
    <?php foreach($msg  as $row):?>
            <tr><td><?=$row['time']?></td><td><?=str_replace('<br/>','',$row['msg'])?></td></tr>
        
    <?php endforeach;?>


</table>

