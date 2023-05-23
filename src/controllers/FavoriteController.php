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
use open20\amos\favorites\models\Favorite;
use open20\amos\favorites\widgets\FavoriteWidget;
use open20\amos\notificationmanager\AmosNotify;
use Yii;
use yii\base\Response;
use yii\web\Controller as YiiController;
use yii\web\NotFoundHttpException;

/**
 * Class FavoriteController
 * @package open20\amos\favorites\controllers
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
    public function init()
    {

        parent::init();
        $this->setUpLayout();
        // custom initialization code goes here
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
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
    public function setUpLayout($layout = null)
    {
        if ($layout === false) {
            $this->layout = false;
            return true;
        }
        $module = \Yii::$app->getModule('layout');
        if (empty($module)) {
            $this->layout = '@vendor/open20/amos-core/views/layouts/' . (!empty($layout) ? $layout : $this->layout);
            return true;
        }
        $this->layout = (!empty($layout)) ? $layout : $this->layout;
        return true;
    }

    /**
     * @return false|string[]
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSelectUnselectFavoriteUrlAjax()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = \Yii::$app->request->post();
        if ($post) {
            $url = urldecode(\Yii::$app->request->post('favoriteUrl'));
            $title = \Yii::$app->request->post('favoriteTitle');
            $classname = \Yii::$app->request->post('favoriteClassname');
            $contentId = \Yii::$app->request->post('favoriteContentId');
            $contentModule = \Yii::$app->request->post('favoriteModule');
            $contentController = \Yii::$app->request->post('favoriteController');

            // se c'è l'id elimino il preferito
            $id = \Yii::$app->request->post('favoriteId');
            if ($id) {
                $favorite = Favorite::findOne($id);
                if ($favorite) {
                    Favorite::deleteAll(['id' => $id]);
                    return [
                        'action' => 'unselected',
                        'id' => $id
                    ];
                }
            }

            //se trovo il preferito tramite url lo elimino altrimetni lo aggiungo
            $favorite = Favorite::find()
                ->andWhere(['user_id' => \Yii::$app->user->id])
                ->andWhere(['url' => $url])->one();

            if ($favorite) {
                Favorite::deleteAll(['user_id' => \Yii::$app->user->id, 'url' => $url]);
                return [
                    'action' => 'unselected'
                ];
//                Favorite::deleteAll(['user_id' => \Yii::$app->user->id, 'url' => $url]);
            } else {
                $favorite = new Favorite();
                $favorite->url = $url;
                $favorite->title = $title;
                $favorite->module = $contentModule;
                $favorite->controller = $contentController;
                $favorite->user_id = \Yii::$app->user->id;
                $favorite->content_classname = $classname;
                $favorite->content_id = $contentId;
                $favorite->save(false);
                return [
                    'action' => 'selected',
                    'bfore' => $favorite->url == $url ? 'uguali' : 'diversi',
                    'after' => urldecode($favorite->url),
                ];
            }

        }
        return false;
    }

    public function actionDeleteFavoriteUrl($id)
    {
        $favorite = Favorite::findOne($id);
        if ($favorite) {
            Favorite::deleteAll(['id' => $id]);
            \Yii::$app->session->addFlash('success', "Preferito eliminato correttamente");
        }
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionFavoriteListAjax()
    {
        $favorites = Favorite::find()->andWhere(['user_id' => \Yii::$app->user->id])->all();
        return $this->renderAjax('@vendor/open20/amos-favorites/src/widgets/views/_list_favorites_items', [
            'favorites' => $favorites
        ]);
    }
}
