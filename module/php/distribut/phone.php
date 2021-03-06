<?php

/**
 *
 * スマホとガラケーを振り分けるクラス
 *
 * コンストラクタ : ユーザーエージェント
 * ***/

class DistributPhone{
	/*ユーザーエージェント*/
	private $userAgent;
	function __construct($u) {
		$this -> userAgent = $u;
	}

	/**
	 * スマホ : 1
	 * ガラケー : 0
	 * をそれぞれ返す
	 * **/
	public function distributPhoneKey(){
		$key = -1;			//端末情報キーを返す
		$ua = $this -> userAgent;  //ユーザーエージェント
		if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false) || (strpos($ua, 'blackberry') !== false) || (strpos($ua, 'Windows Phone') !== false)) {
			// スマートフォンからアクセスされた場合
			$key = 1;
		} elseif ((strpos($ua, 'Android') !== false) || (strpos($ua, 'iPad') !== false)) {
			// タブレットからアクセスされた場合
			$key = 1;
		} elseif ((strpos($ua, 'DoCoMo') !== false) || (strpos($ua, 'KDDI') !== false) || (strpos($ua, 'SoftBank') !== false) || (strpos($ua, 'Vodafone') !== false) || (strpos($ua, 'J-PHONE') !== false)) {
			// 携帯からアクセスされた場合
			$key = 0;
		} else {
			// その他（PC）からアクセスされた場合
			$key = 1;
		}
		return $key;
	}
}

?>
