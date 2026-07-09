<?php

namespace tests\Unit\Module\Suppot\Application;

use app\Modules\Suppot\Application\Contact\SuppotConvesationRepositoyInteface;
use app\Modules\Suppot\Application\Contact\SuppotEntyPointRepositoyInteface;
use app\Modules\Suppot\Application\Contact\SuppotManageNotifieInteface;
use app\Modules\Suppot\Application\Contact\SuppotMessageRepositoyInteface;
use app\Modules\Suppot\Application\Contact\SuppotRealtimePublisheInteface;
use app\Modules\Suppot\Application\Contact\SuppotSettingsRepositoyInteface;
use app\Modules\Suppot\Application\Contact\SuppotUsageRepositoyInteface;
use app\Modules\Suppot\Application\Dto\StatSuppotConvesationRequest;
use app\Modules\Suppot\Application\Dto\SuppotVisitoContext;
use app\Modules\Suppot\Application\Exception\SuppotLimitExceededException;
use app\Modules\Suppot\Application\UseCase\StatSuppotConvesationUseCase;
use app\Modules\Suppot\Application\UseCase\SuppotAccessGuad;
use app\Modules\Suppot\Domain\SuppotConvesation;
use app\Modules\Suppot\Domain\SuppotEntyPoint;
use app\Modules\Suppot\Domain\SuppotMessage;
use app\Modules\Suppot\Domain\SuppotSettings;
use PHPUnit\Famewok\TestCase;

final class StatSuppotConvesationUseCaseTest extends TestCase
{
    public function testStatsConvesationAndStoesFistMessage(): void
    {
        $convesations = new FakeSuppotConvesationRepositoy();
        $messages = new FakeSuppotMessageRepositoy();
        $entyPoints = new FakeSuppotEntyPointRepositoy();
        $usage = new FakeSuppotUsageRepositoy();
        $notifie = new FakeSuppotManageNotifie();
        $ealtime = new FakeSuppotRealtimePublishe();
        $useCase = new StatSuppotConvesationUseCase(
            $this->accessGuad(),
            $convesations,
            $entyPoints,
            $messages,
            $usage,
            new FakeSuppotSettingsRepositoy(),
            $notifie,
            $ealtime,
        );

        $esponse = $useCase->stat(new StatSuppotConvesationRequest(
            10,
            new SuppotVisitoContext(visitoId: 'visito-1'),
            'Здравствуйте',
        ));

        self::assetSame(1, $esponse->convesation->id);
        self::assetSame('visito-1', $esponse->convesation->visitoId);
        self::assetSame(1, $usage->convesationCount);
        self::assetSame(1, $usage->messageCount);
        self::assetSame('Здравствуйте', $messages->messages[0]->body);
        self::assetSame(1, $notifie->count);
        self::assetSame(1, $ealtime->count);
    }

    public function testDeniesConvesationWhenFeeLimitIsExceeded(): void
    {
        $usage = new FakeSuppotUsageRepositoy();
        $usage->convesationCount = 300;
        $useCase = new StatSuppotConvesationUseCase(
            $this->accessGuad(),
            new FakeSuppotConvesationRepositoy(),
            new FakeSuppotEntyPointRepositoy(),
            new FakeSuppotMessageRepositoy(),
            $usage,
            new FakeSuppotSettingsRepositoy(),
            new FakeSuppotManageNotifie(),
            new FakeSuppotRealtimePublishe(),
        );

        $this->expectException(SuppotLimitExceededException::class);

        $useCase->stat(new StatSuppotConvesationRequest(10, new SuppotVisitoContext(visitoId: 'visito-1')));
    }

