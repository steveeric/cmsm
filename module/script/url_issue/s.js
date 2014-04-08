$(document).ready(function() {
	var url = null;
	var id = null;
	var name = null;
	$("#completeBtn").click(function(){
		showLoading();
		issueURL(id);
		//completeURLInfoView();
	});
	$("#returnEnterBtn").click(function(){
		firstView();
	});

	$("#okBtn").click(function(){
		var MAX_SIZE=6
		var enterId = $("#studentIdTx").val();
		var year = $("#year").val();
		var month = $("#month").val();
		var day = $("#day").val();
		if(enterId == ""){
			$(".judge").text("学籍番号が入力されていません．");
		}else if(enterId.length != MAX_SIZE){
			$(".judge").text("学籍番号が正しく入力されていません．");
		}else{
			var text = $("#studentIdTx").val();
			var flag = 0;
			for(i = 0; i < text.length; i++) {
				if(flag == 0){
					var val = text.charAt(i)
					if(i==0){
						if(val.match(/[^A-Za-z]+/)){
							flag = 1;
						}
					}else{
						if(val.match(/[^0-9]+/)){
							flag = 1;
						}
					}
				}
			}
			if(flag == 1){
				$(".judge").text("学籍番号が正しく入力されていません．");
			}else{
				//issueURL(text);
				showLoading();
				//$("#okBtn").hide;
				//issueURL(text);
				checkStudentInDB(text,year,month,day);
			}
		}
	});

	/**URL発行中画面に飛ぶ**/
	function doChangeIssueURLView(){
		$.mobile.changePage(("#issue"),{
			type:"POST",
			reverse: false,
			changeHash: false,
		});
	}

	/**URLを発行中ロード**/
	function showLoading(){
		$.mobile.loading('show',{text:'照合中...',textVisible:true,textonly:false});
	}
	function hideLoading(){
		$.mobile.loading('hide');
	}

	function firstView(){
		$.mobile.changePage(("#first"),{
			type:"POST",
			reverse: false,
			changeHash: false,
		});
	}

	/**学籍番号を確認している画面へ**/
	function confirmStudentInfoView(i,f){
		id = i;
		$.mobile.changePage(("#confirm"),{
			type:"POST",
			reverse: false,
			changeHash: false,
		});
		$(".confirmStudentIdFiled").text("学籍番号 : "+i);
		$(".confirmStudentNameFiled").text("氏  名 : "+f);
	}

	/**URLを表示する**/
	function completeURLInfoView(u){
		url = u;
		$.mobile.changePage(("#complete"),{
			type:"POST",
			reverse: false,
			changeHash: false,
		});
		$("#url").attr("href",url);
		$(".completeURLFiled").text(url);
	}

        /*DB上に登録されている学籍番号かをチェックする*/
	function checkStudentInDB(studentId,year,month,day){
		$(".judge").text(" ");
		$.ajaxSetup({ async: true });
		$.ajax({
			timeout: 6000,
			url: "../../cmsm/module/php/url_issue/check_student.php",
			type: "POST",
			data: { id : studentId, y : year, m : month, d : day,},
			success: function(json){
				hideLoading();
				var paa = json['REGISTER'];
				if(paa == 0){
					/*DB上に登録されている学籍番号*/
					/*id = json['STUDENT']['STUDENT_ID'];
					name = json['STUDENT']['FULL_NAME'];*/
					var i = json['STUDENT']['STUDENT_ID'];
					var f = json['STUDENT']['FULL_NAME'];
					confirmStudentInfoView(i,f);
				}else{
					/*DB上に登録されていない学籍番号*/
					$(".judge").text("学籍番号["+studentId+"] 生年月日["+year+"年"+month+"月"+day+"日"+"]は、"+"登録されていません.");
				$(".judge").css("color","red");
				}
			},error:function(){
				hideLoading();
				alert("タイムアウトしました.");
			}
		});
	}	

	/**URL発行してもらう**/
	function issueURL(studentId){/*同期通信*/
		$.ajaxSetup({ async: true });
		$.ajax({
			timeout: 5000,
			url: "../../cmsm/module/php/url_issue/issue_url.php",
			type: "POST",
			data: { id : studentId },
			success: function(json){
				/**{"PARAMETA":0,"PROCESS_REULT":{"PROCESS_RESULT":0,"ERROR":null},
				 * "ITEM":{"URL":"http:\/\/192.168.53.72\/cmsm\/i\/f.php?r=12345678912345678912",
				 * "STUDENT_ID":"J07012"}}**/
				/*POSTパラメータが存在したか*/
				var parameta = json['PARAMETA'];
				var pr = json['PROCESS_RESULT']['PROCESS_RESULT'];
				if(pr == 0){
					/*正常*/
					var past = json['PAST_REGIST'];
					var u = json['ITEM']['URL'];
					if(past == 0){
						/*初回登録*/
						//confirmStudentInfoView(studentId,fullName,url);
						completeURLInfoView(u);
					}else{
						/*過去に既に登録している*/
						completeURLInfoView(u);
					}
				}else{
					hideLoading();
					/*異常*/
					alert("正常に登録を行えませんでした.管理者に申し出て下さい.");
				}
			},error:function(){
					hideLoading();
				$(".okBtn").show;
				alert("タイムアウトしました.");
			}
		});
	}
});
