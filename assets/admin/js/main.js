;(function($){
    $('.omb_dp').datepicker({
        changeMonth: true,
        changeYear: true
    });

    var image_url = $("#omb_image_url").val();
    if(image_url){
        $("#omb_image_container").html(`<img width="250px" height="auto" src="${image_url}">`);
    }

    $('#omb_image').on('click', function(e){
        e.preventDefault();
        
        
        frame = wp.media({
            title: "Upload Image",
            button: {
                text: "Select Image",
            },
            multiple:false,
        });

        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            console.log(attachment);
            $("#omb_image_id").val(attachment.id);
            $("#omb_image_url").val(attachment.url);
            $("#omb_image_container").html(`<img width="250px" height="auto" src="${attachment.url}">`);
        });

        if(frame){
            frame.open();
            return;
        }
        frame.open();
    });
})(jQuery)