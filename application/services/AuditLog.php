<?php
class Services_AuditLog {

    private $data;

    /**
     * 解析源日志文件
     */
    function translate($s_auditlog) {

        $a_matchs = null;
        preg_match_all('(.*\n)',$s_auditlog,$a_matchs);

        $s_Aauditlog = null;
        $s_Bauditlog = null;
        $s_Fauditlog = null;
        $s_Hauditlog = null;

        foreach($a_matchs[0] as $s_key=>$s_val){

            preg_match('(-A--)',$s_val,$a_A);
            preg_match('(-B--)',$s_val,$a_B);
            preg_match('(-F--)',$s_val,$a_F);
            preg_match('(-H--)',$s_val,$a_H);
            preg_match('(-Z--)',$s_val,$a_Z);
            if (!empty($a_A)) {
                $s_Aflag = true;
            }
            if (!empty($a_B)) {
                $s_Bflag = true;
                $s_Aflag = false;
            }
            if (!empty($a_F)) {
                $s_Fflag = true;
                $s_Bflag = false;
            }
            if (!empty($a_H)) {
                $s_Hflag = true;
                $s_Fflag = false;
            }
            if (!empty($a_Z)) {
                $s_Hflag = false;
            }
            if (true==$s_Aflag) {
                $s_Aauditlog .= $a_matchs[0][$s_key];
            }
            if (true==$s_Bflag) {
                $s_Bauditlog .= $a_matchs[0][$s_key];
            }
            if (true==$s_Fflag) {
                $s_Fauditlog .= $a_matchs[0][$s_key];
            }
            if (true==$s_Hflag) {
                $s_Hauditlog .= $a_matchs[0][$s_key];
            }
        }
        $a_fa = $this->fomateA($s_Aauditlog);
        $a_fb = $this->fomateB($s_Bauditlog);
        $a_fh = $this->fomateH($s_Hauditlog);
        $a_ff = $this->fomateF($s_Fauditlog);
        $a_merg = array_merge($a_fa,$a_fb,$a_fh,$a_ff);
//        print_r($a_merg);
        $this->data = $a_merg;
    }

    public function fomateA ($s_Aauditlog) {

        preg_match('(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})',$s_Aauditlog,$a_happentime);
        $a_tmp = explode(' ',$s_Aauditlog);
        $a_fomateA = array(
            'happentime' => $a_happentime[0],
            'uniqueId' => $a_tmp[2],
            'sip' => $a_tmp[3],
            'sport' => $a_tmp[4],
            'dip' => $a_tmp[5],
            'dport' => $a_tmp[6],
        );
//        print_r($a_fomateA);
        return $a_fomateA;
}
        public function fomateB ($s_Bauditlog) {

            preg_match('(Host:.*)',$s_Bauditlog,$a_host);
            preg_match('(GET.* |POST.*)',$s_Bauditlog,$a_url);
            preg_match('(User-Agent.*)',$s_Bauditlog,$a_agent);

            $s_host = str_replace("Host: ","",$a_host[0]);
            if (false!==strpos($a_url[0],'POST')) {
                $s_url = str_replace("POST ","",$a_url[0]);
            } elseif (false!==strpos($a_url[0],'GET')) {
                $s_url = str_replace("GET ","",$a_url[0]);
            }
            $s_agent = str_replace("User-Agent:","",$a_agent[0]);
            $a_formateB = array(
                'hostname' => $s_host,
                'url' => $s_url,
                'userAgent' => $s_agent,
            );
//            print_r($a_formateB);
            return $a_formateB;
}
        public function fomateH ($s_Hauditlog) {

            preg_match('(\[.*\])',$s_Hauditlog,$a_message);
            $a_tmp = explode('"]',$a_message[0]);
            $s_id = str_replace("[id \"","",$a_tmp[0]);
            $s_msg = str_replace("[msg \"","",$a_tmp[1]);
            $s_date = str_replace("[data \"","",$a_tmp[2]);
            $s_severity = str_replace("[severity \"","",$a_tmp[3]);
            $s_tag = str_replace("[tag \"","",$a_tmp[4]);
            $a_formateH = array(
                'ruleId' => $s_id,
                'msgId' => $s_msg,
                'matchs' => $s_date,
                'severityId' => $s_severity,
                'tagId' => $s_tag,
            );
//            print_r($a_formateH);
            return $a_formateH;
}
        public function fomateF ($s_Fauditlog) {

            preg_match('(HTTP.*)',$s_Fauditlog,$a_response_code);
            $a_response_code = explode(' ',$a_response_code[0]);
            $a_formateF = array(
                'responseCode' => $a_response_code[1],
            );
//            print_r($a_formateF);
            return $a_formateF;
}

    public function getData() {
        return $this->data;
    }
}
