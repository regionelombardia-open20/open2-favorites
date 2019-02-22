<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\favorites\widgets
 * @category   CategoryName
 */

namespace lispa\amos\favorites\widgets;

use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\favorites\AmosFavorites;
use lispa\amos\favorites\exceptions\FavoritesException;
use lispa\amos\notificationmanager\AmosNotify;
use yii\base\Widget;
use yii\web\View;

/**
 * Class FavoriteWidget
 *
 * Widget to show the favorite icon.
 *
 * @package lispa\amos\favorites\widgets
 */
class FavoriteWidget extends Widget
{
    public $layout = '{beginContainerSection}{favoriteButton}{endContainerSection}';
    
    /**
     * @var \lispa\amos\core\record\Record $model
     */
    public $model;
    
    /**
     * @var array $containerOptions Default to ['id' => 'favorite-container-MODEL_ID']
     */
    public $containerOptions = [];
    
    /**
     * @var bool $isAlreadyFavorite
     */
    private $isAlreadyFavorite = false;
    
    /**
     * @throws FavoritesException
     */
    public function init()
    {
        parent::init();
        
        if (is_null($this->model)) {
            throw new FavoritesException(AmosFavorites::t('amosfavorites', 'FavoriteWidget: model required'));
        }
    }
    
    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->model->isNewRecord) {
            return '';
        }
    
        /** @var AmosFavorites $favorites */
        $favorites = \Yii::$app->getModule('favorites');
        if (!isset($favorites->modelsEnabled) || !in_array($this->model->className(), $favorites->modelsEnabled)) {
            return false;
        }
        
        $this->initDefaultOptions();
        
        $content = preg_replace_callback("/{\\w+}/", function ($matches) {
            $content = $this->renderSection($matches[0]);
            
            return $content === false ? $matches[0] : $content;
        }, $this->layout);
        
        $this->registerWidgetJs();
        
        return $content;
    }
    
    /**
     * Set default options values.
     */
    private function initDefaultOptions()
    {
        $this->containerOptions['id'] = 'favorite-container-' . $this->model->id;
        $this->containerOptions['class'] = 'pull-left favorites-container';
    }
    
    /**
     * Return the container id.
     * @return string
     */
    public function getContainerId()
    {
        return $this->containerOptions['id'];
    }
    
    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     * @param string $name the section name, e.g., `{summary}`, `{items}`.
     * @return string|boolean the rendering result of the section, or false if the named section is not supported.
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{beginContainerSection}':
                return $this->renderBeginContainerSection();
            case '{favoriteButton}':
                return $this->favoriteButton();
            case '{endContainerSection}':
                return $this->renderEndContainerSection();
            default:
                return false;
        }
    }
    
    /**
     * This method render the beginning part of the container.
     * @return string
     */
    protected function renderBeginContainerSection()
    {
        $sectionContent = Html::beginTag('div', $this->containerOptions);
        return $sectionContent;
    }
    
    /**
     * Method that render the section of the comment container.
     * @return string
     */
    public function favoriteButton()
    {
        /** @var AmosNotify $notify */
        $notify = \Yii::$app->getModule('notify');
        $this->isAlreadyFavorite = $notify->isFavorite($this->model, \Yii::$app->user->id);
        $button = Html::a(AmosIcons::show('star', ['class' => 'am-2', 'id' => $this->favoriteIconId()]), null, [
            'title' => self::favoriteBtnTitle($this->isAlreadyFavorite),
            'id' => $this->favoriteBtnId()
        ]);
        return $button;
    }
    
    /**
     * This method render the end part of the container.
     * @return string
     */
    protected function renderEndContainerSection()
    {
        $sectionContent = Html::endTag('div');
        return $sectionContent;
    }
    
    /**
     * Return the favorite button title.
     * @return string
     */
    public static function favoriteBtnTitle($isAlreadyFavorite = false)
    {
        return ($isAlreadyFavorite ?
            AmosFavorites::t('amosfavorites', 'Remove favorite') :
            AmosFavorites::t('amosfavorites', 'Add to favorites')
        );
    }
    
    /**
     * Return the favorite button ID.
     * @return string
     */
    private function favoriteBtnId()
    {
        return 'favorite-btn-id-' . $this->model->id;
    }
    
    /**
     * Return the favorite icon ID.
     * @return string
     */
    private function favoriteIconId()
    {
        return 'favorite-icon-id-' . $this->model->id;
    }
    
    /**
     * This method registers all widget javascript.
     */
    private function registerWidgetJs()
    {
        $alreadyFavorite = ($this->isAlreadyFavorite ? 1 : 0);
        $js = "
        var disableFavoriteClick" . $this->model->id . " = 0;
        $('#" . $this->favoriteBtnId() . "').on('click', function(event) {
            if (disableFavoriteClick" . $this->model->id . " == 1) {
                return false;
            } else {
                disableFavoriteClick" . $this->model->id . " = 1;
            }
            event.preventDefault();
            var params = {};
            params.className = '" . addslashes($this->model->className()) . "';
            params.id = " . $this->model->id . ";
            $.ajax({
                url: '/favorites/favorite/favorite',
                data: params,
                type: 'post',
                dataType: 'json',
                complete: function (jjqXHR, textStatus) {
                    disableFavoriteClick" . $this->model->id . " = 0;
                },
                success: function (response) {
                    if (response.success == 1) {
                        if (response.nowFavorite == 1) {
                            $('#" . $this->favoriteIconId() . "').addClass('favorite');
                        } else if (response.nowNotFavorite == 1) {
                            $('#" . $this->favoriteIconId() . "').removeClass('favorite');
                        }
                        $('#" . $this->favoriteBtnId() . "').prop('title', response.favoriteBtnTitle);
                    }
                    alert(response.msg);
                },
                error: function (response) {
                    alert('Favorite AJAX error');
                }
            });
            return false;
        });
        
        $('#" . $this->favoriteBtnId() . "').prop('title', '" . self::favoriteBtnTitle($this->isAlreadyFavorite) . "');
        if (" . $alreadyFavorite . " == 1) {
            $('#" . $this->favoriteIconId() . "').addClass('favorite');
        } else {
            $('#" . $this->favoriteIconId() . "').removeClass('favorite');
        }
        ";
        \Yii::$app->view->registerJs($js, View::POS_READY);
    }
}
