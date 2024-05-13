<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\content\controllers;

use Yii;
use humhub\components\Controller;
use humhub\modules\content\models\WallEntry;
use humhub\modules\content\models\Content;
use yii\web\HttpException;

/**
 * PermaController is used to create permanent links to content.
 *
 * @package humhub.modules_core.wall.controllers
 * @since 0.5
 * @author Luke
 */
class PermaController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'acl' => [
                'class' => \humhub\components\behaviors\AccessControl::class,
                'guestAllowedActions' => ['index', 'wall-entry']
            ]
        ];
    }

    /**
     * Redirects to given HActiveRecordContent or HActiveRecordContentAddon
     */
    public function actionIndex()
    {
        $id = (int)Yii::$app->request->get('id');
        $commentId = (int)Yii::$app->request->get('commentId');

        $content = Content::findOne(['id' => $id]);
        if ($content !== null) {

            if (method_exists($content->getPolymorphicRelation(), 'getUrl')) {
                $url = $content->getPolymorphicRelation()->getUrl();
            } elseif ($content->container !== null) {
                $urlParams = ['contentId' => $id];
                if ($commentId) {
                    $urlParams['commentId'] = $commentId;
                }
                $url = $content->container->createUrl(null, $urlParams);
            }

            if (!empty($url)) {
                return $this->redirect($url);
            }
        }

        throw new HttpException(404, Yii::t('ContentModule.base', 'Could not find requested content!'));
    }
}
