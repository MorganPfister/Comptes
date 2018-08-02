$transfert_table = $("#table-transfert");

$(document).ready(function() {
    var transfert_table = $transfert_table.DataTable({
        "order": [[5, 'desc']],
        "columnDefs": [
            { "orderable": false, "targets": 0 },
            { "targets": [6, 7, 8, 9, 10], "visible": false }
        ],
        "info": false,
        "language": {
            "decimal": ".",
            "thousands": " ",
            "emptyTable": "Aucun transfert n'a encore été enregistré",
            "search": "",
            "zeroRecords": "Aucun résultat trouvé",
            "paginate": {
                "first": "Premier",
                "last": "Dernier",
                "next": "Suivant",
                "previous": "Précédent"
            },
            "loadingRecords": "Chargement...",
            "processing": "Recherche...",
            "lengthMenu": "_MENU_ transferts par page"
        }
    });

    $transfert_table.on('click', '.transfert-details-chevron', function () {
        $(this).toggleClass("fa-angle-right fa-angle-down");
        var tr = $(this).closest('tr');
        var row = transfert_table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );

    $( "#form-add-transfert" ).submit(function( event ) {
        event.preventDefault();
        $data = $("#form-add-transfert :input").serializeArray();
        $.ajax({
            url: Routing.generate("cdc_core_addtransfert"),
            type: "post",
            data: $data,
            dataType: 'json',
            success: function(data){
                if (data['success'] === true){
                    $("#modalAddTransfert").nifty("hide");
                    $new_row = transfert_table.row.add( [ '', data['Transfert']['titre'], data['Compte']['nom'], data['Transfert']['montant'], '', data['Transfert']['date'], data['Compte']['titulaire'], data['Compte']['banque'], data['MoyenTransfert']['description'], data['Transfert']['commentaire'], data['Transfert']['id'] ] ).draw().node();
                    if (data['Transfert']['montant'] >= 0){
                        $($new_row).addClass('info');
                    }
                    else {
                        $($new_row).addClass('danger');
                    }
                    $($new_row).find('td').eq(0).html('<i class="fa fa-angle-right transfert-details-chevron"></i>');
                    $($new_row).find('td').eq(4).html('<i class="' + data['Categorie']['icon'] + ' icon-categorie" style="color:' + data['Categorie']['color'] +'"></i>' + data['Categorie']['titre']);
                }
            }
        });
    });

    var transfert_datepicker = $('#transfert-datepicker input').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        language: 'fr',
        todayBtn: "linked"
    });

    $("#modal-add-transfert").click(function() {
        $('#form-add-transfert').find("input").val("");
        $('#form-add-transfert').find("textarea").val("");
        transfert_datepicker.datepicker("setDate", 'now');
    });

    $transfert_table.on('click', '.edit-transfert', function(){
        event.preventDefault();
        $transfert_id = $(this).attr('data-transfert-id');
        $.ajax({
            url: Routing.generate("cdc_core_retrievetransfert", {id: $transfert_id}),
            type: "post",
            dataType: 'json',
            success: function (data) {
                if (data['success'] === true) {
                    $modal_edit_transfert = $('#modalEditTransfert');
                    $modal_edit_transfert.find('input[id=titre]').val(data['Transfert']['titre']);
                    $modal_edit_transfert.find('input[id=montant]').val(data['Transfert']['montant']);
                    $modal_edit_transfert.find('select[id=compte]').val(data['Transfert']['compte']);
                    $modal_edit_transfert.find('select[id=categorie]').val(data['Transfert']['categorie']);
                    transfert_datepicker.datepicker("setDate", data['Transfert']['date']);
                    $modal_edit_transfert.find('select[id=moyentransfert]').val(data['Transfert']['moyentransfert']);
                    $modal_edit_transfert.find('textarea[id=description]').val(data['Transfert']['description']);
                    $modal_edit_transfert.find('button[id=btn-edit_transfert]').attr('data-transfert-id', $transfert_id);
                    $modal_edit_transfert.nifty('show');
                }
            }
        });
    });

    $( "#form-edit-transfert" ).submit(function( event ) {
        event.preventDefault();
        $transfert_id = $modal_edit_transfert.find('button[id=btn-edit_transfert]').attr('data-transfert-id');
        $data = $("#form-edit-transfert :input").serializeArray();
        console.log($data, $transfert_id);
        $.ajax({
            url: Routing.generate("cdc_core_edittransfert", {id: $transfert_id}),
            type: "post",
            data: $data,
            dataType: 'json',
            success: function(data){
                console.log(data);
                if (data['success'] === true){
                    $("#modalEditTransfert").nifty("hide");
                    $row = transfert_table.row($transfert_table.find('tr[data-transfert-id=' + data['Transfert']['id'] + ']'));
                    console.log($row);
                    $row_node = $($row.node());
                    console.log($row_node);
                    $row.data([ '', data['Transfert']['titre'], data['Compte']['nom'], data['Transfert']['montant'], '', data['Transfert']['date'], data['Compte']['titulaire'], data['Compte']['banque'], data['MoyenTransfert']['description'], data['Transfert']['commentaire'], data['Transfert']['id'] ]);
                    if (data['Transfert']['montant'] >= 0){
                        $row_node.removeClass('danger');
                        $row_node.removeClass('info');
                        $row_node.addClass('info');
                    }
                    else if (data['Transfert']['montant'] < 0) {
                        $row_node.removeClass('info');
                        $row_node.removeClass('danger');
                        $row_node.addClass('danger');
                    }
                    $row_node.find('td').eq(0).html('<i class="fa fa-angle-down transfert-details-chevron"></i>');
                    $row_node.find('td').eq(4).html('<i class="' + data['Categorie']['icon'] + ' icon-categorie" style="color:' + data['Categorie']['color'] +'"></i>' + data['Categorie']['titre']);
                }
            }
        });
    });

    $transfert_table.on('click', '.delete-transfert', function(){
        event.preventDefault();
        $transfert_id = $(this).attr('data-transfert-id');
        $.ajax({
            url: Routing.generate("cdc_core_deletetransfert", {id: $transfert_id}),
            type: "post",
            dataType: 'json',
            success: function (data) {
                if (data['success'] === true) {
                    transfert_table.row($transfert_table.find('tr[data-transfert-id=' + $transfert_id + ']')).remove().draw();
                }
            }
        });
    });
} );

