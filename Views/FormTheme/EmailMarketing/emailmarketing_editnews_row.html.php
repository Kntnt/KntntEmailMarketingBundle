<?php

/*
 * @copyright   Kntnt Sweden AB
 * @author      Thomas Barregren
 *
 * @link        https:/www.kntnt.se/
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>

<div class="alert alert-info">
  <?php echo $view['translator']->trans('kntnt.emailmarketing.list.update'); ?>
</div>
<div class="row">
  <div class="col-md-8">
    <?php echo $view['form']->row($form['list']); ?>
  </div>
</div>
