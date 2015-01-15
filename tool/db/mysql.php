<?php
// include('../log/logger.php');

/**
 * MySQLの接続の為のクラス
 * Class for connecting MySQL
 */
class DB {
	/*
	 * コンストラクタ @var host:ホスト @var user:ユーザー @var pass:パス @var db:データーベース名
	 */
	private $host;
	private $user;
	private $pass;
	private $db;
	private $dsn;
	private $log;
	function __construct($host, $user, $pass, $db) {
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
		$this->dsn = "mysql:dbname=$db;$host=$host";
	}

	/**
	 * 現在の時刻から現在のTIMETABLE_IDを取得する
	 *
	 * **
	 */
	public function getNowTimeTableId($time) {
		$sql = "SELECT `TIMETABLE_ID`, `TIMETABLE_NAME`, `CLASS_START_TIME`, `CLASS_END_TIME` FROM `TIMETABLE_MST` WHERE CLASS_END_TIME >= '" . $time . "' ORDER BY `TIMETABLE_MST`.`CLASS_START_TIME` ASC LIMIT 1 ";
		return $this->query ( $sql );
	}

	/**
	 * 現在の日時からSCHEDULE_IDを返す
	 *
	 * *
	 */
	public function getNowScheduleId($teacherId, $year, $month, $day, $timeTableId) {
		$sql = "SELECT SCHEDULE_ID FROM `SYLLABUS_MST` WHERE `YEAR` = '" . $year . "' AND `MONTH` LIKE '" . $month . "' AND `DAY` LIKE '" . $day . "'  AND `TIMETABLE_ID` LIKE '" . $timeTableId . "' ";
		return $this->query ( $sql );
	}

	/**
	 * DB上に登録されている学籍番号かを調べる
	 * **
	 */
	public function checkExistenceStudentId($studentId) {
		// $sql = "SELECT STUDENT_ID,FULL_NAME FROM `STUDENT_MST` WHERE STUDENT_ID = '".$studentId."' ";
		$sql = "SELECT R.RANDOM_NO, R.STUDENT_ID, S.FULL_NAME, R.REGISTER_TIME
				FROM REGISTER_MST R, STUDENT_MST S
				WHERE R.STUDENT_ID = S.STUDENT_ID AND R.STUDENT_ID = '" . $studentId . "'";
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$stmt->execute ();
			$data = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			return $data;
		} catch ( PDOException $e ) {
			// echo 'Connection failed:'.$e->getMessage();
			errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}

	/**
	 * DB上に登録されている学籍番号かを調べる
	 * **
	 */
	public function checkExistenceBirthDayAndStudentId($studentId, $byear, $bmonth, $bday) {
		// $sql = "SELECT STUDENT_ID,FULL_NAME FROM `STUDENT_MST` WHERE STUDENT_ID = '".$studentId."' ";
		$sql = "SELECT R.RANDOM_NO,S.STUDENT_ID, S.FULL_NAME, R.REGISTER_TIME
				FROM `STUDENT_MST`S,REGISTER_MST R
				WHERE S.STUDENT_ID = R.STUDENT_ID
				AND S.`STUDENT_ID` LIKE '" . $studentId . "'
						AND S.`BIRTH_YEAR` LIKE '" . $byear . "'
								AND S.`BIRTH_MONTH` LIKE '" . $bmonth . "'
										AND S.`BIRTH_DAY` LIKE '" . $bday . "' ";
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$stmt->execute ();
			$data = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			return $data;
		} catch ( PDOException $e ) {
			// echo 'Connection failed:'.$e->getMessage();
			errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}

