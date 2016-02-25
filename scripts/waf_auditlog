#!/bin/bash
BASE_HOME=$(dirname $(cd `dirname $0`; pwd))
echo 'projec home: '$BASE_HOME
scriptFileName=index.php
logFileName=waf_auditlog.log

status(){
   echo '==========status=======';
   tail -f ${BASE_HOME}/datas/logs/${logFileName}
}

start() {
    echo '==========start==========';
    #这里需要先确认php命令是否存在。
    if [ -z $(which php) ]; then
        echo 'php does not exist.'
        exit 1
    fi
    nohup php ${BASE_HOME}/${scriptFileName} >> ${BASE_HOME}/datas/logs/${logFileName} 2>&1 &
    echo '==========done===========';
}

stop() {
    echo '===========stop===========';
    if [ $( ps auxf | grep ${scriptFileName} | grep -v grep | wc -l ) -gt 0 ]; then
        ps auxf | grep ${scriptFileName} | grep -v grep |awk {'print $2'} |xargs kill -9
        echo '===========done===========';
    else
        echo "The process of ${scriptFileName} does not exist!";
    fi
}

restart() {
    stop;
    echo 'sleeping.........';
    sleep 3;
    start;
}

case "$1" in
    'start')
        start
        ;;
    'stop')
        stop
        ;;
    'status')
        status
        ;;
    'restart')
        restart
        ;;
    *)
    echo "usage: $0 {start|stop|restart|status}"
    exit 1
        ;;
    esac
