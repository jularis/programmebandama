@extends('manager.layouts.app')
@section('panel')
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
@can('manager.traca.producteur.index')
                   <div class="col">
					 <div class="card radius-10 border-start border-0 border-4 border-info">
           
						<div class="card-body">
							<div class="d-flex align-items-center">
								<div>
									<p class="mb-0 text-secondary">@lang('Total Producteurs')</p>
									<h4 class="my-1 text-info">{{ number_format(@$nbproducteur,0,'',' ')}}</h4>
									<p class="mb-0 font-13"></p>
								</div>
								<div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i class="fa fa-users"></i>
								</div>
							</div>
						</div>
					 </div>
				   </div>
           @endcan
           @can('manager.traca.parcelle.index')
				   <div class="col">
					<div class="card radius-10 border-start border-0 border-4 border-danger">
					   <div class="card-body">
						   <div class="d-flex align-items-center">
							   <div>
								   <p class="mb-0 text-secondary">@lang('Total Superficie Parcelles')</p>
								   <h4 class="my-1 text-danger">{{ number_format(@$nbparcelle,0,'',' ')}} ha</h4>
								   <p class="mb-0 font-13"></p>
							   </div>
							   <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i class="fa fa-map"></i>
							   </div>
						   </div>
					   </div>
					</div>
				  </div>
          @endcan
          @can('manager.agro.distribution.index')
				  <div class="col">
					<div class="card radius-10 border-start border-0 border-4 border-success">
					   <div class="card-body">
						   <div class="d-flex align-items-center">
							   <div>
								   <p class="mb-0 text-secondary">@lang('Total Arbres distribues')</p>
								   <h4 class="my-1 text-success">{{ number_format(@$nbarbredistribue,0,'',' ')}}</h4>
								   <p class="mb-0 font-13"></p>
							   </div>
							   <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class="fa fa-tree"></i>
							   </div>
						   </div>
					   </div>
					</div>
				  </div>
          @endcan
          @can('manager.suivi.inspection.index')
				  <div class="col">
					<div class="card radius-10 border-start border-0 border-4 border-warning">
					   <div class="card-body">
						   <div class="d-flex align-items-center">
							   <div>
                 <?php
                 $taux = 0; 
                 if($totalparcelle && $nbinspection){
                  $taux = round(($nbinspection / $totalparcelle)*100,2);
                 } 
                 ?>
								   <p class="mb-0 text-secondary">@lang('Total Inspection')</p>
								   <h4 class="my-1 text-warning"> {{ $taux }} %</h4>
								   <p class="mb-0 font-13"></p>
							   </div>
							   <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i class="fa fa-search"></i>
							   </div>
						   </div>
					   </div>
					</div>
				  </div> 
          @endcan
				</div>
    <div class="row gy-4"> 
    @can('manager.traca.producteur.index')
    <div class="col-xxl-4 col-sm-4">  
                        <div class="card box--shadow2 bg--white" id="producteur" style="min-height:230px;"> 
                        </div>
        </div>
        @endcan
        @can('manager.traca.parcelle.index')
        <div class="col-xxl-4 col-sm-4"> 
        <div class="card box--shadow2 bg--white" id="mapping" style="min-height:230px;"> 
         
        </div>
        </div>
@endcan
@can('manager.suivi.formation.index')
        <div class="col-xxl-4 col-sm-4"> 
                    <div class="card box--shadow2 bg--white" id="formationmodule" style="min-height:230px;">  
                    
                    </div>
        </div>
       
        <div class="col-xxl-4 col-sm-4"> 
                    <div class="card box--shadow2 bg--white" id="producteurmodule" style="min-height:230px;"> 
                    </div>
        </div>
        @endcan
        @can('manager.traca.parcelle.index')
        <div class="col-xxl-4 col-sm-4"> 
                    <div class="card box--shadow2 bg--white" id="parcellespargenre" style="min-height:230px;"> 
                    </div>
        </div>
        @endcan
        @can('manager.traca.producteur.index')
        <div class="col-xxl-4 col-sm-4"> 
                    <div class="card box--shadow2 bg--white" id="producteurparcertification" style="min-height:230px;"> 
                    </div>
        </div>
        <div class="col-xxl-4 col-sm-4"> 
                    <div class="card box--shadow2 bg--white" id="producteurparcertificationparsexe" style="min-height:230px;"> 
                    </div>
        </div>
