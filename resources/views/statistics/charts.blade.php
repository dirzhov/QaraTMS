<div class="m-3" style="width:500px;height:210px">
    <canvas id="priorityChart" style="width:100%;max-width:500px; height:210px;"></canvas>
</div>
<div class="m-3" style="width:500px;height:210px">
    <canvas id="severityChart" style="width:100%;max-width:500px; height:210px;"></canvas>
</div>
<div class="m-3" style="width:500px;height:210px">
    <canvas id="durationChart" style="width:100%;max-width:500px; height:210px;"></canvas>
</div>

<script>
    const interpolateBetweenColors = (
        fromColor,
        toColor,
        percent
    ) => {
        const delta = percent / 100;
        const r = Math.round(toColor.r + (fromColor.r - toColor.r) * delta);
        const g = Math.round(toColor.g + (fromColor.g - toColor.g) * delta);
        const b = Math.round(toColor.b + (fromColor.b - toColor.b) * delta);

        return `rgb(${r}, ${g}, ${b})`;
    };

    let chartData = {
        labels: ['account', 'vip_page', 'contact', 'messages', 'search'],
        data: [
            [11, 19, 3, 5, 1],
            [12, 19, 3, 5, 2],
            [13, 19, 3, 5, 3],
            [14, 19, 3, 5, 4],
        ]
    }

    const data3 = {
        labels: chartData.labels,
        datasets: [{
            label: '# of Fails',
            data: chartData.data[0],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
            ],
            borderColor: [
                'rgb(255, 99, 132)',
            ],
            datalabels: {
                // align: 'top',
                color: '#303030'
            },
            //animations: false,
            borderWidth: 1
        },{
            label: '# of Passed',
            hidden: true,
            data: chartData.data[1],
            backgroundColor: [
                'rgba(75, 192, 192, 0.2)',
            ],
            borderColor: [
                'rgb(75, 192, 192)',
            ],
            datalabels: {
                color: '#303030'
            },
            //animations: false,
            borderWidth: 1
        },{
            label: '# of Skipped',
            hidden: true,
            data: chartData.data[2],
            backgroundColor: [
                'rgba(201, 203, 207, 0.2)',
            ],
            borderColor: [
                'rgb(201, 203, 207)',
            ],
            datalabels: {
                color: '#303030'
            },
            //animations: false,
            borderWidth: 1
        },{
            label: '# of Fixed',
            hidden: true,
            data: chartData.data[3],
            backgroundColor: [
                'rgba(255, 205, 86, 0.2)',
            ],
            borderColor: [
                'rgb(255, 205, 86)',
            ],
            datalabels: {
                color: '#303030'
            },
            //animations: false,
            borderWidth: 1
        }
        ]
    };

    const ctx3 = document.getElementById('groupsChart');

    const groupChart = new Chart(ctx3, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: data3,
        options: {
            responsive: true,
            indexAxis: 'y',
            scales: {
                y: {
                    afterFit: function(axis) {axis.width = 150;},
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 15,
                            weight: "bold"
                        }
                    }
                }
            },
            // legend: {
            //     display: false
            // },
            plugins: {
                title: {
                    display: true,
                    text: 'By Groups'
                },
                legend: {
                    position: 'right',
                },
                // Change options for ALL labels of THIS CHART
                datalabels: {
                    color: '#36A2EB'
                }
            }
        }
    });

    ctx3.onclick = function(evt) {
        var points = groupChart.getElementsAtEventForMode(evt, 'nearest', {
            intersect: true
        }, true);
        if (points.length) {
            const firstPoint = points[0];
            const label = groupChart.data.labels[firstPoint.index];
            const value = groupChart.data.datasets[firstPoint.datasetIndex].data[firstPoint.index];
            if (firstPoint.datasetIndex == 0) {
                // open defects in group
                if (!$('#nav-group-defects').hasClass('active')) {
                    const triggerEl = document.querySelector('#nav-tab button[data-bs-target="#nav-group-defects"]')
                    bootstrap.Tab.getOrCreateInstance(triggerEl).show()
                }

                $('#group_issues_list').prev('#selected_group').html(`<div class="fs-5 fw-bold my-2">${label}</div>`);
                loadDefects(`/api/statistics/get_defects_of_group/{{$testRun->id}}/${label}`, '#group_issues_list');

            } else if (firstPoint.datasetIndex == 3) {
                // open test defects
                if (!$('#nav-test-defects').hasClass('active')) {
                    const triggerEl = document.querySelector('#nav-tab button[data-bs-target="#nav-test-defects"]')
                    bootstrap.Tab.getOrCreateInstance(triggerEl).show()
                }
            }
        }
    };
