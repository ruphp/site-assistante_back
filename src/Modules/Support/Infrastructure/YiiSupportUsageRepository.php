<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportUsageRepositoryInterface;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportUsageDayRecord;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportUsageMonthRecord;
use Yii;

final class YiiSupportUsageRepository implements SupportUsageRepositoryInterface
{
    public function monthlyConversationCount(int $publicKey, \DateTimeImmutable $month): int
    {
        return $this->monthRecord($publicKey, $month)?->conversation_count ?? 0;
    }

    public function monthlyMessageCount(int $publicKey, \DateTimeImmutable $month): int
    {
        return $this->monthRecord($publicKey, $month)?->message_count ?? 0;
    }

    public function dailyOperatorReplyCount(int $publicKey, \DateTimeImmutable $day): int
    {
        return $this->dayRecord($publicKey, $day)?->operator_reply_count ?? 0;
    }

    public function incrementConversations(int $publicKey, \DateTimeImmutable $month): void
    {
        $this->incrementMonth($publicKey, $month, 'conversation_count');
    }

    public function incrementMessages(int $publicKey, \DateTimeImmutable $month): void
    {
        $this->incrementMonth($publicKey, $month, 'message_count');
    }

    public function incrementOperatorReplies(int $publicKey, \DateTimeImmutable $day): void
    {
        $periodDay = $this->day($day);
        Yii::$app->db->createCommand(
            "INSERT INTO support_usage_day (public_key, period_day, operator_reply_count)
                VALUES (:public_key, :period_day, 1)
                ON CONFLICT (public_key, period_day)
                DO UPDATE SET
                    operator_reply_count = support_usage_day.operator_reply_count + 1,
                    updated_at = NOW()",
            [
                ':public_key' => $publicKey,
                ':period_day' => $periodDay,
            ],
        )->execute();
    }

    private function monthRecord(int $publicKey, \DateTimeImmutable $month): ?SupportUsageMonthRecord
    {
        return SupportUsageMonthRecord::findOne([
            'public_key' => $publicKey,
            'period_month' => $this->month($month),
        ]);
    }

    private function dayRecord(int $publicKey, \DateTimeImmutable $day): ?SupportUsageDayRecord
    {
        return SupportUsageDayRecord::findOne([
            'public_key' => $publicKey,
            'period_day' => $this->day($day),
        ]);
    }

    private function incrementMonth(int $publicKey, \DateTimeImmutable $month, string $field): void
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

    private function day(\DateTimeImmutable $day): string
    {
        return $day->format('Y-m-d');
    }
}
