<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\favorites\controllers
 * @category   CategoryName
 */

namespace lispa\amos\favorites\controllers;

use lispa\amos\core\record\Record;
use lispa\amos\favorites\AmosFavorites;
use lispa\amos\favorites\exceptions\FavoritesException;
use lispa\amos\favorites\widgets\FavoriteWidget;
use lispa\amos\notificationmanager\AmosNotify;
use Yii;
use yii\web\Controller as YiiController;
use yii\web\NotFoundHttpException;

/**
 * Class FavoriteController
 * @package lispa\amos\favorites\controllers
 */
class FavoriteController extends YiiController
{
    /**
     * @var string $layout
     */
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public function init() {

        parent::init();
        $this->setUpLayout();
        // custom initialization code goes here
    }

    /**
     * The action manages the favorite add or remove.
     * @return string
     * @throws FavoritesException
     */
    public function actionFavorite()
    {
        // If the request is not AJAX throws an exception because this action can only be called via AJAX.
        if (!Yii::$app->getRequest()->getIsAjax()) {
            throw new FavoritesException(AmosFavorites::t('amosfavorites', 'This action cannot be reached directly'));
        }
        
        $retVal = [
            'success' => 0,
            'nowFavorite' => 0,
            'nowNotFavorite' => 1,
            'msg' => '',
            'favoriteBtnTitle' => ''
        ];
        
        // If the request is not via POST method or there is at least one parameter missing stop the execution.
        if (!Yii::$app->getRequest()->post()) {
            $retVal['msg'] = AmosFavorites::t('amosfavorites', 'Request not via POST method.');
            return json_encode($retVal);
        }
        
        $post = Yii::$app->getRequest()->post();
        
        // Missing request parameters.
        if (!isset($post['id']) || !isset($post['className'])) {
            $retVal['msg'] = AmosFavorites::t('amosfavorites', 'Missing request parameters.');
            return json_encode($retVal);
        }
        
        /** @var AmosNotify $notify */
        $notify = Yii::$app->getModule('notify');
        if (is_null($notify)) {
            $retVal['msg'] = AmosFavorites::t('amosfavorites', 'Notify module not present.');
            return json_encode($retVal);
        }
        
        $model = $this->findModel($post['id'], $post['className']);
        $readPerm = $this->makeReadPermission($model);
        if (Yii::$app->user->can($readPerm, ['model' => $model])) {
            $alreadyFavorite = $notify->isFavorite($model, Yii::$app->user->id);
            if ($alreadyFavorite) {
                $ok = $notify->favouriteOff(Yii::$app->user->id, $post['className'], $post['id']);
                return $this->returnValues($ok, $retVal, 'OFF');
            } else {
                $ok = $notify->favouriteOn(Yii::$app->user->id, $post['className'], $post['id']);
                return $this->returnValues($ok, $retVal, 'ON');
            }
        } else {
            $retVal['msg'] = AmosFavorites::t('amosfavorites', 'User cannot read the content.');
            return json_encode($retVal);
        }
    }
    
    /**
     * Make the final return values array and then encode it in JSON.
     * @param bool $ok
     * @param array $retVal
     * @param string $type
     * @return string
     */
    private function returnValues($ok, $retVal, $type)
    {
        $retVal['success'] = ($ok ? 1 : 0);
        
        if (($ok && ($type == 'ON')) || (!$ok && ($type == 'OFF'))) {
            $retVal['nowFavorite'] = 1;
            $retVal['nowNotFavorite'] = 0;
            $retVal['favoriteBtnTitle'] = FavoriteWidget::favoriteBtnTitle(true);
        } elseif ((!$ok && ($type == 'ON')) || ($ok && ($type == 'OFF'))) {
            $retVal['nowFavorite'] = 0;
            $retVal['nowNotFavorite'] = 1;
            $retVal['favoriteBtnTitle'] = FavoriteWidget::favoriteBtnTitle(false);
        }
        
        if ($type == 'ON') {
            $retVal['msg'] = ($ok ?
                AmosFavorites::t('amosfavorites', 'Favorite successfully added.') :
                AmosFavorites::t('amosfavorites', 'Error while adding favorite.'));
        } elseif ($type == 'OFF') {
            $retVal['msg'] = ($ok ?
                AmosFavorites::t('amosfavorites', 'Favorite successfully removed.') :
                AmosFavorites::t('amosfavorites', 'Error while removing favorite.'));
        }
        
        return json_encode($retVal);
    }
    
    /**
     * Find the content model.
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
            throw new NotFoundHttpException(AmosFavorites::t('amosfavorites', 'The requested page does not exist.'));
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
    public function setUpLayout($layout = null){
        if ($layout === false){
            $this->layout = false;
            return true;
        }
        $module = \Yii::$app->getModule('layout');
        if(empty($module)){
            $this->layout =  '@vendor/lispa/amos-core/views/layouts/' . (!empty($layout) ? $layout : $this->layout);
            return true;
        }
        $this->layout = (!empty($layout)) ? $layout : $this->layout;
        return true;
    }
}
