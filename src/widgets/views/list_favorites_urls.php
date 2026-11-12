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

/**
 * @var $favorites
 * @var $listIcon
 * @var $listTitle
 * @var $listDescription
 */

use yii\helpers\Html;
use \open20\amos\favorites\AmosFavorites;

$labelCaricamento = AmosFavorites::t('amosfavorites', "Caricamento ...");
$this->registerJsVar('labelCaricamento', $labelCaricamento);
$js = <<<JS
$('#open-dropdown-favorites').click(function(){
    $('#favorite-list-id').html('<span class="label-caricamento">'+labelCaricamento+'</span>');
    $.ajax({
        url: '/favorites/favorite/favorite-list-ajax',
        type: 'get',
        success: function (data) {
            $('#favorite-list-id').html(data);
        }
    });
});

JS;
$this->registerJs($js);
?>

<div class="container-list-favorites">
    <div class="dropdown dropright">
        <a id="open-dropdown-favorites" href="#" class="nav-item-link" role="button" id="dropdownMenuDropright"
           data-toggle="dropdown"
           aria-haspopup="true" aria-expanded="false"
           title="<?= AmosFavorites::t('amosfavorites', $listDescription) ?>">
            <?php if ($listIcon) { ?>
                <span class="mdi mdi-<?= $listIcon ?> icon-sidebar"></span>
            <?php } ?>
            <span class="nav-label-link">
                <?= AmosFavorites::t('amosfavorites', $listTitle) ?>
            </span>
            <span class="mdi mdi-chevron-right icon-expand icon"></span>
        </a>
        <div id="container-favorite-list-id" class="dropdown-menu dark dropdown-sidebar"
             aria-labelledby="dropdownMenuDropright">
            <div id="favorite-list-id" class="link-list-wrapper">
                <span class="label-caricamento"><?= $labelCaricamento ?></span>
            </div>
        </div>
    </div>
</div>
