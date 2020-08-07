  URL=location.search;
    if(URL=="")
	{
	    sid="WTX%26";
	    sname="台指期";
	    mid="01";
	    type="1";
}
    else
	{
	    sid=URL.substring(URL.indexOf("sid=",0)+4,URL.indexOf("&sname=",0));
	    sname=URL.substring(URL.indexOf("sname=",0)+6,URL.indexOf("&mid=",0));
	    mid=URL.substring(URL.indexOf("mid=",0)+4,URL.indexOf("&type=",0));
	    type=URL.substring(URL.indexOf("type=",0)+5,URL.length).replace(/&/,"");
}
    function refreshcomm(req) 
	{
	    var vmid = mid;
	    var vtype = type;
	    var vrequest = req;
	    var vsid = vrequest.substring(vrequest.indexOf("id=",0)+3,vrequest.indexOf("&name=",0));
	    var vsname = vrequest.substring(vrequest.indexOf("name=",0)+5,vrequest.length).replace(/&/,"");
	    parent.location.href="https://tw.futures.finance.yahoo.com/future/charts.html?sid="+vsid+"&sname="+vsname+"&mid="+vmid+"&type="+vtype+"";
	    
}
 