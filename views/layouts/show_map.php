<!DOCTYPE html>
<html>
    <?
    require "head_normal.php";
  ?>
  <body>
    <div data-role="page" id="<?= $page_id ?: '' ?>" >
      <?= $this->render_partial('layouts/side_menu') ?>
      <div data-role="header"  data-theme="<?=TOOLBAR_THEME ?>">
        <?= $this->render_partial('layouts/side_menu_link') ?>

        <h1><?= $page_title ?: 'Stud.IP' ?></h1>
        <a href="javascript:history.back()" class="externallink" data-ajax="false" data-icon="delete" data-iconpos="notext" data-theme="d"></a>
      </div><!-- /header -->
      
      <div data-role="content">
        <?= $content_for_layout ?>
      </div><!-- /content -->
  </body>
</html>

