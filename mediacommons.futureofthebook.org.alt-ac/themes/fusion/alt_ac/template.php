<?php
// $Id$: template.php

/**
 * Add CSS and JS files.
 */

$modal = 0;

if ( !empty( $_GET['modal'] ) ) {
  $modal = $_GET['modal'];
}

if ( $modal == 0 ) {
  $theme_path = path_to_theme();
  drupal_add_css($theme_path . '/css/jScrollPane.css');
  drupal_add_js($theme_path  . '/js/jquery.easing.1.3.js');
  drupal_add_js($theme_path  . '/js/jquery.mousewheel.js');
  drupal_add_js($theme_path  . '/js/jquery.em.js');
  drupal_add_js($theme_path  . '/js/jquery.scrollfollow.js');
  drupal_add_js($theme_path  . '/js/jScrollPane.js');
  drupal_add_js($theme_path  . '/js/swfobject.js');
  drupal_add_js($theme_path  . '/js/truncate.js');
  drupal_add_js($theme_path  . '/js/the_new_everyday.js');
}

/**
 * Override of the Search Box for d6
 * first, select the form ID
 */

function alt_ac_theme() {
  return array(
    // The form ID.
    'search_theme_form' => array(
      // Forms always take the form argument.
      'arguments' => array('form' => NULL),
    ),
  );
}
  /**
   * Theme override for search form.
   * The function is named themename_formid.
   * more infos : <a href="http://www.lullabot.com/articles/modifying-forms-5-and-6" title="http://www.lullabot.com/articles/modifying-forms-5-and-6" rel="nofollow">http://www.lullabot.com/articles/modifying-forms-5-and-6</a>   * Here is where you can modify the selected form
   */
  function alt_ac_search_block_form($form) {
    // deactivate the title of the form
    unset($form['search_block_form']['#title']);
    $form['search_block_form']['#value'] = t('search');
    $output .= drupal_render($form);
    return $output;
}

function alt_ac_preprocess_page(&$vars) {

  $vars['template_file'] = 'page';

  /* Loading the user menus */

  if (user_access('contribute content')) {
    $um = module_invoke('menu', 'block', 'view', 'menu-menu-logged-in');
  }
  else {
    $um = module_invoke('menu', 'block', 'view', 'menu-anon-menu');
  }
  $vars['user_menu'] = $um['content'];

  /* Loading the search box */
  $sb = module_invoke('search', 'block', 'view', '0');
  $vars['search_box'] = $sb['content'];

}
function alt_ac_preprocess_comment_wrapper(&$vars) {
  if ( $vars['node']->type == 'contributed_pieces' ) {
    if ( $GLOBALS['user']->uid == 0 ) {
      $dest_before = 'destination=' . $vars['node']->path;
      $vars['links_comments'] =  '<span class="login-link">' . l( t('Login'), 'user/login', array('attributes' => array('title' => t('Login to respond'), 'class' => 'login-link'), 'query' => $dest_before)) .  t(' to respond') . '</span>';
    }
    else {
      $vars['links_comments'] = l( t('Respond'), $vars['node']->links['comment_add']['href'], array('attributes' => array('title' => t('Respond'), 'class' => 'respond-button responses')));
    }
  }
}

