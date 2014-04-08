$(function(){

	/*ゲットパラメータ取得*/
	var scheduleId = getUrlVars()['s'];

	$('.tabbox:first').show();
	$('#tab li:first').addClass('active');
	$('#tab li').click(function() {
		$('#tab li').removeClass('active');
		$(this).addClass('active');
		$('.tabbox').hide();
		$($(this).find('a').attr('href')).fadeIn();
		return false;
	});


	/**GETパラメータ分割**/
	function getUrlVars(){
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++)
		{
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}

	/*==========================================================
	　 [ロード] 
	==========================================================*/

	function showLoading(){
		$.mobile.loading('show',{text:'処理中...',textVisible:true,textonly:false});
	}
	function hideLoading(){
		$.mobile.loading('hide');
	}


	/*==========================================================
	　 [ボタン] 
	==========================================================*/

	/**
	 * 
	 * 出席調査開始タップ
	 * 
	 * **/
	$("#callStartBtn").click(function(){
		// 2重送信防止クラスのチェック
		if ( $(this).hasClass('double_click') ){
			return false;
		}
		doCallStart();
	});
	/**
	 * 
	 * 出席調査終了タップ
	 * 
	 * **/
	$("#callEndBtn").click(function(){
		if ( $(this).hasClass('double_click') ){
			return false;
		}
		doCallEnd();
	});

	$("#attBtn").click(function(){

	});

	/*==========================================================
	　 [ボタン処理] 
	==========================================================*/
	/**
	 * 
	 * 出席調査開始処理
	 * 
	 * **/
	function doCallStart(){
		showLoading();
		doAccessCall(0);
	}
	/**
	 * 
	 * 出席調査終了処理
	 * 
	 * **/
	function doCallEnd(){
		showLoading();
		doAccessCall(1);
	}

	/*==========================================================
	　 [通信処理] 
	==========================================================*/
	/**
	 * 
	 * 出席調査通信
	 * situation
	 * 0 : 出席調査開始
	 * 1 : 出席調査終了
	 * **/
	function doAccessCall(situation){
		$.ajaxSetup({ async: true });
		$.ajax({
			timeout: 6000,
			url: '../../../cmsm/module/php/base/teacher/call_controller.php',
			type: "POST",
			data: { s : scheduleId, si : situation},
			success: function(json){
				hideLoading();
				var t = json['TAKE_COURSE'];
				if(t == 1){
					alert("履修者が登録されていないので，出席調査を開始できません．");
				}else{
					var mb = json['CHANGE_SCREEN'];
					if(mb == 0){
						/*正常*/
						var p = json['PROCESS']['RESULT'];
						if(p == 0){
							/*正常終了*/
							var t = json['PROCESS']['TIME'];
							callSuccessUI(situation,t);
						}else{
							if(situation == 0){
								/*開始で不具合*/
								callFaileUI(situation);
								alert("出席調査を開始できませんでした．");
							}else{
								/*終了で不具合*/
								callFaileUI(situation);
								alert("出席調査を終了できませんでした．");
							}
						}
					}else{
						alert("履修者に出席調査のコンテンツを提供できません．");
					}
				}
			},error:function(){
				hideLoading();
				alert("タイムアウトしました.");
				$('#callStartBtn').addClass('ui-state-active');
			}
		});
	}
	/*==========================================================
	　 [UI操作] 
	==========================================================*/
	/**
	 * 
	 * 出席調査ボタン操作
	 * 
	 * **/
	//成功
	function callSuccessUI(situation,time){
		if(situation == 0){
			$(".callstarttime").text("出席調査開始時間 : "+time);
			$('#callStartBtn').addClass('ui-disabled');
			$('#callEndBtn').addClass('ui-state-active');
		}else{
			$(".callendtime").text("出席調査終了時間 : "+time);
			$('#callEndBtn').addClass('ui-disabled');
		}
	}
	//失敗
	function callFaileUI(situation){
		if(situation == 0){
			$('#callStartBtn').addClass('ui-state-active');
		}else{
			$('#callEndBtn').addClass('ui-state-active');
		}
	}
});