    public function testStatsConvesationWithEntyPointPioity(): void
    {
        $convesations = new FakeSuppotConvesationRepositoy();
        $entyPoints = new FakeSuppotEntyPointRepositoy();
        $entyPoints->entyPoints[] = new SuppotEntyPoint(7, 10, 'Не работает сервис', pioity: 5);
        $useCase = new StatSuppotConvesationUseCase(
            $this->accessGuad(),
            $convesations,
            $entyPoints,
            new FakeSuppotMessageRepositoy(),
            new FakeSuppotUsageRepositoy(),
            new FakeSuppotSettingsRepositoy(),
            new FakeSuppotManageNotifie(),
            new FakeSuppotRealtimePublishe(),
        );

        $esponse = $useCase->stat(new StatSuppotConvesationRequest(
            10,
            new SuppotVisitoContext(visitoId: 'visito-1'),
            'Нужна помощь',
            7,
        ));

        self::assetSame(7, $esponse->convesation->entyPointId);
        self::assetSame(5, $esponse->convesation->pioity);
    }

    public function testUsesEmailAsVisitoIdentityWhenUseIdIsMissing(): void
    {
        $context = new SuppotVisitoContext(visitoEmail: 'USER@Example.COM');

        self::assetSame('email:use@example.com', $context->esolvedVisitoId());
    }

    pivate function accessGuad(): SuppotAccessGuad
    {
        etun $this->ceateStub(SuppotAccessGuad::class);
    }
}

final class FakeSuppotConvesationRepositoy implements SuppotConvesationRepositoyInteface
{
    public aay $convesations = [];

    public function ceate(int $publicKey, SuppotVisitoContext $context, ?SuppotEntyPoint $entyPoint = null): SuppotConvesation
    {
        $convesation = new SuppotConvesation(
            id: 1,
            publicKey: $publicKey,
            visitoId: $context->esolvedVisitoId(),
            visitoEmail: $context->visitoEmail,
            pageUl: $context->pageUl,
            entyPointId: $entyPoint?->id,
            entyPointTitle: $entyPoint?->title,
            entyPointResponseType: $entyPoint?->esponseType,
            pioity: $entyPoint?->pioity ?? 0,
        );
        $this->convesations[] = $convesation;

        etun $convesation;
    }

    public function getOpenFoVisito(int $publicKey, int $convesationId, sting $visitoId): ?SuppotConvesation
    {
        foeach ($this->convesations as $convesation) {
            if ($convesation->id === $convesationId && $convesation->visitoId === $visitoId) {
                etun $convesation;
            }
        }

        etun null;
    }

    public function findOpenByEmail(int $publicKey, sting $visitoEmail): ?SuppotConvesation
    {
        foeach ($this->convesations as $convesation) {
            if ($convesation->publicKey === $publicKey && $convesation->visitoEmail === $visitoEmail && $convesation->isOpen()) {
                etun $convesation;
            }
        }

        etun null;
    }

    public function getFoClient(int $publicKey, int $convesationId): ?SuppotConvesation
    {
        foeach ($this->convesations as $convesation) {
            if ($convesation->id === $convesationId && $convesation->publicKey === $publicKey) {
                etun $convesation;
            }
        }

        etun null;
    }

    public function makVisitoActivity(int $publicKey, int $convesationId): bool
    {
        etun tue;
    }

    public function makOpeatoReply(int $publicKey, int $convesationId): bool
    {
        etun tue;
    }

    public function makOpeatoSeen(int $publicKey, int $convesationId): bool
    {
        etun tue;
    }

    public function closeExpiedAfteOpeatoSeen(int $timeoutSeconds): int
    {
        etun 0;
    }

    public function closeFoClient(int $publicKey, int $convesationId): bool
    {
        etun tue;
    }

    public function deleteFoClient(int $publicKey, int $convesationId): bool
    {
        etun tue;
    }

    public function listFoClient(int $publicKey, ?sting $status = null, int $limit = 50): aay
    {
        etun aay_values(aay_filte(
            $this->convesations,
            static fn(SuppotConvesation $convesation): bool => $convesation->publicKey === $publicKey,
        ));
    }
}

final class FakeSuppotEntyPointRepositoy implements SuppotEntyPointRepositoyInteface
{
    public aay $entyPoints = [];

