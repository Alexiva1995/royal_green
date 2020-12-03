$(document).ready(function () {
  carruselRango()
  carruselPaquetes()
  let url = 'admin/chart/usuarios'
  $.get(url, function(data){
    data = JSON.parse(data)
    graphicReferred(data)
  })
})

/**
 * Permite inicializar el carrusel de los rangos
 */
function carruselRango() {
  $('.carrusel_rango').slick({
    infinite: true,
    centerMode: true,
    centerPadding: '80px',
    variableWidth: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    touchMove: false,
    accessibility: false,
    arrows: false,
    responsive: [
      {
        breakpoint: 768,
        settings: {
          arrows: false,
          centerMode: true,
          centerPadding: '40px',
          slidesToShow: 3
        }
      },
      {
        breakpoint: 480,
        settings: {
          arrows: false,
          centerMode: true,
          centerPadding: '40px',
          slidesToShow: 1
        }
      }
    ]
  });
}

function carruselPaquetes() {
  $('.carrusel_paquete').slick({
    infinite: true,
    centerMode: true,
    centerPadding: '20px',
    variableWidth: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    arrows: false,
    responsive: [
      {
        breakpoint: 768,
        settings: {
          arrows: false,
          centerMode: true,
          centerPadding: '40px',
          slidesToShow: 3
        }
      },
      {
        breakpoint: 480,
        settings: {
          arrows: false,
          centerMode: true,
          centerPadding: '40px',
          slidesToShow: 1
        }
      }
    ]
  });
}

function graphicReferred(data) {

  var themeColors = ['#19BAFD'];

  var columnChartOptions = {
    chart: {
      height: 350,
      type: 'bar',
    },
    colors: themeColors,
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '55%',
      },
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      show: true,
      width: 2,
      colors: ['transparent']
    },
    series: [{
      name: 'Referidos',
      data: data
    }],
    legend: {
      offsetY: -10
    },
    xaxis: {
      categories: ['Jua', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    },
    yaxis: {
      title: {
        text: 'Referidos'
      }
    },
    fill: {
      opacity: 1

    },
    tooltip: {
      y: {
        formatter: function (val) {
          return val + " Referidos"
        }
      }
    }
  }
  var columnChart = new ApexCharts(
    document.querySelector("#grafica_user"),
    columnChartOptions
  );

  columnChart.render();
}