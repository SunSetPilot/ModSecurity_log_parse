# ModSecurity log parse
ModSecurity log parse project</br>
this project is used to format ModSecurity  generate log and inserted ModSecurity log into to mysql database</br>
this project is background program,i was write script waf_auditlog to control service</br>

step1: Create the database see:waf_alarm/docs/sql/waf_alarm_table.sql</br>
step2: Modify Profile,  see:waf_alarm/application/configs/waf_alarm.yaml</br>

service start：/scripts/waf_auditlog start</br>
service stop：/scripts/waf_auditlog stop</br>
service restart：/scripts/waf_auditlog restart</br>
