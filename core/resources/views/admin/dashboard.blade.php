@extends('admin.layouts.app')
@section('panel') 
    <div class="row gy-4">
        
     <!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--deep-purple has-link box--shadow2">
                <a href="#" class="item-link"></a>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-university f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang("Total Cooperative")</span>
                            <h2 class="text-white">{{ $cooperativeCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--pink has-link box--shadow2">
                <a href="#" class="item-link"></a>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-university f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Total Section')</span>
                            <h2 class="text-white">{{ @$sectionCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--pink has-link box--shadow2">
                <a href="#" class="item-link"></a>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-university f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Total Localité')</span>
                            <h2 class="text-white">{{ @$localiteCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--primary has-link overflow-hidden box--shadow2">
                <a href="#" class="item-link"></a>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-users f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Total Producteur')</span>
                            <h2 class="text-white">{{ @$producteurCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
 
        <div class="col-xxl-4 col-sm-6">
            <div class="card bg--orange has-link box--shadow2">
                <a href="#" class="item-link"></a>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <i class="las la-map f-size--56"></i>
                        </div>
                        <div class="col-10 text-end">
                            <span class="text-white text--small">@lang("Total Parcelle")</span>
                            <h2 class="text-white">{{ @$parcelleCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->

        <div class="col-xxl-4 col-sm-6">
            <div class="card bg--green has-link box--shadow2">
                <a href="#" class="item-link"></a>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <i class="las la-graduation-cap f-size--56"></i>
                        </div>
                        <div class="col-10 text-end">
                            <span class="text-white text--small">@lang('Total Formation')</span>
                            <h2 class="text-white">{{ @$formationCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-4 col-sm-6">
            <div class="card bg--orange has-link box--shadow2">
                <a href="#" class="item-link"></a>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-2">
                            <i class="las la-map-marker f-size--56"></i>
                        </div>
                        <div class="col-10 text-end">
                            <span class="text-white text--small">@lang("Total Suivi Parcelle")</span>
                            <h2 class="text-white">{{ @$suiviparcelleCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
      
      

    </div><!-- row end-->

 

    <div class="row mb-none-30 mt-5">
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <h5 class="card-title" style="font-size: 16px;">Section par Coopérative</h5>
                    <canvas id="userBrowserChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="font-size: 16px;">Localite par Coopérative</h5>
                    <canvas id="userOsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="font-size: 16px;">Producteur par Coopérative</h5>
                    <canvas id="userCountryChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="font-size: 16px;">Producteur par Genre et Coopérative</h5>
                    <canvas id="cooperativeGenderChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="font-size: 16px;">Formation par Coopérative</h5>
                    <canvas id="userFormationChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="font-size: 16px;">Parcelle par Genre et Coopérative</h5>
                    <canvas id="parcelleGenderChart"></canvas>
                </div>
            </div>
        </div>

        
    </div>
     
@endsection

@push('script')
    <script src="{{ asset('assets/fcadmin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script>
        "use strict";
        var ctx = document.getElementById('userBrowserChart');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: @json(Arr::whereNotNull(Arr::pluck(@$sectionByCoop,'name'))),
                datasets: [{
                    data:  @json(Arr::whereNotNull(Arr::pluck(@$sectionByCoop,'total'))),
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
                        '#D980FA',
                        '#fccbcb',
                        '#45aaf2',
                        '#05dfd7',
                        '#FF00F6',
                        '#1e90ff',
                        '#2ed573',
                        '#eccc68',
                        '#ff5200',
                        '#cd84f1',
                        '#7efff5',
                        '#7158e2',
                        '#fff200',
                        '#ff9ff3',
                        '#08ffc8',
                        '#3742fa',
                        '#1089ff',
                        '#70FF61',
                        '#bf9fee',
                        '#574b90'
                    ],
                    borderColor: [
                        'rgba(231, 80, 90, 0.75)'
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                maintainAspectRatio: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: false
                    }],
                    yAxes: [{
                        display: false
                    }]
                },
                legend: {
                    display: true,
                }
            }
        });
        var ctx = document.getElementById('userOsChart');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json(Arr::whereNotNull(Arr::pluck(@$localiteByCoop,'name'))),
                datasets: [{
                    data: @json(Arr::whereNotNull(Arr::pluck(@$localiteByCoop,'total'))),
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
                        '#D980FA',
                        '#fccbcb',
                        '#45aaf2',
                        '#05dfd7',
                        '#FF00F6',
                        '#1e90ff',
                        '#2ed573',
                        '#eccc68',
                        '#ff5200',
                        '#cd84f1',
                        '#7efff5',
                        '#7158e2',
                        '#fff200',
                        '#ff9ff3',
                        '#08ffc8',
                        '#3742fa',
                        '#1089ff',
                        '#70FF61',
                        '#bf9fee',
                        '#574b90'
                    ],
                    borderColor: [
                        'rgba(0, 0, 0, 0.05)'
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: true
                    }],
                    yAxes: [{
                        display: true
                    }]
                },
                legend: {
                    display: false,
                }
            },
        });
        // Donut chart
        var ctx = document.getElementById('userCountryChart');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json(Arr::whereNotNull(Arr::pluck(@$producteurByCoop,'name'))),
                datasets: [{
                    data: @json(Arr::whereNotNull(Arr::pluck(@$producteurByCoop,'total'))),
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
                        '#D980FA',
                        '#fccbcb',
                        '#45aaf2',
                        '#05dfd7',
                        '#FF00F6',
                        '#1e90ff',
                        '#2ed573',
                        '#eccc68',
                        '#ff5200',
                        '#cd84f1',
                        '#7efff5',
                        '#7158e2',
                        '#fff200',
                        '#ff9ff3',
                        '#08ffc8',
                        '#3742fa',
                        '#1089ff',
                        '#70FF61',
                        '#bf9fee',
                        '#574b90'
                    ],
                    borderColor: [
                        'rgba(231, 80, 90, 0.75)'
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: true
                    }],
                    yAxes: [{
                        display: true
                    }]
                },
                legend: {
                    display: false,
                }
            }
        });

              // Donut chart
        var ctx = document.getElementById('userFormationChart');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json(Arr::whereNotNull(Arr::pluck(@$formationByCoop,'name'))),
                datasets: [{
                    data: @json(Arr::whereNotNull(Arr::pluck(@$formationByCoop,'total'))),
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
                        '#D980FA',
                        '#fccbcb',
                        '#45aaf2',
                        '#05dfd7',
                        '#FF00F6',
                        '#1e90ff',
                        '#2ed573',
                        '#eccc68',
                        '#ff5200',
                        '#cd84f1',
                        '#7efff5',
                        '#7158e2',
                        '#fff200',
                        '#ff9ff3',
                        '#08ffc8',
                        '#3742fa',
                        '#1089ff',
                        '#70FF61',
                        '#bf9fee',
                        '#574b90'
                    ],
                    borderColor: [
                        'rgba(231, 80, 90, 0.75)'
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: true
                    }],
                    yAxes: [{
                        display: true
                    }]
                },
                legend: {
                    display: false,
                }
            }
        });



