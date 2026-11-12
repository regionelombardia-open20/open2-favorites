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

namespace open20\amos\favorites\models\base;

use Yii;

/**
 * This is the base-model class for table "favorite".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $url
 * @property string $module
 * @property string $controller
 * @property string $content_classname
 * @property integer $content_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 */
class  Favorite extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'favorite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'content_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['controller', 'module','created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['title', 'url', 'content_classname'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('amosfavorites', 'ID'),
            'title' => \Yii::t('amosfavorites', 'Title'),
            'url' => \Yii::t('amosfavorites', 'Url'),
            'content_classname' => \Yii::t('amosfavorites', 'Classname'),
            'content_id' => \Yii::t('amosfavorites', 'Content id'),
            'created_at' => \Yii::t('amosfavorites', 'Created at'),
            'updated_at' => \Yii::t('amosfavorites', 'Updated at'),
            'deleted_at' => \Yii::t('amosfavorites', 'Deleted at'),
            'created_by' => \Yii::t('amosfavorites', 'Created by'),
            'updated_by' => \Yii::t('amosfavorites', 'Updated at'),
            'deleted_by' => \Yii::t('amosfavorites', 'Deleted at'),
        ];
    }
}
