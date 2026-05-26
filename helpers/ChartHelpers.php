<?php

namespace app\helpers;

use app\models\LogsUsageDay;
use app\models\LogsUsageMonth;
use app\models\LogsUsageQuart;
use app\models\LogsUsageWeek;
use app\models\LogsUsageYear;
use app\models\LogsApiConfiguration;
use app\models\LogsApiOpen;
use app\models\Users;
use app\modules\courses\models\Courses;
use app\modules\hints\models\Hints;
use app\modules\onboardings\models\Onboardings;
use app\modules\surveys\models\Surveys;
use DateInterval;
use DatePeriod;
use DateTime;
use Yii;

class ChartHelpers
{
    public static function getChart($chart_name, $chart_filters = []): array|string
    {

        $categories=[];
        switch ($chart_name) {
            case 'api_configurations':
                $series = ChartHelpers::getChartApiConfigurations($categories, $chart_filters);
                return ['series'=>$series,'categories'=>$categories];
            case 'module_contents':
                $list_modules = [];
                $series = ChartHelpers::getChartModuleContents($categories,  $chart_filters);
                //debug( $series,1);
                return ['series'=>$series,'categories'=>$categories,'list_modules'=>$list_modules];

            case 'open_widget':
                $series = ChartHelpers::getChartOpenWidget($categories, $chart_filters);
                //debug($series,1);
                return ['series' => $series, 'categories' => $categories];

            case 'usage':
                //ManagerHelpers::debug($chart_filters,1);
                $series = ChartHelpers::getChartUsage($categories, $chart_filters);
                //debug($series,1);
                return ['series' => $series, 'categories' => $categories];
            default;
                return '';
        }
    }
    public static function getDataChart($chart_name, $chart_filters = [],$is_type_json=true): array|string
    {
        $data=[];
        switch ($chart_name) {
            case 'chart_api_configurations':
                $categories=[];
                $series = self::getChartApiConfigurations($categories, $chart_filters);
                $data = ['categories'=>$categories,'series'=>$series];
                break;
            case 'chart_open_widget':
                $categories=[];
                $series = self::getChartOpenWidget($categories, $chart_filters);
                $data = ['categories'=>$categories,'series'=>$series];
                break;
            case 'chart_usage':
                $categories=[];
                $series = self::getChartUsage($categories, $chart_filters);
                $data = ['categories'=>$categories,'series'=>$series];
                break;
            case 'chart_module_contents':
                $categories=[];
                $series = self::getChartModuleContents($categories, $chart_filters);
                $data = ['categories'=>$categories,'series'=>$series];
                break;
            default;
        }
        if($is_type_json){
            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = $data;
        }
        return $data;
    }

