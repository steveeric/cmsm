<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Page Title</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet"
	href="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.css" />
<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script
	src="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.js"></script>

<script>
	$(document).bind("mobileinit", function() {
		$.mobile.defaultTransition = "flip";
	});
</script>
<script type="text/javascript" charset="utf-8">
		$(document).bind("mobileinit", function(){
			$.mobile.page.prototype.options.addBackBtn = false;
			$.mobile.ajaxEnabled = false;
		});
</script>


<style>
.ul-body-b {
	background: #ccc;
	background-image: -moz-linear-gradient(top, #FFF, #CCC);
	background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #fff),
		color-stop(1, #ccc));
	-ms-filter:
		"progid:DXImageTransform.Microsoft.gradient(startColorStr='#FFF', EndColorStr='#CCC')";
}

.ui-content .h1 {
	color: #5E87B0;
	font-size: 17px;
	text-shadow: 1px 0 0 #FFF;
}

.wordbreak {
	overflow: visible;
	white-space: normal;
}

div#map {
	width: 100%;
	height: 400px;
	border: 4px solid white;
	-webkig-box-sizing: border-box;
	box-sizing: border-box;
}

#loading {
	position: absolute;
	left: 50%;
	top: 20%;
	margin-left: -30px;
}

#footer {
	position: absolute;
	bottom: 0;
	width: 100%;
	height: 100px;
}
</style>
<script>
	$(document).ready(function() {
		showLoading();
		var randomNo=-1;
		var scheduleId=-1;
		var seatInfo;
		var attendInfo;
		var seatId = -1;
		var block = -1;
		var row = -1;
		var column = -1;
		getCTRParam();

		/*画面情報再読み込み*/
	    $("#reloadBtn").click(function(){
	    	moveToFirst();
	    	getCTRParam();
	  	});
		/*出席ボタン*/
	    $("#confirmBtn").click(function(){
		    //alert(seatId+":"+block+":"+row+":"+column);
		    if(block == -1 && row == -1 && column ==-1){
			    /*全てが入力さていない*/
			    alert("「群」・「行」・「列」を選択してください．");
		    }else if(block != -1 && row == -1 && column == -1){
			    /*行と列が入力されていない*/
		    	alert("「行」・「列」が選択されていません．");
		    }else if(block != -1 && row != -1 && column == -1){
			    /*列が入力されていない*/
		    	alert("「列」が選択されていません．");
		    }else{
			    /*全て入力されているので，もう一度seatIdを割だす．*/
		    	var id = getSeatId();
		    	if(id == -1){
		    		alert("入力された座席位置は使用できません．" +block+":群 "+row+"行 "+column+"-列");
		    	}else{
		    		moveToConfirm();
		    	}
		    }
	  	});


	    /*戻るボタン*/
	    $("#returnEnterBtn").click(function(){
	    	$.mobile.changePage(("#entryForm"),{
				type:"POST",
				reverse: false,
				changeHash: false
    		});
	  	});
	  	/*OKボタン*/
	    $("#attendBtn").click(function(){
	    	if ( $(this).hasClass('double_click') ){
				return false;
			}
	    	showAttendRecordLoading();
	    	//notPushAttendBtn();
		    //出席情報をDBに記録する
	    	insertAttendInfo();
	  	});

	  	function notPushAttendBtn(){
	  		/* ボタンの無効化 */
	  		$('#returnEnterBtn').hide();
	  		$('#attendBtn').hide();
	  		//$('#returnEnterBtn').attr('disabled', true);
	    	//$('#attendBtn').attr('disabled', true);
	  	}
	  	function yesPushAttendBtn(){
	  		/* ボタンの有効化 */
	  		/*$('#returnEnterBtn').attr('disabled', false);
	  		$('#returnEnterBtn').removeAttr('disabled');
	  		$('#attendBtn').attr('disabled', false);
	  		$('#attendBtn').removeAttr('disabled');*/
	  		$('#returnEnterBtn').toggle();
	  		$('#attendBtn').toggle();
		}

		function showLoading(){
			$.mobile.loading('show'/*,
					{ text: 'ロード中',
			    textVisible: true,
			    textonly: false}*/);
		}
		function hiddenLoading(){
			$.mobile.loading('hide');
		}

		function showAttendRecordLoading(){
			$.mobile.loading('show',
					{ text: '通信中...',
			    textVisible: true,
			    textonly: false});
		}

	  	/*被ったための座席再入力*/
	    $("#reSelectBtn").click(function(){
	    	moveToFirst();
	    	getCTRParam();
	  	});


		/**ゲットパラメータを取得する**/
	    function getCTRParam() {
	        var url = location.href;
	        parameters    = url.split("?");
			if(parameters[1]==null){
				 var categoryKey = null;
				 alert("URLに不具合があります．もう一度初めからアクセスし直して下さい．");
			}else{
				 params   = parameters[1].split("&");
			        var paramsArray = [];
			        for ( i = 0; i < params.length; i++ ) {
			            neet = params[i].split("=");
			            paramsArray.push(neet[0]);
			            paramsArray[neet[0]] = neet[1];
			        }
			        /*乱数*/
			        randomNo = paramsArray["r"];

			        $.ajaxSetup({ async: true });
			        $.ajax({
			        　　　　　timeout: 6000,
			         url: "../../../../module/php/view/class_time.php",
			         type: "POST",
			         data: { r : randomNo},
			         success: function(json){
			         	var today = json['TODAY_CLASS'];
			         	if(today == 0){
				         	/*授業時間外です画面へ*/
			         		moveToNotTodayClass();
			         	}else{
				         	var t = json['TAKE']['TAKE_COURSE'];
				         	if(t == 0){
					         	/*現在の時間帯，履修科目がない画面へ*/
				         		moveToNotTakeCourse();
				         	}else{
					         	/**/
					         	scheduleId = json['TAKE']['SCHEDULE_ID'];
					         	var during = 1;
					         	distributCTRFistScreen(randomNo,scheduleId,during);
				         	}
			         	}
			         },
			         error: function(){
			        	 alert("接続タイムアウトしました．");
			         }
			        });
			        
			        /*scheduleId*/
			        //scheduleId = paramsArray["s"];
			        /*授業状態 1なら授業中*/
			        //var during = paramsArray["d"];
			        //distributCTRFistScreen(randomNo,scheduleId,during);
					
			}
	 	}
	 	/**画面振り分け**/
	    function distributCTRFistScreen(randomNo,scheduleId,during) {
	    $.ajaxSetup({ async: true });
        $.ajax({
        　　　　　timeout: 6000,
         url: "../../../../module/php/view/ctr/check_call_state.php",
         type: "POST",
         data: { r : randomNo, s : scheduleId},
         success: function(json){
            console.log(json);
         	var startState = json['START']['STATE'];
         	if(startState==1){
         		/*出席調査が開始されています.*/
         		var endState = json['END']['STATE'];
         		if(endState==0){
         			/*出席調査続行中*/
         			/*出席情報があるかをチェック*/
         			/*なければ、座席情報を取得*/
         			var attendeeInfo = json['ATTENDEE'];
         			if(attendeeInfo!=null){
         				/*既に出席している*/
         				/*attendInfo*/
         				var t = json['ATTENDEE']['ATTEND_TIME'];
         				var b = json['ATTENDEE']['SEAT_BLOCK_NAME'];
         				var r = json['ATTENDEE']['SEAT_ROW'];
         				var s = json['ATTENDEE']['SEAT_COLUMN'];
						moveToAttendInfo(t,b,r,s);
         			}else{
             			var room = json['ROOM'];
             			//console.log(room);
         				var roomInfo = json['ROOM']['ROOM_NAME'];
         				var seatBlock =json['ROOM']['SEAT_BLOCK'];
         				/*for(var i in seatBlock){
         					console.log(seatBlock[i].SEAT_BLOCK_NAME+"群");
         					for(var j in seatBlock[i].SEAT){
         						console.log(seatBlock[i].SEAT[j].SEAT_ROW+"行");
         						for(var k in seatBlock[i].SEAT[j].SEAT_INFO){
         							console.log(seatBlock[i].SEAT[j].SEAT_INFO[k].SEST_COLUMN+"列");
         						}
         					}
         				}*/
         				/*まだ出席していない*/
         				$('#entryFormRoomInfo').text("教室名 : "+roomInfo);
         				moveToEntryFrom(seatBlock);
         			}
         		}else{
         			/*出席調査終了している*/
         			/*出席情報を取得*/
         			//なければ、いつ終了したのかを表示する
					moveToEndCTR(json['END']['TIME']);
         		}
         	}else{
         		/*出席調査が開始されていません.*/
         		//alert("出席調査が開始されていません.");
         		moveToNotStartCTR();
         	}
         },
         error: function(){
        	 alert("接続タイムアウトしました．");
         }
        });
	 	}

		/**出席申請内容をDBに記録する**/
	    function insertAttendInfo() {
		    /*DEBUG*/
		   //alert(randomNo+",スケジュール:"+scheduleId+",座席番号:"+seatId);
	    	/*同期通信*/
       		$.ajaxSetup({ async: true });
       		$.ajax({
       　　　　　		//timeout: 6000,
        		url: " ../../../../module/php/view/ctr/insert_attend_info.php",
        		type: "POST",
        		data: { r : randomNo,s: scheduleId,sid: seatId },
        		success: function(json){
        			hiddenLoading();
					var callState = json['CALL']['CALL_STATE'];
					if(callState == 1){
						/*出席申請受付中*/
						var use = json['RESULT']['USED']['USED_STATE'];
            		if(use == 1){
                		/*既に同一授業で誰かに使用されている．*/
                		var id = json['RESULT']['USED']['USES_STUDET_ID'];
            			moveToUsedSeat(id,block,row,column);
            		}else{
        				var state = json['RESULT']['STATE'];
        				//alert("USE"+state);
	        			if(state==0){
    	    				var time = json['RESULT']['ATTEND_TIME'];
        					//出席申請情報を表示する
        					moveToConfirmAttendInfo(time);
        				}else{
        					yesPushAttendBtn();
        					alert("DBに不具合が発生したため，出席申請が正常に行えませんでした．");
        				}
            		}
					}else if(callState == 0){
						/*出席申請停止*/
						var callState = json['CALL']['CALL_END_TIME'];
						moveToEndCTR(callState);
					}else if(callState == -1){
						/*出席申請異常発生*/
						alert("出席状態に不具合が生じました.");
					}else{
						/**/
						alert("不具合が生じました.");
					}
        		},
        		error: function(){
        			hiddenLoading();
        			yesPushAttendBtn();
       	 			alert("接続タイムアウトしました．");
        		}
       		});
	 	}

		/**本日の講義はすべて終了しました画面***/
	 	function moveToNotTodayClass(){
	 		 $.mobile.changePage(("#notTodayClass"),{
					type:"POST",
					reverse: false,
					changeHash: false
	    		});
	 	}

		/**現在履修している授業はありません画面***/
	 	function moveToNotTakeCourse(){
	 		 $.mobile.changePage(("#notTakeCourse"),{
					type:"POST",
					reverse: false,
					changeHash: false
	    		});
	 	}

		/**出席調査がまだ開始してません画面へ遷移する**/
	    function moveToNotStartCTR() {
	       $.mobile.changePage(("#notStartCTR"),{
				type:"POST",
				reverse: false,
				changeHash: false/*,
   				data : { 'att' : att }*/
    		});
		}

		/**出席情報を表示する画面へ遷移する**/
	    function moveToConfirmAttendInfo(time) {
	       $.mobile.changePage(("#attendInfo"),{
				type:"POST",
				reverse: false,
				changeHash: false/*,
   				data : { 'att' : att }*/
    		});
			$('#attendDate').text("申請日時 : "+time);
			//$('#attendSeatBlock').text("群 : "+block);
			$('#attendSeatPosition').text("着席位置 : "+block+" 群 "+row+" 行 - "+column+" 列");
		}

		/**出席情報を表示する画面へ遷移する**/
	    function moveToAttendInfo(time,block,row,column) {
	       $.mobile.changePage(("#attendInfo"),{
				type:"POST",
				reverse: false,
				changeHash: false/*,
   				data : { 'attendInfo' : at }*/
    		});
			$('#attendDate').text("申請日時 : "+time);
			//$('#attendSeatBlock').text("群 : "+block);
			$('#attendSeatPosition').text("着席位置 : "+block+" 群 "+row+" 行 - "+column+" 列");		}
	 	/**出席情報を表示する画面へ遷移する**/
	    function moveToEntryFrom(si) {
		    seatInfo=si;

			//参考URL:http://jsdo.it/linclip/lBAZ

			$("#selectSeatBlockName").append('<option value="-1">群を選択して下さい．</option>');
			$("#selectSeatRow").append('<option value="-1">行を選択して下さい．</option>');
			$("#selectSeatColumn").append('<option value="-1">列を選択して下さい．</option>');
			//for (var i in seatInfo){
			for(var i=0;i<seatInfo.length;i++){
    			$("#selectSeatBlockName").append('<option value="' + seatInfo[i].SEAT_BLOCK_NAME + '">' + seatInfo[i].SEAT_BLOCK_NAME +'</option>');
				$("#selectSeatBlockName").removeAttr("disabled");//触れるように
    		}


    		$("#selectSeatBlockName").change(function(){
        		//alert("CHAGE:selectSeatBlockName");
        		$("#selectSeatRow").empty();
        		//群に追加
        		$("#selectSeatRow").append('<option value="-1">行を選択して下さい．</option>');
        			for (var i in seatInfo){
            			if(seatInfo[i].SEAT_BLOCK_NAME == $(this).val()){
                			block=seatInfo[i].SEAT_BLOCK_NAME;
			    			for (var j in seatInfo[i].SEAT){
 			   					$("#selectSeatRow").append('<option value="' + seatInfo[i].SEAT[j].SEAT_ROW + '">' + seatInfo[i].SEAT[j].SEAT_ROW+'</option>');
    						}
            			}
    				}
    			//行の初期化
        		row=-1;
        		$("#selectSeatRow").selectmenu('refresh', true);
    			//列
    			if($("#selectSeatColumn").val() != -1){
    				$("#selectSeatColumn").empty();
    				$("#selectSeatColumn").append('<option value="-1">列を選択して下さい．</option>');
    				$("#selectSeatColumn").selectmenu('refresh', true);
    				column=-1;
        			$("#selectSeatColumn").selectmenu('refresh', true);
    			}
    		});

    		$("#selectSeatRow").change(function(){
        		$("#selectSeatColumn").empty();
        		$("#selectSeatColumn").append('<option value="-1">列を選択して下さい．</option>');
        		$("#selectSeatColumn").selectmenu('refresh', true);
        		column=-1;
        			for (var i in seatInfo){
            			if(seatInfo[i].SEAT_BLOCK_NAME == $("#selectSeatBlockName").val()){
			    			for (var j in seatInfo[i].SEAT){
				    			if(seatInfo[i].SEAT[j].SEAT_ROW == $(this).val()){
					    			block=seatInfo[i].SEAT_BLOCK_NAME;
					    			row=seatInfo[i].SEAT[j].SEAT_ROW;
					    			for(var k in seatInfo[i].SEAT[j].SEAT_INFO){
					    				$("#selectSeatColumn").append('<option value="' + seatInfo[i].SEAT[j].SEAT_INFO[k].SEAT_COLUMN + '">' + seatInfo[i].SEAT[j].SEAT_INFO[k].SEAT_COLUMN +'</option>');
					    			}
				    			}
    						}
            			}
    				}
    		});

    		$("#selectSeatColumn").change(function(){
        		var sId=-1;
    			for (var i in seatInfo){
        			if(seatInfo[i].SEAT_BLOCK_NAME == $("#selectSeatBlockName").val()){
		    			for (var j in seatInfo[i].SEAT){
			    			if(seatInfo[i].SEAT[j].SEAT_ROW == $("#selectSeatRow").val()){
				    			for(var k in seatInfo[i].SEAT[j].SEAT_INFO){
					    			if(seatInfo[i].SEAT[j].SEAT_INFO[k].SEAT_COLUMN == $("#selectSeatColumn").val()){
						    			seatId = seatInfo[i].SEAT[j].SEAT_INFO[k].SEAT_ID;
						    			sid = seatId;
						    			if(sid == -1){
							    			column=-1;
						    			}else{
						    				block=seatInfo[i].SEAT_BLOCK_NAME;
							    			row=seatInfo[i].SEAT[j].SEAT_ROW;
							    			column=seatInfo[i].SEAT[j].SEAT_INFO[k].SEAT_COLUMN;
						    			}
				    				}
			    				}
							}
        				}
					}
    			}
    		});

    		  $.mobile.changePage(("#entryForm"),{
  				type:"POST",
  				reverse: false,
  				changeHash: false,
  			});
	 	}

	    /**プルダウンに入力された内容からSeatIdを返す**/
	    function getSeatId() {
		    var id = -1;
	    	for (var i in seatInfo){
    			if(seatInfo[i].SEAT_BLOCK_NAME == $("#selectSeatBlockName").val()){
	    			for (var j in seatInfo[i].SEAT){
		    			if(seatInfo[i].SEAT[j].SEAT_ROW == $("#selectSeatRow").val()){
			    			for(var k in seatInfo[i].SEAT[j].SEAT_INFO){
				    			if(seatInfo[i].SEAT[j].SEAT_INFO[k].SEAT_COLUMN == $("#selectSeatColumn").val()){
					    			block=seatInfo[i].SEAT_BLOCK_NAME;
					    			row=seatInfo[i].SEAT[j].SEAT_ROW;
					    			column=seatInfo[i].SEAT[j].SEAT_INFO[k].SEAT_COLUMN;
					    			seatId = seatInfo[i].SEAT[j].SEAT_INFO[k].SEAT_ID;
									id = seatId;
			    				}
		    				}
						}
    				}
				}
			}
			return id;
		}

	    /**ロード画面へ遷移する**/
	    function moveToFirst(){
	       $.mobile.changePage(("#first"),{
				type:"POST",
				reverse: false,
				changeHash: false
    		});
		}

	    /**既に使用されていることを表示する画面へ遷移する**/
	    function moveToUsedSeat(id,block,row,column) {
	       $.mobile.changePage(("#usedSeat"),{
				type:"POST",
				reverse: false,
				changeHash: false
    		});
			$('#usedSeatStudentId').text("学籍番号 : "+id);
			$('#usedSeatBlock').text("群 : "+block);
			$('#usedSeatPosition').text("位　置 : "+row+" 行 - "+column+" 列");
		}

	    /**出席調査終了画面へ遷移する**/
	    function moveToConfirm() {
	       $.mobile.changePage(("#confirm"),{
				type:"POST",
				reverse: false,
				changeHash: false/*,
   				data : { 'att' : att }*/
    		});
			//$('#confirmSeatBlock').text("群 : "+block);
			$('#confirmSeatPosition').text(block+"群 : "+" "+row+" 行 - "+column+" 列");
		}

	 	/**出席調査終了画面へ遷移する**/
	    function moveToEndCTR(endTime) {
	       $.mobile.changePage(("#endCTR"),{
				type:"POST",
				reverse: false,
				changeHash: false/*,
   				data : { 'att' : att }*/
    		});
	       $('#endCTRTime').text(endTime+" に，");
		}
	    //hiddenLoading();
	});
