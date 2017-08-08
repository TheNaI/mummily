// Porto Config Scss Theme File


// Created at <?php echo date("Y-m-d H:i:s") ?>





<?php


$b = porto_check_theme_options();


$dark = $b['css-type'] == 'dark';


?>





//** Padding between columns. Gets divided in half for the left and right.


$grid-gutter-width:         <?php echo $b['grid-gutter-width'] ?>px !default;


// Large screen / wide screen


$container-large-desktop: (<?php echo $b['container-width'] ?>px) !default;


$screen-large: "(max-width: <?php echo $b['container-width'] + $b['grid-gutter-width'] - 1 ?>px)";


<?php if( $b['container-width'] == '1170' ): /* ?>
@media (min-width: 768px){ .container { width: 750px !important; } }
@media (min-width: 992px){ .container { width: 970px !important; } }
@media (min-width: 1190px){ .container { width: 1170px !important; } }
<?php */ endif; ?>


// Border radius


<?php if ($b['border-radius']) : ?>


     $border-xs:     1px;


    $border-small:  2px;


    $border-thin:   3px;


    $border-base:   4px;


    $border-normal: 5px;


    $border-medium: 6px;


    $border-thick:  7px;


    $border-strong: 8px;


    $border-large:  16px;


    $progress-border: 25px;


    $feature-icon-border: 35px;


    $thumb-link-border: 25px;


<?php else : ?>


    $border-xs:     0;


    $border-small:  0;


    $border-thin:   0;


    $border-base:   0;


    $border-normal: 0;


    $border-medium: 0;


    $border-thick:  0;


    $border-strong: 0;


    $border-large:  0;


    $progress-border: 0;


    $feature-icon-border: 0;


    $thumb-link-border: 0;


<?php endif; ?>


// Search Border Radius


<?php if( $b['search-border-radius'] ): ?>
	
	$searchform-border: 20px;

    $searchform-border-large: 25px;

	$searchform-extra-width: 0px;
	 
	@mixin searchform-select-padding() {
		padding: side-values(0 15px 0 10px);
	}

	@mixin searchform-input-padding() {
		padding: side-values(0 15px 0 20px);
	}

	@mixin searchform-button-padding() {
		padding: side-values(0 20px 0 13px);
	}



<?php else: ?>

	$searchform-border: 0;

    $searchform-border-large: 0;

    $searchform-extra-width: 0px;

	@mixin searchform-select-padding() {
		padding: side-values(0 15px 0 15px);
	}

	@mixin searchform-input-padding() {
		padding: 0 15px 0 15px;
	}

	@mixin searchform-button-padding() {
		padding: side-values(0 15px 0 17.5px);
	}

<?php endif; ?>


<?php if ($b['thumb-padding']) : ?>


    //** Padding around the thumbnail image


    $thumbnail-padding:      4px !default;


    //** Thumbnail border color


    $thumbnail-border:       #ddd !default;


    $thumbnail-border-width: 1px;


    $thumbnail-slide-width:  99.5%;


    $product-image-padding:  0.2381em;


    $product-image-border-width: 1px;


    $widget-product-image-padding: 3px;


    $image-slider-padding: 3px;


<?php else : ?>


    //** Padding around the thumbnail image


    $thumbnail-padding:      0 !default;


    //** Thumbnail border color


    $thumbnail-border:       transparent !default;


    $thumbnail-border-width: 0;


    $thumbnail-slide-width:  100%;


    $product-image-padding:  0;


    $product-image-border-width: 0;


    $widget-product-image-padding: 0;


    $image-slider-padding: 0;


<?php endif; ?>





$dark: <?php echo ($dark) ? '1' : '0' ?>;





<?php if ($dark) : ?>


$color-dark: <?php echo $b['color-dark'] ?>;


<?php else : ?>


$color-dark: #1d2127;


<?php endif; ?>


$color-dark-inverse: #FFF;


$color-dark-1: $color-dark;


$color-dark-2: lighten($color-dark-1, 2%);


$color-dark-3: lighten($color-dark-1, 5%);


$color-dark-4: lighten($color-dark-1, 8%);


$color-dark-5: lighten($color-dark-1, 3%);


$color-darken-1: darken($color-dark-1, 2%);





$dark-bg: $color-dark;


$dark-default-text: #808697;





<?php if ($dark) : ?>





    $color-price: #eee;





    $widget-bg-color: $color-dark-3;


    $widget-title-bg-color: $color-dark-4;


    $widget-border-color: transparent;





    $input-border-color: $color-dark-3;


    $image-border-color: $color-dark-4;


    $color-widget-title: #fff;





    $price-slide-bg-color: $color-dark;


    $panel-default-border: $color-dark-3 !default;








<?php else : ?>





    $color-price: #444;





    $widget-bg-color: #fbfbfb;


    $widget-title-bg-color: #f5f5f5;


    $widget-border-color: #ddd;





    $input-border-color: #ccc;


    $image-border-color: #ddd;


    $color-widget-title: #313131;





    $price-slide-bg-color: #eee;


    $panel-default-border: #ddd !default;








<?php endif; ?>