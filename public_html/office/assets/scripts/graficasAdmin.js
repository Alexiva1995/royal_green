var $primary = '#7367F0',
    $success = '#28C76F',
    $danger = '#EA5455',
    $warning = '#FF9F43',
    $info = '#00cfe8',
    $label_color_light = '#dae1e7';

  var themeColors = [$primary, $success, $danger, $warning, $info];

$(document).ready(function () {
    let url = 'chart/all'
    $.get(url, function(data){
      datos = JSON.parse(data)
      user(datos.user)
      compras(datos.compras)
    })
  })
  
function compras(data) {
//     // Line Chart
  // ----------------------------------
  var lineChartOptions = {
    chart: {
      height: 350,
      type: 'line',
      zoom: {
        enabled: false
      }
    },
    colors: themeColors,
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'straight'
    },
    series: [{
      name: "Totas compras",
      data: data[1],
    }],
    grid: {
      row: {
        colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
        opacity: 0.5
      },
    },
    xaxis: {
      categories: data[0],
    },
    yaxis: {
      tickAmount: 5,
    }
  }
  var lineChart = new ApexCharts(
    document.querySelector("#line-chart"),
    lineChartOptions
  );
  lineChart.render();
}

function user(data) {
    // Pie Chart
  // -----------------------------
  var pieChartOptions = {
    chart: {
      type: 'pie',
      height: 350
    },
    colors: themeColors,
    labels: ['Activos', 'Inactivos'],
    series: [data[1].total, data[0].total],
    legend: {
      itemMargin: {
        horizontal: 2
      },
    },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 350
        },
        legend: {
          position: 'bottom'
        }
      }
    }]
  }
  var pieChart = new ApexCharts(
    document.querySelector("#pie-chart"),
    pieChartOptions
  );
  pieChart.render();
}