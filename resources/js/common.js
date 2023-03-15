let imagePreview = false;

$(function () {
    $('.image').on('click', function () {
        imagePreview = true;
        renderImagePreview($(this).data());
    });

    $('.close-preview').on('click', function () {
       imagePreview = false;
        renderImagePreview();
    });

    $('.paginate_count').on('change', function () {
       $('.paginate_count_form').submit();
    });

    $('.status, .rate, .tag').on('change', function () {
        $('.filters-from').submit();
    });

    $(window).on('resize', function() {
        if (window.innerWidth <= 1024) {
            imagePreview = false;
            renderImagePreview();
        }
    });
});

function renderImagePreview(data) {
    if (!imagePreview) {
        $('.preview-block').hide();
        $('.content-block').addClass('col-12').removeClass('col-8');
    } else {
        //console.log('this', data);
        if (typeof data !== 'undefined') {
            $('#image-preview').attr('src', data.image);
            $('#uuid-preview').html(data.uuid);
            $('#name-preview').html(data.name);
            $('#description-preview').html(data.description);
            $('#rate-preview').html(data.rate);
            $('#status-preview').html(data.status);
            $('#tags-preview').html(data.tags);

            if (window.innerWidth > 1024) {
                $('.preview-block').show();
                $('.content-block').addClass('col-8').removeClass('col-12');
            } else {
                let html = '<div class="preview-block fancybox-content" style="display: inline-block !important;">';
                html += $('.preview-block').html();
                html += '</div>';
                $.fancybox.open(html);
            }
        }
    }
}




