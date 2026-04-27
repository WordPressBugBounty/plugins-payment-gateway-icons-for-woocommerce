jQuery(document).ready(function ($) {

    let mediaFrame;

    $(document).on('click', '.fcpgifw-upload-btn', function (e) {
        e.preventDefault();

        const button = $(this);
        const input = button.closest('td').find('.fcpgifw-icon-url');

        // Open existing frame if already created
        if (mediaFrame) {
            mediaFrame.open();
            mediaFrame.currentInput = input;
            return;
        }

        // Create frame
        mediaFrame = wp.media({
            title: 'Select Icon',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        // Store target input safely
        mediaFrame.on('select', function () {

            const attachment = mediaFrame.state()
                .get('selection')
                .first()
                .toJSON();

            if (mediaFrame.currentInput) {
                mediaFrame.currentInput.val(attachment.url);
            }
        });

        mediaFrame.open();
        mediaFrame.currentInput = input;
    });

});