function alt_ac_preprocess_search_result( &$vars ) {

  // ddebug_backtrace();
  // dprint_r($array)

  $result = $vars['result'];
  $result_node = $result['node'];

  if ( $result_node->type == 'contributed_pieces' ) {
    $div = '<div class="search-result-item search-result-contributed-piece">';
    $vars['type'] = 'contributed-piece';
    if ( !empty( $result_node->ss_thumbnail ) ) {
      // the problem with the image is here.
      $thumbnail = $result_node->ss_thumbnail;
    }
    else {
      $thumbnail = '<img src="'. $base_url . '/' . file_directory_path() . '/contributed-pieces-th/alt-academy-logo-sq.png' . '" />'; // <----- Replace this path with the path to the real placeholder thumbnail
    }
    $div .= '<div class="search-result-thumbnail-contributed-piece">' . $thumbnail . '</div>';
    $div .= '<h4 class="search-result-title">' . l(truncate_utf8($result_node->title, 80, TRUE, TRUE), 'node/' . $result['node']->nid, array('attributes' => array('title' => t("$result_node->title")))) . '</h4>';
    $div .= '<div class="search-result-author-contributed-piece"><span class="contributor-name">Contributed by ' . l($result_node->realname, 'user/' . $result_node->uid, array('attributes' => array('title' => t($result_node->realname), 'class' => 'username')));
    $div .= '<br />' . format_date( $result_node->changed, 'custom', 'F d, Y' ) . '</span>';
    $div .= '<div class="user-picture"><img src="' . alt_ac_get_user_avatar($result_node->uid) . '" /></div>';
    $div .= '</div>';
  }
  if ( $result_node->type == 'response' ) {
    $div = '<div class="search-result-item search-result-response">';
    $div .= '<div class="search-result-author-response"><span class="contributor-name">Response from ' . l($result_node->realname, 'user/'. $result_node->uid, array('attributes' => array('title' => t($result_node->realname), 'class' => 'username'))) . '</span>';
    $div .= '<div class="user-picture"><img src="' . alt_ac_get_user_avatar($result_node->uid) . '" /></div>';
    $div .= '<div class="search-result-timestamp">' . format_date( $result_node->changed, 'custom', 'F d, Y' ) . '</div>';
    $div .= '</div>';
    $div .= '<h5 class="search-result-title">' . l(truncate_utf8($result_node->title, 80, TRUE, TRUE), 'node/' . $result['node']->nid, array('attributes' => array('title' => t("$result_node->title")))) . '</h5>';
  }

  if ( $result_node->comment_count == 1 ) {
    $div .= '<div class="search-result-comment-count">1 Response</div>';
  }
  elseif ( $result_node->comment_count > 1 ) {
    $div .= '<div class="search-result-comment-count">' . $result_node->comment_count . ' Responses</div>';
  }

    $div .= '<div class="search-result-snippet">' . $result['snippet'] . '</div>';
    $div .= '</div>';
    $vars['result_div'] = $div;
    $vars['template_files'][] = 'search-result-contributed-piece';
}

function alt_ac_get_user_avatar($uid) {
  global $base_url;
  $result = db_query('SELECT mail, picture FROM {users} WHERE uid = "%s"', $uid);
  $res_array = db_fetch_array($result);
  if ( count($res_array) > 0 ) {
    if ( count($res_array) > 2 ) {
      watchdog('TNE theme template', 'Found more than one mail entry for a single uid'); //This should never happen
    }
    else {
      if ( strlen($res_array['picture']) ) {
        $picture = _gravatar_get_gravatar(array('mail' => $res_array['mail'], 'default' => $base_url . '/' . $res_array['picture']));
      }
      else {
        $picture = _gravatar_get_gravatar(array('mail' => $res_array['mail']));
      }
    }
  }
  if ( $picture == NULL ) {
    $picture = $base_url . '/' . file_directory_path() . '/contributed-pieces-th/alt-academy-logo-sq.png'; //temporary
  }
  return $picture;
}

