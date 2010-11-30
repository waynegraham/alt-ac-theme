<?php
// $Id$

/**
 * @file
 * This template outputs individual thumbnails in Image Browser's browser.
 *
 * Available variables:
 *  string $thumbnail
 *  string $path
 *  string $title
 */
?>
<span class="individualThumbnails">
 <a href="<?php print $path; ?>" title="<?php print $title; ?>"><img src="<?php print $thumbnail; ?>" alt="<?php print $title; ?>" /></a>
 <span class="title"><?php print truncate_utf8( $title, 20, TRUE, TRUE ); ?></span>
</span>