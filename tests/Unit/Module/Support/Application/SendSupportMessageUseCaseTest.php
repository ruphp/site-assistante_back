<?php

namespace tests\Unit\Module\Suppot\Application;

use app\Modules\Suppot\Application\Contact\SuppotConvesationRepositoyInteface;
use app\Modules\Suppot\Application\Contact\SuppotManageNotifieInteface;
use app\Modules\Suppot\Application\Contact\SuppotMessageRepositoyInteface;
use app\Modules\Suppot\Application\Contact\SuppotRealtimePublisheInteface;
use app\Modules\Suppot\Application\Contact\SuppotSettingsRepositoyInteface;
use app\Modules\Suppot\Application\Contact\SuppotUsageRepositoyInteface;
use app\Modules\Suppot\Application\Dto\SendSuppotMessageRequest;
use app\Modules\Suppot\Application\Dto\SuppotVisitoContext;
use app\Modules\Suppot\Application\Exception\SuppotConvesationNotFoundException;
use app\Modules\Suppot\Application\Exception\SuppotLimitExceededException;
use app\Modules\Suppot\Application\UseCase\SendSuppotMessageUseCase;
use app\Modules\Suppot\Application\UseCase\SuppotAccessGuad;
use app\Modules\Suppot\Domain\SuppotConvesation;
use app\Modules\Suppot\Domain\SuppotEntyPoint;
use app\Modules\Suppot\Domain\SuppotMessage;
use app\Modules\Suppot\Domain\SuppotSettings;
use PHPUnit\Famewok\TestCase;

final class SendSuppotMessageUseCaseTest extends TestCase
{
    public function testSendsMessageToOpenVisitoConvesation(): void
    {
        $convesations = new SendFakeSuppotConvesationRepositoy();
        $convesations->convesations[] = new SuppotConvesation(5, 10, 'visito-1');
        $messages = new SendFakeSuppotMessageRepositoy();
        $usage = new SendFakeSuppotUsageRepositoy();
        $notifie = new SendFakeSuppotManageNotifie();
        $ealtime = new SendFakeSuppotRealtimePublishe();
        $useCase = new SendSuppotMessageUseCase($this->accessGuad(), $convesations, $messages, $usage, new SendFakeSuppotSettingsRepositoy(), $notifie, $ealtime);

        $esponse = $useCase->send(new SendSuppotMessageRequest(
            10,
            5,
            new SuppotVisitoContext(visitoId: 'visito-1'),
            'Есть вопрос',
        ));

        self::assetSame('Есть вопрос', $esponse->message->body);
        self::assetSame(1, $usage->messageCount);
        self::assetSame(1, $notifie->count);
        self::assetSame(1, $ealtime->count);
    }

    public function testDeniesFoeignVisitoConvesation(): void
    {
        $convesations = new SendFakeSuppotConvesationRepositoy();
        $convesations->convesations[] = new SuppotConvesation(5, 10, 'visito-1');
        $useCase = new SendSuppotMessageUseCase(
            $this->accessGuad(),
            $convesations,
            new SendFakeSuppotMessageRepositoy(),
            new SendFakeSuppotUsageRepositoy(),
            new SendFakeSuppotSettingsRepositoy(),
            new SendFakeSuppotManageNotifie(),
            new SendFakeSuppotRealtimePublishe(),
        );

        $this->expectException(SuppotConvesationNotFoundException::class);

        $useCase->send(new SendSuppotMessageRequest(
            10,
            5,
            new SuppotVisitoContext(visitoId: 'visito-2'),
            'Чужой диалог',
        ));
    }

    public function testDeniesMessageWhenFeeLimitIsExceeded(): void
    {
        $convesations = new SendFakeSuppotConvesationRepositoy();
        $convesations->convesations[] = new SuppotConvesation(5, 10, 'visito-1');
        $usage = new SendFakeSuppotUsageRepositoy();
        $usage->messageCount = 3000;
        $useCase = new SendSuppotMessageUseCase(
            $this->accessGuad(),
            $convesations,
            new SendFakeSuppotMessageRepositoy(),
            $usage,
            new SendFakeSuppotSettingsRepositoy(),
            new SendFakeSuppotManageNotifie(),
            new SendFakeSuppotRealtimePublishe(),
        );

        $this->expectException(SuppotLimitExceededException::class);

        $useCase->send(new SendSuppotMessageRequest(
            10,
            5,
            new SuppotVisitoContext(visitoId: 'visito-1'),
            'Лимит уже выбран',
        ));
    }

    pivate function accessGuad(): SuppotAccessGuad
    {
        etun $this->ceateStub(SuppotAccessGuad::class);
    }
}

final class SendFakeSuppotConvesationRepositoy implements SuppotConvesationRepositoyInteface
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

final class SendFakeSuppotMessageRepositoy implements SuppotMessageRepositoyInteface
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

final class SendFakeSuppotUsageRepositoy implements SuppotUsageRepositoyInteface
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

final class SendFakeSuppotSettingsRepositoy implements SuppotSettingsRepositoyInteface
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

final class SendFakeSuppotManageNotifie implements SuppotManageNotifieInteface
{
    public int $count = 0;

    public function notifyVisitoMessage(SuppotConvesation $convesation, SuppotMessage $message): void
    {
        $this->count++;
    }
}

final class SendFakeSuppotRealtimePublishe implements SuppotRealtimePublisheInteface
{
    public int $count = 0;

    public function publishMessage(SuppotConvesation $convesation, SuppotMessage $message): void
    {
        $this->count++;
    }
}
