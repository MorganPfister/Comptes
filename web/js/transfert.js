$(document).ready(function() {
    $('#table-transfert').DataTable({
        "order": [[5, 'desc']],
        "columnDefs": [
            { "orderable": false, "targets": 0 }
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
} );