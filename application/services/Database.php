<?php
class Services_Database {
    private $a_config;
    private $_map = array(//所有数据库字段
        'actionId' => 'action_id',
        'ruleId' =>'rule_id',
        'happentime' => 'happentime',
        'hostname' => 'hostname',
        'sip' => 'sip',
        'sport' =>'sport',
        'dip' => 'dip',
        'dport' => 'dport',
        'uniqueId' => 'unique_id',
        'msgId' => 'msg_id',
        'matchs' => 'matchs',
        'severityId' => 'severity_id',
        'tagId' => 'tag_id',
        'userAgent' => 'user_agent',
        'url' => 'url',
        'post' => 'post',
        'responseCode' => 'response_code',
        'requestHeader' => 'request_header',
        'responseHeader' => 'response_header',
        'responseBody' => 'response_body',
        'country' => 'country',
        'province' => 'province',
        'city' => 'city',
    );

    /**获取数据库连接
     * @param 配置信息
     * @return PDO
     */
    public function getConnection() {

        $o_loadfile = new Services_LoadFile();
//        $o_bootstrap->_initConfig();
        $a_config = $o_loadfile->getConfig();
        $this->a_config = $a_config;

        $s_db_username = $a_config['db_username'];
        $s_db_password = $a_config['db_password'];

        switch($a_config['db_type']) {
            case 'mysql':
                $s_db = "mysql:host=$a_config[db_host];port=$a_config[db_port];dbname=$a_config[db_name]";
                break;
            case 'oracle':
                $s_db = "oci:dbname=yoursid";
                break;
            default:
                die(error_log("unknow database type! ".date("Y-m-d h:i:s")."\r\n", 3, "$a_config[error_log]"));
                break;
        }
        try {
            $rs_conn = new PDO($s_db,$s_db_username,$s_db_password, array(PDO::ATTR_PERSISTENT => true));
        } catch (PDOException $e) {
            die(error_log("ERROR: ".$e->getMessage() .date("Y-m-d h:i:s"). "\r\n",3,"$a_config[error_log]"));
        }
        return $rs_conn;
    }

    /**
     * 回收数据库连接资源
     */
    public function disconnected() {
        return null;
    }

    /**
     * 向数据库插入数据
     * @param $a_log 字段数据
     */
    public function add($a_log) {

        $s_sql = null;
        $s_sql_val = null;
        foreach ($this->_map as $s_map_key => $s_map_val) {
            foreach ($a_log as $s_key => $s_val) {
                if ($s_map_key===$s_key) {//如字段匹配则生成sql
                    $s_sql .= $s_map_val . ",";
                    $s_sql_val .= ":" . $s_map_val . ",";
                }
            }
        }
        //去除末尾的逗号
        $s_sql = substr($s_sql,0,strlen($s_sql)-1);
        $s_sql_val = substr($s_sql_val,0,strlen($s_sql_val)-1);

        $rs_conn = $this->getConnection();
        $s_db_table = $this->a_config['db_table'];
        $s_query ="INSERT INTO $s_db_table ($s_sql)VALUES($s_sql_val)";
        $rs_result = $rs_conn->prepare($s_query);
        foreach ($this->_map as $s_map_key => $s_map_val) {
            foreach ($a_log as $s_key => $s_val) {
                if ($s_map_key===$s_key) {//如字段匹配则binvalue
                    $rs_result->bindValue(":".$s_map_val,$s_val);
                }
            }
        }
//        var_dump($rs_result);
        $b_return = $rs_result->execute();
       if (false===$b_return) {
           error_log("WARN: INSERT DATE FAILED! SQL:$s_query".date("Y-m-d h:i:s"). "\r\n",3,$this->a_config['error_log']);
       }
    }

    /**
     * 根据unique_id字段查询数据库是否已经入库该条数据
     * @param $s_item
     * @return bool 如查询到数据则返回true
     */
    public function checkVal($s_item) {
//        return true;
        $rs_conn = $this->getConnection();
        $s_db_table = $this->a_config['db_table'];
        $s_query="SELECT id FROM $s_db_table WHERE unique_id = '$s_item'";
        $rs_result=$rs_conn->prepare($s_query);
        $b_return = $rs_result->execute();
        if (false===$b_return) {
            error_log("WARN: SELECT DATE FAILED! SQL:$s_query".date("Y-m-d h:i:s"). "\r\n",3,$this->a_config['error_log']);
        }
        while($res=$rs_result->fetch(PDO::FETCH_ASSOC)){
            if (isset($res)) {
                return true;
            }
        }
    }
}
