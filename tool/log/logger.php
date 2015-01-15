<?php
#
# Logger
#
class MyLogger
{
    private $log_dir;
    private $log_fname;

    /*public function __construct($baseDir, $fname) {
        $this->log_dir = $baseDir;
        $this->log_fname = $fname;
	}*/
    public function __construct($fname) {
        date_default_timezone_set('Asia/Tokyo');
        $this->log_dir = "Log";
        $this->log_fname = $fname;
    }

    public function chkDir(){
	return "chkDir";
}

    public function Error($msg)
    {
        $log = "Error: ";
        $this->Output($log, $msg);
    }

    protected function Output($str, $msg)
    {
        $date = date("Y-m-d H:i:s");

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
	echo "PATH:".$this->GetFileName();
        error_log($log, 3, $this->GetFileName());
    }
}
?>
