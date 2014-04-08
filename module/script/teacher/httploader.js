/*==========================================================
　 [Object定義] RequestData
==========================================================*/
var RequestData = function(){
	this.url = 0;
	this.httpRequest = function(type){
		var _xhr= new XMLHttpRequest();
		_xhr.open("GET",this.url);
		_xhr.responseType = type;
		_xhr.send();
		return _xhr;
	};
};
/*==========================================================
　 [Object作成] RequestData
==========================================================*/
var requestData = new RequestData();

requestData.testAlert = function(target_id,url) {
	alert("ようこそ！"+target_id+":"+url);
};

requestData.readData = function(target_id,url) {
	this.url = url;
	var xhr = this.httpRequest('text');
	xhr.onload=function(ev){
		document.getElementById(target_id).innerHTML = xhr.response;
	};
};

requestData.readSort = function(target_id,show_url,sort_url,status) {
	this.playData(target_id,show_url,sort_url,'席替えを実行します。',status);
};
requestData.playData = function(target_id,show_url,play_url,massege,status) {
	if(window.confirm(massege)){
		this.url = play_url;
		var xhr1 = this.httpRequest('text');
		sleep(300);
		this.url = show_url;
		var xhr2 = this.httpRequest('text');
		xhr2.onload=function(ev){
			document.getElementById(target_id).innerHTML = xhr2.response;
		};
		writeString('status_disp',status);
	}
};
/*==========================================================
　 [関数] writeString
==========================================================*/
function writeString(target_id,string){
	var jikan= new Date();
	var hour = jikan.getHours();
	var minute = jikan.getMinutes();
	var second = jikan.getSeconds();
	document.getElementById(target_id).innerHTML = string+" ("+hour+"時"+minute+"分"+second+"秒 現在)";
}
function sleep(milliSeconds){
	var time = new Date().getTime();
	while(new Date().getTime() < time + milliSeconds);
}