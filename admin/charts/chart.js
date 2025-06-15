let TwelveMChart;
let ThirtyDChart;
let HubChart;

(Chart.defaults.global.defaultFontFamily = "Metropolis"),
'-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = "#858796";

window.onload = function () {
    Load12MonthFlightChart();
    Load30DayFlightChart();
    LoadHubMonthChart();
}

async function Load12MonthFlightChart(){
    var year = document.getElementById("12mYear");
    await fetch(url + 'admin/charts/data/twelvemonthsstats.php?year=' + year.value).then(function (response) {
      return response.json();
    }).then(function (json) {
        var type = document.getElementById("12mType");
        if(TwelveMChart != null){
            TwelveMChart.destroy();
        }
        draw12MonthChart(json, type.value);
    }).catch(function (error) {
      console.error(error);
    });
}

async function Load30DayFlightChart(){
    await fetch(url + 'admin/charts/data/currentmonthsstats.php').then(function (response) {
      return response.json();
    }).then(function (json) {
        var type = document.getElementById("30dType");
        if(ThirtyDChart != null){
            ThirtyDChart.destroy();
        }
        draw30DayChart(json, type.value);  
    }).catch(function (error) {
      console.error(error);
    });
}

async function LoadHubMonthChart(){
   await fetch(url + 'admin/charts/data/hubmonthstats.php').then(function (response) {
      return response.json();
    }).then(function (json) {
        var type = document.getElementById("hubType");
        if(HubChart != null){
            HubChart.destroy();
        }
        drawHubChart(json, type.value);  
    }).catch(function (error) {
      console.error(error);
    });
}

//12 month flights chart
function draw12MonthChart(json, type){
    var graphData = json.map(e => e[type]);
    var graphLabels = json.map(e => e.month);
var ctx = document.getElementById("12MonthFlights");
TwelveMChart = new Chart(ctx, {
type: "line",
data: {
    labels: graphLabels,
    datasets: [{
        label: type,
        lineTension: 0.3,
        backgroundColor: "#eee",
        borderColor: "rgba(14, 154, 201)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(14, 154, 201)",
        pointBorderColor: "rgba(14, 154, 201)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(14, 154, 201)",
        pointHoverBorderColor: "rgba(14, 154, 201)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
        data: graphData
    }]
},
options: {
    maintainAspectRatio: false,
    layout: {
        padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 0
        }
    },
    scales: {
        xAxes: [{
            time: {
                unit: "date"
            },
            gridLines: {
                display: false,
                drawBorder: false
            },
            ticks: {
                maxTicksLimit: 7
            }
        }],
        yAxes: [{
            ticks: {
                maxTicksLimit: 5,
                padding: 10,
                callback: function(value, index, values) {
                    return number_format(value);
                }
            },
            gridLines: {
                color: "rgb(234, 236, 244)",
                zeroLineColor: "rgb(234, 236, 244)",
                drawBorder: false,
                borderDash: [2],
                zeroLineBorderDash: [2]
            }
        }]
    },
    legend: {
        display: false
    },
    tooltips: {
        backgroundColor: "rgb(255,255,255)",
        bodyFontColor: "#858796",
        titleMarginBottom: 10,
        titleFontColor: "#6e707e",
        titleFontSize: 14,
        borderColor: "#dddfeb",
        borderWidth: 1,
        xPadding: 15,
        yPadding: 15,
        displayColors: false,
        intersect: false,
        mode: "index",
        caretPadding: 10,
        callbacks: {
            label: function(tooltipItem, chart) {
                var datasetLabel =
                    chart.datasets[tooltipItem.datasetIndex].label || "";
                return datasetLabel + ": " + number_format(tooltipItem.yLabel, 2);
            }
        },
    },
    animation: {
        onComplete: function() {
           $(".12mloading").hide();
        }
     },
}
});
}

