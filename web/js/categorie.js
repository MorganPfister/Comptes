$(window).on('load', function(){
    $(".icon-picker").iconpicker({
        defaultValue: "fa fa-info-circle",
    });
    $(".colorpicker-component").colorpicker();
});

$list_categorie = $(".list-categorie");

$list_categorie.on("click", ".child-categorie-chevron", function(){
    $(this).toggleClass("fa-angle-right fa-angle-down")
});

$list_categorie.on("mouseenter", ".categorie-item", function(){
    $this = $(this);
    $this.find("#delete-categorie").css("visibility", "visible");
});

$list_categorie.on("mouseleave", ".categorie-item", function(){
    $this = $(this);
    $this.find("#delete-categorie").css("visibility", "hidden");
});

$list_categorie.on("mouseenter", "#delete-categorie", function(){
    $(this).css("cursor", "pointer");
});

$list_categorie.on("mouseleave", "#delete-categorie", function(){
    $(this).css("cursor", "default");
});

$list_categorie.on("click", "#delete-categorie", function(){
    $this = $(this);
    $("#modalDeleteCategorie").attr('data-categorie-id', $this.attr('data-categorie-id'));
});

$(".btn-delete-categorie-retour").on("click", function(){
    $("#modalDeleteCategorie").nifty("hide");
});

$(".btn-delete-categorie-ok").on("click", function(){
    $id_categorie = $("#modalDeleteCategorie").attr('data-categorie-id');
    $.ajax({
        url: Routing.generate("cdc_core_deletecategorie", {id: $id_categorie}),
        type: "post",
        data: {'id': $id_categorie},
        success: function(data){
            if (data['success']) {
                $("#modalDeleteCategorie").nifty("hide");
                $(location).attr('href', Routing.generate("cdc_core_categoriepage"))
            }
        }
    });
});