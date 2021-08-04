var vm_dashboard = new Vue({
    el: '#dashboard-analytics',
    created:function (){
        // this.getDataGraphics()
    },
    data: function(){
        return {
            Colores: {
                primary: '#D6A83E',
                success: '#28C76F',
                danger: '#EA5455',
                warning: '#FF9F43',
                primary_light: '#A9A2F6',
                success_light: '#55DD92',
                warning_light: '#ffc085',
            },
            DataInfoGraphic: [],
            idrango: 0
        }
    },
    methods:{
        /**
         * Permite obtener los datos para la grafica e inicializarla
         */
        getDataGraphics: function(){
            let url = route('home.data.graphic')
            axios.get(url).then((response) => {
                this.DataInfoGraphic = response.data
                this.graphicSaldo()
                this.graphicComision()
                this.graphicOrdenes()
                this.graphicTickets()
            })
        },

        /**
         * Permite Actualizar el lado a registrar un usuario
         * @param {string} side 
         */
        updateBinarySide: function(side){
             let url = route('ajax.update.side.binary', side)
             axios.get(url).then((response) => {
                 if (response.data == 'bien') {
                    location.reload();
                }
            }).catch(function (error) {
                toastr.warning("Ocurrio un error al Actualizar el lado binario", '¡Advertencia!', { "progressBar": true });
            })
        },

        /**
         * Permite generar la grafica de saldo
         */
        graphicSaldo: function(){
            var gainedChartoptions = {
                chart: {
                    height: 100,
                    type: 'area',
                    toolbar:{
                      show: false,
                    },
                    sparkline: {
                        enabled: true
                    },
                    grid: {
                        show: false,
                        padding: {
                            left: 0,
                            right: 0
                        }
                    },
                },
                colors: [this.Colores.primary],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.9,
                        opacityFrom: 0.7,
                        opacityTo: 0.5,
                        stops: [0, 80, 100]
                    }
                },
                series: [{
                    name: 'Saldo',
                    data: this.DataInfoGraphic.saldo
                }],
        
                xaxis: {
                  labels: {
                    show: false,
                  },
                  axisBorder: {
                    show: false,
                  }
                },
                yaxis: [{
                    y: 0,
                    offsetX: 0,
                    offsetY: 0,
                    padding: { left: 0, right: 0 },
                }],
                tooltip: {
                    x: { show: false }
                },
            }
        
            var gainedChart = new ApexCharts(
                document.querySelector("#line-area-chart-1"),
                gainedChartoptions
            );
        
            gainedChart.render();
        },

        /**
         * Permite generar la grafica de comisiones
         */
        graphicComision: function(){
            var revenueChartoptions = {
                chart: {
                    height: 100,
                    type: 'area',
                    toolbar:{
                      show: false,
                    },
                    sparkline: {
                        enabled: true
                    },
                    grid: {
                        show: false,
                        padding: {
                            left: 0,
                            right: 0
                        }
                    },
                },
                colors: [this.Colores.success],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.9,
                        opacityFrom: 0.7,
                        opacityTo: 0.5,
                        stops: [0, 80, 100]
                    }
                },
                series: [{
                    name: 'Comisiones',
                    data: this.DataInfoGraphic.comisiones
                }],
        
                xaxis: {
                  labels: {
                    show: false,
                  },
                  axisBorder: {
                    show: false,
                  }
                },
                yaxis: [{
                    y: 0,
                    offsetX: 0,
                    offsetY: 0,
                    padding: { left: 0, right: 0 },
                }],
                tooltip: {
                    x: { show: false }
                },
            }
        
            var revenueChart = new ApexCharts(
                document.querySelector("#line-area-chart-2"),
                revenueChartoptions
            );
        
            revenueChart.render();
        },

        /**
         * Permite generar la grafica de los tickets
         */
        graphicTickets: function(){
            var salesChartoptions = {
                chart: {
                    height: 100,
                    type: 'area',
                    toolbar:{
                      show: false,
                    },
                    sparkline: {
                        enabled: true
                    },
                    grid: {
                        show: false,
                        padding: {
                            left: 0,
                            right: 0
                        }
                    },
                },
                colors: [this.Colores.danger],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.9,
                        opacityFrom: 0.7,
                        opacityTo: 0.5,
                        stops: [0, 80, 100]
                    }
                },
                series: [{
                    name: 'Ordenes',
                    data: this.DataInfoGraphic.tickets
                }],
        
                xaxis: {
                  labels: {
                    show: false,
                  },
                  axisBorder: {
                    show: false,
                  }
                },
                yaxis: [{
                    y: 0,
                    offsetX: 0,
                    offsetY: 0,
                    padding: { left: 0, right: 0 },
                }],
                tooltip: {
                    x: { show: false }
                },
            }
        
            var salesChart = new ApexCharts(
                document.querySelector("#line-area-chart-3"),
                salesChartoptions
            );
        
            salesChart.render();
        },

        /**
         * Permite generar la grafica de las ordenes
         */
        graphicOrdenes: function(){
            var orderChartoptions = {
                chart: {
                    height: 100,
                    type: 'area',
                    toolbar:{
                      show: false,
                    },
                    sparkline: {
                        enabled: true
                    },
                    grid: {
                        show: false,
                        padding: {
                            left: 0,
                            right: 0
                        }
                    },
                },
                colors: [this.Colores.warning],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.9,
                        opacityFrom: 0.7,
                        opacityTo: 0.5,
                        stops: [0, 80, 100]
                    }
                },
                series: [{
                    name: 'Ordenes',
                    data: this.DataInfoGraphic.ordenes
                }],
        
                xaxis: {
                  labels: {
                    show: false,
                  },
                  axisBorder: {
                    show: false,
                  }
                },
                yaxis: [{
                    y: 0,
                    offsetX: 0,
                    offsetY: 0,
                    padding: { left: 0, right: 0 },
                }],
                tooltip: {
                    x: { show: false }
                },
            }
        
            var orderChart = new ApexCharts(
                document.querySelector("#line-area-chart-4"),
                orderChartoptions
            );
        
            orderChart.render();
        }
    }
})