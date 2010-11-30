<?php
// $Id$
?>

<div id="node-<?php print $node->nid; ?>" class="wrapper">

<div class="single node-response <?php print $node_classes; ?>">
  <div class="inner">


	<?php if ($responder): ?>
		<?php print $responder ?>
    <?php endif; ?>
    <?php if ($page == 0): ?>
    <h2 class="title"><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a> </h2>
    <?php endif; ?>
    <?php if ($node_top && !$teaser): ?>
    <div id="node-top" class="node-top row nested">
      <div id="node-top-inner" class="node-top-inner inner">
        <?php print $node_top; ?>
      </div><!-- /node-top-inner -->
    </div><!-- /node-top -->
    <?php endif; ?>

    <div class="content clearfix">
      <?php print $content ?>
    </div>

	<?php if (count($taxonomy)): ?>
		<div class="taxonomy">
			<?php print the_new_everyday_taxonomy_links($node, 4); ?>
		</div>
	<?php endif; ?>

    <?php if ($links): ?>
    <div class="links">
      <?php print $links; ?>
    </div>
    <?php endif; ?>
  </div><!-- /inner -->

  <?php if ($node_bottom && !$teaser): ?>
  <div id="node-bottom" class="node-bottom row nested">
    <div id="node-bottom-inner" class="node-bottom-inner inner">
      <?php print $node_bottom; ?>
    </div><!-- /node-bottom-inner -->
  </div><!-- /node-bottom -->
  <?php endif; ?>

</div>
</div><!-- /node-<?php print $node->nid; ?> -->