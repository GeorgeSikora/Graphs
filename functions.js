
function buildGraphFromUrl(divId, targetGraphUrl) {

    var ctx = $('#'+divId+' canvas')[0].getContext('2d');

    $(`#${divId} .overlay`).html('Načítám...');

    $.ajax({
        type: "POST",
        url: targetGraphUrl,
        data: {

        },
        success: (data) => {
            console.log(data);

            try {
                JSON.parse(data);
            } catch (e) {
                $(`#${divId} .overlay`).addClass('error');
                $(`#${divId} .overlay`).html('Data parse error <i class="fas fa-exclamation-triangle"></i>');
                return;
            }

            var dataObj = JSON.parse(data);
            var timeFormat = 'YYYY-MM-DD';

            console.log(dataObj);

            $(`#${divId} .overlay`).html('');
            var chart = new Chart(ctx, {

                type: dataObj.graphType, // line / bar / radar / doughnut / scatter
            
                data: dataObj.graphData,

                options: {
                    spanGaps: true,
                    layout: {
                        padding: 10,
                    },
                    title: {
                        display: true,
                        text: dataObj.graphName,
                    },
                    scales: {
                        xAxes: [{
                            type: 'time',
                            time: {
                                minUnit: "day",
                            },
                            ticks: {
                                autoSkip: true,
                                maxTicksLimit: 20,
                            },
                        }],
                        yAxes: [{
                            ticks: {
                                autoSkip: true,
                                maxTicksLimit: 8,
                            },
                        }]
                    },
                }
            });
        },
        error: (XMLHttpRequest, textStatus, errorThrown) => {
            $(`#${divId} .overlay`).addClass('error');
            $(`#${divId} .overlay`).html('URL post error <i class="fas fa-exclamation-triangle"></i>');
        }
    });
}