function alt_ac_preprocess_node(&$vars) {

  /**
  * Cluster Page Vars
  * Basic Variables
  */

  if ( $vars['node']->type == 'image' ) {
    if ( $vars['node']->images['_original'] &&  module_exists('dltsimageviewer') ) {
      global $base_url;
      $image_url = $base_url . '/' . $vars['node']->images['_original'];
      $vars['j2k_link'] = dltsimageviewer_l( t( 'Full Size with Zoom Functions' ), $image_url );
    }
  }

  if ( $vars['node']->type == 'cluster' ) {
    $vars['curator'] = '<div class="cluster-curator">Curated by ' . l($vars['realname'], 'user/'. $vars['uid'], array('attributes' => array('title' => t($vars['realname']), 'class' => 'username'))) . '</div>';
    $vars['time_period'] = '<h6 class="time-period">'.format_date(strtotime($vars['node']->field_period[0]['value']), 'custom', 'F d, Y' ).'&ndash;'.format_date( strtotime($vars['node']->field_period[0]['value2']), 'custom', 'F d, Y' ).'</h6>';
    $vars['description'] = '<div class="cluster-description">' . truncate_utf8( $vars['node']->field_description[0]['safe'], 500, TRUE, TRUE ) . '</div>';
    if ( !empty( $vars['node']->field_video_embed_link[0]['embed'] ) ) {
      $vars['frontispiece'] = '<div class="cluster-frontispiece">'.$vars['node']->field_video_embed_link[0]['view'].'</div>';
    }
    elseif ( !empty( $vars['node']->field_image[0]['filepath'] ) ) {
      $vars['frontispiece'] = '<div class="cluster-frontispiece"><img src="' . base_path() . $vars['node']->field_image[0]['filepath'] . '" alt="'. $vars['title'] .'" /></div>';
    }

    if ( !empty( $vars['node']->field_contributed_piece ) ) {
      $vars['pieces'] = '<div class="cluster-pieces">';
      foreach ( $vars['node']->field_contributed_piece as $piece ) {
        $piece_node = node_load( $piece['nid'] );
        $vars['pieces'] .= alt_ac_piece_div( $piece_node, 'cluster-page' );
      }
      $vars['pieces'] .= '</div>';
    }
    if ( $vars['node']->field_contributors ) {
      $gravatar_module_path = base_path() . drupal_get_path('module', 'gravatar');
      $vars['contributors'] = '<div class="cluster-contributors-inner">';
      
      // TODO: fix multiple authors
      foreach ( (array)$vars['node']->field_contributors as $cont ) {
			$account = user_load( array( 'uid' => $cont['uid'] ) );
			
			$vars['contributors'] .= '<div class="cluster-contributor">';
			$vars['contributors'] .= '<div class="user-info"><p class="user-name">' . l($account->realname, 'user/'. $account->uid , array('attributes' => array('title' => t($account->realname),'class' => 'username'))). '<br />';
			
			if ( $account->profile_title ) { 
			    $vars['contributors'] .= '<span>' . $account->profile_title . ' at ' . truncate_utf8($account->profile_affiliation, 50, TRUE, TRUE )  . '</span>'; 
			}
			
			$vars['contributors'] .= '</p></div>';
            /* User pictures */
			$vars['contributors'] .= '<div class="user-picture">';
			
			if ($account->picture) {
				$up = theme('user_picture', $account);
				$up = str_replace('files/files', 'files', $up);
				$vars['contributors'] .= $up;
			}
			else {
              //Took out the default param, which for some reason making gravatar return an HTML URL instead of an image.
              // $alt = _gravatar_get_gravatar(array('mail' => $account->mail, 'default' => $gravatar_module_path . '/avatar.png'));
              $alt = _gravatar_get_gravatar(array('mail' => $account->mail));
              $vars['contributors'] .= '<img src="' . $alt . '" alt="" />';
//				$vars['contributors'] .= '<img src="'. $gravatar_module_path .'/avatar.png" alt="" />';
			}
			$vars['contributors'] .= '</div>';
			$vars['contributors'] .= '</div>';
      }
      $vars['contributors'] .= '</div>';
    }


    $ub = module_invoke('tne_blocks', 'block', 'view', 'upcoming_cluster_block');
    $vars['upcoming'] = $ub['content'];

  }
  /*END Cluster Node PreProcessing */

   /* Contributed piece node preprocessing */
   if (preg_match('/(contributed)/i', $vars['node']->type)) {
        $vars['template_file'] = 'node-contributed-piece';
		$authors_string = alt_ac_contributed_by_title( l($vars['realname'], 'user/'. $vars['uid'], array('attributes' => array('title' => t($vars['realname']),'class' => 'username'))), $vars['uid'], $vars['node']->field_additional_authors );
		$authors_photos = alt_ac_contributed_by_photos( str_replace('files/files', 'files', $vars['picture']),  $vars['node']->field_additional_authors );
	    //$authors_description =  alt_ac_contributed_by_description($vars['uid'], $vars['node']->field_additional_authors);
	    //$vars['authors_description'] = alt_ac_description($vars['uid']);
	    //$vars['authors_description'] = alt_ac_contributed_by_description($vars['uid'], $vars['node']->field_additional_authors);
	if(!empty($vars['node']->field_tagline[0]['view'])) {
			$vars['tagline'] = '<h4 class="tagline">'.$vars['node']->field_tagline[0]['view']. '</h4>';
		}
		/*   checking to see if there are two authors with the same deafult image
		if (strpbrk($vars['picture'], 'avatar.png')) {
					//print 'Yes avatar';
				}  */

		if(!empty($vars['node']->field_additional_authors[0]['view'])) {
			$vars['contributor'] = '<div id="cluster-contributor"><div class="contributor-name">Contributed by ' . $authors_string .'<br /><em>'. format_date( $vars['node']->created, 'custom', 'F d, Y' ).'</em></div><div id="contributor-pictures">' . $authors_photos  . '</div></div>';
		} else {
			$vars['contributor'] = '<div id="cluster-contributor"><div class="contributor-name">Contributed by ' . $authors_string .'<br /><em>'. format_date( $vars['node']->created, 'custom', 'F d, Y' ).'</em></div><div id="contributor-picture">' . $authors_photos  . '</div></div>';
		}

		if ( !empty( $vars['node']->field_cluster[0]['view'] )) {
		  $vars['parent_cluster'] = '<span class="part-cluster">' . t('Part of the Cluster') . ':</span><h4>'. $vars['node']->field_cluster[0]['view'] . '</h4>';
          $vars['navigation'] = alt_ac_cluster_navigator($vars['node']->field_cluster[0]['nid'], $vars['nid']);

		}

		if ($vars['comment_count'] == 0) {
          if ($GLOBALS['user']->uid == 0) {
            $dest_before = 'destination='.$vars['node']->path;
            $vars['respond'] =  '<span class="login-link">'.l('Login', 'user/login' , array('attributes' => array('title' => t('Login to respond'),'class' => 'login-link'), 'query' => $dest_before)).  ' to respond</span>';
          }
          else {
            $vars['respond'] =  l('Respond', $vars['node']->links['comment_add']['href'], array('attributes' => array('title' => t('Respond'),'class' => 'respond-button responses')));
          }
		}
		else {
		  $vars['respond'] = '';
		}
	}
    /*END Contributed piece Node PreProcessing */

	/* Response node preprocessing */
   if (preg_match('/(response)/i', $vars['node']->type)) {
     $vars['template_file'] = 'node-response';
	 $vars['responder'] = '<div class="responder"><div class="responder-picture">' . $vars['picture'] . '</div><span>Response from <br/>' . l($vars['realname'], 'user/'. $vars['uid'] , array('attributes' => array('title' => t($vars['realname']),'class' => 'username'))) . '</span><br /><span class="responder-time">' . format_date( $vars['created'], 'custom', 'F d, Y' ) . '</span></div>';
	foreach($vars['node']->links as $link) {
		$linkz .= '<li>'.l( t($link['title']), $link['href'], array('attributes' => array('title' => t($link['title']), 'class' => 'responses'), 'query' => $link['query'])).'</li>';
	}
	$vars['links'] = '<ul class="links inline">'.$linkz.'</ul>';

  }

}

