<?php
class  Run {
    public function init() {

        $o_indexlog = new Services_IndexLog();
        $o_indexlog->indexLog();
        while(true){
            $o_indexlog->addLog();
            sleep(2);
        }
    }
}
