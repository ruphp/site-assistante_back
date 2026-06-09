<?php

namespace tests\Integration\Support;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\console\Application;
use yii\db\Transaction;

abstract class YiiIntegrationTestCase extends TestCase
{
    private ?Transaction $transaction = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootYii();
        $this->transaction = Yii::$app->db->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->transaction?->isActive) {
            $this->transaction->rollBack();
        }

        parent::tearDown();
    }

    protected function createClient(int $publicKey): void
    {
        Yii::$app->db->createCommand(
            'INSERT INTO users (id, name, email, passhash, status, firm, public_key)
                VALUES (:id, :name, :email, :passhash, 1, :firm, :public_key)',
            [
                ':id' => $publicKey,
                ':name' => 'test-client-' . $publicKey,
                ':email' => 'test-client-' . $publicKey . '@help-layer.local',
                ':passhash' => 'test',
                ':firm' => 'Test Client ' . $publicKey,
                ':public_key' => $publicKey,
            ]
        )->execute();
    }

    protected function assignRole(int $userId, string $role): void
    {
        Yii::$app->db->createCommand(
            'INSERT INTO auth_assignment (item_name, user_id, created_at)
                VALUES (:item_name, :user_id, :created_at)',
            [
                ':item_name' => $role,
                ':user_id' => $userId,
                ':created_at' => time(),
            ]
        )->execute();
    }

    protected function jsonColumn(string $table, string $column, array $where): array
    {
        $query = (new \yii\db\Query())
            ->select([$column])
            ->from($table)
            ->where($where)
            ->one(Yii::$app->db);

        $value = $query[$column] ?? [];

        if (is_string($value)) {
            return json_decode($value, true);
        }

        return $value;
    }

    private function bootYii(): void
    {
        if (class_exists('Yii', false) && Yii::$app !== null) {
            return;
        }

        $root = dirname(__DIR__, 3);

        Dotenv::createImmutable($root)->safeLoad();

        require $root . '/vendor/yiisoft/yii2/Yii.php';

        $config = require $root . '/config/console.php';

        new Application($config);
        Yii::$app->errorHandler->unregister();
    }
}