function alt_ac_piece_div( $piece_node, $location ) {
  $div = '';
  $piece_account = user_load( array( 'uid' => $piece_node->uid ) );
	$authors_string = alt_ac_contributed_by( l($piece_account->realname, 'user/'. $piece_node->uid , array('attributes' => array('title' => t($piece_account->realname), 'class' => 'username'))), $piece_node->field_additional_authors );
  if ($piece_node->comment_count == 0) {
	 $comment_count = '';
	} elseif ($piece_node->comment_count == 1){
	    $comment_count = '1 Response';
	} else {
	    $comment_count = $piece_node->comment_count .' Responses';
	}
  if ( $location == 'cluster-page') {

    $div .= '<div class="cluster-piece-item">';

	if ( !empty( $piece_node->field_thumbnail[0]['filepath'] ) ) {
	  $piece_thumbnail_path = $piece_node->field_thumbnail[0]['filepath'];
	}
	else {
      $piece_thumbnail_path = '/' . file_directory_path() . '/contributed-pieces-th/alt-academy-logo-sq.png'; // <----- Replace this path with the path to the real placeholder thumbnail
	}
 	$div .= '<div class="cluster-piece-image">' . '<img src="' . base_path() . $piece_thumbnail_path . '" alt="'. check_plain($piece_node->title) .'" />' . '</div>';
    $div .= '<h4 class="class">' . l(t(truncate_utf8($piece_node->title, 80, TRUE, TRUE)), 'node/' . $piece_node->nid, array('attributes' => array('title' => t("$piece_node->title")))) . '</h4>';
    $div .= '<p><span class="cluster-piece-contributor">' . $authors_string  . '</span>';
    $div .= '<span class="cluster-piece-revision">' . format_date( $piece_node->revision_timestamp, 'custom', 'F d, Y' ) . '</span></p>';
    $div .= '<div class="cluster-piece-comments">' . $comment_count . '</div>';
    $div .= '</div>';
  }
  return $div;
}

