@extends('layouts.admin.admin')
@section('content')
<!-- Main content -->
<div class="content-wrapper">

    <div class="content-inner">

        <div class="page-header page-header-light">
            <div class="page-header-content header-elements-lg-inline">
                <div class="page-title d-flex">
                    <h4> <span class="font-weight-semibold">Dashboard</span></h4>
                    <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
                </div>
            </div>
        </div>

        <div class="content">

            <div class="row">
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-body bg-primary text-white has-bg-image">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="mb-0">{{ $data['user_count'] ?? 0 }}</h3>
                                <span class="text-uppercase font-size-xs">users</span>
                            </div>

                            <div class="ml-3 align-self-center">
                                <i class="icon-users2 icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card card-body bg-danger text-white has-bg-image">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="mb-0">{{ $data['employer_count'] ?? 0 }}</h3>
                                <span class="text-uppercase font-size-xs">employers</span>
                            </div>

                            <div class="ml-3 align-self-center">
                                <i class="icon-users4 icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card card-body bg-success text-white has-bg-image">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="mb-0">{{ $data['interview_video_count'] ?? 0 }}</h3>
                                <span class="text-uppercase font-size-xs">Interview Videos</span>
                            </div>

                            <div class="ml-3 align-self-center">
                                <i class="icon-video-camera2 icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card card-body bg-indigo text-white has-bg-image">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="mb-0">{{ $data['job_count'] ?? 0 }}</h3>
                                <span class="text-uppercase font-size-xs">jobs</span>
                            </div>

                            <div class="ml-3 align-self-center">
                                <i class="icon-users4 icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Jobs</h5>
                        </div>

                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="job-graph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Users</h5>
                        </div>

                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="user-graph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Employers</h5>
                        </div>

                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="employer-graph"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                
            </div>

            <div class="row">
                
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_script')
<script>
    // JOB GRAPH : START
    let jobMonthlyData = '<?php echo json_encode($monthlyJobData) ?>';
    jobMonthlyData = JSON.parse(jobMonthlyData);

    let jobGraphLabels = [];
    let jobGraphData = [];

    jobMonthlyData.map((value, index) => {
        jobGraphLabels.push(value.month);
        jobGraphData.push(value.total);
    });

    const jobData = {
        labels: jobGraphLabels,
        datasets: [{
            label: '',
            data: jobGraphData,
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
                '#4e8c95',
                '#cc8683',
                '#6c953d',
                '#3d647a',
                '#1e74ae',
            ],
            borderColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
                '#4e8c95',
                '#cc8683',
                '#6c953d',
                '#3d647a',
                '#1e74ae',
            ],
            borderWidth: 1
        }]
    };

    const jobGraphConfig = {
        type: 'bar',
        data: jobData,
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        // forces step size to be 50 units
                        stepSize: 10
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                },
            },
            plugins: {
                legend: {
                    labels: {
                        boxWidth: 0
                    }
                }
            },
        },
    };

    var jobGraph = new Chart(
        document.getElementById('job-graph'),
        jobGraphConfig
    );
    // JOB GRAPH : END

    // USER GRAPH : START
    let userMonthlyData = '<?php echo json_encode($monthlyUserData) ?>';
    userMonthlyData = JSON.parse(userMonthlyData);

    let userGraphLabels = [];
    let userGraphData = [];

    userMonthlyData.map((value, index) => {
        userGraphLabels.push(value.month);
        userGraphData.push(value.total);
    });

    const userData = {
        labels: userGraphLabels,
        datasets: [{
            label: '',
            data: userGraphData,
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
                '#4e8c95',
                '#cc8683',
                '#6c953d',
                '#3d647a',
                '#1e74ae',
            ],
            borderColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
                '#4e8c95',
                '#cc8683',
                '#6c953d',
                '#3d647a',
                '#1e74ae',
            ],
            borderWidth: 1
        }]
    };

    const userGraphConfig = {
        type: 'bar',
        data: userData,
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        // forces step size to be 50 units
                        stepSize: 10
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                },
            },
            plugins: {
                legend: {
                    labels: {
                        boxWidth: 0
                    }
                }
            },
        },
    };

    var userGraph = new Chart(
        document.getElementById('user-graph'),
        userGraphConfig
    );
    // USER GRAPH : END

    // EMPLOYER GRAPH : START
    let employerMonthlyData = '<?php echo json_encode($monthlyEmployerData) ?>';
    employerMonthlyData = JSON.parse(employerMonthlyData);

    let employerGraphLabels = [];
    let employerGraphData = [];

    employerMonthlyData.map((value, index) => {
        employerGraphLabels.push(value.month);
        employerGraphData.push(value.total);
    });

    const employerData = {
        labels: employerGraphLabels,
        datasets: [{
            label: '',
            data: employerGraphData,
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
                '#4e8c95',
                '#cc8683',
                '#6c953d',
                '#3d647a',
                '#1e74ae',
            ],
            borderColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)',
                '#4e8c95',
                '#cc8683',
                '#6c953d',
                '#3d647a',
                '#1e74ae',
            ],
            borderWidth: 1
        }]
    };

    const employerGraphConfig = {
        type: 'bar',
        data: employerData,
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        // forces step size to be 50 units
                        stepSize: 10
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                },
            },
            plugins: {
                legend: {
                    labels: {
                        boxWidth: 0
                    }
                }
            },
        },
    };

    var employerGraph = new Chart(
        document.getElementById('employer-graph'),
        employerGraphConfig
    );
    // EMPLOYER GRAPH : END
</script>
@endsection
@section('page_style')
<style>
    .popular-items-chart-wrapper {
        width: 50%;
        float: left;
    }
</style>
@endsection