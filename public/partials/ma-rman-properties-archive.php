 <?php $town = get_post_meta(get_the_ID(), 'address4', true);

?>

<article class="property__card">
    <a href="<?php the_permalink(); ?>" class="property__image-container">
        <?php the_post_thumbnail('large', ['class' => 'property__image']); ?>
    </a>
    <h3 class="property__title"><?php the_title(); ?></h3>
	<h3 class="property-town"><?php  echo $town; ?></h3>
    <p class="property__price"><?=  $post_data['display_price'] ?></p>
    <p class="property__description"><?= wp_trim_words(get_the_excerpt(), 15); ?></p>
    <div class="property__button">
        <a href="<?php the_permalink(); ?>" class="orange-button">More Details</a>
    </div>
</article>

