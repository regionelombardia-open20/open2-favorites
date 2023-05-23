<?php

namespace open20\amos\favorites\widgets;

use open20\amos\favorites\assets\FavoritesAsset;
use open20\amos\favorites\models\Favorite;
use yii\base\Widget;

class ListFavoriteUrlsWidget extends Widget
{
    public $enableTable = false;

    public function init()
    {
        parent::init();
    }

    public function run()
    {

        $module = \Yii::$app->getModule('favorites');
        if ($module->enableFavoritesUrl) {
            $this->registerAsset();

            $favorites = Favorite::find()->andWhere(['user_id' => \Yii::$app->user->id])->all();
            if($this->enableTable){
                return $this->render('table_favorites_urls', [
                    'favorites' => $favorites
                ]);
            }else {
                return $this->render('list_favorites_urls', [
                    'favorites' => $favorites
                ]);
            }
        }
        return '';
    }



    /**
     * @return void
     */
    public function registerAsset(){
        FavoritesAsset::register($this->view);
    }

}