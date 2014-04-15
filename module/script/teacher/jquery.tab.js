$(function(){
	/***
	 * 
	 * 出席調査終了ボタン不具合アリ
	 * 
	 * **/
	//var rootPath = '../../../cmsm/module/php/base/teacher';
	/*ゲットパラメータ取得*/
	var scheduleId = getUrlVars()['s'];
	nowActionState();
	$('.tabbox:first').show();
	$('#tab li:first').addClass('active');
	$('#tab li').click(function() {
		$('#tab li').removeClass('active');
		$(this).addClass('active');
		$('.tabbox').hide();
		$($(this).find('a').attr('href')).fadeIn();
		return false;
	});

	/*==========================================================
	　 [状態確認処理](毎アクセス時) 
	==========================================================*/
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

	//状況取得
	function nowActionState(){
		showPrcessLoading('画面情報取得中...');
		doCheckNowActionState();
	}

	/*==========================================================
	　 [ロード] 
	==========================================================*/
	function showPrcessLoading(ms){
		$.mobile.loading('show',{text:ms,textVisible:true,textonly:false});
	}
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
	function doCheckNowActionState(){
		$.ajaxSetup({ async: true });
		$.ajax({
			timeout: 6000,
			url: '../../../cmsm/module/php/base/teacher/now_action_state.php',
			type: "POST",
			data: { s : scheduleId},
			success: function(json){
				hideLoading();
				var a = json['ACTION'];
				if(a==null){
					/*出席終了ボタンを押せなくする*/
					//$('#callEndBtn').addClass('ui-disabled');
					/*出席調査開始ボタンを押せるようにする*/
					$('#callStartBtn').addClass('ui-state-active');
				}else{
					var st = json['ACTION']['CALL']['START_TIME'];
					var et = json['ACTION']['CALL']['END_TIME'];
					/*出席者数を表示*/
					var ac = json['ATTENDEE'].length;
					$(".attendCount").text("出席 : "+ac+" 人");
					if(et==null){
						/*出席調査中*/
						callStartSuccessUI(st);
					}else{
						/*出席申請終了している*/
						callEndSuccessUI(st,et);
					}
					/*出席者情報をリストビューに加える	*/
					addAtendListView(json['ATTENDEE']);
					/*着席状況を描く*/
					addSitInfo(json['ROOM']);
				}
			},error:function(){
				hideLoading();
				alert("タイムアウトしました.");
			}
		});
	}
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
	//出席調査開始成功
	function callStartSuccessUI(time){
		$(".callstarttime").text("出席調査開始時間 : "+time);
		$(".callendtime").text("出席調査終了時間 : 出席調査を終了していません.");
		$('#callStartBtn').addClass('ui-disabled');
		$('#callEndBtn').addClass('ui-state-active');
	}
	//出席調査終了成功
	function callEndSuccessUI(st,et){
		$(".callstarttime").text("出席調査開始時間 : "+st);
		$(".callendtime").text("出席調査終了時間 : "+et);
		$('#callStartBtn').addClass('ui-disabled');
		$('#callEndBtn').addClass('ui-disabled');
	}
	//失敗
	function callFaileUI(situation){
		if(situation == 0){
			$('#callStartBtn').addClass('ui-state-active');
		}else{
			$('#callEndBtn').addClass('ui-state-active');
		}
	}

	/*==========================================================
	　 [出席者のリストビュー]
	==========================================================*/
	function addAtendListView(at){
		for(var i in at){  
			//$("#tableviewid").append("<dl>");
			//$("#tableviewid").append("<dt>B</dt>");
			$("#tableviewid").append("<dd>"+at[i].SEAT_BLOCK_NAME+"群 "
					+at[i].SEAT_ROW+"行 - "
					+at[i].SEAT_COLUMN+"列 "
					+at[i].STUDENT_ID +" "
					+at[i].FULL_NAME +" "
					+at[i].ATTEND_TIME +"</dd>");
			//$("#tableviewid").append("</dl>");
		}
		/*$("#jslistview_ul").empty();
		for(var i in at){
			$("#jslistview_ul").append("<li>"+at[i].SEAT_BLOCK_NAME+"群 "
					+at[i].SEAT_ROW+"行 - "
					+at[i].SEAT_COLUMN+"列 "
					+at[i].STUDENT_ID +" "
					+at[i].FULL_NAME +" "
					+at[i].ATTEND_TIME +"</li>").listview("refresh");
		}*/
		/*<dl>
            <dt>A</dt>
            <dd>AC/DC</dd>
            <dd>Aphex Twin</dd>
            <dd>Asian Dub Foundation</dd>
        </dl>
        <dl>

            <dd>The Beatles</dd>
            <dd>Bill Evans &amp; Jim Hall</dd>
            <dd>The Blues Brothers</dd>
            <dd>Bob Dylan</dd>
            <dd>Bruse Springsteen</dd>
        </dl>
        <dl>
            <dt>C</dt>
            <dd>Carole King</dd>
            <dd>Char</dd>
            <dd>Coldplay</dd>
            <dd>Cream</dd>
            <dd>Crosby, Stills, Nash &amp; Young</dd>
        </dl>
         etc ...*/
	}
	/*==========================================================
[座席を描く]
==========================================================*/
	function addSitInfo(at){
		var sbrc = at['LAYOUT']['BLOCK_LAYOUT']['SEAT_BLOCK_ROW_COUNT'];
		var sbcc = at['LAYOUT']['BLOCK_LAYOUT']['SEAT_BLOCK_COLUMN_COUNT'];
		var bb = at['DETAILE_INFO'];
		for(var i=sbrc;i>0;i--){
			$("#sit-content").append("<tr>");
			for(var j=1;j<=sbcc;j++){
				for(var k in bb){ 
					if(bb[k].BLOCK.SEAT_BLOCK_ROW == i && bb[k].BLOCK.SEAT_BLOCK_COLUMN == j){
						/*SEAT_BLOCK_IDから*/
						/*var sc = at['LAYOUT']['SEAT_LAYOUT'];
						for(var l in sc){
							if(sc[l].SEAT_BLOCK_ID == bb[k].BLOCK.SEAT_BLOCK_ID){
								//console.log(bb[k].BLOCK.SEAT_BLOCK_NAME+":"+sc[l].SEAT_ROW_COUNT+"-"+sc[l].SEAT_COLUMN_COUNT);
								//$("#sit-content").append("<td>"+bb[k].BLOCK.SEAT_BLOCK_NAME+"</td>");
								var se = bb[k].BLOCK.SEAT;

								for(var e in se){
									$("#sit-content").append("<tr>");
									for(var r=1;r<=sc[l].SEAT_ROW_COUNT;r++){
										for(var c=1;c<=sc[l].SEAT_COLUMN_COUNT;c++){
											if(se[e].SEAT_ROW == r && se[e].SEAT_COLUMN==c){
												$("#sit-content").append("<td>"+se[e].SEAT_ID+":"+se[e].SEAT_ROW+"-"+se[e].SEAT_COLUMN+"</td>");
											}
										}
									}
									$("#sit-content").append("</tr>");
								}
							}
						}
					}*/
						$("#sit-content").append("<td>111</td>");
					}
				}
				$("#sit-content").append("</tr>");
			}
		}
	}
});