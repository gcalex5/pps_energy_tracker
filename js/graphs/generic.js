/**
 * Created by alexm on 6/3/2016.
 */

window.onload = function () {

    var test = JSON.parse('{{ graph_data|raw }}');
    var chart = new CanvasJS.Chart("chartContainer",
        {

            title:{
                text: "Earthquakes - per month"
            },
            axisX: {
                valueFormatString: "MMM",
                interval:1,
                intervalType: "month"
            },
            axisY:{
                includeZero: false

            },
            data: [
                {
                    type: "line",

                    dataPoints: [
                        { x: new Date(2012, 00, 1), y: 450 },
                        { x: new Date(2012, 01, 1), y: 414},
                        { x: new Date(2012, 02, 1), y: 520, indexLabel: "highest",markerColor: "red", markerType: "triangle"},
                        { x: new Date(2012, 03, 1), y: 460 },
                        { x: new Date(2012, 04, 1), y: 450 },
                        { x: new Date(2012, 05, 1), y: 500 },
                        { x: new Date(2012, 06, 1), y: 480 },
                        { x: new Date(2012, 07, 1), y: 480 },
                        { x: new Date(2012, 08, 1), y: 410 , indexLabel: "lowest",markerColor: "DarkSlateGrey", markerType: "cross"},
                        { x: new Date(2012, 09, 1), y: 500 },
                        { x: new Date(2012, 10, 1), y: 480 },
                        { x: new Date(2012, 11, 1), y: 510 }
                    ]
                }
            ]
        });
    console.log('foo');
    chart.render();
}