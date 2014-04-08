<?php
#
# Logger
#
class MyLogger
{
    private $log_dir;
    private $log_fname;

    /*public function __construct($baseDir, $fname) {
        date_default_timezone_set('Asia/Tokyo');
        $this->log_dir = $baseDir;
        $this->log_fname = $fname;
	}*/

    public function __construct() {
      /*日付けを日本に設定*/
      date_default_timezone_set('Asia/Tokyo');
      $this->log_dir = "log";
      $this->log_fname = "test_log.log";
    }

    public function chkDir(){
      return $this->log_dir;
    }

    public function Error($msg)
    {
        $log = "Error: ";
        $this->Output($log, $msg);
    }
    
    public function ErrorQuery($msg)
    {
    	$log = "QUERY_ERROR: ";
    	$this->Output($log, $msg);
    }

    public function Info($msg)
    {
        $log = "Info: ";
        $this->Output($log, $msg);
    }

    protected function GetFileName()
    {
        $today = date("Ymd");
        return $this->log_dir."/".$today."_".$this->log_fname;
    }

    protected function Output($str, $msg)
    {
        echo $date = date("Y-m-d H:i:s");

        if (is_array($msg))
        {
            $log = "[".$date."]".$str.PHP_EOL;
            $log .= "Array(".PHP_EOL;
            foreach ($msg as $key => $value) {
                $log .= $key." - ".$value.PHP_EOL;
            }
            $log .= ")".PHP_EOL;
        }
        else
        {
            $log = "[".$date."]".$str.$msg.PHP_EOL;
        }
	echo "FILE_NAME:".$this->GetFileName();
        error_log($log, 3, $this->GetFileName());
    }
}
?>
