$(document).ready( function(){
	/*ロード画面を消す*/
    hideLoading();
    /*ボタンリリース*/
    releaseBtnSendMatrixValue();
    /*入力FORM初期化*/
    initFromEnterMatrixValue();
});


/*入力画面*/

/*OKボタンの処理*/
$(document).delegate('#BTN-SEND-MATRIX-VALUE', "click", function () {
	/*入力すべき桁数*/
	var digit = $("#DIG").val();
	/*出席ID*/
	var attendeId = $("#ATT").val();
	/*出席ID*/
	var matrixLogId = $("#MAT").val();
	/*入力値を取得*/
	var inputValue = $("#FORM_ENTER_MATRIX_VALUE").val();
	if(inputValue.length == 0){
		/*値が入力されていません.*/
		alert("値を入力して下さい.");
	}else{
		/*すべて数字化をチェックする.*/
		var flag = 0;
		for(i = 0; i < inputValue.length; i++){
			var val = inputValue.charAt(i);
			if(val.match(/[^0-9]+/)){
            	/*数字以外が含まれていた*/
				flag = 1;
            }
		}

		if(flag == 1){
			alert("半角数字以外の文字が入力されています.");
		}else{
			//2回処理をさせないようにするためにボタンを無効にします.
			btn = document.getElementById("BTN-SEND-MATRIX-VALUE");
			btn.disabled = "disabled";
			//ロード画面を出します.
			showLoading("照合中です...");
			//入力された値が正しいかをチェックする
			checkInputMatrixValue(attendeId,matrixLogId,inputValue);
		}
	}
});

/*入力された値が正しいかをチェックする*/
function checkInputMatrixValue(attendeeId,matrixLogId,inputValue){
	$.ajaxSetup({ async: false });
	$.ajax({
      timeout: 30000,
      url: "../../../../module/php/view/matrix/receives_input_value.php",
      data: {att:attendeeId, mat:matrixLogId, inp:inputValue},
      type: "POST",
          success: function(json){
			  endChekingInputMatrixValue();
              //リロード
              location.reload();
          },error:function(){
			  endChekingInputMatrixValue();
              alert("タイムアウトしました.");
          }
    });
}

/**入力照合処理終了後**/
function endChekingInputMatrixValue(){
	hideLoading();
	releaseBtnSendMatrixValue();
	//initFromEnterMatrixValue();
}

/*入力フォームを初期化する*/
function initFromEnterMatrixValue(){
	document.getElementById('FORM_ENTER_MATRIX_VALUE').value= '';
}

function releaseBtnSendMatrixValue(){
	/*入力画面のOKボタンを押せるようにする*/
    btn = document.getElementById("BTN-SEND-MATRIX-VALUE");
    btn.disabled = "";
}

/**ロード画面開始**/
function showLoading(str){
    $.mobile.loading('show',{text:str,textVisible:true,textonly:false});
}
 /**ロード画面終了**/
function hideLoading(){
    $.mobile.loading('hide');
}
