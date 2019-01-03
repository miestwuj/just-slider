/* global jQuery */
/* global ajaxurl */
/* global justsliderConfig */

(function ($) {

    'use strict';

    var config = $.parseJSON(justsliderConfig);

    var bindGlobal = function(){
        $( '.jslider-slides-list' ).sortable( {
            items: '.jslider-slide',
            forcePlaceholderSize: true,
            update: function(){
                updateSettings();
            }
        } ).disableSelection();
        $('.jslider-sliders-remove').on('click', function(){
            $(this).closest('.jslider-slide').remove();
            updateSettings();
        });
        $('.jslider-sliders-add').on('click', function(){
            var $element = $(config.template).appendTo($('.jslider-slides-list'));
            bindSlide($element);
            updateSettings();
        });
        $('.jslider-slide').each(function(index, el){
            bindSlide($(el));
        });
        $('.jslider-slider-parameter').on('change', function() {
            updateSettings();
        });
    };
    var updateSettings = function() {
        var values = {};
        values.transitionType = $('.jslider-transition-type').val();
        values.autoplay = $('.jslider-autoplay').val();
        values.scaling = $('.jslider-scaling').val();
        if(values.scaling === 'crop') {
            $('.jslider-height').closest('label').css('visibility','visible');
        } else {
            $('.jslider-height').closest('label').css('visibility','hidden');
        }
        values.time = $('.jslider-transition-time').val();
        values.height = $('.jslider-height').val();
        values.slides = {};
        $('.jslider-slides-list .jslider-slide').each(function(index, that){
            var slide = {};
            slide.content = $('textarea', that).val();
            slide.content = encodeURIComponent( slide.content );
            slide.image = $('.just-slider-image-id', that).val();
            values.slides[index] = slide;
        });
        values = JSON.stringify(values);
        $('#just-slider-settings').val(values);
    };
    var bindSlide = function($scope){
        $('.jslider-sliders-remove', $scope).on('click', function(){
            $scope.remove();
        });
        $('textarea, input', $scope).on('input change', function() {
            updateSettings();
        });
        imageUploadControl($('.just-slider-image-upload', $scope));
    };

    var imageUploadControl = function($el) {
        var $image      = $el.find('.just-slider-image');
        var $addLink    = $el.find('.just-slider-add-image');
        var $deleteLink = $el.find('.just-slider-delete-image');
        var $imageId    = $el.find('.just-slider-image-id');

        if ( $imageId.val().length > 0 ) {
            $addLink.hide();
            $deleteLink.show();
        } else {
            $addLink.show();
            $deleteLink.hide();
        }

        $addLink.on('click', function(e) {
            e.preventDefault();

            openMediaLibrary(function(imageObj) {
                var thumb = imageObj.sizes.thumbnail;
                if (thumb){
                    $image.html('<img src="' + thumb.url + '" width="' + thumb.width + '" height="' + thumb.height + '" />');
                }

                $imageId.val(imageObj.id);

                $addLink.hide();
                $deleteLink.show();
                updateSettings();
            });
        });

        $deleteLink.on('click', function(e) {
            e.preventDefault();

            if ( ! confirm( 'Are you sure?' ) ) {
                return;
            }

            $image.empty();
            $imageId.val('');

            $addLink.show();
            $deleteLink.hide();
            updateSettings();
        });
    };

    var openMediaLibrary = function(callback) {
        var frame = wp.media({
            'title':    'Select an image',
            'multiple': false,
            'library':  {
                'type': 'image'
            },
            'button': {
                'text': 'Insert'
            }
        });

        frame.on('select',function() {
            var objSelected = frame.state().get('selection').first().toJSON();

            callback(objSelected);
        });

        frame.open();
    };

    $(document).ready(function() {
        bindGlobal();
        updateSettings();
    });


})(jQuery);