@endcan
    </div><!-- row end-->

   
@endsection


@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end">
        <h3>{{ __(auth()->user()->cooperative->name) }}</h3>
    </div>
@endpush
@push('script')  
<?php
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

$donnees = $donnees2 = $donnees3 = $donnees4 = $donnees5 = $donnees6 = $donnees7 = $donnees9 = array();
$labels = $labels2 = $labels3 = $labels4 = $labels5 = $labels6 = $labels7 = $labels9 = array();
$total = $total2 = $total3 = $total4 = $total5 = $total6 = $total7 = $total9 = array();


            foreach(@$genre as $data){
                $labels[] = utf8_encode(Str::remove("\r\n",utf8_decode($data->sexe)));
                $total[] = $data->nombre;
                $name = utf8_encode(Str::remove("\r\n",utf8_decode($data->sexe)));
                $value = $data->nombre;
                $donnees[] = "{ value: $value, name: '$name' }";
            }
            foreach(@$parcelle as $data){
                $labels[] = utf8_encode(Str::remove("\r\n",utf8_decode($data->typedeclaration)));
                $total[] = $data->nombre;
                $name = utf8_encode(Str::remove("\r\n",utf8_decode($data->typedeclaration ? $data->typedeclaration : 'Aucun')));
                $value = $data->nombre;
                $donnees2[] = "{ value: $value, name: '$name' }";
            }
            
            
            foreach(@$formation as $data){ 
                $labels3[] = utf8_encode(Str::remove("\r\n",utf8_decode(Str::between($data->nom,"(",")"))));
                $total3[] = $data->nombre; 
            }
             
            $totalsexe=$sexe=$xAxisData=array();
             
            foreach(@$modules as $data){

              if(!in_array(Str::between($data->module,"(",")"),$xAxisData))
              {  
                $xAxisData[] = utf8_encode(Str::remove("\r\n",utf8_decode(Str::between($data->module,"(",")"))));
              }
                foreach($modules as $data2){
                  if($data->sexe_producteur ==$data2->sexe_producteur){
                    $totalsexe[] = $data2->nombre_producteurs;
                  } 
                }
                $totalsexe = implode(",",$totalsexe);
                if(!in_array($data->sexe_producteur,$sexe)){
                  
                  $sexe[]=$data->sexe_producteur;
                  $donnees3[] = "
                  { name:'$data->sexe_producteur',
                    nameTextStyle: {
                      fontStyle: 'oblique'
                    },
                    type: 'bar', 
                    stack: 'one',
                    data: [$totalsexe]
                  }";
                }
                
              
              $totalsexe=array();
                
            }

            foreach(@$parcellespargenre as $data){
              $labels[] = utf8_encode(Str::remove("\r\n",utf8_decode($data->genre)));
              $total[] = $data->nombre;
              $name = utf8_encode(Str::remove("\r\n",utf8_decode($data->genre ? $data->genre : 'Aucun')));
              $value = $data->nombre;
              $donnees4[] = "{ value: $value, name: '$name' }";
          }

          foreach(@$producteurparcertification as $data){ 
            $labels6[] = utf8_encode(Str::remove("\r\n",utf8_decode(Str::between($data->certification,"(",")"))));
            $total6[] = $data->nombre; 
        }

        $totalsexe7=$sexe7=$xAxisData7=array();
             
            foreach(@$producteurparGenreCertification as $data){

              if(!in_array(Str::between($data->certification,"(",")"),$xAxisData7))
              {  
                $xAxisData7[] = utf8_encode(Str::remove("\r\n",utf8_decode(Str::between($data->certification,"(",")"))));
              }
                foreach($producteurparGenreCertification as $data2){
                  if($data->genre ==$data2->genre){
                    $totalsexe7[] = $data2->nombre;
                  } 
                }
                $totalsexe7 = implode(",",$totalsexe7);
                if(!in_array($data->genre,$sexe7)){
                  
                  $sexe7[]=$data->genre;
                  $donnees7[] = "
                  { name:'$data->genre',
                    nameTextStyle: {
                      fontStyle: 'oblique'
                    },
                    type: 'bar', 
                    stack: 'one',
                    data: [$totalsexe7]
                  }";
                }
                
              
              $totalsexe7=array();
                
            }
            ?>
