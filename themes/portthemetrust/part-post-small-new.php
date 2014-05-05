<?php global $post; ?>

<div class="project small <?php echo $p; ?>" id="<?php echo $post->ID; ?>">		
	<div class="inside">

<div id="box">
 <?php foreach (get_the_category() as $cat) : ?>
 
 <img src="<?php echo z_taxonomy_image_url($cat->term_id); ?>" />

 <div id="overlay">
   <a class="overlay-text <?php echo $cat->cat_name; ?>" href="<?php echo get_category_link($cat->term_id); ?>"><?php echo $cat->cat_name; ?></a>
  </div>
 

 <?php endforeach; ?>
</div>
</div>
</div>
	