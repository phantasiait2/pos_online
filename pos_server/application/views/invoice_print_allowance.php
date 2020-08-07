<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pos_order.js"></script>

<style>
    @charset "utf-8";/* v1.0 | 20080212 */
html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,font,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td{margin:0;padding:0;font-size:100%;border:0;outline:0;vertical-align:baseline;}
body{line-height:1;}
ol,ul{list-style:none;}
blockquote,q{quotes:none;}
blockquote:before,blockquote:after,q:before,q:after{content:'';content:none;}
/* remember to define focus styles! */
a:focus,button:focus,input{outline:none;-moz-outline-style:none;}
/* remember to highlight inserts somehow! */
ins{text-decoration:none;}
del{text-decoration:line-through;}
/* tables still need 'cellspacing="0"' in the markup */
table{border-collapse:collapse;border-spacing:0;}
th,td{vertical-align:middle;}
*,*:before,*:after{box-sizing:border-box;}

@page{margin:1.2cm 0 0 0;size:A4;}

.prod_w{margin:0 auto;width:20cm;overflow:hidden;}
.prod_w #WRAPPER{width:100%;}
.block_C{float:none;margin:0 auto;padding:30px 0;width:19.8cm;background-color:#fff;}
.invoice_form{position:relative;;}
.invoice_form .logotype{margin:2px auto;width:4.8cm;}
.invoice_form .logotype img{height:43px;}
.invoice_form .header h5{margin:20px auto;font:bold 24px/1.2 Verdana;text-align:center;letter-spacing:3px;}
.invoice_form .content{padding:0.85cm;padding-top:0;}
.invoice_form .content table{width:100%;}
.invoice_form .main{height:252px;border:2px solid ;}
.invoice_form .main th,.invoice_form .main td{padding:6px 8px;border:1px solid ;}
.invoice_form caption{margin-bottom:5px;font:15px/1.2 Verdana;text-align:left;}
.invoice_form .main th{height:28px;font:15px/1.2 Verdana;}
.invoice_form .main td{font:12px/1.2 Verdana;vertical-align:top;}
.invoice_form .main td li{margin:10px 0;}
.invoice_form .col3f{position:relative;margin-top:30px;}
.invoice_form .block1{position:relative;float:none;width:5.7cm;}
.invoice_form .block1>div{width:100%;height:9cm;padding:0.3cm 0.2cm;border:1px dashed ;overflow:hidden;z-index:10;}
.invoice_form .block1>div>.table_box{position:relative;width:5.1cm;font: 13px/1 naomal, serif;z-index:1;}
.invoice_form dd h5{font:bold 19px/1.2 Verdana;}
.invoice_form .block1 h6{font:bold 22px/1 Verdana;}
.invoice_form .block1 h6 .value{font-size:30px;vertical-align:-3px;font-family:"Times New Roman";}
.invoice_form .block1 .type_1{width:180px;height:46px;margin:0 auto;}
.invoice_form .block1 .type_1 li{margin-left:5px;}
.invoice_form .block1 .type_1 th,.invoice_form .block1 .type_1 td{font:13px/1 naomal, serif;/*font-size:13px\0;*/text-align:left;}
.invoice_form .type_2 td{padding:2px 0 4px 0;width:50%;}
.invoice_form .type_2 tr:first-child img{width:200px;height:28px;}
.invoice_form .type_2 tr:nth-child(2) img{width:92px;height:92px;}
.invoice_form dd li span{display:block;}
.invoice_form .block1>div:first-child{border-bottom-width:0;}
.invoice_form .block1>div.s_border{border-bottom-width:1px;}
.invoice_form .block1 .s_list{border-top:0;}
.invoice_form .block1 .s_list th,.invoice_form .block1 .s_list td{padding:2px 0;}
.invoice_form .block1 .s_list th{text-align:center;}
.invoice_form .block1 .s_list h5{margin:0 0 2px 0;}
.invoice_form .block1 .s_list th span{font:0.3cm Verdana;}
.invoice_form .block1 .s_list ul{padding:18px 14px 10px 14px;}
.invoice_form .block1 .s_list li{font:0.3cm;}
.invoice_form .block1 .s_list li.sp{margin-top:12px;}
.invoice_form .block2{position:absolute;top:0;right:0;width:200px;border:0;}
.invoice_form .block2 .sign{display:block;padding:6px 0 12px 0;padding:12px 0 4px 0\0;color:#f00;border:2px solid #f00;font:46px/1 Verdana;letter-spacing:20px;text-indent:18px;text-align:center;}
.invoice_form .duplicate{position:absolute;left:0;top:72px;width:100%;z-index:0;color:#ffb2b2;font:72px/1 Verdana;text-align:center;}
.invoice_form .block3 ul{position:absolute;left:230px;bottom:2px;font:13px/1.4 Verdana;}
.invoice_form .block3 li{margin-top:10px;}
.block_C .bar_tool{margin-top:15px;}
.block_C .bar_tool li{display:inline-block;margin:0 6px;}
.block_C .bar_tool .button{padding:6px;min-width:120px;border:2px solid ;font-size:15px;cursor:pointer;background-color:#fff;}
.block_C .bar_tool .gray{border-color:#b8b8b8;color:#b9b9b9;}
.block_C .msg_box{margin-top:36px;font:12px/2 Verdana;}
.block_C .msg_box p{color:#f90;}
.invoice_form .block1,.invoice_form .type_2,.block_C .bar_tool,.block_C .msg_box{text-align:center;}
.productName{
    width:77%;
    height: 0.4cm;
    overflow: hidden;
    text-align: left;
   
 
    float: left;
  
    }
.productPrice
    {
       width:23%;
        height: 0.4cm;
        overflow: hidden;
        text-align: right;
  
        float: left;   
        
        
    }
@media print
{
    /*
    .invoice_form .col3f{min-height:330px;}
.invoice_form .block1,.invoice_form .block2{position:absolute;-ms-transform:rotate(-90deg);-moz-transform:rotate(-90deg);-o-transform:rotate(-90deg);-webkit-transform:rotate(-90deg);transform:rotate(-90deg);}
    .invoice_form .block1{top:-233px;left:233px;}
   
    .invoice_form .block2{top:1.92cm;left:14.2cm;}
    */
    .invoice_form .block3 {
        display: none;
    }
}
    
</style>
</head>
<body>
<dl class="invoice_form" style="width:60%; " >
<dd class="block1" >
                                        <div class="s_border" >
                        <table class="table_box" style="text-align:center;">
                            <tr>
                                <td>
                                    <div class="logotype"><img src="/images/2019logo.jpg" /></div>
                                    <h5>營業人銷貨退回、進貨退出或折讓證明單</h5>
                                   
                                    <table class="table_box type_1">
                                        <tr>
                                            <td colspan="4">
                                                <ul class="inline">
                                                    <li class="clear_magi" style="text-align:center"><?=substr($invoiceInf['AllowanceDateTime'],0,10)?></span></li>
                                                   </ul>
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                          <td>賣方統編：<?=$invoiceInf['SellerIdentifier']?></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:9pt">賣方名稱：幻遊天下股份有限公司</td>
                                        </tr>
                                         <tr>
                                          <td>買方統編：      
                                      <?php if($invoiceInf['BuyerIdentifier']!='00000000'):?>
                                         <?=$invoiceInf['BuyerIdentifier']?></td>
                                            <?php   endif;?>     
                                        </tr>
                                        <tr>
                                            <td>買方名稱：<?php if($invoiceInf['BuyerName']!='00000000'):?>
                                         <?=$invoiceInf['BuyerName']?></td>
                                            <?php   endif;?>   </td>
                                        </tr>
                                        <?php $line=0;$subTotal=0;foreach($ProductArrays as $row):?>
                                        <tr>
                                          <td>發票開立日期：<?=$row['OriginalInvoiceDate']?></td>
                                          
                                       
                                        </tr>
                                            <tr>
                                          <td><?=$row['OriginalInvoiceNumber']?></td>
                                          
                                       
                                        </tr>
                                         <tr>
                                          <th>
                                              
                                     
                                     <?php 
                                                $str = $row['OriginalDescription'];
                                                  do
                                                {
                                                     
                                                    $out = mb_strimwidth($str, 0,26,'', 'UTF-8');
                                                      $l =  mb_strwidth($str,'UTF-8');
                                                      
                                                    $str = mb_strimwidth($str, mb_strlen($out,'UTF-8'), $l, '', 'UTF-8');
                                                     
                                                    if(mb_strwidth($str, 'UTF-8')>0 ) 
                                                    {
                                                       
                                                        $key = true; 
                                                        echo '<span class="productName">'.$out.'</span><span class="productPrice"></span><br/>';
                                                           
                                                        $line++;
                                                    }
                                                    else 
                                                    {
                                                        $key = false;
                                                        echo '<span class="productName">'.$out.' '.$row['UnitPrice'].' X '.$row['Quantity'].'</span>';
                                                    }
                                                }while($key);
                                       ?>
                                          <span class="productPrice"><?=$st = $row['Amount']?>TX</span><br/>
                                    <?php   $line++;
                                             $subTotal+=$st;?>
                    
       
                                              
                                              
                                              
                                          </th>
                                       
                                        </tr>
                                              <?php endforeach;?>
                            <tr><td><br/></td></tr>      
                                         <tr>
                                            <td>
                                                <ul>
                                                    
                                                     <li>總計：<?=number_format($invoiceInf['TotalAmount'])?></li>
                                                     <li>未稅金額：<?=$t = round($invoiceInf['TotalAmount']/1.05)?></li>
                                                    <li>稅額：<?=$invoiceInf['TotalAmount']-$t?></li>
                                

                                                     <li>&nbsp;</li>                                                                              <!--<li class="sp">備註：<span>個人識別碼：lint</span>
                                                      <span>載具號碼：6614921</span>
                                                      </li>-->
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr><td>簽收人：</td></td>      
                                      </table>
                                </td>
                            </tr>
                            
                  
                        </table>
                                  
                                </div>     
                      
                           
                  
                                   
                                   
                                   
                                    </dd>
                <dd class="block2">
                                                        </dd>
                <dd class="block3">
                    <ul>
                        <li>財政部電子發票客服專線：0800-521-988 </li>
                        <li>發票與商品分開寄送，查件請至顧客中心。</li>
                        <li>客服專線：(02)8671-9616<span>(本公司不會以上述電話號碼撥打給您，若有顯示此號碼，請勿理會)</span></li>
                    </ul>
                </dd>    
                    </dd>
   
    </dl>

</body>
</html>