<?php

namespace app\Infrastructure\Panel;

use app\Application\Panel\Metrics\Contract\PanelModuleMetricChartRepositoryInterface;
use app\Application\Panel\Metrics\Dto\PanelModuleMetricChart;
use Throwable;
use Yii;

final class YiiPanelModuleMetricChartRepository implements PanelModuleMetricChartRepositoryInterface
{
    public function getChartsForClient(int $publicKey): array
    {
        $charts = [];
        $auth = Yii::$app->authManager;
        $permissions = $auth->getPermissionsByUser($publicKey);
        $moduleAccesses = $auth->getChildren('accesses_modules');
        $orderModules = json_decode($_ENV['ORDER_MODULES'] ?? '[]', true) ?: [];

        uksort($permissions, static function ($key1, $key2) use ($orderModules) {
            $pos1 = array_search($key1, $orderModules, true);
            $pos2 = array_search($key2, $orderModules, true);

            return ($pos1 === false ? PHP_INT_MAX : $pos1) <=> ($pos2 === false ? PHP_INT_MAX : $pos2);
        });

        foreach ($permissions as $key => $permission) {
            if (!isset($moduleAccesses[$key])) {
                continue;
            }

            $module = Yii::$app->getModule($key);
            $params = $module?->params ?? [];

            if (!isset($params['charts'], $params['class_helpers'], $params['dir_view'])) {
                continue;
            }

            foreach ($params['charts'] as $chartName) {
                try {
                    $charts[] = new PanelModuleMetricChart(
                        $chartName,
                        $params['dir_view'] . 'manager/panel/_chart_' . $chartName . '_html',
                        $params['dir_view'] . 'manager/panel/_chart_' . $chartName . '_js',
                        $params['class_helpers']::getChart($chartName) ?? [],
                    );
                } catch (Throwable $e) {
                    if (YII_DEBUG) {
                        Yii::error([$key, $chartName, $e->getMessage()], __METHOD__);
                    }
                }
            }
        }

        return $charts;
    }
}
