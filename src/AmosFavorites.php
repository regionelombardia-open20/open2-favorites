<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\favorites
 * @category   CategoryName
 */

namespace open20\amos\favorites;

use open20\amos\core\module\AmosModule;
use open20\amos\core\module\ModuleInterface;

/**
 * Class AmosFavorites
 * @package open20\amos\favorites
 */
class AmosFavorites extends AmosModule implements ModuleInterface
{
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
    
    public $newFileMode = 0666;
    
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
     * @return string
     */
    public static function getModuleName()
    {
        return 'favorites';
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::setAlias('@open20/amos/' . static::getModuleName() . '/controllers/', __DIR__ . '/controllers/');
        // custom initialization code goes here
        \Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php'));
    }
    
    /**
     * @inheritdoc
     */
    public function getWidgetGraphics()
    {
        return NULL;
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