</script>
</head>
<body>
	<div data-role="page" id="first" data-theme="b">
		<div data-role="header">
			<h1>ロード</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>読み込み中です．．．</p>
			<p>しばらくお待ちください．</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="notStartCTR" data-theme="b">
		<div data-role="header">
			<h1>出席申請</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>出席申請がまだ開始されていません．</p>
			<p></p>
			<p></p>
			<p></p>
			<a href="" id="reloadBtn" data-role="button" data-inline="true"
				data-theme="b">再読み込み</a>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="endCTR" data-theme="b">
		<div data-role="header">
			<h1>出席申請終了</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p></p>
			<p id="endCTRTime"></p>
			<p>出席調査は終了しています.</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="entryForm" data-theme="b">
		<div data-role="header">
			<h1>出席申請</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p id="entryFormRoomInfo"></p>
			<p>着席位置を選択して下さい．</p>
			<p></p>
			<hr>
			<p>群</p>
			<select id="selectSeatBlockName" class="design-select-box">
			</select>
			<hr>
			<p>行</p>
			<select id="selectSeatRow" class="design-select-box">
			</select>
			<hr>
			<p>列</p>
			<select id="selectSeatColumn" class="design-select-box">
			</select>
			<hr>
			<p></p>
			<p></p>
			<a href="" id="confirmBtn" data-role="button" data-inline="true"
				data-theme="b"> 出 席 </a>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>


	<div data-role="page" id="confirm" data-theme="b">
		<div data-role="header">
			<h1>入力内容の確認</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>選択した内容に間違いないですか.</p>
			<p></p>
			<p>着席位置</p>
			<p id="confirmSeatPosition"></p>
			<a href="" id="returnEnterBtn" data-role="button" data-inline="true">訂正</a>
			<a href="" id="attendBtn" data-role="button" data-inline="true"
				data-theme="b">OK</a>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="usedSeat" data-theme="b">
		<div data-role="header">
			<h1>使用できません</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>選択された座席はすでに以下の者が使用しています．</p>
			<p id="usedSeatStudentId"></p>
			<p id="usedSeatBlock"></p>
			<p id="usedSeatPosition"></p>
			<p></p>
			<p>座席位置の再入力をお願いします．</p>
			<a href="" id="reSelectBtn" data-role="button" data-inline="true"
				data-theme="b">座席再入力</a>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="attendInfo" data-theme="b">
		<div data-role="header">
			<h1>出席内容確認</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>出席申請が正常に完了しました.</p>
			<p id="attendDate"></p>
			<p id="attendSeatPosition"></p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>


	<div data-role="page" id="prametaError" data-theme="b">
		<div data-role="header">
			<h1>エラー</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>URLに誤りがあります．</p>
			<p>もう一度初めからやりなして下さい．</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>
	<!--content end-->
	<div data-role="footer" style="text-align: center">
		<h4>&copy; 2014 Primary Meta Works</h4>
	</div>

	<div data-role="page" id="notTodayClass" data-theme="b">
		<div data-role="header">
			<h1>授業時間外</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>本日の授業はすべて終了しました．</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="notTakeCourse" data-theme="b">
		<div data-role="header">
			<h1>授業時間外</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>現在の時間帯に履修している科目がありません．</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>
	<!--content end-->
	<div data-role="footer" style="text-align: center">
		<h4>&copy; 2014 Primary Meta Works</h4>
	</div>

	<!-- footer end-->
	</div>
</body>
ロード中...
<!-- [http://coolbodymaker.com/coolbodymaker_m/:title] -->
</html>