var ctx = document.getElementById('cooperativeGenderChart');
const labels = <?php echo json_encode($cooperativeGenderChart->pluck('cooperative_name')->unique()->values()); ?>;
const maleData = <?php echo json_encode($cooperativeGenderChart->where('gender', 'Homme')->pluck('number_of_producers')); ?>;
const femaleData = <?php echo json_encode($cooperativeGenderChart->where('gender', 'Femme')->pluck('number_of_producers')); ?>;
 
var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets:[
                            {
                            label: "Hommes",
                            data: maleData,
                            backgroundColor: "#6C5CE7", 
                            stack: 'combined', // Activate stacking
                            },
                            {
                            label: "Femmes",
                            data: femaleData,
                            backgroundColor: "#78a903", 
                            stack: 'combined', // Activate stacking
                            },
                        ],
            },
            options: {
                aspectRatio: 1,
                responsive: true, 
                scales: {
                    x: {
                    stacked: true,
                    },
                    y: {
                    stacked: true,
                    },
                }, 
            }
        });
 

  var ctx = document.getElementById('parcelleGenderChart');
const labelsParc = <?php echo json_encode($parcelleGenderChart->pluck('cooperative_name')->unique()->values()); ?>;
const maleParcData = <?php echo json_encode($parcelleGenderChart->where('gender', 'Homme')->pluck('total_parcelle')); ?>;
const femaleParcData = <?php echo json_encode($parcelleGenderChart->where('gender', 'Femme')->pluck('total_parcelle')); ?>;

        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelsParc,
                datasets:[
                            {
                            label: "Hommes",
                            data: maleParcData,
                            backgroundColor: "#6C5CE7", 
                            stack: 'combined', // Activate stacking
                            },
                            {
                            label: "Femmes",
                            data: femaleParcData,
                            backgroundColor: "#78a903", 
                            stack: 'combined', // Activate stacking
                            },
                        ],
            },
            options: {
                aspectRatio: 1,
                responsive: true, 
                scales: {
                    x: {
                    stacked: true,
                    },
                    y: {
                    stacked: true,
                    },
                }, 
            }
        });


    </script>
@endpush