<?php

namespace tests\Unit\Module\Support\Application;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Dto\CloseSupportConversationRequest;
use app\Modules\Support\Application\Dto\SupportVisitorContext;
use app\Modules\Support\Application\Exception\SupportConversationNotFoundException;
use app\Modules\Support\Application\UseCase\CloseSupportConversationUseCase;
use app\Modules\Support\Application\UseCase\SupportAccessGuard;
use app\Modules\Support\Domain\SupportConversation;
use PHPUnit\Framework\TestCase;

final class CloseSupportConversationUseCaseTest extends TestCase
{
    public function testVisitorCanCloseOwnOpenConversation(): void
    {
        $context = new SupportVisitorContext(visitorId: 'visitor-1');
        $accessGuard = $this->createMock(SupportAccessGuard::class);
        $accessGuard->expects(self::once())->method('assertAvailable')->with(2, $context);

        $conversations = $this->createMock(SupportConversationRepositoryInterface::class);
        $conversations->expects(self::once())
            ->method('getOpenForVisitor')
            ->with(2, 39, 'visitor-1')
            ->willReturn(new SupportConversation(39, 2, 'visitor-1'));
        $conversations->expects(self::once())->method('closeForClient')->with(2, 39)->willReturn(true);

        (new CloseSupportConversationUseCase($accessGuard, $conversations))
            ->close(new CloseSupportConversationRequest(2, 39, $context));
    }

    public function testVisitorCannotCloseAnotherConversation(): void
    {
        $context = new SupportVisitorContext(visitorId: 'visitor-2');
        $accessGuard = $this->createMock(SupportAccessGuard::class);
        $conversations = $this->createMock(SupportConversationRepositoryInterface::class);
        $conversations->method('getOpenForVisitor')->willReturn(null);
        $conversations->expects(self::never())->method('closeForClient');

        $this->expectException(SupportConversationNotFoundException::class);

        (new CloseSupportConversationUseCase($accessGuard, $conversations))
            ->close(new CloseSupportConversationRequest(2, 39, $context));
    }
}