	/**
	 * 過去に登録したことがある学生かを調べる
	 * **
	 */
	public function checkRegisterStudentId($studentId) {
		// $sql = "SELECT RANDOM_NO,STUDENT_ID,REGISTER_TIME FROM REGISTER_MST WHERE STUDENT_ID = '".$studentId."' ";
		$sql = "SELECT R.RANDOM_NO, R.STUDENT_ID, S.FULL_NAME, R.REGISTER_TIME
				FROM REGISTER_MST R, STUDENT_MST S
				WHERE R.STUDENT_ID = S.STUDENT_ID AND R.STUDENT_ID = '" . $studentId . "'";
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$stmt->execute ();
			$data = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			return $data;
		} catch ( PDOException $e ) {
			// echo 'Connection failed:'.$e->getMessage();
			errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}

	/**
	 * 乱数を元にして学生情報を取得する
	 *
	 * **
	 */
	public function checkExistenceRegisterInfo($randomNo) {
		// $sql = "SELECT RANDOM_NO,STUDENT_ID,REGISTER_TIME FROM REGISTER_MST WHERE STUDENT_ID = '".$studentId."' ";
		$sql = "SELECT R.RANDOM_NO, R.STUDENT_ID, S.FULL_NAME, R.REGISTER_TIME
				FROM REGISTER_MST R, STUDENT_MST S
				WHERE R.STUDENT_ID = S.STUDENT_ID AND R.RANDOM_NO = '" . $randomNo . "'";
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$stmt->execute ();
			$data = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			return $data;
		} catch ( PDOException $e ) {
			// echo 'Connection failed:'.$e->getMessage();
			errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}

	/**
	 * 乱数を元に学籍番号を返す
	 *
	 * *
	 */
	public function getStudentId($randomNo) {
		$sql = "SELECT STUDENT_ID FROM REGISTER_MST WHERE RANDOM_NO = '" . $randomNo . "'";
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$stmt->execute ();
			$data = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			return $data;
		} catch ( PDOException $e ) {
			// echo 'Connection failed:'.$e->getMessage();
			errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}

	/**
	 * DBにとうろくされているURLを取得する
	 * **
	 */
	public function getURL() {
		$sql = "SELECT URL FROM `URL_MST` WHERE 1 ";
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					// カラム型に合わない値がINSERTされようとしたときSQLエラーとする
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					// SQLエラー発生時にPDOExceptionをスローさせる
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					// プリペアドステートメントのエミュレーションを無効化する
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$stmt->execute ();
			$data = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			return $data;
		} catch ( PDOException $e ) {
			// echo 'Connection failed:'.$e->getMessage();
			errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}

	/**
	 * 紐付けを行った時間を記録する
	 * *
	 */
	public function recordRegisterTime($randomNo, $time) {
		$sql = "UPDATE `REGISTER_MST` SET `REGISTER_TIME` = '" . $time . "' WHERE `RANDOM_NO` = '" . $randomNo . "'";
		return $this->execute ( $sql );
	}
	public function insertMobileScreen($randomNo, $time) {
		$sql = "INSERT INTO `MOBILE_SCREEN` (`RANDOM_NO`, `NOW_SCREEN_CONTENT_ID`, `SCHEDULE_ID`, `LAST_ACCESS_TIME`) VALUES ('" . $randomNo . "', 'register', '0', '" . $time . "')";
		return $this->execute ( $sql );
	}

	/**
	 * DB上のMOBILE_SCREENの内容を乱数を元に取得してくる
	 */
	public function getNowScreenContent($randomNo) {
		$sql = "SELECT NOW_SCREEN_CONTENT_ID,SCHEDULE_ID FROM `MOBILE_SCREEN` WHERE `RANDOM_NO` LIKE '" . $randomNo . "'";
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$stmt->execute ();
			$data = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			return $data;
		} catch ( PDOException $e ) {
			// echo 'Connection failed:'.$e->getMessage();
			errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}

