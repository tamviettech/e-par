$('.tile.align-center .container').each(function(){
    height_content = $(this).outerHeight();
    height_item    = $(this).parent().height() - 45;
    if(height_content < height_item)    {
        mar_gin_top_content = (height_item - height_content) / 2;
        $(this).css({
            'margin-top': mar_gin_top_content
        });
    }
});