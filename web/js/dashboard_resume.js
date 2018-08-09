$(document).ready(function() {
    google.charts.load('current', {'packages':['corechart']});

    var depense_by_categorie = $('.depense-by-categorie-nav');
    var depense_current_index = 0;

    var balance = $('.balance-nav');
    var balance_current_index = 0;

    var compte_id_a = JSON.parse("[" + balance.closest('.row').attr('data-compte-id-a') + "]");
    var compte_nom_a = balance.closest('.row').attr('data-compte-nom-a').split(',');

    depense_by_categorie.hover(
        function(){
            $(this).css('cursor', 'pointer');
        },
        function(){
            $(this).css('cursor', 'auto');
        }
    );

    balance.hover(
        function(){
            $(this).css('cursor', 'pointer');
        },
        function(){
            $(this).css('cursor', 'auto');
        }
    );

    $data = {'months_number' :'10', 'compte_id': '-1'};
    $.ajax({
        url: Routing.generate("cdc_core_getbalancechart"),
        type: "post",
        data: $data,
        dataType: 'json',
        success: function (data) {
            drawComboChart(data['combo_chart_data']);
            var previous_index = ((balance_current_index - 1)+compte_id_a.length)%compte_id_a.length;
            var next_index = ((balance_current_index + 1)+compte_id_a.length)%compte_id_a.length;
            $('.balance-compte-titre-current').text(compte_nom_a[balance_current_index]);
            $('.balance-compte-titre-nav[data-direction="-1"]').text(compte_nom_a[previous_index]);
            $('.balance-compte-titre-nav[data-direction="1"]').text(compte_nom_a[next_index]);
        }
    });

    $data = {'id' :'-1', 'month': -1, 'year': -1};
    $.ajax({
        url: Routing.generate("cdc_core_retrievedepenseforcompte"),
        type: "post",
        data: $data,
        dataType: 'json',
        success: function (data) {
            drawPieChart(data['pie_chart_data']);
            $('.depense-compte-titre-current').text(compte_nom_a[depense_current_index]);
            var previous_index = ((depense_current_index - 1)+compte_id_a.length)%compte_id_a.length;
            var next_index = ((depense_current_index + 1)+compte_id_a.length)%compte_id_a.length;
            $('.depense-compte-titre-nav[data-direction="-1"]').text(compte_nom_a[previous_index]);
            $('.depense-compte-titre-nav[data-direction="1"]').text(compte_nom_a[next_index]);
        }
    });

    balance.on('click', function() {
        $this = $(this);
        if ($this.attr('data-direction') > 0) {
            balance_current_index = ((balance_current_index + 1) + compte_id_a.length) % compte_id_a.length;
        }
        else {
            balance_current_index = ((balance_current_index - 1) + compte_id_a.length) % compte_id_a.length;
        }
        $compte_id = compte_id_a[balance_current_index];
        $data = {'months_number': '10', 'compte_id': $compte_id};
        $.ajax({
            url: Routing.generate("cdc_core_getbalancechart"),
            type: "post",
            data: $data,
            dataType: 'json',
            success: function (data) {
                drawComboChart(data['combo_chart_data']);
                $('.balance-compte-titre-current').text(compte_nom_a[balance_current_index]);
                var previous_index = ((balance_current_index - 1) + compte_id_a.length) % compte_id_a.length;
                var next_index = ((balance_current_index + 1) + compte_id_a.length) % compte_id_a.length;
                $('.balance-compte-titre-nav[data-direction="-1"]').text(compte_nom_a[previous_index]);
                $('.balance-compte-titre-nav[data-direction="1"]').text(compte_nom_a[next_index]);
            }
        });
    });

    depense_by_categorie.on('click', function(){
        $this = $(this);
        if ($this.attr('data-direction') > 0){
            depense_current_index = ((depense_current_index + 1)+compte_id_a.length)%compte_id_a.length;
        }
        else {
            depense_current_index = ((depense_current_index - 1)+compte_id_a.length)%compte_id_a.length;
        }
        $compte_id = compte_id_a[depense_current_index];
        $data = {'id' :$compte_id, 'month': -1, 'year': -1};
        $.ajax({
            url: Routing.generate("cdc_core_retrievedepenseforcompte"),
            type: "post",
            data: $data,
            dataType: 'json',
            success: function (data) {
                drawPieChart(data['pie_chart_data']);
                $('.depense-compte-titre-current').text(compte_nom_a[depense_current_index]);
                var previous_index = ((depense_current_index - 1)+compte_id_a.length)%compte_id_a.length;
                var next_index = ((depense_current_index + 1)+compte_id_a.length)%compte_id_a.length;
                $('.depense-compte-titre-nav[data-direction="-1"]').text(compte_nom_a[previous_index]);
                $('.depense-compte-titre-nav[data-direction="1"]').text(compte_nom_a[next_index]);
            }
        });
    });
});

function drawComboChart(combo_chart_data){
    var data = new google.visualization.arrayToDataTable(combo_chart_data);

    var options = {
        width: 500,
        height: 400,
        legend : {position: 'none'},
        pointsVisible: true,
        chartArea: {left: 65, width:'95%', height:'85%'},
        seriesType: 'bars',
        series: {
            0: {color: '#28a745'},
            1: {color: '#dc3545'},
            2: {type: 'line', color: '#337ab7'}
        }
    };

    var chart = new google.visualization.ComboChart(document.getElementById('dashboard_combo_chart'));
    chart.draw(data, options);
}

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