<script type="text/javascript">
      // Initialize the echarts instance based on the prepared dom
      var myChart = echarts.init(document.getElementById('producteur'));

      // Specify the configuration items and data for the chart
      var option = {
  title: {
    text: 'Producteur par Genre',
    subtext: '',
    left: 'center',
    textStyle:{
    fontSize: 16,
    fontWeight: 'normal',
    fontStyle: 'normal'
    }
  },
  toolbox: {
          show: true,
          //orient: 'vertical',
          left: 'left',
          bottom: 'bottom',
          feature: {
            dataView: { readOnly: false },
            restore: {},
            saveAsImage: {}
          }
        },
  tooltip: {
    trigger: 'item'
  },
  legend: {
    orient: 'horizontal',
    bottom: 'bottom'
  },
  series: [
    {
      name: '',
      type: 'pie',
      label: {
        formatter: '{d}%',
        position: 'outside'
      },
      radius: '50%',
      data: [
        <?php echo implode(",",$donnees); ?>
      ],
      emphasis: {
        itemStyle: {
          shadowBlur: 10,
          shadowOffsetX: 0,
          shadowColor: 'rgba(0, 0, 0, 0.5)'
        }
      }
    }
  ]
};
// Display the chart using the configuration items and data just specified.
myChart.setOption(option);

 // Initialize the echarts instance based on the prepared dom
 var myChart2 = echarts.init(document.getElementById('mapping'));

// Specify the configuration items and data for the chart
var option2 = {
title: {
text: 'Mapping par Parcelle',
subtext: '',
left: 'center',
textStyle:{
    fontSize: 16,
    fontWeight: 'normal',
    fontStyle: 'normal'
    }
},
toolbox: {
          show: true,
          //orient: 'vertical',
          left: 'left',
          bottom: 'bottom',
          feature: {
            dataView: { readOnly: false },
            restore: {},
            saveAsImage: {}
          }
        },
tooltip: {
trigger: 'item'
},
legend: {
orient: 'horizontal',
right:'right',
bottom: 'bottom'
},
series: [
{
name: '',
type: 'pie',
label: {
  formatter: '{d}%',
  position: 'outside'
},
radius: '50%',
data: [
  <?php echo implode(",",$donnees2); ?>
],
emphasis: {
  itemStyle: {
    shadowBlur: 10,
    shadowOffsetX: 0,
    shadowColor: 'rgba(0, 0, 0, 0.5)'
  }
}
}
]
};
// Display the chart using the configuration items and data just specified.
myChart2.setOption(option2);

var myChart3 = echarts.init(document.getElementById('formationmodule'));
        var option3 = {
            title: { 
                show: true,
                text: 'Formations par Module',
                textStyle:{
                fontSize: 16,
                fontWeight: 'normal',
                fontStyle: 'normal'
                }
            },
            toolbox: {
          show: true,
          //orient: 'vertical',
          left: 'left',
          bottom: 'bottom',
          feature: {
            dataView: { readOnly: false },
            restore: {},
            saveAsImage: {}
          }
        },
            tooltip: {}, 
            legend: {
                data: [<?php echo "'".implode("','",$labels3)."'"; ?>]
            },
            xAxis: {
                data: [<?php echo "'".implode("','",$labels3)."'"; ?>]
            },
            yAxis: {},
            series: [{
                name: '',
                label: {
            show: true
            },
                type: 'bar',
                data: [<?php echo "'".implode("','",$total3)."'"; ?>]
            }]
        };
        myChart3.setOption(option3);


        // Display the chart using the configuration items and data just specified.

