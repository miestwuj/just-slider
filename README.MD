# Just slider
When you need just a slider, not a revolution...

This is a simple, efficient slider plugin for WordPress dedicated towards more advanced users.

## How to use
Install the plugin in your **wp-content/plugins** folder. There will be a Just Sliders menu entry in your wp-admin:

![Doc image](img/doc1.jpg?raw=true)

There you will be able to manage sliders like any other post type:

![Doc image](img/doc2.jpg?raw=true)

## Slider editor

![Doc image](img/doc3.jpg?raw=true)

1. Add new slide
2. Transition type between slides
3. Select "yes" if you want the slides to change automatically
4. Time to display each slide if autoplay is active
5. Height of the slider
6. Your slider image.
7. Your HTML code. You can use any markup here. It will be displayed over the image, vertically and horizontally centered
8. Delete slide
9. You can drag slides to reorder them
10. Built in classes that you can use in your HTML

## Useful PHP filters
```php
// The html code of the whole slider.
add_filter( 'just_slider_html', 'just_slider_html_filter' 10, 2 );
function just_slider_html_filter( $html, $slider_id ){
    $html .= $html . ' example ';
    return $html;
}
// Filter the params passed to the slick slider. Please see slicks doc to check the full parameter list.
add_filter( 'just_slider_script_parameters', 'just_slider_script_parameters_filter' 10, 2 );
function just_slider_script_parameters_filter( $params, $slider_id ){
    $params['autoplay'] = false;
    return $params;
}
// Filter the whole script code in case you need to.
add_filter( 'just_slider_script', 'just_slider_script_filter' 10, 2 );
function just_slider_script_filter( $html, $slider_id ){
    return $html;
}
```

## Version history
**1.1.0**

NEW - proportional or crop scaling

## Dependencies
* [slick](https://github.com/kenwheeler/slick/) - the last carousel you'll ever need

## License

Copyright (c) 2018 Mateusz Dorywalski

Licensed under the MIT license.