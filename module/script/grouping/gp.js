$(document).ready(function() {
	var randomNo;
	var scheduleId;

	showOnlyLoading();
	getGPParam();


	/*==========================================================
	[関数]
	==========================================================*/

	/**ゲットパラメータを取得する**/
	function getGPParam() {
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
			scheduleId = paramsArray["s"];

			$.ajaxSetup({ async: true });
			$.ajax({
				timeout: 6000,
				url: "../../../../../cmsm/module/php/view/gp/json_sp.php",
				type: "POST",
				data: { r : randomNo, s : scheduleId},
				success: function(json){
					hideLoading();
					var endState = json['END_CLASS_STATE']['END_CLASS_ROOM'];
					if(endState == 1){
						/*授業終了*/
						var subject = json['END_CLASS_STATE']['SUBJECT_NAME'];
						var date = json['END_CLASS_STATE']['DATE'];
						moveToEndClass(subject,date);
					}else{
						var not = json['NOT_SEAT'];
						if(not==1){
							/*座席が割り当てれなかった*/
							moveToNotSeat();
						}else{
							/*座席が正常に割り当てられた*/
							var patt = json['PAST']['PAST_ATTEND'];
							var stid = json['STUDENT_ID'];
							var gname = json['POSITION']['GROUP_NAME'];
							var b = json['POSITION']['SEAT_BLOCK_NAME'];
							var r = json['POSITION']['SEAT_ROW'];
							var c = json['POSITION']['SEAT_COLUMN'];
							if(patt == 1){
								/*過去の情報確認画面へ*/
								moveToGroup(gname,b,r,c);
							}else{
								/*着席位置表示画面へ*/
								moveTotodayGroup(stid,gname,b,r,c);
							}
						}
					}
				},
				error: function(){
					alert("接続タイムアウトしました．");
				}
			});
		}
	}

	/*==========================================================
	[ボタン効果]
	==========================================================*/
	/*画面情報再読み込み*/
	$("#reloadBtn").click(function(){
		moveToFirst();
		getGPParam();
	});

	/*==========================================================
    [画面遷移]
    ==========================================================*/
	/**ロード画面へ遷移する**/
	function moveToFirst(){
		$.mobile.changePage(("#first"),{
			type:"POST",
			reverse: false,
			changeHash: false
		});
	}
	/**座席不足を知らせる画面へ飛ぶ**/
	function moveToNotSeat(){
		$.mobile.changePage(("#notSeat"),{
			type:"POST",
			reverse: false,
			changeHash: false
		});
	}
	/**本日の着席位置を表示する画面へ飛ぶ**/
	function moveTotodayGroup(id,g,b,r,c){
		$(".idFieald").text(id+"さんは,");
		$(".groupFieald").text(g+"グループです.");
		$(".positionFieald").text(b+"群 "+r+"行 - "+c+"列");
		$.mobile.changePage(("#todayGroup"),{
			type:"POST",
			reverse: false,
			changeHash: false
		});
	}
	/**着席位置確認画面へ**/
	function moveToGroup(g,b,r,c){
		$(".groupPastFieald").text(g+"グループの一員です.");
		$(".positionPastFieald").text(b+"群 "+r+"行 - "+c+"列");
		$.mobile.changePage(("#group"),{
			type:"POST",
			reverse: false,
			changeHash: false
		});
	}

    /**授業終了知らせる画面へ飛ぶ**/
    function moveToEndClass($subjectName,$endData){
    	$(".endClassDate").text($endData+"の");
    	$(".endClassSubjectName").text($subjectName+"は,");
       $.mobile.changePage(("#endClass"),{
			type:"POST",
			reverse: false,
			changeHash: false
		});
	}
	/*==========================================================
	　 [ロード] 
	==========================================================*/
	function showPrcessLoading(ms){
		$.mobile.loading('show',{text:ms,textVisible:true,textonly:false});
	}
	function showOnlyLoading(){
		$.mobile.loading('show');
	}
	function showLoading(){
		$.mobile.loading('show',{text:'処理中...',textVisible:true,textonly:false});
	}
	function hideLoading(){
		$.mobile.loading('hide');
	}

	/*==========================================================
    [座席を描く]
    ==========================================================*/
});