</script>
<script>
    const ctx = document.getElementById('priorityChart');

    const labels = ['P1', 'P2', 'P3', 'P4', 'P5'];
    const data = {
        labels: labels,
        datasets: [{
            label: '# of Fails',
            data: [12, 19, 3, 5, 2],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
            ],
            borderColor: [
                'rgb(255, 99, 132)',
            ],
            datalabels: {
                // align: 'top',
                color: '#303030'
            },
            borderWidth: 1
        },{
            label: '# of Passed',
            hidden: true,
            data: [20, 3, 12, 5, 2],
            backgroundColor: [
                'rgba(75, 192, 192, 0.2)',
            ],
            borderColor: [
                'rgb(75, 192, 192)',
            ],
            datalabels: {
                color: '#303030'
            },
            borderWidth: 1
        },{
            label: '# of Skipped',
            hidden: true,
            data: [20, 3, 12, 5, 2],
            backgroundColor: [
                'rgba(201, 203, 207, 0.2)',
            ],
            borderColor: [
                'rgb(201, 203, 207)',
            ],
            datalabels: {
                color: '#303030'
            },
            borderWidth: 1
        },{
            label: '# of Fixed',
            hidden: true,
            data: [20, 3, 12, 5, 2],
            backgroundColor: [
                'rgba(255, 205, 86, 0.2)',
            ],
            borderColor: [
                'rgb(255, 205, 86)',
            ],
            datalabels: {
                color: '#303030'
            },
            borderWidth: 1
        }
        ]
    };

    const prioritiesChart = new Chart(ctx, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: data,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'By Priority'
                },
                // Change options for ALL labels of THIS CHART
                datalabels: {
                    color: '#36A2EB'
                }
            }
        }
    });

</script>

<script>
    const ctx2 = document.getElementById('severityChart');

    const labels2 = ['P1', 'P2', 'P3', 'P4', 'P5'];
    const data2 = {
        labels: labels2,
        datasets: [{
            label: '# of Fails',
            data: [12, 19, 3, 5, 2],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
            ],
            borderColor: [
                'rgb(255, 99, 132)',
            ],
            datalabels: {
                // align: 'top',
                color: '#303030'
            },
            borderWidth: 1
        },{
            label: '# of Passed',
            hidden: true,
            data: [20, 3, 12, 5, 2],
            backgroundColor: [
                'rgba(75, 192, 192, 0.2)',
            ],
            borderColor: [
                'rgb(75, 192, 192)',
            ],
            datalabels: {
                color: '#303030'
            },
            borderWidth: 1
        },{
            label: '# of Skipped',
            hidden: true,
            data: [20, 3, 12, 5, 2],
            backgroundColor: [
                'rgba(201, 203, 207, 0.2)',
            ],
            borderColor: [
                'rgb(201, 203, 207)',
            ],
            datalabels: {
                color: '#303030'
            },
            borderWidth: 1
        },{
            label: '# of Fixed',
            hidden: true,
            data: chartData.data[3],
            backgroundColor: [
                'rgba(255, 205, 86, 0.2)',
            ],
            borderColor: [
                'rgb(255, 205, 86)',
            ],
            datalabels: {
                color: '#303030'
            },
            borderWidth: 1
        }
        ]
    };

    const severitiesChart = new Chart(ctx2, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: data2,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'By Severity'
                },
                // Change options for ALL labels of THIS CHART
                datalabels: {
                    color: '#36A2EB'
                }
            }
        }
    });

</script>

<script>
    const ctx4 = document.getElementById('durationChart');

    let colors = new Array();
    for (let i = 0; i < 100; i += 10) {
        colors.push(interpolateBetweenColors(
            {r: 255, g: 0, b: 0},
            {r: 0, g: 200, b: 0},
            i
        ));
    }

    let gradient = ctx4.getContext("2d").createLinearGradient(0, 0, 600, 0)
    gradient.addColorStop(0, 'rgb(115,255,0)')
    gradient.addColorStop(1, '#f00000')

    const labels4 = ['P1', 'P2', 'P3', 'P4', 'P5'];
    const data4 = {
        labels: labels4,
        datasets: [{
            label: '# of Test Cases',
            data: [12, 19, 3, 5, 2],
            backgroundColor: [
                //'rgba(54, 162, 235, 0.2)',
                gradient
            ],
            borderColor: [
                'rgb(54, 162, 235)',
            ],
            datalabels: {
                // align: 'top',
                color: '#303030'
            },
            borderWidth: 2
        }]
    };

    durationsChart = new Chart(ctx4, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: data4,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                },
                x: {
                    title: {
                        display: true,
                        text: 'Duration (ms)',
                        padding: {top: 5, left: 0, right: 0, bottom: 0}
                    },
                    ticks: {
                        color: colors
                    },
                    beginAtZero: true,
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Test Duration'
                },
                // Change options for ALL labels of THIS CHART
                datalabels: {
                    color: '#36A2EB'
                }
            }
        }
    });



    var headers = {
        'Authorization': 'Bearer ' + '{{ Config::get('app.api_token') }}',
        'Content-Type': 'application/json'
    }

    function updateChart(url, chart) {
        $.ajax({
            url: url,
            type: 'get',
            headers: headers,
            success: function (data) {
                chart.data.labels = data.data.labels;

                chart.data.datasets[0].data = data.data.data[0];
                chart.data.datasets[1].data = data.data.data[1];
                chart.data.datasets[2].data = data.data.data[2];
                chart.data.datasets[3].data = data.data.data[3];

                chart.update();
            }
        });
    }


    function updateDurations() {
        $.ajax({
            url: "/api/statistics/get_all_by_duration/{{$testRun->id}}",
            type: 'get',
            headers: headers,
            success: function (data) {
                durationsChart.data.labels = data.data.labels;
                durationsChart.data.datasets[0].data = data.data.data;
                durationsChart.update();
            }
        });
    }

    updateChart("/api/statistics/get_all_by_groups/{{$testRun->id}}", groupChart);
    updateChart("/api/statistics/get_all_by_priority/{{$testRun->id}}", prioritiesChart);
    updateChart("/api/statistics/get_all_by_severity/{{$testRun->id}}", severitiesChart);
    updateDurations();


</script>
