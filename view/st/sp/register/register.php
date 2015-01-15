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

<script>
$(document).ready(function() {
	var key;
	if(key == null){
		key = getParam();
	}
	if(key!=null){
		if(key.length == 20){
			checkRegist(key);
		}else{
			/*パラメータがおかしい．*/
			/*時間のある時に変更を*/
			moveParametaError();
		}
	}else{
		/*パラメータが全くない*/
		moveParametaError();
	}

	$("#reloadBtn").click(function(){
		checkRegist(key);
	});
	
	$("#nextBtn").click(function(){
        var MAX_SIZE=6
    	 var enterId = $("#studentIdTx").val();
    	 if(enterId == ""){
        	//alert('空文字');
        	$(".judge").text("値が入力されていません．");
        	//$(".judge").css("display", "");
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
    		 	checkStudentExistenceInDB(text);
    		 }
       	 }
    });

    /*戻るボタン*/
    $("#returnEnterBtn").click(function(){
    	 $.mobile.changePage(("#register"),{
 			type:"POST",
 			reverse: false,
 			changeHash: false,
 		});
  	});

    /*登録ボタン*/
    $("#registBtn").click(function(){
    	updateRegist(key,id);
  	});

    function getParam() {
        var url   = location.href;
        parameters    = url.split("?");
		if(parameters[1]==null){
			 var categoryKey = key;
		}else{
			 params   = parameters[1].split("&");
		        var paramsArray = [];
		        for ( i = 0; i < params.length; i++ ) {
		            neet = params[i].split("=");
		            paramsArray.push(neet[0]);
		            paramsArray[neet[0]] = neet[1];
		        }
		        var categoryKey = paramsArray["r"];
		}
        return categoryKey;
    }

    /*REGISTER_MSTにGETパラメータの乱数が存在するかのチェックと誰がそれを使用しているかのチェックを行う*/
  	function checkRegist(key){
  		//console.log('checkRegist key:'+key);
        /*同期通信*/
        $.ajaxSetup({ async: true });
        $.ajax({
        　　　　　timeout: 3000,
         url: "../../../../module/php/view/register/check_regist.php",
         type: "POST",
         data: { r : key },
         success: function(arr){
         	parm = arr['PARAMETA'];
        	if(parm == 1){
        		alert("このURLは使用できません．");
        	}else{
            	if(arr['REGISTER']['REGISTER_STATE']==0){
                	/*誰にも登録でしようされていない*/
                	/*乱数を自分にPOSTしてフォーム画面へ促す*/
            		moveForm(key);
            	}else{
                	/*登録されている*/
            		movePastRegister(arr);
            	}
        	}
         },
         error: function(){
        	 alert("DBに不具合が発生しました．");
         }
        });
  	  }

  	 /*REGISTER_MSTにあるパラメータと学籍番号を紐づける*/
  	function updateRegist(randomNo,studentId){
  		console.log('updateRegist');
        /*同期通信*/
        $.ajaxSetup({ async: true });
        $.ajax({
        　　　　　timeout: 3000,
         url: "../../../module/php/view/regist.php",
         type: "POST",
         data: { r : randomNo,studentId:studentId },
         success: function(json){
			console.log(json);
			state = json['STATE'];
			if(state==1){
				/**/
				 alert("DBの不具合を生じ紐付けが正常に行えませんでした．");
			}else{
				 moveRegistContent();
			}
         },
         error: function(){
        	 alert("DBの不具合を生じました．");
         }
        });
  	  }
    
    function checkStudentExistenceInDB(parameta){
        console.log('checkStudentExistenceInDB:'+parameta);
        var state = -1;
        /*同期通信*/
        $.ajaxSetup({ async: true });
        $.ajax({
        　　　　　timeout: 2000,
         url: "../../../module/php/view/check_student_existence_in_db.php",
         type: "POST",
         data: { studentId : parameta },
         success: function(arr){
        	//state = arr;
    		state = arr['STATE'].toString();
    		console.log("ITEM"+state);
    		if(state==0){
    			moveConfirm(arr);
    			id = arr['STUDENT_ID'].toString();
    			name = arr['FULL_NAME'].toString();
    			$('#confirmStudentId').text("学籍番号 : "+id);
    			$('#confirmStudentName').text("氏　　名 : "+name);
    		}else{
    			 alert("入力された「"+parameta+"」は登録されていません．教員に申し出て下さい．");
    		}
         },
         error: function(){
        	 alert("DBの不具合のため，学籍番号のチェックができません．");
         }
        });
       // return arr;
    }

    /*確認画面へ遷移*/
    function moveForm(parameta){
    	 $.mobile.changePage(("#register"),{
    			type:"POST",
    			reverse: false,
    			changeHash: false,
    			data : { 'r' : key }
    	});
    }  
    
    /*確認画面へ遷移*/
    function moveConfirm(parameta){
    	//id = item['STUDENT_ID'].toString();
		//name = item['FULL_NAME'].toString();
    	 $.mobile.changePage(("#confirm"),{
    			type:"POST",
    			reverse: false,
    			changeHash: false,
    			data : { 'r' : key }
    	});
    }  
    /*登録完了画面へ遷移*/
    function moveRegistContent(){
    	 $.mobile.changePage(("#registContent"),{
    			type:"POST",
    			reverse: false,
    			changeHash: false
    	});
    } 
    /*登録内容確認へ遷移*/
    function movePastRegister(json){
    	 $.mobile.changePage(("#pastRegister"),{
    			type:"POST",
    			reverse: false,
    			changeHash: false
    	});
    	var id = json['REGISTER']['REGISTER_INFO']['STUDENT_ID'];
    	var time = json['REGISTER']['REGISTER_INFO']['REGISTER_TIME'];
    	$('#pastRegisteStudentId').text("学籍番号 : "+id);
    	$('#pastRegisterTime').text("登録日 : "+time);
    }
    /*エラー画面へ遷移*/
    function moveParametaError(){
    	 $.mobile.changePage(("#prametaError"),{
 			type:"POST",
			reverse: false,
			changeHash: false
		});
    } 
});