	/**
	 * DB上のMOBILE_SCREENの内容を乱数を元に取得してくる
	 */
	public function getNowScreenContentDetaile($nowTime, $randomNo) {
		/* LAST_ACCESS_TIMEを更新 */
		$insert = "UPDATE `MOBILE_SCREEN` SET `LAST_ACCESS_TIME` = '" . $nowTime . "' WHERE `RANDOM_NO` = '" . $randomNo . "'";
		$result = $this->execute ( $insert );
		/* 更新できなければアップデート */
		if (! ($result)) {
			errorLog ( $insert, "Don'T UPDATE_ACCESS_TIME" );
		}
		/* 画面コンテンツを取得 */
		$sql = "SELECT M.NOW_SCREEN_CONTENT_ID,M.SCHEDULE_ID,R.REGISTER_TIME
				FROM `MOBILE_SCREEN`M,REGISTER_MST R
				WHERE M.RANDOM_NO = R.RANDOM_NO AND R.`RANDOM_NO` LIKE '" . $randomNo . "'";
		return $this->query ( $sql );
	}

	/**
	 * SCHEDULE_IDと学籍番号を元に出席しているかを割り出す．
	 *
	 * **
	 */
	public function getAttendInfo($scheduleId, $studentId) {
		$sql = "SELECT A.ATTEND_TIME,ST.STUDENT_ID,ST.FULL_NAME,B.SEAT_BLOCK_NAME,S.SEAT_ROW,S.SEAT_COLUMN
				FROM `ATTENDEE` A,STUDENT_MST ST,SEAT_MST S,SEAT_BLOCK_MST B
				WHERE A.STUDENT_ID = ST.STUDENT_ID
				AND S.SEAT_ID = A.SEAT_ID
				AND S.SEAT_BLOCK_ID = B.SEAT_BLOCK_ID
				AND A.`SCHEDULE_ID` LIKE '" . $scheduleId . "'
						AND A.`STUDENT_ID` LIKE '" . $studentId . "' ";
		return $this->query ( $sql );
	}

	/**
	 * 乱数がDBに登録されているかをチェックする関数
	 * 引数 : URLにくっついているr=(20)桁
	 * 返り値 : DBに登録されている情報
	 * *
	 */
	public function checkRnadomNoInDB($randomNo) {
		$sql = "SELECT RANDOM_NO,STUDENT_ID,REGISTER_TIME FROM `REGISTER_MST` WHERE RANDOM_NO='" . $randomNo . "'";
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$stmt->execute ();
			$data = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			return $data;
		} catch ( PDOException $e ) {
			// echo 'Connection failed:'.$e->getMessage();
			errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}

	/**
	 * 現在DBに設定されている画面情報を取得してくる
	 *
	 * **
	 */
	public function checkNowScreenStateInfo($randomNo) {
		$sql = "SELECT NOW_SCREEN_CONTENT_ID,SCHEDULE_ID FROM `MOBILE_SCREEN` WHERE `RANDOM_NO` LIKE '" . $randomNo . "' ";
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$stmt->execute ();
			$data = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			return $data;
		} catch ( PDOException $e ) {
			// echo 'Connection failed:'.$e->getMessage();
			errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}

