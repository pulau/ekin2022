<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
    $(function () {
        /* ChartJS
        * -------
        * Here we will create a few charts using ChartJS
        */
       var areaChartData = {
         labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
         datasets: [
           {
             label               : 'Electronics',
             fillColor           : 'rgba(210, 214, 222, 1)',
             strokeColor         : 'rgba(210, 214, 222, 1)',
             pointColor          : 'rgba(210, 214, 222, 1)',
             pointStrokeColor    : '#c1c7d1',
             pointHighlightFill  : '#fff',
             pointHighlightStroke: 'rgba(220,220,220,1)',
             data                : [65, 59, 80, 81, 56, 55, 40]
           },
           {
             label               : 'Digital Goods',
             fillColor           : 'rgba(60,141,188,0.9)',
             strokeColor         : 'rgba(60,141,188,0.8)',
             pointColor          : '#3b8bba',
             pointStrokeColor    : 'rgba(60,141,188,1)',
             pointHighlightFill  : '#fff',
             pointHighlightStroke: 'rgba(60,141,188,1)',
             data                : [28, 48, 40, 19, 86, 27, 90]
           }
         ]
       }

       var areaChartOptions = {
         //Boolean - If we should show the scale at all
         showScale               : true,
         //Boolean - Whether grid lines are shown across the chart
         scaleShowGridLines      : false,
         //String - Colour of the grid lines
         scaleGridLineColor      : 'rgba(0,0,0,.05)',
         //Number - Width of the grid lines
         scaleGridLineWidth      : 1,
         //Boolean - Whether to show horizontal lines (except X axis)
         scaleShowHorizontalLines: true,
         //Boolean - Whether to show vertical lines (except Y axis)
         scaleShowVerticalLines  : true,
         //Boolean - Whether the line is curved between points
         bezierCurve             : true,
         //Number - Tension of the bezier curve between points
         bezierCurveTension      : 0.3,
         //Boolean - Whether to show a dot for each point
         pointDot                : false,
         //Number - Radius of each point dot in pixels
         pointDotRadius          : 4,
         //Number - Pixel width of point dot stroke
         pointDotStrokeWidth     : 1,
         //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
         pointHitDetectionRadius : 20,
         //Boolean - Whether to show a stroke for datasets
         datasetStroke           : true,
         //Number - Pixel width of dataset stroke
         datasetStrokeWidth      : 2,
         //Boolean - Whether to fill the dataset with a color
         datasetFill             : true,
         //String - A legend template
         legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].lineColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
         //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
         maintainAspectRatio     : true,
         //Boolean - whether to make the chart responsive to window resizing
         responsive              : true
       }

       //-------------
       //- BAR CHART -
       //-------------
       var barChartCanvas                   = $('#barChart').get(0).getContext('2d')
       var barChart                         = new Chart(barChartCanvas)
       var barChartData                     = areaChartData
       barChartData.datasets[1].fillColor   = '#00a65a'
       barChartData.datasets[1].strokeColor = '#00a65a'
       barChartData.datasets[1].pointColor  = '#00a65a'
       var barChartOptions                  = {
         //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
         scaleBeginAtZero        : true,
         //Boolean - Whether grid lines are shown across the chart
         scaleShowGridLines      : true,
         //String - Colour of the grid lines
         scaleGridLineColor      : 'rgba(0,0,0,.05)',
         //Number - Width of the grid lines
         scaleGridLineWidth      : 1,
         //Boolean - Whether to show horizontal lines (except X axis)
         scaleShowHorizontalLines: true,
         //Boolean - Whether to show vertical lines (except Y axis)
         scaleShowVerticalLines  : true,
         //Boolean - If there is a stroke on each bar
         barShowStroke           : true,
         //Number - Pixel width of the bar stroke
         barStrokeWidth          : 2,
         //Number - Spacing between each of the X value sets
         barValueSpacing         : 5,
         //Number - Spacing between data sets within X values
         barDatasetSpacing       : 1,
         //String - A legend template
         legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
         //Boolean - whether to make the chart responsive
         responsive              : true,
         maintainAspectRatio     : true
       }

       barChartOptions.datasetFill = false
       barChart.Bar(barChartData, barChartOptions)
    });
</script>