</script>
</head>
<body>
	<div data-role="page" id="first" data-theme="b">
		<div data-role="header">
			<h1>ロード</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>画面情報を取得中です．．．</p>
			<p>しばらくお待ちください．</p>
			<p></p>
			<p></p>
			<p>5秒待っても画面が</p>
			<p>切り替わらない場合は，</p>
			<p>再読み込みをして下さい．</p>
			<a href="" id="reloadBtn" data-role="button" data-inline="true"
				data-theme="b">再読み込み</a>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="register" data-theme="b">
		<div data-role="header">
			<h1>登録</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>学籍番号を入力して下さい．</p>
			<p>例 : J07011</p>
			<p>
				<input type="text" id="studentIdTx" 　maxlength="6"
					style="ime-mode: disabled">
			</p>
			<p class="judge"></p>
			<p>
				<input type="button" id="nextBtn" value="次へ" />
			</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="confirm" data-theme="b">
		<div data-role="header">
			<h1>登録内容の確認</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>以下の内容で間違いありませんか．</p>
			<p id="confirmStudentId"></p>
			<p id="confirmStudentName"></p>
			<a href="" id="returnEnterBtn" data-role="button" data-inline="true">戻る</a>
			<a href="" id="registBtn" data-role="button" data-inline="true"
				data-theme="b">登録</a>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="registContent" data-theme="b">
		<div data-role="header">
			<h1>登録完了</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>登録が正常に完了しました．</p>
			<p>次回からは，登録でしようしたURLを使用しますので</p>
			<p>忘れないようにブックマークに登録しておいて下さい．</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="pastRegister" data-theme="b">
		<div data-role="header">
			<h1>登録内容確認</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>以下の学生が登録しています．</p>
			<p id="pastRegisteStudentId"></p>
			<p id="pastRegisterTime"></p>
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
	<!-- footer end-->
	</div>
</body>
ロード中...
<!-- [http://coolbodymaker.com/coolbodymaker_m/:title] -->
</html>
