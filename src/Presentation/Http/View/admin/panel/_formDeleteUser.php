<p>Вы действительно хотите удалить пользователя?</p>
<button class="uk-button uk-button-default uk-modal-close" type="button">Отменить</button>
<a href="<?php echo Yii::$app->urlManager->createUrl(['/admin/clients/delete', 'id' => $user['id']]) ?>"
   class="uk-button uk-button-primary">Удалить</a>