var myChart4 = echarts.init(document.getElementById('producteurmodule'));
 
        // specify chart configuration item and data
        var option4 = {
            title: { 
                show: true,
                text: 'Producteurs formés par genre/Module',
                textStyle:{
                fontSize: 16,
                fontWeight: 'normal',
                fontStyle: 'normal'
                }
            },
            toolbox: {
          show: true,
          //orient: 'vertical',
          left: 'left',
          bottom: 'bottom',
          feature: {
            dataView: { readOnly: false },
            restore: {},
            saveAsImage: {}
          }
        },
            tooltip: {}, 
            legend: {
              show: false
            },
            xAxis: { 
              type: 'category',
                data: [<?php echo "'".implode("','",$xAxisData)."'"; ?>]
            },
            yAxis: { },
            series: [<?php echo implode(",",$donnees3); ?>]
        };

        // use configuration item and data specified to show chart
        myChart4.setOption(option4);

         // Initialize the echarts instance based on the prepared dom
 var myChart5 = echarts.init(document.getElementById('parcellespargenre')); 
var option5 = {
title: {
text: 'Parcelles par Genre',
subtext: '',
left: 'center',
textStyle:{
    fontSize: 16,
    fontWeight: 'normal',
    fontStyle: 'normal'
    }
},
toolbox: {
          show: true,
          //orient: 'vertical',
          left: 'left',
          bottom: 'bottom',
          feature: {
            dataView: { readOnly: false },
            restore: {},
            saveAsImage: {}
          }
        },
tooltip: {
trigger: 'item'
},
legend: {
orient: 'horizontal',
bottom: 'bottom'
},
series: [
{
name: '',
type: 'pie',
label: {
  formatter: '{d}%',
  position: 'outside'
},
radius: '50%',
data: [
  <?php echo implode(",",$donnees4); ?>
],
emphasis: {
  itemStyle: {
    shadowBlur: 10,
    shadowOffsetX: 0,
    shadowColor: 'rgba(0, 0, 0, 0.5)'
  }
}
}
]
}; 
myChart5.setOption(option5);


var myChart6 = echarts.init(document.getElementById('producteurparcertification'));
        var option6 = {
            title: { 
                show: true,
                text: 'Producteurs par Certification',
                textStyle:{
                fontSize: 16,
                fontWeight: 'normal',
                fontStyle: 'normal'
                }
            },
            toolbox: {
          show: true,
          //orient: 'vertical',
          left: 'left',
          bottom: 'bottom',
          feature: {
            dataView: { readOnly: false },
            restore: {},
            saveAsImage: {}
          }
        },
            tooltip: {}, 
            legend: {
                data: [<?php echo "'".implode("','",$labels6)."'"; ?>]
            },
            xAxis: {
                data: [<?php echo "'".implode("','",$labels6)."'"; ?>]
            },
            yAxis: {},
            series: [{
                name: '',
                label: {
            show: true
            },
                type: 'bar',
                data: [<?php echo "'".implode("','",$total6)."'"; ?>]
            }]
        };
        myChart6.setOption(option6);


        var myChart7 = echarts.init(document.getElementById('producteurparcertificationparsexe')); 
        var option7 = {
            title: { 
                show: true,
                text: 'Producteurs par Genre/Certification',
                textStyle:{
                fontSize: 16,
                fontWeight: 'normal',
                fontStyle: 'normal'
                }
            },
            toolbox: {
          show: true,
          //orient: 'vertical',
          left: 'left',
          bottom: 'bottom',
          feature: {
            dataView: { readOnly: false },
            restore: {},
            saveAsImage: {}
          }
        },
            tooltip: {}, 
            legend: {
              show: false
            },
            xAxis: { 
              type: 'category',
                data: [<?php echo "'".implode("','",$xAxisData7)."'"; ?>]
            },
            yAxis: { },
            series: [<?php echo implode(",",$donnees7); ?>]
        }; 
        myChart7.setOption(option7);
    </script>
@endpush