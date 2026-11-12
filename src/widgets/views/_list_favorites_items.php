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
 */
use yii\helpers\Html;
use open20\amos\favorites\AmosFavorites;

?>
<ul class="link-list">
    <?php if (count($favorites) > 0) { ?>
        <?php foreach ($favorites as $favorite) {
            $moduleIcon = "<strong>" . $favorite->getFavoriteType() . "</strong>";
            ?>
            <li>
                <?= Html::a("<span class='mdi mdi-bookmark icon-preferiti'></span><span class='label-text'>{$moduleIcon}{$favorite->title}</span>",
                    urldecode($favorite->url),
                    [
                        'class' => 'list-item',
                        'title' => $favorite->getFavoriteType() . $favorite->title,
                        'data-toggle' => 'tooltip',
                        'data-html' => 'true',
                        'target' => '_blank'
                    ]) ?>
            </li>
        <?php } ?>
    <?php } else { ?>
        <li>
            <span class='no-favorites label-text'><?= AmosFavorites::t('amosfavorites', 'Nessun segnalibro aggiunto') ?></span>
        </li>
    <?php } ?>
</ul>