
/**
 * Javascript code for page that displays images
 */
$(document).ready(function(){

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    /**
     * PLUGIN FOR ZOOMING IMAGES
     */
    $('.img-fluid').bighover({
        width: 'auto',
        height: 'auto'
    });

    /**
     * PLUGIN FOR VALIDATING INPUT
     */
    $("#formImg").validate({
        rules: {
            title: {
                required: true
            },
            image: {
                required: true
            }
        },
        errorClass: "is-invalid"
    });
});