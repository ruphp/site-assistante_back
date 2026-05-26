<?php
namespace app\commands;

use yii\console\Controller;
use Yii;
use yii\console\ExitCode;
use yii\helpers\Console;
use OpenApi\Generator;

class SwaggerController extends Controller
{
    public function actionBuildYaml()
    {

        $openApi = Generator::scan(['./controllers/','./modules/courses/controllers/api/']);
        $url = "{$_ENV['SWAGGER_SERVER_API']}";
        $openApi->servers = [['url'=>$url,'description'=>'API server']];
        header('Content-Type: application/x-yaml');

        echo $openApi->toYaml();
        $file = Yii::getAlias('./web/documentation/swagger.yaml');

        $handle = fopen($file, 'wb');
        fwrite($handle, $openApi->toYaml());
        fclose($handle);

        echo $this->ansiFormat('Created \n', Console::FG_BLUE);
        
        return ExitCode::OK;
    }
}
