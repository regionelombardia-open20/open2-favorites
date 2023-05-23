<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\favorites\controllers
 * @category   CategoryName
 */

namespace open20\amos\favorites\controllers;

use open20\amos\core\record\Record;
use open20\amos\favorites\AmosFavorites;
use open20\amos\favorites\exceptions\FavoritesException;
use open20\amos\favorites\widgets\FavoriteWidget;
use open20\amos\notificationmanager\AmosNotify;
use Yii;
use yii\web\Controller as YiiController;
use yii\web\NotFoundHttpException;
use yii\rest\Controller;

/**
 * Class FavoriteController
 * @package open20\amos\favorites\controllers
 */
class FavoriteController extends Controller
{
    /**
     * @inheritdoc
     */
    const FAVORITE_OFF = 'OFF';
    const FAVORITE_ON  = 'ON';

    /**
     * @var string $layout
     */
    public $layout = 'main';

    /**
     * @var AmosNotify $notify
     */
    protected $notify;

    /**
     * @var User id
     */
    protected $user_id;

    /**
     * @var type array
     */
    protected $retVal;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setUpLayout();

        // custom initialization code goes here
        $this->notify = Yii::$app->getModule('notify');
        $this->user_id = Yii::$app->user->id;

        $this->retVal = [
            'success' => 0,
            'nowFavorite' => 0,
            'nowNotFavorite' => 1,
            'msg' => '',
            'favoriteBtnTitle' => ''
        ];

        /** @var AmosNotify $notify */
        if (is_null($this->notify)) {
            $this->retVal['msg'] = AmosFavorites::t('amosfavorites', '#missing_notify_module');

            return $this->retVal;
        }

    }

    /**
     * The action manages the favorite add or remove
     * @return string
     * @throws FavoritesException
     */
    public function actionFavorite($id = null, $className = null, $user_id = null)
    {
        // If the request is not AJAX throws an exception because this action can only be called via AJAX
        if (!Yii::$app->getRequest()->getIsAjax()) {
            throw new FavoritesException(AmosFavorites::t('amosfavorites', '#only_ajax_request'));
        }

        // If the request is not via POST method or there is at least one parameter missing stop the execution
        if ((!Yii::$app->getRequest()->post()) && (empty($id) && empty($className))) {
            $this->retVal['msg'] = AmosFavorites::t('amosfavorites', '#not_post_request');

            return $this->retVal;
        }

        $post = Yii::$app->getRequest()->post();
        $className = empty($className) ? $post['className'] : $className;
        $modelId = empty($id) ? $post['id'] : $id;
        $this->user_id = empty($this->user_id) ? $user_id : $this->user_id;

        // Missing request parameters
        if (empty($modelId) || empty($className)) {
            $this->retVal['msg'] = AmosFavorites::t('amosfavorites', '#missing_request_params');

            return $this->retVal;
        }

        $model = $this->findModel($modelId, $className);
        $readPerm = $this->makeReadPermission($model);
        if (!(Yii::$app->user->can($readPerm, ['model' => $model]))) {
            $this->retVal['msg'] = AmosFavorites::t('amosfavorites', '#no_perms_to_read');

            return $this->retVal;
        }

        $alreadyFavorite = $this->notify->isFavorite($model, $this->user_id);

        if ($alreadyFavorite) {
            $ok = $this->notify->favouriteOff($this->user_id, $className, $modelId);

            return $this->returnValues($ok, self::FAVORITE_OFF);
        } else {
            $ok = $this->notify->favouriteOn($this->user_id, $className, $modelId);

            return $this->returnValues($ok, self::FAVORITE_ON);
        }

    }

    /**
     * Make the final return values array and then encode it in JSON
     * @param bool $ok
     * @param string $type
     * @return string
     */
    private function returnValues($ok, $type)
    {
        $this->retVal['success'] = $ok ? 1 : 0;

        if ($ok && ($type == self::FAVORITE_ON)) {
            $this->retVal['nowFavorite'] = 1;
            $this->retVal['nowNotFavorite'] = 0;
            $this->retVal['favoriteBtnTitle'] = FavoriteWidget::favoriteBtnTitle(true);
            $this->retVal['msg'] = $ok 
                ? AmosFavorites::t('amosfavorites', '#successfully_added')
                : AmosFavorites::t('amosfavorites', '#error_while_adding');
        } elseif ($ok && ($type == self::FAVORITE_OFF)) {
            $this->retVal['nowFavorite'] = 0;
            $this->retVal['nowNotFavorite'] = 1;
            $this->retVal['favoriteBtnTitle'] = FavoriteWidget::favoriteBtnTitle(false);
            $this->retVal['msg'] = $ok 
                ? AmosFavorites::t('amosfavorites', '#successfully_removed')
                : AmosFavorites::t('amosfavorites', '#error_while_removing');
        }

        return $this->retVal;
    }

    /**
     * Find the content model
     * @param int $id
     * @param string $className
     * @return Record
     * @throws NotFoundHttpException
     */
    private function findModel($id, $className)
    {
        /** @var Record $className */
        $model = $className::findOne($id);
        if (is_null($model)) {
            throw new NotFoundHttpException(AmosFavorites::t('amosfavorites', '#requested_page_not_found'));
        }

        return $model;
    }

    /**
     * Return the read permission for a model by his class name.
     * @param Record $model
     * @return string
     */
    private function makeReadPermission($model)
    {
        $modelClassName = $model::className();
        $splitModelClassName = explode("\\", $modelClassName);
        $modelName = end($splitModelClassName);
        $modelNameUpper = strtoupper($modelName);

        return $modelNameUpper . '_READ';
    }

    /**
     * @param null $layout
     * @return bool
     */
    public function setUpLayout($layout = null)
    {
        if ($layout === false){
            $this->layout = $layout;

            return true;
        }

        $module = \Yii::$app->getModule('layout');
        if (empty($module)) {
            $this->layout = '@vendor/open20/amos-core/views/layouts/' . (!empty($layout) ? $layout : $this->layout);
        } else {
            $this->layout = (!empty($layout)) ? $layout : $this->layout;
        }

        return true;
    }
}
