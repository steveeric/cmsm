$(function(){

	//画面サイズ取得
	var screenWidth = window.innerWidth ? window.innerWidth: $(window).width() + 500;
	var screenHeight = window.innerHeight ? window.innerHeight: $(window).height();// + 1000;
	/*var g = new Graphics();
	g.drawLine(100,50,200,70);
	g.paint();*/

	/***
	 * 
	 * 出席調査終了ボタン不具合アリ
	 * 
	 * **/
	//var rootPath = '../../../cmsm/module/php/base/teacher';
	/*ゲットパラメータ取得*/
	var scheduleId = getUrlVars()['s'];
	var attendeeList = null;

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
				var abnormary = json['ABNORMARY_SCHEDULE'];
				if(abnormary == 1){
					/*SCHEDULE_IDに異常アリ*/
					alert("SCHEDULE_IDがおかしい!");
				}else{
					var munualCall = json['MANUAL_CALL'];
					if(munualCall == 0){
						/*出席調査を行わない*/
						var ac = json['ATTENDEE'].length;
						$(".tab2TodayAttendeeCount").text("本日の出席者数 : "+ac+" 人");
						/*出席者情報をリストビューに加える	*/
						attendeeList = json['ATTENDEE'];
						addAtendListView(json['ATTENDEE']);
						/*着席状況を描く*/
						addSitInfo(json['ROOM']);
					}else{
						var a = json['ACTION'];
						if(a==null){
							/*出席終了ボタンを押せなくする*/
							//$('#callEndBtn').addClass('ui-disabled');
							/*出席調査開始ボタンを押せるようにする*/
							$('#callStartBtn').addClass('ui-state-active');
						}else{
							/*出席調査を行う*/
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
							attendeeList = json['ATTENDEE'];
							addAtendListView(json['ATTENDEE']);
							/*着席状況を描く*/
							addSitInfo(json['ROOM']);
						}
					}
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
	 * 0 : 出席調査開始http://localhost/cmsm/view/te/index.php?s=0001022304KK020140002
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
	[出席者一覧の検索window]
	==========================================================*/
	$('#search-basic').live("keypress", function(e){
		if(e.which == 13){
			//var stid = String.fromCharCode(e.which);
			var stid = $('#search-basic').val();
			if(stid.length == 6){
				for(var i in attendeeList){
					if(attendeeList[i].STUDENT_ID == stid.toUpperCase()){
						var d = attendeeList[i].SEAT_BLOCK_NAME+"群 "
						+attendeeList[i].SEAT_ROW+"行 - "
						+attendeeList[i].SEAT_COLUMN+"列 "
						+attendeeList[i].STUDENT_ID +" "
						+attendeeList[i].FULL_NAME;
						alert(d);
					}
				}
			}else{
				alert("入力された学籍番号が正しくありません.");
			}



		}
	})
	/*==========================================================
[座席を描く]
==========================================================*/
	function addSitInfo(at){
		console.log(at);
		var sbrc = at['LAYOUT']['BLOCK_LAYOUT']['SEAT_BLOCK_ROW_COUNT'];
		var sbcc = at['LAYOUT']['BLOCK_LAYOUT']['SEAT_BLOCK_COLUMN_COUNT'];
		//alert(sbrc+":"+sbcc);
		var blockMarginWidth = 15;
		var blockMarginHeight = 0;
		//一つのブロックサイズを算出する
		var seatBlockViewWidth = screenWidth / sbcc ;
		var seatBlockViewHeight = screenHeight / sbrc;
		var barMargin = 45;
		
		var beforeBlockId = 0;
		var top = barMargin;		
		var left = 0;
		var blockRowChangeCount = 0;
		var psbr = 0;
		var psbc = 0;
		//alert(seatBlockViewWidth+":"+seatBlockViewHeight);
		for(var i in at['LAYOUT']['SEAT_LAYOUT']){
			var nowSeatBlockId = at['LAYOUT']['SEAT_LAYOUT'][i].SEAT_BLOCK_ID;
			var nowSeatBlockInSeatColumnMaxCount = at['LAYOUT']['SEAT_LAYOUT'][i].SEAT_COLUMN_COUNT;
			var nowSeatBlockInSeatMaxRow = at['LAYOUT']['SEAT_LAYOUT'][i].SEAT_ROW_COUNT;
			var seatSizeWidth = seatBlockViewWidth / nowSeatBlockInSeatColumnMaxCount;
			var seatSizeHeight = seatBlockViewHeight / nowSeatBlockInSeatMaxRow;
			//alert(seatSizeWidth+";"+seatSizeHeight);
			//alert(seatSizeWidth+":"+seatSizeHeight);
			//console.log(nowSeatBlockId+":"+nowSeatBlockInSeatRow+":"+nowSeatBlockInSeatColumnCount);
			//var pastSeatRow=0;
			//var pastSeatColumn=0;
			for(var j in at['DETAILE_INFO']){
				if(at['DETAILE_INFO'][j].BLOCK.SEAT_BLOCK_ID == nowSeatBlockId){
					var nowSeatBlockName = at['DETAILE_INFO'][j].BLOCK.SEAT_BLOCK_NAME;
					var nowBlockSeatRow = at['DETAILE_INFO'][j].BLOCK.SEAT_BLOCK_ROW;
					var nowBlockSeatColumn = at['DETAILE_INFO'][j].BLOCK.SEAT_BLOCK_COLUMN;

					//シートブロックを描く
					if(psbr != nowBlockSeatRow){
						//top = barMargin+((nowBlockSeatRow-1)*seatBlockViewHeight);
						top = (barMargin + screenHeight) - seatBlockViewHeight*(nowBlockSeatRow);
						//alert("screenHeight:"+screenHeight+",TOP:"+top+",seatBlockViewHeight:"+seatBlockViewHeight+"NOW_BLOCK_SEAT_ROW:"+nowBlockSeatRow);
						psbr = nowBlockSeatRow;
					}
					if(psbc != nowBlockSeatColumn){
						left = blockMarginHeight+(nowBlockSeatColumn-1)*seatBlockViewWidth;
						if(nowBlockSeatColumn > 1 /*&& nowBlockSeatColumn < nowSeatBlockInSeatColumnMaxCount*/){
							left = left + blockMarginWidth/3;
							//alert("after"+left);
						}

						psbc = nowBlockSeatColumn;
					}
					//alert(nowSeatBlockId+"  "+nowSeatBlockName+"  TOP:"+top+",LEFT:"+left);
					//$("#seatViewid").append("<div style='position: absolute; top: "+(top)+"px; left:"+left+"px; width:"+(seatBlockViewWidth)+"px; height:"+seatBlockViewHeight+"px; background-color:#00DDFF;'>"+nowSeatBlockName+" "+nowBlockSeatRow+"行-"+nowBlockSeatColumn+"列"+"</div>");

					//シートブロックを描く
					//出席者塊をオブジェクトを出す
					for(var k in at['DETAILE_INFO'][j].BLOCK.SEAT){
						//console.log(nowSeatBlockName+":"+at['DETAILE_INFO'][j].BLOCK.SEAT[k].SEAT_ROW+"-"+at['DETAILE_INFO'][j].BLOCK.SEAT[k].SEAT_COLUMN);
						//	top = (barMargin + screenHeight) - seatBlockViewHeight*(nowBlockSeatRow);
						var nowSeatRow = at['DETAILE_INFO'][j].BLOCK.SEAT[k].SEAT_ROW;
						var nowSeatColumn = at['DETAILE_INFO'][j].BLOCK.SEAT[k].SEAT_COLUMN;
						var seatTop = top +seatBlockViewHeight - seatSizeHeight*nowSeatRow;
						var seatLeft = left + (nowSeatColumn-1)*seatSizeWidth;
						/*if(at['DETAILE_INFO'][j].BLOCK.SEAT_BLOCK_ID == 3 && nowSeatRow == 1){
							console.log(nowSeatBlockName+" "+nowSeatRow+"行-"+nowSeatColumn+"列");
						}*/

						//$("#seatViewid").append("<div style='position: absolute; top: "+seatTop+"px; left:"+seatLeft+"px; width:"+(seatSizeWidth)+"px; height:"+seatSizeHeight+"px; background-color:#00DDFF;'>"+nowSeatBlockName+" "+nowSeatRow+"行-"+nowSeatColumn+"列"+"</div>");

						if(at['DETAILE_INFO'][j].BLOCK.SEAT[k].ATTENDEE != null){
							//console.log(at['DETAILE_INFO'][j].BLOCK.SEAT[k].SEAT_ID+" "+at['DETAILE_INFO'][j].BLOCK.SEAT[k].SEAT_ROW+"-"+at['DETAILE_INFO'][j].BLOCK.SEAT[k].SEAT_COLUMN+":"+at['DETAILE_INFO'][j].BLOCK.SEAT[k].ATTENDEE.STUDENT_ID);
							$("#seatViewid").append("<div style='position: absolute; top: "+seatTop+"px; left:"+seatLeft+"px; width:"+(seatSizeWidth)+"px; height:"+seatSizeHeight+"px; background-color:#00DDFF;'>"+""+at['DETAILE_INFO'][j].BLOCK.SEAT[k].ATTENDEE.STUDENT_ID+"<BR>"+"<font size='1'>"+at['DETAILE_INFO'][j].BLOCK.SEAT[k].ATTENDEE.FULL_NAME+"</font></BR>"+"</div>");
							//$("#seatViewid").append("<div style='position: absolute; top: "+seatTop+"px; left:"+seatLeft+"px; width:"+(seatSizeWidth)+"px; height:"+seatSizeHeight+"px; background-color:#00DDFF;'>"+""+at['DETAILE_INFO'][j].BLOCK.SEAT[k].ATTENDEE.STUDENT_ID+"<BR>"+at['DETAILE_INFO'][j].BLOCK.SEAT[k].ATTENDEE.FULL_NAME+"</BR>"+"</div>");
						}else{
							$("#seatViewid").append("<div style='position: absolute; top: "+seatTop+"px; left:"+seatLeft+"px; width:"+(seatSizeWidth)+"px; height:"+seatSizeHeight+"px; background-color:#A0A0A0;'></div>");
						}
					}
				}
			}
		}
	}
});