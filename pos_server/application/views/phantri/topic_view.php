    <?php $t = $list[0];?>
    <?php ?>
    
    <?php foreach($list as $row):?>
     <tr  id="parent_<?=$row['id']?>"  class="<?=($key)?'waitReply':'alreadyReply'?> reply_parent_<?=$row['parentID']?> <?=$t['type']?> allTopic" 
      style="  <?=($row['id']==$row['parentID'])?' background-color:#FFF; color:#F00;height:35px ':'background-color:#CFF; color:#000'?>">
        <th style=" width:180px; "><?=($row['id']==$row['parentID'])?'':'->'?><?=substr($row['time'],0,16)?></th>
        <th style=" width:300px;">[<?=$row['name']?>]<?=$row['account']?> <?=$row['type']?></th>
        <td><?=$row['content']?>
        
        <?php if(isset($row['img'])):?>
			        <div class="divider"></div>
                    <div class="img">
		<?php foreach($row['img'] as $each):?>
        	<a href="<?=$each?>">
			<img src="<?=$each?>"   style="max-width:100px; max-height:100px" >
            </a>
		<?php endforeach; ?>
        </div>
       	<?php endif; ?>
        </th>
        <td><?=$row['status']?></td>
    </tr>
    
    
    <?php endforeach;?>
    <?php if($t['status']=='已結案'):?>
                <tr  id="closeTask_<?=$t['parentID']?>" class="reply_parent_<?=$row['parentID']?> <?=$t['type']?> allTopic"  style="background-color:#CFF; color:#000">	
            		<td colspan="5">已結案</td>
            	</tr>
            	<?php else:?>
               	<tr style="background-color:#CFF; color:#000" id="replyTr_<?=$row['parentID']?>" class="<?=($key)?'waitReply':'alreadyReply'?> reply_parent_<?=$row['parentID']?> <?=$t['type']?> allTopic" >
                    <td colspan="1">
                    
                    <?php if($t['type']=="到貨詢問" &&$this->data['shopID']==0):?>
                    <input type="button" class="big_button" value="填寫預計到貨日" onclick="getProductPreTime(<?=$t['productID']?>)">
                      <input type="button" id="tokingTaskBtn_<?=$t['parentID']?>" class="big_button" value="洽談中" onclick="talkingTask(<?=$t['parentID']?>)">
             
					<script  type="application/javascript">getAlreadyOrder(<?=$t['productID']?>); </script>				
                    <?php elseif($t['type']=="缺件回報"):?>
                    <form id="shortForm<?=$t['parentID']?>">
                    <input type="hidden" value="<?=$t['parentID']?>" name="parentID">
                    <label><input  onchange="shortStatus(<?=$t['parentID']?>)" type="checkbox"  value="1" name="status_<?=$t['parentID']?>[]" id="status_<?=$t['parentID']?>_1">聯絡廠商</label>
                    <label><input  onchange="shortStatus(<?=$t['parentID']?>)" type="checkbox"  value="2" name="status_<?=$t['parentID']?>[]" id="status_<?=$t['parentID']?>_2">廠商回應</label><br/>
                    <label><input  onchange="shortStatus(<?=$t['parentID']?>)" type="checkbox"  value="3" name="status_<?=$t['parentID']?>[]" id="status_<?=$t['parentID']?>_3">廠商寄出</label>
                    <label><input  onchange="shortStatus(<?=$t['parentID']?>)" type="checkbox"  value="4" name="status_<?=$t['parentID']?>[]" id="status_<?=$t['parentID']?>_4">公司寄出</label>
                    </form>
                    <script  type="application/javascript">loadShortStatus(<?=$t['parentID']?>); </script>			
                    <?php endif;?>
                     <?php if($t['type']=="退貨問題" ):?>
                     	<input type="button" class="big_button" value="查看退貨狀況" onclick="showOrderBack(<?=$t['productID']?>,'<?=($this->data['shopID']==0)?'staff':'watch'?>')">

                     <?php endif;?>
                    
                    
                    
                    
                      <?php if($this->data['shopID']==0):?>
                	<input type="button" id="transferTaskBtn_<?=$t['parentID']?>" class="big_button" value="轉部門" onclick="transferTask(<?=$t['parentID']?>)">
                    <?php endif;?>
                    </td><td>回覆留言</td>
                    <td colspan="1"><textarea  class="big_text" style="width:80%"  placeholder="回覆" id="msg_<?=$t['parentID']?>"   onkeypress="reply(<?=$t['parentID']?>,1)"></textarea><img id="pic_<?=$t['parentID']?>" src="/images/pictures.png" onclick="replyImageUpload(<?=$t['parentID']?>)" style="width:30px; float:right; cursor:pointer" title="上傳圖片"><div id="reply_image_canvas_<?=$t['parentID']?>" style="display:none"></div></td>
                    <td><input type="button"  class="big_button" value="留言"  id="replyBtn_<?=$t['parentID']?>" onclick="reply(<?=$t['parentID']?>,0)"></td>
           		 </tr>
                 	
                        <tr id="closeTask_<?=$t['parentID']?>" class="<?=($key)?'waitReply':'alreadyReply'?>  reply_parent_<?=$row['parentID']?> <?=$t['type']?> allTopic"  style="background-color:#CFF; color:#000">
                             <td><input type="button" id="closeTaskBtn_<?=$t['parentID']?>" class="big_button" value="結案" onclick="closeTask(<?=$t['parentID']?>,<?=($t['type']=="到貨詢問" &&$this->data['shopID']==0)?$t['productID']:0?>)"></td>
                            <td colspan="1">
                            	     <?php if($this->data['shopID']==0):?>
                            	   
                            	<input type="button" id="processTaskBtn_<?=$t['parentID']?>" class="big_button" value="詢問後續進度" onclick="processTask(<?=$t['parentID']?>)">
                            		<?php endif;?>
                            </td><td></td>
                            <td colspan="1"></td>
                        </tr>
            		
                <?php endif;?>
                	
            <tr style="height:20px" class="<?=($key)?'waitReply':'alreadyReply'?>  reply_parent_<?=$row['parentID']?> <?=$t['type']?> allTopic"  ><td colspan="5"></td></tr>
            
