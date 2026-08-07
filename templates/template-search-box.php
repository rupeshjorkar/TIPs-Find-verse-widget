<?php

/**
 * Template Name: Find Verse Short Code
 * Description: Find Verse Search Box
 */
$color = get_option('tips_find_verse_color', '#31bbd8'); 
$button_text = get_option('tips_find_verse_button_text', 'Find Verse'); 
$place_order = get_option('tips_find_verse_place_order', 'Tips: Find Verse');

$color2 = get_option('tips_find_sec_verse_color', '#31bbd8'); // Default color
$button_text2 = get_option('tips_find_sec_verse_button_text', 'Search text...'); // Default button text
$place_order2 = get_option('tips_find_sec_verse_place_order', 'Search'); // Default empty value

$color3 = get_option('tips_find_thir_verse_color', '#31bbd8'); // Default color
$button_text3 = get_option('tips_find_thir_verse_button_text', 'Pick category'); // Default button text
$place_order3 = get_option('tips_find_thir_verse_place_order', 'Select'); // Default empty value

$enable_first_search = get_option('tips_enable_first_search', 'off');
$enable_secound_search = get_option('tips_enable_secound_search', 'off'); // Default value is 'off'
$enable_third_search   = get_option('tips_enable_third_search', 'off');

$data = Tips_API_Common::pick_category_api();
?>
<div class="entry-content">
    <div class="main_box">
        <?php if ($enable_first_search === 'on') : ?>
		  <div id="find-verse">
		    <form id="lookup-verse" method="get" target="_blank" action="<?php echo esc_url(home_url('/find-verse')); ?>">
		      <input type="text" name="verseId" id="verseId" required placeholder="<?php echo esc_attr($place_order); ?>">
		      <input type="submit" value="<?php echo esc_attr($button_text); ?>" style="background-color: <?php echo esc_attr($color); ?>;">
		    </form>
		  </div>
	    <?php endif; ?>
	  	<?php if ($enable_secound_search === 'on') : ?>
		  <div id="search-term">
		    <form id="search-term" method="get" target="_blank" action="<?php echo esc_url(home_url('/search-story')); ?>">
		      <input type="text" name="Id" id="Id" required placeholder="<?php echo esc_attr($place_order2); ?>">
		      <input type="submit" value="<?php echo esc_attr($button_text2); ?>" style="background-color: <?php echo esc_attr($color2); ?>;">
		    </form>
		  </div>
		<?php endif; ?>
		<?php if ($enable_third_search === 'on') : ?>
		   <div id="find-cat">
	    		<div class="category-search">
		    		<?php 
		    			if (isset($data) && is_array($data) && count($data) > 0) {?>
		    					<select id="stories_select2_posts_new" class="js-select2" style="width: calc(100% - 50px);" data-placeholder="<?php echo esc_attr($button_text3); ?>">
		    						<option value=""><?php echo esc_attr($button_text3); ?></option>
		    					<?php foreach ($data as $item) {
							        if (isset($item['category'], $item['category_slug'])) {
							        	$name = $item['category'];
							            $slug = $item['category_slug'];
							        	?>
							           	<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></option>
							        <?php }
						     	}?>
						    </select>
						    <input type="submit" value="<?php echo esc_attr($place_order3); ?>" id="cat-sercch-btn" style="background-color: <?php echo esc_attr($color3); ?>;">
		    			<?php }
		    		?>
	            </div>
	    	</div>
	    <?php endif; ?>
	</div>
</div>

