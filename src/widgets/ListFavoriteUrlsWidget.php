<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-favorites
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

namespace open20\amos\favorites\widgets;

use open20\amos\favorites\AmosFavorites;
use open20\amos\favorites\assets\FavoritesAsset;
use open20\amos\favorites\models\Favorite;
use yii\base\Widget;

class ListFavoriteUrlsWidget extends Widget
{
    public $enableTable = false;

/*
 * I seguenti parametri servono per compatibilità con la sidebar redattore, senza non è possibile personalizzare testo e icona del widget
 * attraverso il model cms-dash-sidebar-item
 */
    public $sidebarTitle = 'Segnalibri';

    public $sidebarMousehoverDescription = 'Segnalibri';

    public $sidebarIcon = 'bookmark';

    public $noFavouriteLabel = '';

    public function init()
    {
        parent::init();
    }

    public function run()
    {

        $module = \Yii::$app->getModule('favorites');
        if ($module->enableFavoritesUrl) {
            $this->registerAsset();

            if(empty($this->noFavouriteLabel)){
               $this->noFavouriteLabel =  AmosFavorites::t('amosfavorites', 'Attualmente non hai selezionato dei preferiti');
            }


            $favorites = Favorite::find()->andWhere(['user_id' => \Yii::$app->user->id])->all();
            if($this->enableTable){
                return $this->render('table_favorites_urls', [
                    'favorites' => $favorites,
                    'noFavouriteLabel' => $this->noFavouriteLabel
                ]);
            }else {
                return $this->render('list_favorites_urls', [
                    'favorites' => $favorites,
                    'listTitle' => $this->sidebarTitle,
                    'listDescription' => $this->sidebarMousehoverDescription,
                    'listIcon' => $this->sidebarIcon
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