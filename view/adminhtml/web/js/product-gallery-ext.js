define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    /**
     * Product gallery widget
     */
    return function (mageProductGallery) {
        $.widget('mage.productGallery', mageProductGallery, {
            /**
             * Override method to get original image dimensions
             */
            _updateImageDimesions: function (imgContainer) {
                const $dimens = imgContainer.find('[data-role=image-dimens]');
                const imageData = imgContainer.data('imageData');

                const imageHeight = imageData?.image_height;
                const imageWidth = imageData?.image_width;

                if(imageWidth !== undefined && imageHeight !== undefined) {
                    $dimens.text(imageWidth + 'x' + imageHeight + ' px');
                } else {
                    this._super(imgContainer);
                }
            },

            onDialogOpen: function (event) {
                this._super(event);

                const imageData = this.$dialog.data('imageData')
                const imageHeight = imageData?.image_height;
                const imageWidth = imageData?.image_width;

                if (imageWidth !== undefined && imageHeight !== undefined) {
                    this.$dialog.find(this.options.imageResolutionLabel)
                        .find('[data-message]').text(
                        imageWidth + "x" + imageHeight + " px"
                    );
                }
            },

        });

        return $.mage.productGallery;

    }
});
