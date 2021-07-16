
function buildGraphFromUrl(divId, targetGraphUrl) {

    var ctx = document.getElementById(divId).getContext('2d');

    $.ajax({
        type: "POST",
        url: targetGraphUrl,
        data: {

        },
        success: (data)=>{
            console.log(data);
            
            var dataObj = JSON.parse(data);
            var timeFormat = 'YYYY-MM-DD';

            console.log(dataObj);

            var chart = new Chart(ctx, {

                type: 'line', // line / bar / radar / doughnut
            
                data: dataObj,

                options: {
                    spanGaps: true,
                    layout: {
                        padding: 10,
                    },
                    plugins: {
                        title: {
                            display: false,
                            text: 'Zalidnění populace',
                        }
                    },
                    scales: {
                        xAxes: [{
                            type: 'time',
                            time: {
                                minUnit: "day",
                            },
                            ticks: {
                                autoSkip: false,
                            },
                        }],
                        yAxes: [{
                        }]
                    },
                }
            });
        }
    });
}