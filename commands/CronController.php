<?php

namespace app\commands;

use app\helpers\ChartHelpers;
use app\models\LogsApiConfiguration;
use app\models\LogsApiOpen;
use app\models\LogsUsageDay;
use app\models\LogsUsageMonth;
use app\models\LogsUsageQuart;
use app\models\LogsUsageWeek;
use app\models\LogsUsageYear;
use app\models\Users;
use DateTime;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class CronController extends Controller
{
    /**
     * @throws \Exception
     */
    public function actionPrepareLogConfiguration()
    {
        $date1301 = new DateTime('2026-02-13');
        // переберем подключенных клиентов
        $users = Users::find()->where(['status' => 1])->asArray()->all();
        $auth = Yii::$app->authManager;
        self::writeLog('START ALL', 'INFO');
        foreach ($users as $client) {
            self::writeLog('START '.$client['public_key'], 'INFO');
            $gmt = (int)$client['gmt'] + (-1*(int)$_ENV['GMT_SERVERS']);
            $permissions = $auth->getPermissionsByUser($client['public_key']);
            foreach($permissions as $key => $val){
                $param=Yii::$app->getModule($key)->params;
                $runner = new \tebazil\runner\ConsoleCommandRunner();
                foreach($param['crons'] as $cron){
                   // for ($i = $_ENV['CRON_LIMIT_READ_REDIS_LINES']; $i > 0; --$i) {
                        $runner->run($cron,[$client]);
                   // }
                    //var_dump($cron);
                }
            }
            // log api config
            self::writeLog('START LOG api config '.$client['public_key'], 'INFO');
            for ($i = $_ENV['CRON_LIMIT_READ_REDIS_LINES']; $i > 0; --$i) {


                $merge_log_json = Yii::$app->redis->rpop('log/configuration/' . $client['public_key']);

                //

                if(!is_null($merge_log_json)){
                    $merge_log = json_decode($merge_log_json, true);
                    $day_log = new DateTime($merge_log['date']);

                   // var_dump($day_log);
                    $day_log->modify("$gmt hours");
                    $day_log = $day_log->format("Y-m-d");
                    $user_id = $merge_log['userId'] ?? 0;

                    $old_logs = LogsApiConfiguration::find()->where(['public_key' => $client['public_key'], 'date_day' => $day_log])->one();
                    if (is_null($old_logs)) {
                        $user_id='{"0":'.$user_id.'}';
                        if (!LogsApiConfiguration::queryInsertLog($client['public_key'], $day_log,$user_id)) {
                            self::writeLog('RETURN OLD DATA no insert api config '.$day_log->format("Y-m-d"), 'INFO');
                            Yii::$app->redis->rpush('log/configuration/' . $client['public_key'], $merge_log_json);
                        }
                    }
                    else {
                        // итерируем существующую запись
                        $users = $old_logs->json_users;
                        $users[] = $user_id;
                        $unic_users=array_unique($users);
                        $json_unic_users = json_encode($unic_users,JSON_FORCE_OBJECT);

                        if (!LogsApiConfiguration::queryUpdateLog($client['public_key'], $day_log,$json_unic_users,count($unic_users))) {
                            self::writeLog('RETURN OLD DATA no update api config '.$day_log->format("Y-m-d"), 'INFO');
                            Yii::$app->redis->rpush('log/configuration/' . $client['public_key'], $merge_log_json);
                        }
                    }
                }
                else{
                    self::writeLog('NULL LOG api config $i='.($_ENV['CRON_LIMIT_READ_REDIS_LINES']-$i).' '.$client['public_key'], 'INFO');
                    $i = 0;
                }



            }

            // log open
            self::writeLog('START LOG open '.$client['public_key'], 'INFO');
            for ($i = $_ENV['CRON_LIMIT_READ_REDIS_LINES']; $i > 0; --$i) {


                $merge_log_json = Yii::$app->redis->rpop('log/open/' . $client['public_key']);

                if(!is_null($merge_log_json)){
                    $merge_log = json_decode($merge_log_json, true);
                    $day_log = new DateTime($merge_log['date']);
                    $day_log->modify("$gmt hours");
                    $day_log = $day_log->format("Y-m-d");
                    $user_id = $merge_log['userId'] ?? 0;
                    $roles_data = $merge_log['userRoles'] ?? [];

                    $old_logs = LogsApiOpen::find()->where(['public_key' => $client['public_key'], 'date_day' => $day_log])->one();
                    if (is_null($old_logs)) {
                        $user_id='{"0":'.$user_id.'}';
                        $roles_data = json_encode(array_fill_keys(array_values($roles_data), 1),JSON_FORCE_OBJECT);
                        if (!LogsApiOpen::queryInsertLog($client['public_key'], $day_log,$user_id,$roles_data)) {
                            Yii::$app->redis->rpush('log/open/' . $client['public_key'], $merge_log_json);
                        }
                    }
                    else {
                        $this->getUserslog( $old_logs,$user_id,$roles_data,$unic_users,$json_unic_users,$json_roles);


                        if (!LogsApiopen::queryUpdateLog($client['public_key'], $day_log,$json_unic_users, $json_roles,count($unic_users))) {
                            Yii::$app->redis->rpush('log/open/' . $client['public_key'], $merge_log_json);
                        }
                    }
                }
                else{
                    self::writeLog('NULL LOG open $i='.($_ENV['CRON_LIMIT_READ_REDIS_LINES']-$i).' '.$client['public_key'], 'INFO');
                    $i = 0;
                }


            }

            // log usage
            self::writeLog('START LOG usage ' . $client['public_key'], 'INFO');
            for ($i = $_ENV['CRON_LIMIT_READ_REDIS_LINES']; $i > 0; --$i) {

                $merge_log_json = Yii::$app->redis->rpop('log/usage/' . $client['public_key']);
                if(!is_null($merge_log_json)){
                    $merge_log = json_decode($merge_log_json, true);
                    $day = new DateTime($merge_log['date']);
                    //var_dump($day_log);
                    $day->modify($gmt. " hours");
                    $day_log = $day->format("Y-m-d");
                    $first_day_month = $day->format('Y-m-01');
                    $first_day_year = $day->format('Y-01-01');
                    $monday_day = ChartHelpers::monday($day_log); // понедельник текущей недели
                    $sunday_day = ChartHelpers::sunday($day_log); // воскресенье текущей недели
                    $first_day_quart = ChartHelpers::startKv(mktime($gmt, 0, 0, date('m'), date('d'), date('Y')));
                    $last_day_quart = ChartHelpers::endKv(mktime($gmt, 0, 0, date('m'), date('d'), date('Y')));
                    $user_id = $merge_log['userId'] ?? 0;
                    $roles_data = $merge_log['userRoles'] ?? [];
                    $type = $merge_log['type'];



                    // заполним дни
                    $old_day_log = LogsUsageDay::find()->where(['public_key' => $client['public_key'], 'date_day' => $day_log, 'type' => $type])->one();

                    if (is_null($old_day_log)) {
                        $user_='{"0":'.$user_id.'}';
                        $roles_ = json_encode(array_fill_keys(array_values($roles_data), 1),JSON_FORCE_OBJECT);
                        LogsUsageDay::queryInsertLog($client['public_key'], $day_log, $user_, $roles_, $type);
                        //todo  решить что сделать при не записи хоть одного
                    }
                    else {

                        $this->getUserslog( $old_day_log,$user_id,$roles_data,$unic_users,$json_unic_users,$json_roles);
                        // итерируем существующую запись


                        LogsUsageDay::queryUpdateLog($client['public_key'], $day_log, $json_unic_users, $json_roles, count($unic_users), $type);
                        //todo  решить что сделать при не записи хоть одного
                    }

                    // заполним недели
                    $old_week_log = LogsUsageWeek::find()->where(['public_key' => $client['public_key'], 'monday_day' => $monday_day, 'sunday_day' => $sunday_day, 'type' => $type])->one();
                    if (is_null($old_week_log)) {
                        $user_='{"0":'.$user_id.'}';
                        $roles_ = json_encode(array_fill_keys(array_values($roles_data), 1),JSON_FORCE_OBJECT);
                        LogsUsageWeek::queryInsertLog($monday_day, $sunday_day, $client['public_key'], $type, $user_, $roles_);
                        //todo  решить что сделать при не записи хоть одного
                    }
                    else {
                        $this->getUserslog($old_week_log,$user_id,$roles_data,$unic_users,$json_unic_users,$json_roles);
                        LogsUsageWeek::queryUpdateLog($monday_day, $sunday_day, $client['public_key'], $type, $json_unic_users, $json_roles, count($unic_users));
                        //todo  решить что сделать при не записи хоть одного
                    }

                    // заполним месяцы
                    // $first_day_month
                    $old_month_log = LogsUsageMonth::find()->where(['public_key' => $client['public_key'], 'first_day' => $first_day_month, 'type' => $type])->one();
                    if (is_null($old_month_log)) {
                        $user_='{"0":'.$user_id.'}';
                        $roles_ = json_encode(array_fill_keys(array_values($roles_data), 1),JSON_FORCE_OBJECT);
                        LogsUsageMonth::queryInsertLog($first_day_month, $client['public_key'], $type, $user_, $roles_);
                        //todo  решить что сделать при не записи хоть одного
                    }
                    else {

                        $this->getUserslog( $old_month_log ,$user_id,$roles_data,$unic_users,$json_unic_users,$json_roles);
                        LogsUsageMonth::queryUpdateLog($first_day_month, $client['public_key'], $type, $json_unic_users, $json_roles, count($unic_users));
                        //todo  решить что сделать при не записи хоть одного
                    }

                    // заполним кварталы
                    // $first_day_quart    $last_day_quart
                    $old_week_log = LogsUsageQuart::find()->where(['public_key' => $client['public_key'], 'first_quart_day' => $first_day_quart, 'last_quart_day' => $last_day_quart, 'type' => $type])->one();
                    if (is_null($old_week_log)) {
                        $user_='{"0":'.$user_id.'}';
                        $roles_ = json_encode(array_fill_keys(array_values($roles_data), 1),JSON_FORCE_OBJECT);
                        LogsUsageQuart::queryInsertLog($first_day_quart, $last_day_quart, $client['public_key'], $type, $user_, $roles_);
                        //todo  решить что сделать при не записи хоть одного
                    }
                    else {
                        $this->getUserslog($old_week_log,$user_id,$roles_data,$unic_users,$json_unic_users,$json_roles);
                        LogsUsageQuart::queryUpdateLog($first_day_quart, $last_day_quart, $client['public_key'], $type, $json_unic_users, $json_roles, count($unic_users));
                        //todo  решить что сделать при не записи хоть одного
                    }

                    // заполним годы
                    // $first_day_year
                    $old_year_log = LogsUsageYear::find()->where(['public_key' => $client['public_key'], 'first_day' => $first_day_year, 'type' => $type])->one();
                    if (is_null($old_year_log)) {
                        $user_='{"0":'.$user_id.'}';
                        $roles_ = json_encode(array_fill_keys(array_values($roles_data), 1),JSON_FORCE_OBJECT);
                        LogsUsageYear::queryInsertLog($first_day_year, $client['public_key'], $type, $user_, $roles_);
                        //todo  решить что сделать при не записи хоть одного
                    }
                    else {

                        $this->getUserslog($old_year_log,$user_id,$roles_data,$unic_users,$json_unic_users,$json_roles);
                        LogsUsageYear::queryUpdateLog($first_day_year, $client['public_key'], $type, $json_unic_users, $json_roles, count($unic_users));
                        //todo  решить что сделать при не записи хоть одного
                    }
                }
                else{
                    self::writeLog('NULL LOG usage $i='.($_ENV['CRON_LIMIT_READ_REDIS_LINES']-$i).' '.$client['public_key'], 'INFO');
                    $i = 0;
                }


            }
            self::writeLog('END LOG  '.$client['public_key'], 'INFO');
        }
        self::writeLog('END LOG  ALL', 'INFO');
        return '';
    }





    public function actionTestCron()
    {
        echo "actionTestCron";
        return ExitCode::OK;
    }

    private function getUserslog($old_logs,$user_id,$roles_data,&$unic_users,&$json_unic_users,&$json_roles)
    {
        $users = $old_logs->json_users;
        $roles = $old_logs->json_roles_data;
        $users[] = $user_id;
        $unic_users=array_unique($users);
        foreach($roles_data as $role){
            if(isset($roles[$role])){
                $roles[$role]+=1;
            }else{
                $roles[$role]=1;
            }
        }


        $json_unic_users = json_encode($unic_users,JSON_FORCE_OBJECT);
        $json_roles = json_encode($roles,JSON_FORCE_OBJECT);
    }

/*    // Использование
writeLog('log api config 3902', 'INFO');
writeLog('Ошибка подключения к БД', 'ERROR');
writeLog('Команда выполнена успешно', 'SUCCESS');*/
    private function writeLog($message, $level = 'INFO') {
        $date = date('Y-m-d H:i:s');
        $pid = getmypid(); // ID процесса
        $logLine = sprintf(
            "[%s] [%s] [PID:%d] %s\n",
            $date,
            str_pad($level, 7),
            $pid,
            $message
        );
        file_put_contents('/var/log/cron.log', $logLine, FILE_APPEND);
    }

}
