<?php
	/*
	* ・DBアクセスクラス
	* ・時間クラス
	* を取り込む.
	* @parm $con (DBアクセスクラス)
	* @parm $time (時間クラス)
	*/
	//include_once(dirname(__FILE__).'../../../base/db/db_access.php');
	//DBアクセスのコンフィグをインクルード
	include_once(dirname(__FILE__).'../../../../../tool/db/DBObject.php');
	//DBアクセス用のmysqlをインクルード
	include_once(dirname(__FILE__).'../../../../../tool/db/mysql.php');
	//マトリックス画面状態保持クラスをインクルード
	include_once(dirname(__FILE__).'../MatrixScreen.php');
	//名札クラスをインクルード
	include_once(dirname(__FILE__).'../NamePlate.php');
   //入力情報クラスをインクルード
   include_once(dirname(__FILE__).'../InputValue.php');

   //学生情報クラスをインクルード
   include_once(dirname(__FILE__).'../../common/Student.php');
   //出席情報クラスをインクルード
   include_once(dirname(__FILE__).'../../common/Attendee.php');
	/*
	* 作成日 : 2015年1月15日
	* Matrixクラスは、Matrixモジュールです.
	*
	*
	*
	*/
	class Matrix{
      //締め切りました
      public $MODE_CLOSED = 1;
      //参加できませんモード
      public $MODE_CAN_NOT_JOIN = 2;
      //名札モード
      public $MODE_NAME_PLATE = 3;
      //入力モード
		public $MODE_INPUT_VALUE = 4;
		//画面状態
		private $screenState;
		//DBコネクション
		private $con;
		//授業ID
		private $scheduleId;
		//乱数
		private $randomNo;
      //
      private $student;

 		/**
		* コンストラクタ
 		**/
 		function __construct() {
 			//初期処理
 			$this -> init();
   		}

   		/**
   		* createdate : 2015年1月15日
   		* setItemメソッド
   		* このクラスで使用するアイテムをセットする.
   		* @parm scheduleId 授業個々のID
   		* @parm randomNo 学生一人一人に割り振られている乱数
   		**/
   		public function setItem($scheduleId,$randomNo){
   			$this -> scheduleId = $scheduleId;
   			$this -> randomNo = $randomNo;
   		}
   		/**
   		* createdate : 2015年1月15日
   		* getScreenStateメソッド
   		* 現在の状態のスクリーン状態をMatrixScreenクラスで返す.
   		**/
   		public function getScreenState(){
   			//終了フラグ
   			$endFlag = $this -> checkEndMatixContent();
   			//
   			$this -> screenState -> setEndFlag($endFlag);

            //学生情報をセットする(出席情報とマトリックス情報もセットされています)
            $this -> student = checkTodayAttendanceState();

            if($endFlag == 1){
               //行っていない.
               $this -> screenState -> setScreenNumber($this -> MODE_CLOSED);
               if(!(is_null($this -> student))){
                  //でも出席していて
                  $resultMatrix = $this -> student -> getAttendee() -> getResultInputMatrix();
                  if($resultMatrix > 0){
                     //かつRESULT_INPUT_MATRIXが0よりおおきい
                     //つまり、マトリックスの入力コンテンツに参加をしたいた.
                     $this -> screenState -> setScreenNumber($this -> MODE_NAME_PLATE);
                  }
               }
            }else{
               //まだマトリックスを実施中...
               if(is_null($this -> student)){
                  //本日一度も出席確認が取れていないので参加できません.
                  //参加できません.
                  $this -> screenState -> setScreenNumber($this -> MODE_CAN_NOT_JOIN);
               }else{
                  //本日一度出席確認はとれていますよ!
                  $resultMatrix = $this -> student -> getAttendee() -> getResultInputMatrix();
                  if($resultMatrix > 0){
                     //正解か不正解している.
                     $this -> screenState -> setScreenNumber($this -> MODE_NAME_PLATE);
                  }else{
                     //入力画面に行く
                     //何を入力するかのマトリックス情報を取得する必要がある.

                     ここからやってね!
                  }
               }
            }

            //正しく入力できている or 入力上限回数を超えた.
            //名札クラスをセットした場合
            //$this -> screenState -> setScreenContent(new NamePlate("J07011","伊藤翔太",1));
   			//終了していた場合は,
   			//正解していたのかを探す.

   			//終了していない場合は、
   			//いろいろ処理を開始する.





   			//画面状態クラスを返す.
   			return $this -> screenState;
   		}


    	/**## private ゾーン ###***/

		/**
   		* createdate : 2015年1月15日
   		* initメソッド
   		* 初期動作
   		**/
   		private function init(){
   			//マトリックススクリーンクラスのインスタンスを生成
   			$this -> screenState = new MatrixScreen();
   			//コネクションセット
   			$this -> con = $this -> getDBConnection();
   		}

    	/**
   		* createdate : 2015年1月15日
   		* getDBConnectionメソッド
		* DBコネクションを生成する.
   		**/
   		private function getDBConnection(){
   			$o = new DBObject();
			$h = $o -> getHost();
			$u = $o -> getUser();
			$p = $o -> getPass();
			$db = $o -> getDBName();
			return new DB($h,$u,$p,$db);
   		}
    	/**
   		* createdate : 2015年1月15日
   		* checkEndMatixContentメソッド
   		* 現在まだマトリックスコンテンツを開催中かを返します.
   		* @return 終了フラグ
   		* 0:開催中
		* 1:終了している
   		*
   		**/
   		public function checkEndMatixContent(){
   			$endFlag = 0;
   			$sql = "SELECT `START_MATRIX_DATE_TIME`,`END_MATRIX_DATE_TIME`
   					FROM `SYLLABUS_MST` WHERE `SCHEDULE_ID` LIKE '".$this -> scheduleId."' ";
   			$result = $this -> con -> query($sql);
   			$startTime = $result[0]["START_MATRIX_DATE_TIME"];
   			$endTime = $result[0]["END_MATRIX_DATE_TIME"];

   			if( (!is_null($endTime)) ){
   				$endFlag = 1;
   			}
   			return $endFlag;
   		}
         /**
         * createdate : 2015年1月15日
         * checkTodayAttendanceStateメソッド
         * 本日の出席状態をチェックします.
         * @return 学生クラスを返します(出席情報も付けて)
         *
         **/
         public function checkTodayAttendanceState(){
            $sql = "SELECT A.ATTEND_ID,ST.STUDENT_ID,ST.FULL_NAME,
                     A.`MATRIX_LOG_ID`,A.`RESULT_INPUT_MATRIX`,
                     A.`REQUEST_COUNT_JUDGEMENT_MATRIX`
                     FROM `REGISTER_MST`R,ATTENDEE A,STUDENT_MST ST
                     WHERE R.`RANDOM_NO` LIKE '".$this -> randomNo."'
                     AND R.STUDENT_ID = A.STUDENT_ID
                     AND A.SCHEDULE_ID LIKE '".$this -> scheduleId."'
                     AND R.STUDENT_ID = ST.STUDENT_ID ";

            //学生
            $student = null;

            $result = $this -> con -> query($sql);


            if(count($result)>0){
               //出席者を確認できた
               $studentId = $result[0]["STUDENT_ID"];
               $fullName = $result[0]["FULL_NAME"];

               //出席関係
               $attenddeId = $result[0]["ATTEND_ID"];
               $matrixLogId = $result[0]["MATRIX_LOG_ID"];
               $resultInputMatrix = $result[0]["RESULT_INPUT_MATRIX"];
               $requestcountJudgementMatrix = $result[0]["REQUEST_COUNT_JUDGEMENT_MATRIX"];
               //出席情報
               $att = new Attendee($attenddeId);
               //マトリックス情報
               $att -> setMatrixInfo($matrixLogId,$resultInputMatrix,$requestcountJudgementMatrix);
               //学生
               $student = new Student($studentId);
               //氏名セット
               $student -> setFullName($fullName);
               //出席情報セット
               $student -> setAttendee($att);
            }

            return $student;
         }
       /**
         * createdate : 2015年1月15日
         * checkTodayAttendanceStateメソッド
         * 本日の出席状態をチェックします.
         * @return 学生クラスを返します(出席情報も付けて)
         *
         **/
         private function aa(){

         }
	}
?>