	/**
	 * DBのREGISTER_MSTに乱数と紐づけた日時を返す．
	 *
	 * *
	 */
	public function getRegistrationTime($randomNo) {
		$sql = "SELECT R.REGISTER_TIME FROM REGISTER_MST R,STUDENT_MST S WHERE R.STUDENT_ID = S.STUDENT_ID AND R.RANDOM_NO = '" . $randomNo . "' ";
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$stmt->execute ();
			$data = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			return $data;
		} catch ( PDOException $e ) {
			// echo 'Connection failed:'.$e->getMessage();
			errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}
	public function query($sql) {
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$stmt->execute ();
			$data = $stmt->fetchAll ( PDO::FETCH_ASSOC );
			return $data;
		} catch ( PDOException $e ) {
			// echo 'Connection failed:'.$e->getMessage();
			errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}
	/**
	 * JSONで返す**
	 */
	public function jsonQuery($sql) {
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$stmt->execute ();
			$rows = array ();
			while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
				$rows [] = $row;
			}
			return $rows;
		} catch ( PDOException $e ) {
			echo 'Connection failed:' . $e->getMessage ();
			// errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}

	// 配列でない結果を得るとき
	/*
	 * public function queryItem(){ try{ $pdo = new PDO ($this->dsn, $this->user, $this->pass, array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'")); $stmt = $pdo->prepare($sql); $stmt->execute(); $data = $stmt->fetchAll(PDO::FETCH_ASSOC); return $data; } catch(PDOException $e) { echo 'Connection failed:'.$e->getMessage(); exit(); } }
	 */
	public function execute($sql) {
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					// カラム型に合わない値がINSERTされようとしたときSQLエラーとする
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='TRADITIONAL'",
					// SQLエラー発生時にPDOExceptionをスローさせる
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					// プリペアドステートメントのエミュレーションを無効化する
					PDO::ATTR_EMULATE_PREPARES => false,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'"
			) );
			$stmt = $pdo->prepare ( $sql );
			$flag = $stmt->execute ();
			$data = $pdo->lastInsertId ();
			return $flag;
		} catch ( PDOException $e ) {
			// echo 'Connection failed:'.$e->getMessage();
			errorLog ( $sql, $e->getMessage () );
			exit ();
		}
	}

	/**
	 * グルーピングや座席指定ﾃﾞ使用する座席使用情報を初期化する**
	 */
	public function initSeatChangeUsing($roomId, $contentId, $attendeeId, $scheduleId, $studentId, $attTime, $randomNo) {
		$data = null;
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_AUTOCOMMIT => true,
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
					PDO::ATTR_EMULATE_PREPARES => false
			) );
			/* 初期化するのでその旨を記録 */
			$initSQL = "INSERT INTO `LAST_USE_CHANGING` (`SCHEDULE_ID` ,`ROOM_ID` ,`SCREEN_CONTENT_ID` ,`RANDOM_NO` ,`ACCESS_TIME`) VALUES ('" . $scheduleId . "', '" . $roomId . "', '" . $contentId . "', '" . $randomNo . "', '" . $attTime . "')";
			$res = $this->execute ( $initSQL );
			$callSQL = "INSERT INTO `CALL_THE_ROLL` (`SCHEDULE_ID` ,`CALL_START_TIME` ,`CALL_END_TIME` ) VALUES ('" . $scheduleId . "', '" . $attTime . "', NULL)";
			$res = $this->execute ( $callSQL );
			if ($res) {
				try {
					$pdo->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					// トランザクション処理を開始
					$pdo->beginTransaction ();
					/* 座席の使用状況を初期化 */
					$sql = "UPDATE `SEAT_CHANGE_MST` SET `USING` = '0' WHERE `ROOM_ID` = ? AND `SCREEN_CONTENT_ID` = ? ";
					$sth = $pdo->prepare ( $sql );
					$sth->bindValue ( 1, $roomId, PDO::PARAM_STR );
					$sth->bindValue ( 2, $contentId, PDO::PARAM_STR );
					$sth->execute ();
					// $initSeatChangeSQL = "UPDATE `SEAT_CHANGE_MST` SET `USING` = '0' WHERE `ROOM_ID` = '" . $roomId . "' AND `SCREEN_CONTENT_ID` = '" . $contentId . "' ";
					// $initResult = $this->execute ( $initSeatChangeSQL );

					$checkSQL = "SELECT RANDOM_NO FROM `LAST_USE_CHANGING` WHERE `SCHEDULE_ID` LIKE '" . $scheduleId . "'";
					$checkResult = $this->query ( $checkSQL );
					$selectRandom = $checkResult [0] ['RANDOM_NO'];
					if (strcasecmp ( $randomNo, $selectRandom ) == 0) {
						// コミット
						$pdo->commit ();
					} else {
						// ロールバック
						$pdo->rollBack ();
					}
					/* 自分の着座位置を取得 */
					/*$selSeatSQL = "SELECT SC.SEAT_ID, SC.GROUP_NAME, SB.SEAT_BLOCK_NAME, SE.SEAT_ROW, SE.SEAT_COLUMN
					FROM `SEAT_CHANGE_MST` SC, SEAT_MST SE, SEAT_BLOCK_MST SB
					WHERE SC.SEAT_ID = SE.SEAT_ID
					AND SE.SEAT_BLOCK_ID = SB.SEAT_BLOCK_ID
					AND SC.`USING` = 0
					AND SC.ROOM_ID = '" . $roomId . "'
					AND SC.SCREEN_CONTENT_ID = '" . $contentId . "'
					ORDER BY SC.`SELECTION_ORDER` ASC
					LIMIT 1 FOR UPDATE";

					$data = $this->query ( $selSeatSQL );
					$seatId = $data [0] ['SEAT_ID'];

					$preAttSQL = "INSERT INTO `ATTENDEE` (`ATTEND_ID` ,`SCHEDULE_ID` ,`STUDENT_ID` ,`SEAT_ID` ,`ATTEND_TIME`)VALUES ('" . $attendeeId . "', '" . $scheduleId . "', '" . $studentId . "', '" . $seatId . "', '" . $attTime . "')";
					$stmt = $pdo->prepare ( $preAttSQL );
					$stmt->execute ();

					$usingSeatSQL = "UPDATE `SEAT_CHANGE_MST` SET `USING` = '1' WHERE `ROOM_ID` = '" . $roomId . "' AND `SCREEN_CONTENT_ID` = '" . $contentId . "' AND `SEAT_ID` = '" . $seatId . "'";
					$stmt3 = $pdo->prepare ( $usingSeatSQL );
					$stmt3->execute ();*/

				} catch ( PDOException $e ) {
					$pdo->rollBack ();
					throw $e;
				}
			} else {
				/* LAST_USE_CHANGEINGにinsertできなかった. */
			}
		} catch ( PDOException $e ) {
			echo 'Connection failed:' . $e->getMessage ();
			// errorLog ( $sql, $e->getMessage () );
			exit ();
		}
		$pdo = null;
		/* 座席を割り当てる */
		$data = $this->assignmentSeat ( $roomId, $contentId, $attendeeId, $scheduleId, $studentId, $attTime );
		return $data;
	}
	/**
	 * 座席の割り振りを行う
	 * 同一授業に先に出席したものがいた場合
	 * *
	 */
	public function assignmentSeat($roomId, $contentId, $attendeeId, $scheduleId, $studentId, $attTime) {
		try {
			$pdo = new PDO ( $this->dsn, $this->user, $this->pass, array (
					PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_AUTOCOMMIT => true,
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
					PDO::ATTR_EMULATE_PREPARES => false
			) );

			try {
				$pdo->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
				// トランザクションを開始する。オートコミットがオフになる
				$pdo->beginTransaction ();

				$seatId = 0;
				/* 使用できる座席を抽出する */
				$selSeatSQL = "SELECT SC.SEAT_ID, SC.GROUP_NAME, SB.SEAT_BLOCK_NAME, SE.SEAT_ROW, SE.SEAT_COLUMN
					FROM `SEAT_CHANGE_MST` SC, SEAT_MST SE, SEAT_BLOCK_MST SB
					WHERE SC.SEAT_ID = SE.SEAT_ID
					AND SE.SEAT_BLOCK_ID = SB.SEAT_BLOCK_ID
					AND SC.`USING` = 0
					AND SC.ROOM_ID = '" . $roomId . "'
					AND SC.SCREEN_CONTENT_ID = '" . $contentId . "'
                  ORDER BY SC.`SELECTION_ORDER` ASC
					LIMIT 1 FOR UPDATE";

				if ($scheduleId == '0001032804C1020140001') {
					// 2014年04月28日の月曜3限用です.
					// ESL配布のためのプログラムになります.
					//一度でも休んだことがある人は､最後尾から詰める仕様になっています.

					//SUBJECT_IDを取得
					$subSQL = "SELECT SUBJECT_ID FROM `SYLLABUS_MST` WHERE `SCHEDULE_ID` LIKE '".$scheduleId."'";
					$subData = $this->query ( $subSQL );
					$subId = $subData[0]['SUBJECT_ID'];
					$abSQL = "SELECT ABSENT_ID FROM `ABSENTEE` AB, SYLLABUS_MST SY, SUBJECT_MST SU
							WHERE AB.SCHEDULE_ID = SY.SCHEDULE_ID
							AND SU.SUBJECT_ID = SY.SUBJECT_ID
							AND SU.SUBJECT_ID = '" . $subId . "'
							AND AB.STUDENT_ID = '" . $studentId . "'";
					$abData = $this->query ( $abSQL );
					if (count ( $abData ) > 0) {
						// 一回でも休んだことがある.
						$selSeatSQL = "SELECT SC.SEAT_ID, SC.GROUP_NAME, SB.SEAT_BLOCK_NAME, SE.SEAT_ROW, SE.SEAT_COLUMN
										FROM `SEAT_CHANGE_MST` SC, SEAT_MST SE, SEAT_BLOCK_MST SB
										WHERE SC.SEAT_ID = SE.SEAT_ID
										AND SE.SEAT_BLOCK_ID = SB.SEAT_BLOCK_ID
										AND SC.`USING` = 0
										AND SC.ROOM_ID = '".$roomId."'
										AND SC.SCREEN_CONTENT_ID = '".$contentId."'
										ORDER BY SC.`SELECTION_ORDER` DESC
										LIMIT 1 FOR UPDATE";
						$data = $this->query ( $selSeatSQL );
						$seatId = $data [0] ['SEAT_ID'];
					} else {
						//一回も休んだことがない
						$data = $this->query ( $selSeatSQL );
						$seatId = $data [0] ['SEAT_ID'];
					}
				} else {
					$data = $this->query ( $selSeatSQL );
					$seatId = $data [0] ['SEAT_ID'];
				}

				/* 出席 */
				$preAttSQL = "INSERT INTO `ATTENDEE` (`ATTEND_ID` ,`SCHEDULE_ID` ,`STUDENT_ID` ,`SEAT_ID` ,`ATTEND_TIME`)VALUES ('" . $attendeeId . "', '" . $scheduleId . "', '" . $studentId . "', '" . $seatId . "', '" . $attTime . "')";
				$stmt = $pdo->prepare ( $preAttSQL );
				$stmt->execute ();

				/* 座席を使用状態に */
				$usingSeatSQL = "UPDATE `SEAT_CHANGE_MST` SET `USING` = '1' WHERE `ROOM_ID` = '" . $roomId . "' AND `SCREEN_CONTENT_ID` = '" . $contentId . "' AND `SEAT_ID` = '" . $seatId . "'";
				$stmt3 = $pdo->prepare ( $usingSeatSQL );
				$stmt3->execute ();

				// 変更をコミットする
				$pdo->commit ();
			} catch ( PDOException $e ) {
				// 変更をロールバックする
				$pdo->rollBack ();
				echo 'ERROR:' . $e->getMessage ();
			}
		} catch ( PDOException $e ) {
			echo 'Connection failed:' . $e->getMessage ();
			// errorLog ( $sql, $e->getMessage () );
			exit ();
		}
		return $data;
	}

	/* REGISTER_MSTに学籍番号が登録されていなかった場合 */
	public function aa() {
		$sql = "UPDATE REGISTER_MST R,(SELECT RANDOM_NO FROM REGISTER_MST WHERE TENTATIVE_RESERVATION IS NULL ORDER BY rand() LIMIT 1 ) X SET R.TENTATIVE_RESERVATION='2' WHERE R.RANDOM_NO = X.RANDOM_NO ";
	}

	/* クエリーエラーを出力 */
	function errorLog($sql, $e) {
		echo $sql . ":" . $e;
		/*
		 * require_once ('../../tool/log/logger.php'); $log = new MyLogger ( "ERROR_QUERY.txt" ); echo $log->chkDir (); $log->Error ( $e . "SQL:" . $sql );
		 */
	}
}
?>
