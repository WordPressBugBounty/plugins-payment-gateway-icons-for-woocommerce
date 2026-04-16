jQuery(function ($) {

    let frame;

    $(document).on('click', '.fcpgifw-upload-btn', function (e) {
        e.preventDefault();

        const wrapper = $(this).closest('.fcpgifw-field');
        const input = wrapper.find('.fcpgifw-icon-url');
        const preview = wrapper.find('.fcpgifw-preview');

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select Payment Gateway Icon',
            button: { text: 'Use this image' },
            multiple: false
        });

        frame.on('select', function () {
            const attachment = frame.state().get('selection').first().toJSON();

            input.val(attachment.url);

            preview.html(
                '<img src="' + attachment.url + '" style="height:24px; margin-top:5px;" />'
            );
        });

        frame.open();
    });

});