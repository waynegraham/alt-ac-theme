<div class='form form-layout-default clear-block'>
  <div class='column-main'>
    <div class='column-wrapper clear-block'>
      <?php print drupal_render($form); ?>
      <div class='buttons'>
        <?php print rubik_render_clone($buttons); ?>
      </div>
    </div>
  </div>
  <div class='column-side'>
    <div class='column-wrapper clear-block'>
      <div class="reference_by_cluster_block">
        <?php
          $block = module_invoke('tne_blocks', 'block', 'view', 'reference_by_cluster_block');
          if ( $block['content'] ) {
            print t('<h2>@title:</h2>', array('@title' => $block['subject']));
            print $block['content'];
          }
        ?>     
     </div>
      <div class='operations'>
        <?php print drupal_render($sidebar); ?>
      </div>      
      <div class='buttons'>
        <?php print drupal_render($buttons); ?>
      </div>
    </div>
  </div>
  <?php if (!empty($footer)): ?>
    <div class='column-wrapper clear-block'>
      <?php print drupal_render($footer); ?>
    </div>
  <?php endif; ?>
</div>