<?php
/*
Plugin Name: MWT-SEO Social Tags
Plugin URI: https://www.minwt.com
Description: 自動在頁面中加入社群標籤，包含Facebook、Twitter、Google+
Version: 1.1
Author: Minggo Zhou
Author URI: http://www.minwt.com/
*/

add_action('wp_head', 'mwt_add_Social_tags');
function mwt_add_Social_tags() {
  if (!is_single()) {
    return;
  }
	global $post;
  $ID            = get_the_ID();
  $title         = mwt_getTitle();
  $desc          = mwt_getDesc();
  $url           = mwt_getUrl();
  $sitename      = esc_attr(get_bloginfo('name'));
  $image         = mwt_getImage();
  $published     = get_the_date('c');
  $modified      = get_the_modified_date('c');
  $author        = get_author_posts_url($post->post_author);

  echo '
  <!-- mwt OG TAG -->
  <!-- Facebook og tag -->
  <meta property="og:type" content="article" />
  <meta property="og:title" content="'.$title.'" />
  <meta property="og:description" content="'.$desc.'" />
  <meta property="og:url" content="'.$url.'" />
  <meta property="og:site_name" content="'.$sitename.'" />
  <meta property="og:image" content="'.$image.'" />
  <!-- Google+ -->
  <meta itemprop="name" content="'.$title.'"/>
  <meta itemprop="description" content="'.$desc.'"/>
  <!-- Twitter Cards -->
  <meta name="twitter:title" content="'.$title.'"/>
  <meta name="twitter:url" content="'.$url.'"/>
  <meta name="twitter:description" content="'.$desc.'"/>
  <meta name="twitter:card" content="summary_large_image"/>
  <!-- Other Meta -->
  <meta property="article:published_time" content="'.$published.'" />
  <meta property="article:modified_time" content="'.$modified.'" />
  <meta property="article:author" content="'.$author.'" />';
  
  $post_tags = get_the_tags();
	if ( $post_tags ) {
    foreach( $post_tags as $tag ){
    echo "\n<meta property=\"article:tag\" content=\"".$tag->name."\" />";
    }
	};

	$post_category = get_the_category();
	if ( $post_category ) {
    foreach( $post_category as $category ){
    echo "\n<meta property=\"article:section\" content=\"".$category->name."\" />";
    }
	};
	echo "\n<!-- mwt OG TAG -->";
}


function mwt_getTitle() {
	$title = wp_title('|', false, 'right');
	if ($title == '') {
		$title = esc_textarea(get_bloginfo('name'));
	} else {
		$title_arr = explode('|', $title);
		$title = trim($title_arr[0]);
	}

	return $title;
}

function mwt_getDesc() {
	$desc_cont = get_post(get_the_ID());
	$desc_cont = preg_split('/<!--more(.*?)?-->/', $desc_cont->post_content);
	$desc_cont = strip_tags(mwt_substr($desc_cont[0],0,400));
	$description = preg_replace("/\n/","",str_replace("　","",$desc_cont));

	return $description;
}

function mwt_getUrl() {
  $host = 'http://';
  if (isset($_SERVER['HTTPS'])) {$host = 'https://';}

  return $host . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function mwt_getImage() {
	$content = get_post(get_the_ID());
	$content = preg_split('/<!--more(.*?)?-->/', $content->post_content);
	preg_match_all("/<img.*? *>/s", $content[0], $img);
	preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $img[0][0], $imgsrc);

	return $imgsrc[1];
}

function mwt_substr($string,$start,$end)
{
	$en = 0;
	$zh_tw = 0;
	for($i=0; $i<$end; $i++)
	{
		preg_match("/[0-9a-zA-Z]/", $string[(int)$i])? $en++ : $zh_tw++;
      $i = $zh_tw/3+$en/2;
      $t = $zh_tw/3+$en;
	}
  $rul_str = mb_substr($string,$start,$t,'utf-8');

	return $rul_str;
}