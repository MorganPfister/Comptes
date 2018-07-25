$( document ).ready(function(){
    $id = $('.page-header').data('id');
    if ($id >= 0){
        $item = $( "a#sidenav-item.btn:eq(" + $id + ")" );
        $item.addClass("clicked");
        $item.css({"background-color": "#225081", "border": "solid", "border-width": "0px 0px 0px 5px", "border-color": "rgb(255, 120, 0)"});
    }
    if ($('#login-row').length > 0){
        $('#main-page').css("padding", "0");
    }
});

$sidenav_item = $( "a#sidenav-item.btn" );

$sidenav_item.hover(
    function() {
        if (!$(this).hasClass("clicked")){
            $(this).css("backgroundColor", "#225081");
        }
    },
    function() {
        if (!$(this).hasClass("clicked")) {
            $(this).css("backgroundColor", "#09192a");
        }
    }
);

$sidenav_item.click(function(){
    $this = $(this);
    $sidenav_item.each(function(){
        $(this).removeClass("clicked");
        $(this).css("backgroundColor", "#09192a");
    });
    $this.addClass("clicked");
    $this.css("backgroundColor", "#225081");
});