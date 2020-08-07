<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>
    <title><?=$invoiceInf['orderNum']?></title>
<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/pos_order.js"></script>

<style>
    @charset "utf-8";/* v1.0 | 20080212 */
html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,font,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td{margin:0;padding:0;font-size:100%;border:0;outline:0;vertical-align:baseline;}
body{line-height:1;width: 5.8cm; }
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

@page{margin:0 0 0 0;}

.prod_w{margin:0 auto;width:20cm;overflow:hidden;}
.prod_w #WRAPPER{width:100%;}
.block_C{float:none;margin:0 auto;padding:30px 0;width:19.8cm;background-color:#fff;}
.invoice_form{position:relative;;}
.invoice_form .logotype{margin:2px auto;width:6cm;}
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
.invoice_form .block1{position:relative;float:none;width:6cm;}
.invoice_form .block1>div{width:100%;height:9cm;padding:0cm 0cm;margin-left:0cm;overflow:hidden;z-index:10;}
.invoice_form .block1>div>.table_box{position:relative;font: 13px/1 naomal, serif;z-index:1;font-weight: bold;}
.invoice_form dd h5{font:bold 19px/1.2 Verdana;}
.invoice_form .block1 h6{font:bold 22px/1 Verdana;}
.invoice_form .block1 h6 .value{font-size:28px;vertical-align:-3px;font-family:"Times New Roman";}
.invoice_form .block1 .type_1{width:180px;height:46px;margin:0 auto;}
.invoice_form .block1 .type_1 li{margin-left:5px;}
.invoice_form .block1 .type_1 th,.invoice_form .block1 .type_1 td{font:14px/1 naomal, serif;/*font-size:13px\0;*/text-align:left;font-weight: bold;}
.invoice_form .type_2 td{padding:2px 0 4px 0;width:50%;}
.invoice_form .type_2 tr:first-child img{width:200px;height:28px;}
.invoice_form .type_2 tr:nth-child(2) img{width:92px;height:92px;}
.invoice_form dd li span{display:block;}
.invoice_form .block1>div:first-child{border-bottom-width:0;}
.invoice_form .block1>div.s_border{border-bottom-width:1px;}
.invoice_form .block1 .s_list{border-top:0;}
.invoice_form .block1 .s_list th,.invoice_form .block1 .s_list td{padding:0 0;}
.invoice_form .block1 .s_list th{text-align:center;}
.invoice_form .block1 .s_list h5{margin:0 0 2px 0;}
.invoice_form .block1 .s_list th span{font:0.3cm Verdana;font-weight: bold;}
.invoice_form .block1 .s_list ul{padding:18px 14px 10px 14px;}
.invoice_form .block1 .s_list li{font:0.3cm;}
.invoice_form .block1 .s_list li.sp{margin-top:12px;}
.invoice_form .block2{position:absolute;top:0;right:0;width:200px;border:0;}
.invoice_form .block2 .sign{display:block;padding:6px 0 12px 0;padding:12px 0 4px 0\0;color:#f00;border:2px solid #f00;font:46px/1 Verdana;letter-spacing:20px;text-indent:18px;text-align:center;}
.invoice_form .duplicate{position:absolute;left:0;top:72px;width:100%;z-index:0;color:#ffb2b2;font:72px/1 Verdana;text-align:center;}
.invoice_form .block3 ul{position:absolute;left:230px;bottom:2px;font:13px/1.4 Verdana;font-weight: bold;}
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

    overflow: hidden;
    text-align: left;
   
 
    float: left;
  
    }
.productPrice
    {
       width:23%;
        height: 0.4cm;
        
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
   
}
    
</style>
<script type="application/javascript">
    $('documennt').ready(function(){
        
       
         <?php if($print!=''&&$commentText==''):?>
     
      //  factory.printing.printer = "<?=$print?>";
        window.print();
        <?php endif;?>
        
        
        
    })
    
    
</script>

</head>
<body>
<?php if($commentText!=''):?>
    <h1>發票號碼:<?=$invoiceInf['InvoiceNum']?></h1>
    <h2>總金額:<?=$invoiceInf['total']?></h2>
    <h2><?=$commentText?></h2>
<?php else:?>
<dl class="invoice_form" >
<dd class="block1" >
                                        <div >
                        <table class="table_box" style="text-align:center;">
                            <tr>
                                <td>
                                    <h5 style="font-size:14pt"?>瘋桌遊 PHANTASIA</h5>
                                    <h5 style="font-size:14pt"?>電子發票證明聯<?=($reprint==1)?'(補印)':''?></h5>
                                    <h6><span class="value"><?=$invoiceInf['year']?></span>年<span class="value"><?=$invoiceInf['period']?></span>月</h6>
                                    <h6><span class="value"><?=$invoiceInf['InvoiceCode']?>-<?=$invoiceInf['InvoiceNum']?></span></h6>
                                    <table class="table_box type_1">
                                        <tr>
                                            <td colspan="4">
                                                <ul class="inline">
                                                    <li class="clear_magi"><span><?=$invoiceInf['InoviceDateTime']?></spanspan> 格式25</li>
                                                   </ul>
                                            </td>
                                        </tr>
                                        <tr>
                                          <td>隨機碼:<?=$invoiceInf['RandomNumber']?></td>
                                          <td>總計:</td>
                                          <td><?=number_format($invoiceInf['total'])?></td>
                                        </tr>
                                        <tr>
                                          <td>賣方<?=$invoiceInf['SellerIdentifier']?></td>
                                      <?php if($invoiceInf['BuyerIdentifier']!='00000000'):?>
                                          <td>買方</td>
                                          <td><?=$invoiceInf['BuyerIdentifier']?></td>
                                        <?php else:?>
                                         <td></td>
                                          <td></td>
                                          
                                        <?php   endif;?>      
                                        </tr>
                                        <!--
                                        <tr>
                                          <th>賣方</th>
                                          <td>16606102</td>
                                          <th>總計</th>
                                          <td>610</td>
                                        </tr>
                                        -->
                                         <tr>
                                          <th></th>
                                          <td colspan="3"></td>
                                        </tr>
                                      </table>
                                </td>
                            </tr>
                            <tr style="height:3.5cm">
                                <td>
                                    <table class="table_box type_2">
                                                                              <tr>
                                          <td colspan="2">
                                                  
                                                   <img  style="width:88%" src="<?=$bcT?>">   
                                                   
                                             
                                                  </td>
                                                                          
                                        </tr>
                                        <tr>
                                         <td style="padding:0"><img style="height:92px;width:92px" src="<?=$qrT?>"></td>
                                          <td style="padding:0"><img style="height:92px;width:92px" src="<?=$qrT2?>"></td>
                          
                                        </tr>
                                       </table>
                                </td>
                            </tr>
                            <tr><td style="margin-top:1cm;font-size:0.3cm"><?=$invoiceInf['shopName']?></td></tr>
                            <tr>
                                
                                <td style="font-size:0.3cm">訂單編號：<span class="value"><?=$invoiceInf['orderNum']?></span></td>
                                
                            </tr>
                           
                        </table>
                                  
                                </div> 
                                <?php if($invoiceInf['BuyerIdentifier']=='00000000'): ?>
    <div style="page-break-after:always;clear:both;height:0"></div>                     
    <?php endif;?>                   
    
                         <div class="s_list" style="height:auto">
                        <table class="table_box type_1">
                            <tr>
                                <th>
                                    <h5>交易明細</h5>
                                    
                                    <?php $line = 0;$subTotal= 0 ;foreach($ProductArrays as $row):$str = $row['Description']?>
                          
                                     <?php 
                                                  do
                                                {
                                                     
                                                    $out = mb_strimwidth($str, 0,21,'', 'UTF-8');
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
                                                        if(mb_strlen($out,'UTF-8')>17)
                                                        {
                                                            echo '<span class="productName">'.$out.'</span><span class="productPrice"></span><br/>';
                                                             echo '<span class="productName">'.$row['UnitPrice'].'X'.$row['Quantity'].'</span>';
                                                            $line++;
                                                        }
                                                            
                                                        else
                                                        {
                                                             echo '<span class="productName">'.$out.' '.$row['UnitPrice'].'X'.$row['Quantity'].'</span>';
                                                            
                                                        }
                                                       
                                                    }
                                                }while($key);
                                       ?>
                                                   <span class="productPrice"><?=$st = $row['Amount']?></span>
                                    <?php   $line++;
                                             $subTotal+=$st;endforeach;?>
                    
       
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <ul>
                                         
                                         <li>總計：<?=number_format($invoiceInf['total'])?></li>
                                         <?php if($invoiceInf['BuyerIdentifier']!='00000000'): 
                                                $SalesAmount = round($invoiceInf['total']/1.05);
                                                $TaxAmount = $invoiceInf['total']-$SalesAmount;
                                             ?>
                                         <li>銷售額(應稅)：<?=number_format($SalesAmount)?></li>
                                         <li>稅額：<?=number_format($TaxAmount)?></li>
                                         <?php endif?>
                                                                              
                                                                                    <!--<li class="sp">備註：<span>個人識別碼：lint</span>
                                          
                                          </li>-->
                                    </ul>
                                </td>
                            </tr>
                            <tr><td>若商品有瑕疵或缺件請攜帶此憑證七天內到原購買店進行補件或更換</td></tr>
                        </table>
                    </div>
                                   
                                   
                                    
                                    </dd>

              
        </dd> 
    </dl>
<?php endif;?>
</body>
</html>