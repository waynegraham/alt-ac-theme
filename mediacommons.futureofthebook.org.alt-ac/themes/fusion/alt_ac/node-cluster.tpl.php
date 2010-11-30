<?php
// $Id$
?>
<div id="node-<?php print $node->nid; ?>" class="node <?php print $node_classes; ?>">


    <div class="cluster">

    <?php if ($page == 0): ?>
    <h3 class="title"><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h3>
    <?php endif; ?> 
	  <?php print $curator ?>
      <?php print $time_period ?>
      <?php print $frontispiece ?> 
      <?php print $description ?>

	<?php if (count($taxonomy)): ?>
        <div class="taxonomy">
			<?php print alt_ac_taxonomy_links($node, 4); ?>
      	</div>
   <?php endif; ?>

    </div><!-- End Cluster -->    

	    <div class="cluster-pieces-section">
			<h3 class="cluster-pieces-title">Contributed Pieces</h3>
	    <?php print $pieces ?>
	    </div>


    <div class="cluster-contributors-section">
  		<h3 class="cluster-contributors-title">Contributors and Participants</h3>
    	<?php print $contributors ?>
    </div> 

     <div class="clear"></div>


      







  <?php if ($node_bottom && !$teaser): ?>
  <div id="node-bottom" class="node-bottom row nested">
    <div id="node-bottom-inner" class="node-bottom-inner inner">
      <?php print $node_bottom; ?>
    </div><!-- /node-bottom-inner -->
  </div><!-- /node-bottom -->
  <?php endif; ?>
</div><!-- /node-<?php print $node->nid; ?> -->
