<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportUsageRepositoryInterface;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportUsageMonthRecord;
use Yii;

final class YiiSupportUsageRepository implements SupportUsageRepositoryInterface
{
    public function monthlyConversationCount(int $publicKey, \DateTimeImmutable $month): int
    {
        return $this->record($publicKey, $month)?->conversation_count ?? 0;
    }

    public function monthlyMessageCount(int $publicKey, \DateTimeImmutable $month): int
    {
        return $this->record($publicKey, $month)?->message_count ?? 0;
    }

    public function incrementConversations(int $publicKey, \DateTimeImmutable $month): void
    {
        $this->increment($publicKey, $month, 'conversation_count');
    }

    public function incrementMessages(int $publicKey, \DateTimeImmutable $month): void
    {
        $this->increment($publicKey, $month, 'message_count');
    }

    private function record(int $publicKey, \DateTimeImmutable $month): ?SupportUsageMonthRecord
    {
        return SupportUsageMonthRecord::findOne([
            'public_key' => $publicKey,
            'period_month' => $this->month($month),
        ]);
    }

    private function increment(int $publicKey, \DateTimeImmutable $month, string $field): void
    {
        $periodMonth = $this->month($month);
        Yii::$app->db->createCommand(
            "INSERT INTO support_usage_month (public_key, period_month, {$field})
                VALUES (:public_key, :period_month, 1)
                ON CONFLICT (public_key, period_month)
                DO UPDATE SET {$field} = support_usage_month.{$field} + 1",
            [
                ':public_key' => $publicKey,
                ':period_month' => $periodMonth,
            ],
        )->execute();
    }

    private function month(\DateTimeImmutable $month): string
    {
        return $month->modify('first day of this month')->format('Y-m-01');
    }
}