function alt_ac_realname($key) {
		$uid = $vars['node']->field_contributors[$key]['uid'];
		$account = user_load(array('uid' => $uid));
		$realname = check_plain($account->profile_name).$key;
		$realnames[$uid] = $realname;
	print $realname;
}
// Theme the taxonomy links From http://drupal.org/node/133223#comment-634019
function alt_ac_taxonomy_links($node, $vid) {

//if the current node has taxonomy terms, get them
  if (count($node->taxonomy)):
    $tags = array();
     foreach ($node->taxonomy as $term) {
       if ($term->vid == $vid):
          $tags[] = l($term->name, taxonomy_term_path($term), array('attributes' => array('rel' => 'tag', 'title' => check_plain($term->name))));
          endif;
}
    if ($tags):
//get the vocabulary name and name it $name
        //$vocab = taxonomy_get_vocabulary($vid); D5 only
        $vocab = taxonomy_vocabulary_load($vid);
        $name = $vocab->name;
        $output .= '<ul class="' . $vocab->name . '"><li><span>' . $vocab->name . ':</span>&#160;';
        $output .= implode(' |  </li><li>', $tags);
        $output .= '</li></ul>';
    endif;

  endif;

     if ($output):
       return $output;
     endif;
}

function alt_ac_contributed_by( $first_author_name, $additional_authors ) {
	$all_authors = $first_author_name;
	if ( !empty( $additional_authors[0]['uid'] ) ) {
	 $all_authors .= ', ';

		foreach( $additional_authors as $addauth ) {
			$account = user_load( array( 'uid' => $addauth['uid']) );
			$all_authors .=  l($account->realname, 'user/'. $addauth['uid'] , array('attributes' => array('title' => t($account->realname),'class' => 'username')));
			if ( next( $additional_authors )==true ) $all_authors .= ', ';
		}
	 }
	return $all_authors;
}


/**
 * 
 */