    public static function getChartApiConfigurations( array &$categories, array $chart_filters = []): array
    {
        // категории это даты , серии это клиенты - сданными
        // получим всех клиентов и зададим дефолтную даты от - периодом неделя
        $public_keys = array_column(Users::getListUsersManager('public_key'), 'public_key');
        $start_date = isset($chart_filters['start_date']) ?date("Y-m-d",strtotime($chart_filters['start_date'])): date("Y-m-d", strtotime("-6 days"));
        $end_date = isset($chart_filters['end_date']) ?  date("Y-m-d",strtotime($chart_filters['end_date'])): date("Y-m-d");
        $only_unic = $chart_filters['only_unic'] ?? 0;

        // cоздадим период из дат
        $from = new DateTime($start_date);
        $to = new DateTime($end_date);
        $period = new DatePeriod($from, new DateInterval('P1D'), $to, DatePeriod::INCLUDE_END_DATE);

        /* для статы  api configuration*/
        //получим данные из бд
        $load_data_api_configurations = LogsApiConfiguration::getDataCount($only_unic?'unic':'all', $public_keys, $start_date, $end_date)->asArray()->all();;
        // преобразуем в нужный формат
        //подготовим правильный массив
        $arr_chart_api_configurations = [];

// получим все даты в формате бд DATE !!!!
        $gorisont = array_map(
            function ($item) {
                return $item->format('Y-m-d');
            },
            iterator_to_array($period)
        );

// получим все даты в формате d.m.Y для нижней шкалы,
// и создадим нужную строку для горизонтальной шкалы графика $str_gorisont
        $categories = array_map(
            function ($item) {
                return $item->format('d.m.Y');
            },
            iterator_to_array($period)
        );


// заполним значения нулями
// и создадим массив $series [дата:значение]
        $vertical_val = array_fill(0, count($gorisont), 0);
        $series = array_combine($gorisont, $vertical_val);

// заполняем массив пустыми значениями по умолчанию
        foreach ($public_keys as $public_key) {
            $arr_chart_api_configurations[$public_key]['name'] = '';
            $arr_chart_api_configurations[$public_key]['series'] = $series;
        }

// прогоняем массив через поступившие данные и дополняем его значения
        foreach ($load_data_api_configurations as $val) {
            $arr_chart_api_configurations[$val['public_key']]['name'] = $val['firm'];
            $arr_chart_api_configurations[$val['public_key']]['series'][$val['date_day']] = $val['value'];
        }
        return $arr_chart_api_configurations;

    }
    public static function getChartOpenWidget( array &$categories, array $chart_filters = []): array
    {
        // категории это даты , серии это клиенты - сданными
        // получим всех клиентов и зададим дефолтную даты от - периодом неделя
        $public_keys = array_column(Users::getListUsersManager('public_key'), 'public_key');
        $start_date = isset($chart_filters['start_date']) ?date("Y-m-d",strtotime($chart_filters['start_date'])): date("Y-m-d", strtotime("-6 days"));
        $end_date = isset($chart_filters['end_date']) ?  date("Y-m-d",strtotime($chart_filters['end_date'])): date("Y-m-d");
        $only_unic = $chart_filters['open_widget_only_unic'] ?? 0;

        // cоздадим период из дат
        $from = new DateTime($start_date);
        $to = new DateTime($end_date);
        $period = new DatePeriod($from, new DateInterval('P1D'), $to, DatePeriod::INCLUDE_END_DATE);

        /* для статы  api configuration*/
        //получим данные из бд
        $load_data_open_widget = LogsApiOpen::getDataCount($only_unic?'unic':'all', $public_keys, $start_date, $end_date)->asArray()->all();;
        // преобразуем в нужный формат
        //подготовим правильный массив
        $arr_chart_open_widget = [];

// получим все даты в формате бд DATE !!!!
        $gorisont = array_map(
            function ($item) {
                return $item->format('Y-m-d');
            },
            iterator_to_array($period)
        );

// получим все даты в формате d.m.Y для нижней шкалы,
// и создадим нужную строку для горизонтальной шкалы графика $str_gorisont
        $categories = array_map(
            function ($item) {
                return $item->format('d.m.Y');
            },
            iterator_to_array($period)
        );


// заполним значения нулями
// и создадим массив $series [дата:значение]
        $vertical_val = array_fill(0, count($gorisont), 0);
        $series = array_combine($gorisont, $vertical_val);

// заполняем массив пустыми значениями по умолчанию
        foreach ($public_keys as $public_key) {
            $arr_chart_open_widget[$public_key]['name'] = '';
            $arr_chart_open_widget[$public_key]['series'] = $series;
        }

// прогоняем массив через поступившие данные и дополняем его значения
        foreach ($load_data_open_widget as $val) {
            $arr_chart_open_widget[$val['public_key']]['name'] = $val['firm'];
            $arr_chart_open_widget[$val['public_key']]['series'][$val['date_day']] = $val['value'];
        }
        return $arr_chart_open_widget;

    }
    private static function getChartModuleContents( array &$categories, array $chart_filters = []): array
    {


        $system = $chart_filters['system'] ?? 0;
        $where=[];
        if($system){
            $where=['id'=>$system];
        }
        $clients=Users::getListUsersManager('*',$where);
        if(!count($clients)){
            return [];
        }
        $categories = array_column($clients, 'firm');
        $categories_pk = array_column($clients, 'public_key');
        $series=[];

        $auth = Yii::$app->authManager;
        $modules = $auth->getChildren('accesses_modules');
        uksort($modules, function ($key1, $key2) {
            $order_modules = json_decode($_ENV['ORDER_MODULES']);
            $pos1 = array_search($key1, $order_modules);
            $pos2 = array_search($key2, $order_modules);
            return $pos1 - $pos2;
        });
        unset($modules['bigdata'],$modules['tpotrs']);
        $modules_keys = array_keys($modules);
        //ManagerHelpers::debug($modules_keys,1);
/*        foreach($modules_keys as $val) {
            $list_modules[$modules[$val]->name] = $modules[$val]->description;
        }*/
        //ManagerHelpers::debug($list_modules,1);
        // заполним series=>data нулями , сколько клиентов столько нулей в массиве

        //$_series = array_combine($categories_pk, array_fill(array_key_first($categories_pk), count($categories_pk), 0));
        $_series[$categories_pk[0]]=0;
        //ManagerHelpers::debug(  $_series,1);
        //debug($modules );
        //debug($_series);
        // подставим эти пустышки в каждый модуль чтобы не было пробелов в массиве
        foreach($modules_keys as $key => $val) {
            $series[$key]['name'] = $modules[$val]->description;
            $series[$key]['series'] = $_series ;
        }
        //ManagerHelpers::debug( $modules_keys,1);
        foreach($modules_keys as $key => $val) {
            //foreach ($categories_pk as $public_key){
              $series[$key]['series'][$categories_pk[0]] = self::getCountContentModule($val, $categories_pk[0])??0;
           // }
        }


        return $series;

    }
    private static function getChartUsage(  array &$categories, array $chart_filters = []): array
    {

        $type_period = $chart_filters['type_period']??'day';
        $start_date = isset($chart_filters['start_date']) ?date("Y-m-d",strtotime($chart_filters['start_date'])): date("Y-m-d", strtotime("-6 days"));
        $end_date = isset($chart_filters['end_date']) ?  date("Y-m-d",strtotime($chart_filters['end_date'])): date("Y-m-d");
        $only_unic = $chart_filters['usage_only_unic'] ?? 0;
        $role = $chart_filters['role'] ?? 0;
        $system = $chart_filters['system'] ?? 0;
        $public_key = Yii::$app->user->identity->getPublicKey();
        $where=[];
        $series=[];
//var_dump($public_key); exit;
        if($public_key == 1){

            if($system ){
                $where=['id'=>$system];
            }
            $clients=Users::getListUsersManager('*',$where);

            if(!count($clients)){
                return [];
            }

            $categories_pk = array_column($clients, 'public_key');
            $public_key = $categories_pk[0];
        }







        // cоздадим период всех дней  из дат
        $from = new DateTime($start_date);
        $to = new DateTime($end_date);
        $period = new DatePeriod($from, new DateInterval('P1D'), $to, DatePeriod::INCLUDE_END_DATE);


//////////////подготовим данные
///  1 для запросов в бд  $categories_dates_monday
///  2 для заполнения пустышек $_series
///  3 возвращаемые параметром функции $categories
/// ///////////////
        switch ($type_period) {
            case 'year':
                $first_day_year = new DateTime($from->format('Y-01-01'));
                $last_day_year = new DateTime($to->format('Y-01-01'));
                $period_first_day = new DatePeriod($first_day_year, new DateInterval('P1Y'), $last_day_year, DatePeriod::INCLUDE_END_DATE);


                //для бд
                $categories_year = array_map(
                    function ($item) {
                        return $item->format('Y-01-01');
                    },
                    iterator_to_array($period_first_day)
                );
                $_series = array_combine($categories_year  , array_fill(array_key_first($categories_year  ), count($categories_year ), 0));


                $categories = array_map(
                    function ($item) {
                        return $item->format('Y');
                    },
                    iterator_to_array($period_first_day)
                );
                break;
            case 'quart':

                $first_quart = new DateTime(self::startKv(strtotime($from->format('Y-m-d'))));
                $last_quart = new DateTime(self::startKv(strtotime($to->format('Y-m-d'))));
                $period_quart = new DatePeriod($first_quart, new DateInterval('P3M'), $last_quart, DatePeriod::INCLUDE_END_DATE );
                //debug($period_quart);
                $categories_dates_quarts = array_map(
                    function ($item) {
                        return $item->format('Y-m-d');
                    },
                    iterator_to_array($period_quart)
                );
                $categories_quart = array_map(
                    function ($item) {
                        return $item->format('Y-m-d');
                    },
                    iterator_to_array($period_quart)
                );

                $_series = array_combine($categories_quart , array_fill(array_key_first($categories_quart ), count($categories_quart), 0));
                $categories = array_map(
                    function ($item) {
                        return ((date('n', strtotime($item->format('Y-m-d'))) - 1) / 3 + 1).' квартал '.$item->format('Y');

                    },
                    iterator_to_array($period_quart)
                );
                break;
            case 'month':
                $first_day_month = new DateTime($from->format('Y-m-01'));
                $last_day_month = new DateTime($to->format('Y-m-01'));
                $period_first_day = new DatePeriod($first_day_month, new DateInterval('P1M'), $last_day_month, DatePeriod::INCLUDE_END_DATE);


                //для бд
                $categories_month = array_map(
                    function ($item) {
                        return $item->format('Y-m-01');
                    },
                    iterator_to_array($period_first_day)
                );
                $_series = array_combine($categories_month  , array_fill(array_key_first($categories_month  ), count($categories_month ), 0));


                $categories = array_map(
                    function ($item) {
                        $F = self::setMonthRu()[$item->format('m')];
                        return $F.' '.$item->format('Y');
                    },
                    iterator_to_array($period_first_day)
                );
                break;
            case 'week':
                $monday_day = new DateTime(self::monday($from->format('Y-m-d'))); // понедельник cамый первый
                $sunday_day =  new DateTime(self::sunday($to->format('Y-m-d')));; // воскресенье самое последнее
                $period_monday = new DatePeriod($monday_day, new DateInterval('P1W'), $sunday_day);
                $categories_dates_monday = array_map(
                    function ($item) {
                        return $item->format('Y-m-d');
                    },
                    iterator_to_array($period_monday)
                );
                $categories_week = array_map(
                    function ($item) {
                        return $item->format('Y-m-d').'-'.$item->modify('+6 days')->format('Y-m-d');
                    },
                    iterator_to_array($period_monday)
                );
                $_series = array_combine($categories_week , array_fill(array_key_first($categories_week ), count($categories_week), 0));
                $categories = array_map(
                    function ($item) {
                        //шкала  периодами
                        return $item->format('d.m.Y').'-'.$item->modify('+6 days')->format('d.m.Y');
                       // return $item->format('d.m.Y');
                    },
                    iterator_to_array($period_monday)
                );
                break;
            default: // day
                $categories_dates = array_map(
                    function ($item) {
                        return $item->format('Y-m-d');
                    },
                    iterator_to_array($period)
                );
                $_series = array_combine($categories_dates, array_fill(array_key_first($categories_dates), count($categories_dates), 0));
                $categories = array_map(
                    function ($item) {
                        return $item->format('d.m.Y');
                    },
                    iterator_to_array($period)
                );
                break;

        }


////////// получим массив модулей
        $auth = Yii::$app->authManager;



        $permissions = $auth->getPermissionsByUser($public_key);
        uksort($permissions, function ($key1, $key2) {
            $order_modules = json_decode($_ENV['ORDER_MODULES']);
            $pos1 = array_search($key1, $order_modules);
            $pos2 = array_search($key2, $order_modules);
            return $pos1 - $pos2;
        });
        $modules_keys=[];

        foreach ($permissions as $key => $val) {

            if ($auth->getChildren('accesses_modules')[$key] ?? false) {
                $modules_keys[] = $key;
            }
        }

////////////// создадим пустышки в каждый модуль
        foreach($modules_keys as $key => $val) {
            $series[$key]['name'] = $permissions[$val]->description;
            $series[$key]['series'] = $_series ;


        }

//////////// дозаполним пустышки полученными данными
        foreach($modules_keys as $key => $val) {
            $results =[];
            switch ($type_period) {
                case 'year'://$categories_year

                        //debug($type_period,1);
                        $results = LogsUsageYear::getData($only_unic ? 'unic' : 'all', $public_key, $categories_year,$role,$val)->asArray()->all();

                    foreach ($results as $result){
                        $series[$key]['series'][$result['first_day']] = $result['value'];
                    }
                    break;
                case 'quart'://$categories_dates_quarts

                        $results = LogsUsageQuart::getData($only_unic ? 'unic' : 'all', $public_key, $categories_dates_quarts,$role,$val)->asArray()->all();

                    foreach ($results as $result){
                        $series[$key]['series'][$result['first_quart_day']] = $result['value'];
                    }
                    break;
                case 'month':

                        //debug($type_period,1);
                        $results = LogsUsageMonth::getData($only_unic ? 'unic' : 'all', $public_key, $categories_month,$role,$val)->asArray()->all();

                    foreach ($results as $result){
                        $series[$key]['series'][$result['first_day']] = $result['value'];
                    }
                    break;
                case 'week':

                        $results = LogsUsageWeek::getData($only_unic ? 'unic' : 'all', $public_key, $categories_dates_monday,$role,$val)->asArray()->all();

                    foreach ($results as $result){
                        $series[$key]['series'][$result['monday_day'].'-'.$result['sunday_day']] = $result['value'];
                    }
                    break;
                default: //day

                        $results = LogsUsageDay::getData($only_unic ? 'unic' : 'all', $public_key, $start_date, $end_date,$role,$val)->asArray()->all();



                    foreach ($results as $result){
                        $series[$key]['series'][$result['date_day']] = $result['value'];
                    }
                    break;
            }
        }


        return $series;

    }
    private static function getCountContentModule(string $key, int $public_key): \yii\db\ActiveQuery|int
    {
        switch ($key) {
            case 'courses':
                $query = Courses::find()->select('*')->where(['public_key'=>$public_key]);
               return $query->count();
            case 'hints':
                $query = Hints::find()->select('*')->where(['public_key'=>$public_key]);
               return $query->count();
            case 'onboardings':
                $query = Onboardings::find()->select('*')->where(['public_key'=>$public_key]);
               return $query->count();
            case 'surveys':
                $query = Surveys::find()->select('*')->where(['public_key'=>$public_key]);
               return $query->count();
            //echo $query->createCommand()->getRawSql();exit;
            default;
                return 0;
        }
    }
    static  function monday($date): string
    {
        $ts = strtotime($date);
        $start = (date('w', $ts) == 1) ? $ts : strtotime('last monday', $ts);
        return date('Y-m-d', $start);
    }
    static function sunday($date): string
    {
        $ts = strtotime($date);
        $start = (date('w', $ts) == 1) ? $ts : strtotime('last monday', $ts);
        return date('Y-m-d', strtotime('next sunday', $start));
    }
    static function startKv($d): string
    {
        $kv = (int)((date('n', $d) - 1) / 3 + 1);
        $year = date('y', $d);
        return date('Y-m-d', mktime(0, 0, 0, ($kv - 1) * 3 + 1, 1, $year));
    }
    static function endKv($d): string
    {
        $kv = (int)((date('n', $d) - 1) / 3 + 1);
        $year = date('y', $d);
        return date('Y-m-d', mktime(0, 0, 0, ($kv) * 3 + 1, 0, $year));
    }
    static function setMonthRu(): array
    {
        $_monthsList = array(
            "01"=>"Январь","02"=>"Февраль","03"=>"Март",
            "04"=>"Апрель","05"=>"Май", "06"=>"Июнь",
            "07"=>"Июль","08"=>"Август","09"=>"Сентябрь",
            "10"=>"Октябрь","11"=>"Ноябрь","12"=>"Декабрь");
        return  $_monthsList;

    }
}