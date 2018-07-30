$( "#form-add-compte" ).submit(function( event ) {
    event.preventDefault();
    $data = $("#form-add-compte :input").serializeArray();
    $.ajax({
        url: Routing.generate("cdc_core_addcompte"),
        type: "post",
        data: $data,
        dataType: 'json',
        success: function(data){
            if (data['success'] === true){
                $("#modalAddCompte").nifty("hide");
                $new_num = 1;
                $color_line = data['Compte']['solde'] > 0 ? "info" : "danger";

                if ($tab_compte.find("tr").length <= 1){
                    $tab_compte.css("visibility", "visible");
                    $tab_compte.find("tbody").prepend(buildCompteText(data, $new_num));
                }
                else {
                    $last_line = $("#tab-compte > tbody > tr:last-child");
                    $new_num = parseInt($("#tab-compte > tbody > tr:last-child > td:first-child").text()) + 1;
                    $last_line.after(buildCompteText(data, $new_num));
                }
            }
            else {
                $('#form-add-compte').prepend("<div class=\"alert alert-danger\"><i class=\"fa fa-exclamation-triangle\"></i><strong style=\"margin-left:5px;\">" + data['error'] + "</strong></div>");
                $('.compte-name > input').css({
                    "color": "#d9534f",
                    "borderColor": "#d9534f"
                });
            }
        }
    });
});

function buildCompteText(data, num){
    $text = "<tr data-id=\"" + data['Compte']['id'] + "\" class=\"" + $color_line + "\">\n" +
        "           <td>" + num + "</td>\n" +
        "           <td>" + data['Compte']['titulaire'] + "</td>\n" +
        "           <td>" + data['Compte']['nom'] + "</td>\n" +
        "           <td>" + data['Compte']['banque'] + "</td>\n" +
        "           <td>" + data['Compte']['solde'] + "</td>\n" +
        "           <td>\n" +
        "               <a href=\"#\" class=\"edit-compte\" id=\"modal-edit-compte\" data-trigger=\"modal\" data-target=\"#modalEditCompte\"><i class=\"fa fa-pencil-alt bg-blue action\"></i></a>\n" +
        "               <a href=\"#\" class=\"delete-compte\"><i class=\"fa fa-times bg-red action\"></i></a>\n" +
        "           </td>\n" +
        "         </tr>";
    return $text;
}

$("#form-edit-compte").submit(function(event){
    event.preventDefault();
    $data = $("#form-edit-compte").serializeArray();
    $id = $(this).attr("data-id");
    $data.push({'id':$id});
    $.ajax({
        url: Routing.generate("cdc_core_editcompte", {id: $id}),
        type: "post",
        data: $data,
        dataType: 'json',
        success: function(data){
            if (data['success'] === true){
                $("#modalEditCompte").nifty("hide");
                $tr = $tab_compte.find("tr[data-id=" + data['Compte']['id'] + "]");
                $tr.find("td:nth-child(2)").text(data['Compte']['titulaire']);
                $tr.find("td:nth-child(3)").text(data['Compte']['nom']);
                $tr.find("td:nth-child(4)").text(data['Compte']['banque']);
                $tr.find("td:nth-child(5)").text(data['Compte']['solde']);
                $tr.removeClass();
                if (data['Compte']['solde'] <= 0){
                    $tr.addClass("danger");
                }
                else {
                    $tr.addClass("info");
                }
            }
            else {
                $('#form-edit-compte').prepend("<div class=\"alert alert-danger\"><i class=\"fa fa-exclamation-triangle\"></i><strong style=\"margin-left:5px;\">" + data['error'] + "</strong></div>");
                $('.compte-name > input').css({
                    "color": "#d9534f",
                    "borderColor": "#d9534f"
                });
            }
        }
    });
});

$("#modal-add-compte").click(function() {
    $alert = $('#form-add-compte > .alert').remove();
    if($alert.length > 0){
        $alert.remove();
        $('.compte-name > input').css({
            "color": "#555555",
            "borderColor": "#cccccc"
        });
    }
    $('#form-add-compte').find("input").val("");
});

$tab_compte = $('#tab-compte');

$tab_compte.on('click',"a#modal-edit-compte.edit-compte", function() {
    $alert = $('#form-edit-compte > .alert').remove();
    if($alert.length > 0){
        $alert.remove();
        $('.compte-name > input').css({
            "color": "#555555",
            "borderColor": "#cccccc"
        });
    }
    $this = $(this);
    $tr = $this.closest("tr");
    $id = $tr.attr("data-id");
    $titulaire = $tr.find("td:nth-child(2)").text();
    $nom = $tr.find("td:nth-child(3)").text();
    $banque = $tr.find("td:nth-child(4)").text();
    $solde = $tr.find("td:nth-child(5)").text();
    $form = $('#form-edit-compte');
    $form.attr("data-id", $id);
    $form.find("input#titulaire.form-control").val($titulaire);
    $form.find("input#nom.form-control").val($nom);
    $form.find("input#banque.form-control").val($banque);
    $form.find("input#solde.form-control").val($solde);
});

$tab_compte.on('click', ".delete-compte", function(event){
    event.preventDefault();
    $this = $(this);
    $id_compte = $this.closest("tr").attr("data-id");
    $.ajax({
        url: Routing.generate("cdc_core_deletecompte", {id: $id_compte}),
        type: "post",
        data: {'id': $id_compte},
        dataType: 'json',
        success: function(data) {
            if (data['success'] === true){
                $this.closest("tr").nextAll().each(function(){
                    $num = parseInt($(this).find("td:first-child").text());
                    $(this).find("td:first-child").text($num - 1);
                });
                $this.closest("tr").remove();
                if ($tab_compte.find("tr").length <= 1){
                    $tab_compte.css("visibility", "collapse");
                }
            }
        }
    });
});