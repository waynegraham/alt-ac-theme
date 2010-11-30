<?php
// $Id$
?>

<div id="node-<?php print $node->nid; ?>" class="wrapper">

<div id="node-pane" class="node <?php print $node_classes; ?>">
  <div class="inner">
   

    <?php if ($page == 0): ?>
    <h2 class="title"><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h2>
    <?php endif; ?>
    <?php if ($node_top && !$teaser): ?>
    <div id="node-top" class="node-top row nested">
      <div id="node-top-inner" class="node-top-inner inner">
        <?php print $node_top; ?>
      </div><!-- /node-top-inner -->
    </div><!-- /node-top -->
    <?php endif; ?>
      <?php if ($tagline): ?>
		<?php print $tagline ?>
    <?php endif; ?>
	<?php if ($contributor): ?>
		<?php print $contributor ?>
    <?php endif; ?> 
    <?php if ($parent_cluster): ?>  
        <div id="parent-cluster">
    		<?php print $parent_cluster ?>
			<?php print $navigation ?>
		</div>
    <?php endif; ?>          
	<div class="node-top-section"></div>    

    <div class="content clearfix">
      <?php print $content ?>
    </div>

		<?php if (count($taxonomy)): ?>
			<div class="taxonomy">
				<?php print the_new_everyday_taxonomy_links($node, 4); ?>
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

<?php if ($response_block): ?>
<div id="responses-pane">
  <?php print $response_block ?>
</div>

<?php endif; ?>

</div>
<!-- /node-<?php print $node->nid; ?> -->  

<?php if ($respond): ?>
    <div class="respond-button-block">
      <?php print $respond; ?>
    </div>
    <?php endif; ?>