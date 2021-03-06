@extends('layouts.master')

@section('title', 'Invoices List')

@section('content')
<div class="container">
    <div class="row">
        <div class="col s12">
            <h1>Dashboard</h1>
            <div id="linechart_material" style="width:100%; height:400px"></div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.load('visualization', '1.1', {packages: ['line']});
        google.setOnLoadCallback(drawChart);

        function drawChart() {

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Month');
            data.addColumn('number', 'Invoiced');

            data.addRows([
            @foreach ($monthly as $month => $value)
                ['{{date('M y', strtotime($month. '-01'))}}', {{$value}}],
            @endforeach
            ]);

            var options = {
                title: 'Past Year Invoiced Value',
                subtitle: 'in domestic currency ({{$settings['domestic_currency']}})',
                width: 900,
                height: 500,
                lineWidth: 5,
                curveType: 'function',
                colors: ['#ff5c5e'],
                legend: { position: 'bottom' },
                pointSize: 10,
                animation: {duration:1000, easing: 'out'}
            };

            var chart = new google.charts.Line(document.getElementById('linechart_material'));
            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    </script>
@endsection