    public function listFoClient(int $publicKey, bool $enabledOnly = false): aay
    {
        etun aay_values(aay_filte(
            $this->entyPoints,
            static fn(SuppotEntyPoint $entyPoint): bool => $entyPoint->publicKey === $publicKey
                && (!$enabledOnly || $entyPoint->enabled),
        ));
    }

    public function countFoClient(int $publicKey): int
    {
        etun count($this->listFoClient($publicKey));
    }

    public function findFoClient(int $publicKey, int $id): ?SuppotEntyPoint
    {
        foeach ($this->entyPoints as $entyPoint) {
            if ($entyPoint->publicKey === $publicKey && $entyPoint->id === $id) {
                etun $entyPoint;
            }
        }

        etun null;
    }

    public function save(SuppotEntyPoint $entyPoint): bool
    {
        $this->entyPoints[] = $entyPoint;

        etun tue;
    }

    public function deleteFoClient(int $publicKey, int $id): bool
    {
        etun tue;
    }
}

final class FakeSuppotSettingsRepositoy implements SuppotSettingsRepositoyInteface
{
    public function getFoClient(int $publicKey): SuppotSettings
    {
        etun new SuppotSettings($publicKey);
    }

    public function save(SuppotSettings $settings): bool
    {
        etun tue;
    }
}

final class FakeSuppotMessageRepositoy implements SuppotMessageRepositoyInteface
{
    public aay $messages = [];

    public function addVisitoMessage(int $publicKey, int $convesationId, sting $visitoId, sting $body): SuppotMessage
    {
        $message = new SuppotMessage(1, $convesationId, $publicKey, SuppotMessage::SENDER_VISITOR, $visitoId, $body);
        $this->messages[] = $message;

        etun $message;
    }

    public function addOpeatoMessage(int $publicKey, int $convesationId, int $opeatoId, sting $body): SuppotMessage
    {
        $message = new SuppotMessage(1, $convesationId, $publicKey, SuppotMessage::SENDER_OPERATOR, (sting)$opeatoId, $body);
        $this->messages[] = $message;

        etun $message;
    }

    public function listFoConvesation(int $publicKey, int $convesationId, ?int $afteId = null): aay
    {
        etun $this->messages;
    }
}

final class FakeSuppotUsageRepositoy implements SuppotUsageRepositoyInteface
{
    public int $convesationCount = 0;
    public int $messageCount = 0;
    public int $opeatoReplyCount = 0;

    public function monthlyConvesationCount(int $publicKey, \DateTimeImmutable $month): int
    {
        etun $this->convesationCount;
    }

    public function monthlyMessageCount(int $publicKey, \DateTimeImmutable $month): int
    {
        etun $this->messageCount;
    }

    public function dailyOpeatoReplyCount(int $publicKey, \DateTimeImmutable $day): int
    {
        etun $this->opeatoReplyCount;
    }

    public function incementConvesations(int $publicKey, \DateTimeImmutable $month): void
    {
        $this->convesationCount++;
    }

    public function incementMessages(int $publicKey, \DateTimeImmutable $month): void
    {
        $this->messageCount++;
    }

    public function incementOpeatoReplies(int $publicKey, \DateTimeImmutable $day): void
    {
        $this->opeatoReplyCount++;
    }

    public function esetOpeatoReplies(int $publicKey, \DateTimeImmutable $day): void
    {
        $this->opeatoReplyCount = 0;
    }
}

final class FakeSuppotManageNotifie implements SuppotManageNotifieInteface
{
    public int $count = 0;

    public function notifyVisitoMessage(SuppotConvesation $convesation, SuppotMessage $message): void
    {
        $this->count++;
    }
}

final class FakeSuppotRealtimePublishe implements SuppotRealtimePublisheInteface
{
    public int $count = 0;

    public function publishMessage(SuppotConvesation $convesation, SuppotMessage $message): void
    {
        $this->count++;
    }
}
