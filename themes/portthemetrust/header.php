<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=1.0, user-scalable=no" />
	<link href='http://fonts.googleapis.com/css?family=Muli:300,400,300italic,400italic' rel='stylesheet' type='text/css'>
	<!-- <link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Serif:regular,bold" /> -->
	<?php $menu_font = of_get_option('ttrust_menu_font'); ?>
	<?php $heading_font = of_get_option('ttrust_heading_font'); ?>
	<?php $sub_heading_font = of_get_option('ttrust_sub_heading_font'); ?>
	<?php $body_font = of_get_option('ttrust_body_font'); ?>
	<?php $banner_main_font = of_get_option('ttrust_banner_main_font'); ?>
	<?php $banner_secondary_font = of_get_option('ttrust_banner_secondary_font'); ?>
	<?php $home_message_font = of_get_option('ttrust_home_message_font'); ?>
	
	<?php if ($menu_font != "") : ?>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php echo(urlencode($menu_font)); ?>:regular,italic,bold,bolditalic" />
	<?php endif; ?>
	<?php if ($heading_font != "") : ?>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php echo(urlencode($heading_font)); ?>:regular,italic,bold,bolditalic" />
	<?php endif; ?>	
	<?php if ($sub_heading_font != "") : ?>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php echo(urlencode($sub_heading_font)); ?>:regular,italic,bold,bolditalic" />
	<?php endif; ?>
	<?php if ($body_font != "" && $body_font != $heading_font) : ?>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php echo(urlencode($body_font)); ?>:regular,italic,bold,bolditalic" />
	<?php endif; ?>	
	<?php if ($banner_main_font != "") : ?>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php echo(urlencode($banner_main_font)); ?>:regular,italic,bold,bolditalic" />
	<?php endif; ?>
	<?php if ($banner_secondary_font != "") : ?>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php echo(urlencode($banner_secondary_font)); ?>:regular,italic,bold,bolditalic" />
	<?php endif; ?>
	<?php if ($home_message_font != "") : ?>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php echo(urlencode($home_message_font)); ?>:regular,italic,bold,bolditalic" />
	<?php endif; ?>

	
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	
	<?php if (of_get_option('ttrust_favicon') ) : ?>
		<link rel="shortcut icon" href="<?php echo of_get_option('ttrust_favicon'); ?>" />
	<?php endif; ?>
	
	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
	
	<?php wp_head(); ?>	
</head>

<body <?php body_class(); ?> >
	<?php $ttrust_menu_type = of_get_option('ttrust_menu_type'); ?>
	<?php if($ttrust_menu_type != "standard"): ?>
		<div id="slideNav" class="panel">
			<a href="javascript:jQuery.pageslide.close()" class="closeBtn"></a>								
			<div id="mainNav">
				<?php wp_nav_menu( array('menu_class' => '', 'theme_location' => 'main', 'fallback_cb' => 'default_nav_slide' )); ?>
			</div>
			<?php if(is_active_sidebar('sidebar_slidenav')) : ?>
			<div class="widgets">
				<?php dynamic_sidebar('sidebar_slidenav'); ?>
			</div>
			<?php endif; ?>			
		</div>
	<?php endif; ?>
<div id="container">	
<div id="header">
	<div class="top">
		<div class="inside clearfix">
			<?php $logoHeadTag = (is_front_page()) ? "h1" : "h3";	?>					
			<?php $ttrust_logo = of_get_option('logo'); ?>
			<div id="logo">
			<?php if($ttrust_logo) : ?>				
				<<?php echo $logoHeadTag; ?> class="logo"><!-- <a href="<?php bloginfo('url'); ?>"> --><a href="http://ideactioncorps.com/"><img src="<?php echo $ttrust_logo; ?>" alt="Ideaction Corps" /></a></<?php echo $logoHeadTag; ?>>
			<?php else : ?>				
				<<?php echo $logoHeadTag; ?>><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></<?php echo $logoHeadTag; ?>>				
			<?php endif; ?>	
			</div>

			<div id="site-name"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></div>	
			
			<a href="#slideNav" class="menuToggle"></a>				
			
		</div>		
	</div>
	<?php if(is_front_page() && of_get_option('ttrust_banner_enabled')) : ?>
	<div class="bottom">
		<div id="homeBanner" class="hasBackground">
			<div id="bannerText">				
				<div class="main"><?php echo of_get_option('ttrust_home_banner_text_main'); ?></div>
				<div class="secondary"><?php echo do_shortcode(of_get_option('ttrust_home_banner_text_secondary')); ?></div>
			</div>
			<div id="downButton"></div>
		</div>
	</div>
	<?php endif; ?>	
</div>

