<?php

class Services_IndexLog {

    private $s_start_time;
    private $s_end_time;
    private $s_log_dir;

    /**
     * 程序初始化时将已有日志文件与数据库对比(仅通过文件名比较),数据库无保存则写入数据库
     */
    public function indexLog() {
        $this->s_start_time = date('Ymd-hs');

        $o_loadfile = new Services_LoadFile();
        $o_database = new Services_Database();
        $o_auditlog = new Services_AuditLog();
        $a_config = $o_loadfile->getConfig();
        $s_log_dir = $a_config['auditlog_dir'];
        $this->s_log_dir = $s_log_dir;
        file_put_contents(FILE_TMP, "");

        $s_index = `ls -lR $s_log_dir|grep "^-"`;
        preg_match_all('(\d+-\d+-.*)',$s_index,$a_file_name);
        foreach ($a_file_name[0] as $s_item) {
            $a_item = explode('-',$s_item);
            $b_item = $o_database->checkVal($a_item[2]);
            if (true!==$b_item) {
                $s_file = `find $s_log_dir -name $s_item`;
                $o_loadfile->_initModsecAudit($s_file);
                $s_auditlog = $o_loadfile->getAuditLog();
                $o_auditlog->translate($s_auditlog);
                $a_data = $o_auditlog->getData();
                $o_database->add($a_data);
                file_put_contents(FILE_TMP,$s_item."\r\n",FILE_APPEND);
            } else {
                file_put_contents(FILE_TMP,$s_item."\r\n",FILE_APPEND);
            }
        }
        $this->s_end_time = date('Ymd-hi');
    }

    /**
     *持续监测按时间生成的目录,扫描并且添加至数据库
     */
    public function addLog() {

        $o_loadfile = new Services_LoadFile();
        $o_database = new Services_Database();
        $o_auditlog = new Services_AuditLog();
        $s_day_dir = date('Ymd');
        $s_minute_dir = date('Ymd-hi');
//        if ($this->s_end_time===$s_minute_dir) {
        $s_file_path = $this->s_log_dir.'/'.$s_day_dir.'/'.$s_minute_dir;
//            $s_file_path = $this->s_log_dir.'/'.$s_day_dir.'/'.$s_day_dir;
        $a_file_name = explode("\n",`ls $s_file_path`);
        $a_file_name = array_filter($a_file_name);
        //改为使用缓存
        foreach ($a_file_name as $s_item) {
            $s_file_tmp = file_get_contents(FILE_TMP);
            preg_match("($s_item)",$s_file_tmp,$a_matches);
            if (empty($a_matches)) {
                $s_file =  $s_file_path."/".$s_item;
                $o_loadfile->_initModsecAudit($s_file);
                $s_auditlog = $o_loadfile->getAuditLog();
                $o_auditlog->translate($s_auditlog);
                $a_data = $o_auditlog->getData();
                $o_database->add($a_data);
                file_put_contents(FILE_TMP,$s_item."\r\n",FILE_APPEND);
            }
        }
//        } else {
//            echo '执行时间过长';
//        }
    }
}
