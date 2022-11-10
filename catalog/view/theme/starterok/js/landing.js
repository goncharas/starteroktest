$(document).ready(function() {
    const MEDIA_DESKTOP = '(min-width: 1200px)';
    var isDesktop = window.matchMedia(MEDIA_DESKTOP);

    $('body').addClass('landing'); 

    if (isDesktop.matches) {
        $('.landing-banner__img').addClass('animate-showup');
    }

    $('.record-form').on('click', '.btn-landing-send', function(e) {
        e.preventDefault();

        var formId = $(this).closest('.record-form').attr('id'); 

        let data = $('#'+formId).find('form').serialize(); 

        $.ajax({
            url: 'index.php?route=ajax/contact',
            type: 'post',
            data: data,
            dataType: 'json',
            success: function(json) {
                $('#' + formId + ' .alert').html('').removeClass('alert-danger alert-success').addClass('hidden');
                $('.form-group').removeClass('has-error');

                if (json['redirect']) {
                    location = json['redirect'];
                } else if (json['error']) {
                    //$('#contact-form input[type="hidden"]').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    $('#' + formId + ' .alert').html('<i class="fa fa-exclamation-circle"></i>' + json['error']['warning']).addClass('alert-danger').removeClass('hidden');
                    //$('#contact-form input[name="'+json['error']['field']+'"]').closest('.form-group').addClass('has-error');
                    $('#' + formId + ' #' + formId + '-'+json['error']['field']).closest('.form-group').addClass('has-error');

            } else {
                $('#' + formId + ' .alert').html(json['success']).addClass('alert-success').removeClass('hidden');
                $('#' + formId + ' input[type="text"], #' + formId + ' textarea').val('');
            }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $('#record-form').on('show.bs.modal', function (event) {
        var btn = $(event.relatedTarget);
        var service = $(btn).parent().find('h3');
        var modal = $('#record-form');
        modal.find('#record-form-service').val(service.text());
    });

    // seo text read more
    $('#seo-text-read-more').click(function(e){ 
        e.preventDefault();
        $('#seo-text-read-more').addClass('invisible');
        $('.seo-box-content').removeClass('seo-text-short');
    }); 

    function InitAnimation() {
        var top_scroll = $(document).scrollTop();
        var screen_height = $(window).height();

        $('.landing-callback').each(function() {
            var elem_pos = $(this).offset();
            var elem_top = elem_pos.top;
            var elem_height = $(this).height();

            if(top_scroll + screen_height >= elem_top + elem_height/2) {
                var form = $(this).find('.landing-form');
                form.addClass('animate-showup');
            } 
        });

        var elem_pos = $('.landing-about').offset();
        if (elem_pos) {
            var elem_top = elem_pos.top;
            var elem_height = $('.landing-about').height();

            if(top_scroll + screen_height >= elem_top + elem_height/2) {
                $('.landing-about__item').addClass('animate-showleft');
            } 
        }
    };

    $(window).scroll(function(){
      if (isDesktop.matches) {
          InitAnimation();
      }
    });

    $('forma').replaceWith( function() { 
        return $( this ).get(0).outerHTML.replace(/<(\/?)forma/g,'<$1form');} );

    $('.nano').nanoScroller({
        alwaysVisible: true,
        sliderMaxHeight: 150 
    });
});