function alt_ac_contributed_by_title( $first_author_name, $first_author_id, $additional_authors ) {
	// $all_authors = $first_author_name.' '.alt_ac_description($first_author_id);
	
  $all_authors = $first_author_name;

  // hacky way of doing this, but the default case is going to do this most of the time
  if(empty($additional_authors[0]['uid'])) {
    $all_authors .= ' ' . alt_ac_description($first_author_id);
    return $all_authors;
  }
  
 // if ( !empty( $additional_authors[0]['uid'] ) ) {

	 //$all_authors .= ',<br />';
	 $all_authors .= ', ';
    
		foreach( $additional_authors as $addauth ) {
			$account = user_load( array( 'uid' => $addauth['uid']) );
			$all_authors .=  l($account->realname, 'user/'. $addauth['uid'] , array('attributes' => array('title' => t($account->realname),'class' => 'username')));
			
			// removed to allow more authors to show up on a line
			
			// if(!empty($account->profile_title)){
			//               $all_authors .=  ' '.$account->profile_title.' at ';
			//           }
			//           $all_authors .=  $account->profile_affiliation;
			if ( next( $additional_authors )==true ) $all_authors .= ', ';
		}
	// }
	return $all_authors;
}
function alt_ac_description($author_id) {
	$account = user_load( array( 'uid' => $author_id) );
	//print_r($account);
	if(!empty($account->profile_title)){
		$author_description =  $account->profile_title.' at ';
	}
	$author_description .=  $account->profile_affiliation;
	if(!empty($account->profile_about)){
		//$author_description .=  '<br />'.$account->profile_about;
	}

	  return $author_description;
}

function alt_ac_contributed_by_photos($first_author, $additional_authors ) {
	$all_authors_photos = $first_author;
	if ( !empty( $additional_authors[0]['uid'] ) ) {
		foreach( $additional_authors as $addauth ) {
			$account = user_load( array( 'uid' => $addauth['uid']) );
			$up = theme('user_picture', $account);
			$all_authors_photos .= str_replace('files/files', 'files', $up);
		}
	 }
	return $all_authors_photos;
}

function alt_ac_cluster_navigator($parent_node_id, $current_piece_id) {

//Load the parent node
$parent_node = node_load($parent_node_id);
//Count the total number of pieces
$total = count($parent_node->field_contributed_piece);
//Create a empty Variable
$navigation;
	foreach($parent_node->field_contributed_piece as $piece) {
		//set up a counter
		$counter += count($piece);
		//find the position of curren t node in the parent node array
		if($piece['nid'] == $current_piece_id){
			$navigation .= '<div id="navigator">';
			//check to see if it is the first article
			if($counter > 1){
				$navigation .=  l('', 'node/'. $parent_node->field_contributed_piece[$counter -2]['nid'],  array('html' => TRUE, 'attributes' => array('title' => t('Previous'), 'class' => 'previous')));
			} else {
				 $navigation .= '<span class="start">&nbsp;</span>';
			}
		    //add custom css classes to the counter part
			if(($counter - $total) == 0) {
				$position = '-end';
			} elseif($counter == 1) {
				$position = '-start';
			}  else {
				 $position = '-'.$counter;
			}
			$navigation .=  '<span class="navigator-counter counter'.$position.'">'.$counter .' of '. $total.'</span>';
			//check to see if it is the last article
			if($counter != ($total)){
	        	$navigation .=  l('', 'node/'. $parent_node->field_contributed_piece[$counter]['nid'], array('html' => TRUE, 'attributes' => array('title' => t('Next'), 'class' => 'next')));
			} else {
				 $navigation .= '<span class="end">&nbsp;</span>';
			}
			$navigation .= '</div>';
		}
	}
	   return $navigation;
}

/*
tne_preprocess_node(&$vars) {
//  drupal_set_message('<pre>' . print_r(get_defined_vars(),TRUE) . '</pre>'); // print what variables are available
//  drupal_set_message('<pre>' . print_r($vars['node']->links, TRUE . '</pre>')); // print what links are available

  // some sanity checks
  if ($vars['is_front']) {
    if ($vars['node']->comment) {
      if (user_access('access comments')) {
        // get the comment count
        $all = comment_num_all($vars['node']->nid);
        if ($all) {
          if ($vars['node']->links[comment_comments]['title']) {
            $vars['node']->links[comment_comments]['title'] = $all;
          }
        }
      }
    }
  }
  // update the themed links
  $vars['links'] = theme_links($vars['node']->links, array('class' => 'links inline'));
}*/
