$(document).ready(function() {
    google.charts.load('current', {'packages':['corechart']});

    var depense_by_categorie = $('.depense-by-categorie-nav');
    var compte_id_a = JSON.parse("[" + depense_by_categorie.closest('.row').attr('data-compte-id-a') + "]");
    var compte_nom_a = depense_by_categorie.closest('.row').attr('data-compte-nom-a').split(',');
    var current_index = 0;

    $month_number = $('.resume-mois-titre').attr('data-month-number');
    $year = $('.resume-mois-titre').attr('data-year-number');

    $data = {'id' :'-1', 'month': $month_number, 'year': $year};
    $.ajax({
        url: Routing.generate("cdc_core_retrievedepenseforcompte"),
        type: "post",
        data: $data,
        dataType: 'json',
        success: function (data) {
            drawPieChart(data['pie_chart_data']);
            var previous_index = ((current_index - 1)+compte_id_a.length)%compte_id_a.length;
            var next_index = ((current_index + 1)+compte_id_a.length)%compte_id_a.length;
            $('.compte-titre-current').text(compte_nom_a[current_index]);
            $('.compte-titre-nav[data-direction="-1"]').text(compte_nom_a[previous_index]);
            $('.compte-titre-nav[data-direction="1"]').text(compte_nom_a[next_index]);
        }
    });

    depense_by_categorie.hover(
        function(){
            $(this).css('cursor', 'pointer');
        },
        function(){
            $(this).css('cursor', 'auto');
        }
    );

    depense_by_categorie.on('click', function(){
        $this = $(this);
        if ($this.attr('data-direction') > 0){
            current_index = ((current_index + 1)+compte_id_a.length)%compte_id_a.length;
        }
        else {
            current_index = ((current_index - 1)+compte_id_a.length)%compte_id_a.length;
        }
        $compte_id = compte_id_a[current_index];
        $month_number = $('.resume-mois-titre').attr('data-month-number');
        $year = $('.resume-mois-titre').attr('data-year-number');
        $data = {'id' :$compte_id, 'month': $month_number, 'year': $year};
        $.ajax({
            url: Routing.generate("cdc_core_retrievedepenseforcompte"),
            type: "post",
            data: $data,
            dataType: 'json',
            success: function (data) {
                drawPieChart(data['pie_chart_data']);
                $('.compte-titre-current').text(compte_nom_a[current_index]);
                var previous_index = ((current_index - 1)+compte_id_a.length)%compte_id_a.length;
                var next_index = ((current_index + 1)+compte_id_a.length)%compte_id_a.length;
                $('.compte-titre-nav[data-direction="-1"]').text(compte_nom_a[previous_index]);
                $('.compte-titre-nav[data-direction="1"]').text(compte_nom_a[next_index]);
            }
        });
    });
});

function drawPieChart(pie_chart_data){
    var data = new google.visualization.DataTable();

    for(var column_name in pie_chart_data['columns']){
        data.addColumn(pie_chart_data['columns'][column_name], column_name);
    }
    data.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});

    var slice_color = [];
    if ('rows' in pie_chart_data) {
        for (var row_name in pie_chart_data['rows']) {
            data.addRow(
                [row_name, pie_chart_data['rows'][row_name]['sum'], pie_chart_data['rows'][row_name]['tooltip']]
            );
            slice_color.push({color: pie_chart_data['rows'][row_name]['color']});
        }
    }

    var options = {
        'width': 500,
        'height': 400,
        'pieHole': 0.3,
        'legend' : {position: 'none'},
        'chartArea': {left: 65, width:'95%', height:'95%'},
        'slices': slice_color,
        'tooltip': {isHtml: true},
        'pieSliceText': 'label'
    };
    var chart = new google.visualization.PieChart(document.getElementById('dashboard_pie_chart'));
    chart.draw(data, options);
}