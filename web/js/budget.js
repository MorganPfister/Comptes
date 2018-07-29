$(document).ready(function() {
    $list_categorie = $(".list-categorie");
    $modal_add_budget = $('#modalAddBudget');
    $modal_edit_budget = $('#modalEditBudget');

    $list_categorie.on("click", ".child-categorie-chevron", function () {
        $(this).toggleClass("fa-angle-right fa-angle-down")
    });

    $list_categorie.on("mouseenter", ".categorie-item", function () {
        $this = $(this);
        $this.find("#add-budget").css("visibility", "visible");
    });

    $list_categorie.on("mouseleave", ".categorie-item", function () {
        $this = $(this);
        $this.find("#add-budget").css("visibility", "hidden");
    });

    $list_categorie.on("mouseenter", "#add-budget", function () {
        $(this).css("cursor", "pointer");
    });

    $list_categorie.on("mouseleave", "#add-budget", function () {
        $(this).css("cursor", "default");
    });

    $list_categorie.on("mouseenter", ".categorie-item", function () {
        $this = $(this);
        $this.find("#delete-budget").css("visibility", "visible");
    });

    $list_categorie.on("mouseleave", ".categorie-item", function () {
        $this = $(this);
        $this.find("#delete-budget").css("visibility", "hidden");
    });

    $list_categorie.on("mouseenter", "#delete-budget", function () {
        $(this).css("cursor", "pointer");
    });

    $list_categorie.on("mouseleave", "#delete-budget", function () {
        $(this).css("cursor", "default");
    });

    $list_categorie.on('click', '#add-budget', function(){
        $this = $(this);
        $add_budget_categorie = $('.add-budget-categorie');
        $add_budget_categorie.find('i').removeClass();
        $add_budget_categorie.find('i').attr('style', 'font-size:30px;margin-right:15px;');
        $add_budget_categorie.find('span').html('');

        $categorie = $this.parent().find('.categorie').html();
        $icon_class = $this.parent().find('.icon-categorie').attr('class');
        $icon_color = $this.parent().find('.icon-categorie').attr('style');
        $add_budget_categorie.find('i').addClass($icon_class);
        $add_budget_categorie.find('i').removeClass('icon-categorie');
        $add_budget_categorie.find('i').attr('style', $add_budget_categorie.find('i').attr('style') + $icon_color);
        $add_budget_categorie.find('span').append($categorie);

        $modal_add_budget.find('button[id=btn-add-budget]').attr('data-categorie-id', $this.data('categorie-id'));

        $('#form-add-budget > .alert').remove();
        $('#form-add-budget').find("input").val("");
        $modal_add_budget.nifty('show');
    });

    $('#form-add-budget').submit(function(event){
        event.preventDefault();
        $categorie_id = $('#modalAddBudget').find('button[id=btn-add-budget]').attr('data-categorie-id');
        $data = $("#form-add-budget :input").serializeArray();
        $.ajax({
            url: Routing.generate("cdc_core_addbudgetmodele", {id: $categorie_id}),
            type: "post",
            data: $data,
            dataType: 'json',
            success: function(data){
                if (data['success'] === true){
                    $list_categorie.find('.categorie-item[data-categorie-id='+ $categorie_id +'] :last-child').remove();
                    $list_categorie.find('.categorie-item[data-categorie-id='+ $categorie_id +']').append('<i id="delete-budget" data-budgetmodele-id="' + data['BudgetModele']['id'] + '" class="pull-right fa fa-minus-circle btn-delete-budget-' + data['BudgetModele']['id'] +'"></i>');
                    $list_categorie.find('.categorie-item[data-categorie-id='+ $categorie_id +']').append('<span class="categorie-budget-seuil badge" data-budget-seuil="' + data['BudgetModele']['seuil'] + '" data-budgetmodele-id="'+ data['BudgetModele']['id'] +'">' + data['BudgetModele']['seuil'] + '€ / mois</span>');
                    $modal_add_budget.nifty('hide');
                }
                else {
                    $('#form-add-budget').prepend("<div class=\"alert alert-danger\"><i class=\"fa fa-exclamation-triangle\"></i><strong style=\"margin-left:5px;\">" + data['error'] + "</strong></div>");
                }
            }
        });
    });

    $list_categorie.on("mouseenter", ".categorie-budget-seuil", function () {
        $(this).css("cursor", "pointer");
    });

    $list_categorie.on("mouseleave", ".categorie-budget-seuil", function () {
        $(this).css("cursor", "default");
    });

    $list_categorie.on('click', '.categorie-budget-seuil', function(){
        $this = $(this);
        $budgetmodele_id = $this.attr('data-budgetmodele-id');
        $modal_edit_budget.find('input[id=seuil]').val($this.data('budget-seuil'));

        $edit_budget_categorie = $('.edit-budget-categorie');
        $edit_budget_categorie.find('i').removeClass();
        $edit_budget_categorie.find('i').attr('style', 'font-size:30px;margin-right:15px;');
        $edit_budget_categorie.find('span').html('');

        $categorie = $this.parent().find('.categorie').html();
        $icon_class = $this.parent().find('.icon-categorie').attr('class');
        $icon_color = $this.parent().find('.icon-categorie').attr('style');
        $edit_budget_categorie.find('i').addClass($icon_class);
        $edit_budget_categorie.find('i').removeClass('icon-categorie');
        $edit_budget_categorie.find('i').attr('style', $edit_budget_categorie.find('i').attr('style') + $icon_color);
        $edit_budget_categorie.find('span').append($categorie);

        $('#form-edit-budget > .alert').remove();
        $modal_edit_budget.find('button[id=btn-edit-budget]').attr('data-budgetmodele-id', $budgetmodele_id);
        $('#modalEditBudget').nifty('show');
    });

    $('#form-edit-budget').submit(function(event){
        event.preventDefault();
        $budgetmodele_id = $modal_edit_budget.find('button[id=btn-edit-budget]').attr('data-budgetmodele-id');
        $data = $("#form-edit-budget :input").serializeArray();
        $.ajax({
            url: Routing.generate("cdc_core_editbudgetmodele", {id: $budgetmodele_id}),
            type: "post",
            data: $data,
            dataType: 'json',
            success: function(data){
                if (data['success'] === true){
                    $categorie_id = data['BudgetModele']['categorie_id'];
                    $list_categorie.find('.categorie-item[data-categorie-id='+ $categorie_id +'] :last-child').remove();
                    $list_categorie.find('.categorie-item[data-categorie-id='+ $categorie_id +']').append('<span class="categorie-budget-seuil badge" data-budget-seuil="' + data['BudgetModele']['seuil'] + '" data-budgetmodele-id="'+ data['BudgetModele']['id'] +'">' + data['BudgetModele']['seuil'] + '€ / mois</span>');
                    $modal_edit_budget.nifty('hide');
                }
                else {
                    $('#form-edit-budget').prepend("<div class=\"alert alert-danger\"><i class=\"fa fa-exclamation-triangle\"></i><strong style=\"margin-left:5px;\">" + data['error'] + "</strong></div>");
                }
            }
        });
    });

    $list_categorie.on('click', '#delete-budget', function(){
        $this = $(this);
        $budgetmodele_id = $this.attr('data-budgetmodele-id');
        $.ajax({
            url: Routing.generate("cdc_core_deletebudgetmodele", {id: $budgetmodele_id}),
            type: "post",
            dataType: 'json',
            success: function(data){
                if (data['success'] === true){
                    $categorie_id = data['categorie_id'];
                    $list_categorie.find('.categorie-item[data-categorie-id='+ $categorie_id +'] :last-child').remove();
                    $list_categorie.find('.categorie-item[data-categorie-id='+ $categorie_id +'] :last-child').remove();
                    $list_categorie.find('.categorie-item[data-categorie-id='+ $categorie_id +']').append('<i id="add-budget" data-categorie-id="' + $categorie_id + '" class="pull-right fa fa-plus-circle btn-add-budget-' + $categorie_id + '"></i>');
                }
            }
        });
    });
});
