$(document).ready( function(){
	$("#bbbtn").click(function(){
		alert("aa");
	});
	//var keys = getUrlVars();
//	var c = getUrlVars()["c"];
//	alert(c);
//	showLoading();
//	/**GETパラメータ分割**/
//	function getUrlVars(){
//	var vars = [], hash;
//	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
//	for(var i = 0; i < hashes.length; i++)
//	{
//	hash = hashes[i].split('=');
//	vars.push(hash[0]);
//	vars[hash[0]] = hash[1];
//	}
//	return vars;
//	}
//	function showLoading(){
//	$.mobile.loading('show',{text:'取得中...',textVisible:true,textonly:false});
//	}
//	function hideLoading(){
//	$.mobile.loading('hide');
//	}
//	function changeScreenCTR(content,scheduleId){
//	$.mobile.changePage(("#ctr"),{
//	type:"get",
//	reverse: false,
//	changeHash: false,
//	{c:content,s:scheduleId}
//	});
//	}
//	function changeScreenATT(){

//	}
});