//Current month flights
function draw30DayChart(json, type){
    var graphData = json.map(e => e[type]);
    var graphLabels = json.map(e => e.day);
var ctx = document.getElementById("CurrentMonthFlights");
 ThirtyDChart = new Chart(ctx, {
type: "line",
data: {
    labels: graphLabels,
    datasets: [{
        label: type,
        lineTension: 0.3,
        backgroundColor: "#eee",
        borderColor: "rgba(14, 154, 201)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(14, 154, 201)",
        pointBorderColor: "rgba(14, 154, 201)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(14, 154, 201)",
        pointHoverBorderColor: "rgba(14, 154, 201)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
        data: graphData
    }]
},
options: {
    maintainAspectRatio: false,
    layout: {
        padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 0
        }
    },
    scales: {
        xAxes: [{
            time: {
                unit: "date"
            },
            gridLines: {
                display: false,
                drawBorder: false
            },
            ticks: {
                maxTicksLimit: 7,
                callback: function(value, index, values) {
                    return number_format(value) + getOrdinal(number_format(value));
                }
            }
        }],
        yAxes: [{
            ticks: {
                maxTicksLimit: 5,
                padding: 10,
                beginAtZero:true,
                callback: function(value, index, values) {
                    return number_format(value);
                }
            },
            gridLines: {
                color: "rgb(234, 236, 244)",
                zeroLineColor: "rgb(234, 236, 244)",
                drawBorder: false,
                borderDash: [2],
                zeroLineBorderDash: [2]
            }
        }]
    },
    legend: {
        display: false
    },
    tooltips: {
        backgroundColor: "rgb(255,255,255)",
        bodyFontColor: "#858796",
        titleMarginBottom: 10,
        titleFontColor: "#6e707e",
        titleFontSize: 14,
        borderColor: "#dddfeb",
        borderWidth: 1,
        xPadding: 15,
        yPadding: 15,
        displayColors: false,
        intersect: false,
        mode: "index",
        caretPadding: 10,
        callbacks: {
            label: function(tooltipItem, chart) {
                var datasetLabel =
                    chart.datasets[tooltipItem.datasetIndex].label || "";
                return datasetLabel + ": " + number_format(tooltipItem.yLabel, 2);
            },
            title: function(tooltipItem, chart) {
                return tooltipItem[0].xLabel + getOrdinal(tooltipItem[0].xLabel);
            }
        }
    },
    animation: {
        onComplete: function() {
           $(".30dloading").hide();
        }
     },
}
});
}

//base activity
function drawHubChart(json, type){
    var graphData = json.map(e => e[type]);
    var graphLabels = json.map(e => e.hub);
var ctx = document.getElementById("hubChart");
HubChart = new Chart(ctx, {
    type: "doughnut",
    data: {
        labels: graphLabels,
        datasets: [{
            data: graphData,
            backgroundColor: [
                "rgba(0, 97, 242, 1)",
                "rgba(0, 172, 105, 1)",
                "rgba(88, 0, 232, 1)",
                "rgba(235, 52, 107)",
                "rgba(235, 192, 52)",
                "rgba(235, 98, 52)",
                "rgba(52, 235, 204)",
            ],
            hoverBackgroundColor: [
                "rgba(0, 97, 242, 0.9)",
                "rgba(0, 172, 105, 0.9)",
                "rgba(88, 0, 232, 0.9)",
                "rgba(235, 52, 101)",
                "rgba(235, 201, 52)",
                "rgba(235, 107, 52)",
                "rgba(52, 235, 214)",
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)"
        }]
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: "#dddfeb",
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10
        },
        legend: {
            display: false
        },
        cutoutPercentage: 80,
        animation: {
            onComplete: function() {
               $(".hubloading").hide();
            }
         },
    }
});
}

function number_format(number, decimals, dec_point, thousands_sep) {
    // *     example: number_format(1234.56, 2, ',', ' ');
    // *     return: '1 234,56'
    number = (number + "").replace(",", "").replace(" ", "");
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = typeof thousands_sep === "undefined" ? "," : thousands_sep,
        dec = typeof dec_point === "undefined" ? "." : dec_point,
        s = "",
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return "" + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || "").length < prec) {
        s[1] = s[1] || "";
        s[1] += new Array(prec - s[1].length + 1).join("0");
    }
    return s.join(dec);
}

function getOrdinal(n) {
    let ord = 'th';
    if (n % 10 == 1 && n % 100 != 11)
    {
      ord = 'st';
    }
    else if (n % 10 == 2 && n % 100 != 12)
    {
      ord = 'nd';
    }
    else if (n % 10 == 3 && n % 100 != 13)
    {
      ord = 'rd';
    }
    return ord;
  }