<?php 
/**
 * @desc    SMTP判断邮箱是否存在,检查email地址是否真实存在

原理解释：

在以往的编程中，比如编写用户的资料时，有时需要确认用户输入的Email是否真实有效，以前我们最多只能做到验证Email是否包含了某些特殊的字符，比如"@",".",".com"等,做到的只是判断了Email的合法性，证明用户填写的Email格式是正确的，但是这个Email是否真正的存在于网络中，则没有办法。


　首先需要大家了解一下SMTP协议。
1.SMTP是工作在两种情况下：一是电子邮件从客户机传输到服务器；二是从某一个服务器传输到另一个
　　服务器
2.SMTP是个请求/响应协议，命令和响应都是基于ASCII文本，并以CR和LF符结束。响应包括一个表示返
　　回状态的三位数字代码
3.SMTP在TCP协议25号端口监听连接请求
4.连接和发送过程
SMTP协议说复杂也不复杂（明明带有"简单"这个词嘛），说简单如果你懂得Sock。不过现在只是我们利用的就是第一条中说的，从客户机传输到服务器，当我们向一台服务器发送邮件时，邮件服务器会首先验证邮件发送地址是否真的存在于本服务器上。
操作的步骤如下：
连接服务器的25端口（如果没有邮件服务，连了也是白连）
发送helo问候
发送mail from命令，如果返回250表示正确可以，连接本服务器，否则则表示服务器需要发送人验证。
发送rcpt to命令，如果返回250表示则Email存在
发送quit命令，退出连接
 */
class email_validation_class {
    var $timeout    = 0;
    var $localhost  = "";
    var $localuser  = "";
    
    function GetLine($connection) {
        for($line = "";;) {
              if(feof($connection)) {
                    return(0);
              }
              $line     .= fgets($connection,100);
              $length   = strlen($line);
              if ($length >= 2 && substr($line,$length-2,2) == "\r\n") {
                    return(substr($line,0,$length-2));
              }
        }
    }
    
    function PutLine($connection,$line) {
        return(fputs($connection,"$line\r\n"));
    }
    
    function ValidateEmailAddress($email) {
        return (preg_match("|^[-_.0-9a-z]+@([-_0-9a-z][-_0-9a-z]+\.)+[a-z]{2,3}$|i",$email));
    }
    
    function ValidateEmailHost($email,&$hosts = 0) {
        if(!$this->ValidateEmailAddress($email)) {
              return(0);
        }
        $user   = strtok($email,"@");
        $domain = strtok("");
        if(GetMXRR($domain,$hosts,$weights)) {
            // -- GetMXRR only used on unix
			
              $mxhosts = array();
              for($host = 0;$host<count($hosts);$host++)
                    $mxhosts[$weights[$host]] = $hosts[$host];
              KSort($mxhosts);
              for(Reset($mxhosts),$host = 0;$host<count($mxhosts);Next($mxhosts),$host++)
                    $hosts[$host] = $mxhosts[Key($mxhosts)];
        } else {
              $hosts = array();
              if(strcmp(@gethostbyname($domain),$domain) != 0)
              $hosts[] = $domain;
        }
        return(count($hosts) != 0);
    }
    
    function VerifyResultLines($connection,$code) {
        while(($line = $this->GetLine($connection))) {
              if (!strcmp(strtok($line," "),$code)) {
                    return(1);
              }
              if (strcmp(strtok($line,"-"),$code)) {
                    return(0);
              }
        }
        return(-1);
    }
    
    function ValidateEmailBox($email) {
        if(!$this->ValidateEmailHost($email,$hosts)) {
              return(0);
        }
        if(!strcmp($localhost = $this->localhost,"") && !strcmp($localhost = getenv("SERVER_NAME"),"") && !strcmp($localhost = getenv("HOST"),"")) {
              $localhost = "localhost";
        }
        if(!strcmp($localuser = $this->localuser,"") && !strcmp($localuser = getenv("USERNAME"),"") && !strcmp($localuser = getenv("USER"),"")) {
              $localuser = "root";
        }
		
        for($host = 0;$host<count($hosts);$host++) {
              if(($connection = ($this->timeout ? fsockopen($hosts[$host],25,$errno,$error,$this->timeout) : fsockopen($hosts[$host],25)))) {
                    if($this->VerifyResultLines($connection,"220")>0 && $this->PutLine($connection,"HELO $localhost") && $this->VerifyResultLines($connection,"250")>0 && $this->PutLine($connection,"MAIL FROM: <$localuser@$localhost>") && $this->VerifyResultLines($connection,"250")>0 && $this->PutLine($connection,"RCPT TO: <$email>") && ($result = $this->VerifyResultLines($connection,"250"))>= 0) {
                          fclose($connection);
                          return($result);
                    }
                    fclose($connection);
              }
        }
        return(-1);
    }
};
?>