function format(row_data){
    $commentaire = row_data[9];
    if ($commentaire.length <= 0){
        $commentaire = "Aucun commentaire"
    }
    return "<div class='row'>" +
        "       <div class='col col-lg-10'>" +
        "           <table cellpadding=\"5\" cellspacing=\"0\" border=\"0\" class=\"transfert-details-row\">\n" +
        "               <tbody>\n" +
        "                   <tr>" +
        "                       <td>Titulaire du compte</td>\n" +
        "                       <td>:</td>\n" +
        "                       <td>"+row_data[6]+"</td>\n" +
        "                   </tr>\n" +
        "                   <tr>" +
        "                       <td>Banque</td>\n" +
        "                       <td>:</td>\n" +
        "                       <td>"+row_data[7]+"</td>\n" +
        "                   </tr>\n" +
        "                   <tr>" +
        "                       <td>Moyen de transfert</td>\n" +
        "                       <td>:</td>\n" +
        "                       <td>"+row_data[8]+"</td>\n" +
        "                   </tr>\n" +
        "                   <tr>" +
        "                       <td>Commentaire</td>\n" +
        "                       <td>:</td>\n" +
        "                       <td>"+$commentaire+"</td>\n" +
        "                   </tr>\n" +
        "               </tbody>\n" +
        "           </table>" +
        "       </div>" +
        "       <div class='col col-lg-2'><div class='text-center' style='margin-top:60px;'>" +
        "           <a href='#' class='edit-transfert' id='modal-edit-transfert' data-transfert-id='" + row_data[10] + "'><i class='fa fa-pencil-alt bg-blue action'></i></a>" +
        "           <a href='#' class='delete-transfert' data-transfert-id='" + row_data[10] + "'><i class='fa fa-times bg-red action'></i></a>"+
        "       </div>" +
        "   </div>" +
        "</div>"
}