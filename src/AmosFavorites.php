<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\favorites
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

namespace open20\amos\favorites;

use open20\amos\core\module\AmosModule;
use open20\amos\core\module\ModuleInterface;
use open20\amos\favorites\assets\AmosFavoriteAsset;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class AmosFavorites
 * @package open20\amos\favorites
 */
class AmosFavorites extends AmosModule implements ModuleInterface
{
    /**
     * @inheritdoc
     */
    public static $CONFIG_FOLDER = 'config';

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'open20\amos\favorites\controllers';

    /**
     * @inheritdoc
     */
    public $name = 'Favorites';

    /**
     * @var array $modelsEnabled
     */
    public $modelsEnabled = [];

    /**
     * @var bool
     */
    public $enableFavoritesUrl = true;

    /**
     * @var array
     */
    public $modelsEnabledFavoritesUrl = [
        'open20\amos\attachments\models\AttachDatabankFile',
        'open20\amos\attachments\models\AttachGalleryImage'
    ];




    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        AmosFavoriteAsset::register(Yii::$app->view);

        Yii::setAlias('@open20/amos/' . self::getModuleName() . '/controllers', __DIR__ . '/controllers/');

        /*
         * Configuration: merge default module configurations loaded from config.php
         * with module configurations set by the application
         */
        $config = require(__DIR__ . DIRECTORY_SEPARATOR . self::$CONFIG_FOLDER . DIRECTORY_SEPARATOR . 'config.php');

        Yii::configure($this, ArrayHelper::merge($config, $this));
    }
    /**
     * @return string
     */
    public static function getModuleName()
    {
        return 'favorites';
    }

    /**
     * @inheritdoc
     */
    public function getWidgetGraphics()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getWidgetIcons()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultModels()
    {
        return [];
    }
}
