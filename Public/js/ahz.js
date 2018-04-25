function setupTags() {
    var area =$('.tab_area');
    if(!area)    return;
    area.children('.tab_cell:first-child').addClass('tab_cell_selected');
    $(".tags_out").each(function (i,n) {
       if(i ==0)
       {
           $(n).show();
       }
    });
    area.children('.tab_cell').each(function (i,n) {
        $(n).click(function () {
            $(this).addClass('tab_cell_selected').siblings().removeClass('tab_cell_selected');
            $("#tags_out"+$(this).data('id')).show().siblings('.tags_out').hide();
        });
    });
}
