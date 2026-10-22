<?php

namespace tests\Integration\Instructions;

use app\Presentation\Http\Controller\api\InstructionsController;
use tests\Integration\Support\YiiIntegrationTestCase;
use Yii;
use yii\web\Request;
use yii\web\Response;

final class InstructionFeedbackTest extends YiiIntegrationTestCase
{
    public function testGuestAndUserCanRateCommentAndFavoriteWithoutDuplicatingCounters(): void
    {
        $request = Yii::$app->get('request');
        $response = Yii::$app->get('response');
        Yii::$app->set('request', new Request(['cookieValidationKey' => 'test-only']));
        Yii::$app->set('response', new Response());
        try {
            $publicKey = 9201022;
            $this->createClient($publicKey);
            $articleId = (int)Yii::$app->db->createCommand(
                "INSERT INTO sw_instruction_articles (public_key, title, html)
                 VALUES (:key, 'Feedback regression', '') RETURNING id",
                [':key' => $publicKey],
            )->queryScalar();
            $controller = new InstructionsController('instructions', Yii::$app);
            foreach ([null, '987654321'] as $userId) {
                Yii::$app->request->setQueryParams(['userId' => $userId]);
                $feedbackId = $controller->actionInstructionEstimate($publicKey, $articleId, 1);
                self::assertIsInt($feedbackId);
                self::assertGreaterThan(0, $feedbackId);
                self::assertSame($feedbackId, $controller->actionInstructionEstimate($publicKey, $articleId, 1));
                self::assertSame($feedbackId, $controller->actionInstructionEstimate($publicKey, $articleId, 0));
                $controller->actionInstructionEstimate($publicKey, $articleId, null, $feedbackId, 'Useful');
                self::assertSame('Useful', Yii::$app->db->createCommand(
                    'SELECT comment FROM sw_instruction_article_feedback WHERE id = :id', [':id' => $feedbackId],
                )->queryScalar());
                Yii::$app->request->setQueryParams(['instruction_add' => $articleId]);
                $controller->actionFavorites($publicKey, $userId);
                $controller->actionFavorites($publicKey, $userId);
                Yii::$app->request->setQueryParams([]);
                self::assertCount(1, $controller->actionFavorites($publicKey, $userId));
                Yii::$app->request->setQueryParams(['instruction_delete' => $articleId]);
                $controller->actionFavorites($publicKey, $userId);
                Yii::$app->request->setQueryParams([]);
                self::assertSame([], $controller->actionFavorites($publicKey, $userId));
            }
            $counts = Yii::$app->db->createCommand(
                'SELECT likes, dislikes FROM sw_instruction_articles WHERE id = :id', [':id' => $articleId],
            )->queryOne();
            self::assertSame(0, (int)$counts['likes']);
            self::assertSame(2, (int)$counts['dislikes']);
        } finally {
            Yii::$app->set('request', $request);
            Yii::$app->set('response', $response);
        }
    }
}
