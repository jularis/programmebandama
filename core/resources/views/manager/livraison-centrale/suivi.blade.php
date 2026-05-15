@extends('manager.layouts.app')
@section('panel')
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
/*process-box*/
#flocal .form-control {
    background-color: #fff;
    border: 1px solid #c1c1c1;
}
.process-box{
    background: #fff;
    padding: 10px;
    border-radius: 15px;
    position: relative;
    box-shadow: 2px 2px 7px 0 #00000057;
}
.process-left:after{
        content: "";
    border-top: 15px solid #ffffff;
    border-bottom: 15px solid #ffffff;
    border-left: 15px solid #ffffff;
    border-right: 15px solid #ffffff;
    display: inline-grid;
    position: absolute;
    right: -15px;
    top: 42%;
    transform: rotate(45deg);
    box-shadow: 3px -2px 3px 0px #00000036;
    z-index: 1;
}
.process-right:after{
        content: "";
    border-top: 15px solid #ffffff00;
    border-bottom: 15px solid #ffffff;
    border-left: 15px solid #ffffff;
    border-right: 15px solid #ffffff00;
    display: inline-grid;
    position: absolute;
    left: -15px;
    top: 42%;
    transform: rotate(45deg);
    box-shadow: -1px 1px 3px 0px #0000001a;
    z-index: 1;
}
.process-step{
    background: var(--main-color-hover);
    text-align: center;
    width: 80%;
    margin: 0 auto;
    color: #fff;
    height: 100%;
    padding-top: 8px;
    position: relative;
    top: -26px;
    border-radius: 0px 0px 10px 10px;
    box-shadow: -6px 8px 0px 0px #00000014;
}
.process-point-right{
    background: #ffffff;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    border: 8px solid var(--main-color-hover);
    box-shadow: 0 0 0px 4px #5c5c5c;
    margin: auto 0;
    position: absolute;
    bottom: 40px;
    left: -63px;
    top: 40px;
}
.process-point-right:before{
    content: "";
    height: 175px;
    width: 11px;
    background: #5c5c5c;
    display: inline-grid;
    transform: rotate(36deg);
    position: relative;
    left: -50px;
    top: -0px;
}
.process-point-left{
    background: #ffffff;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    border: 8px solid var(--main-color-hover);
    box-shadow: 0 0 0px 4px #5c5c5c;
    margin: auto 0;
    position: absolute;
    bottom: 40px;
    right: -63px;
}
.process-point-left:before {
    content: "";
    height: 144px;
    width: 11px;
    background: #5c5c5c;
    display: inline-grid;
    transform: rotate(-38deg);
    position: relative;
    left: 50px;
    top: 0px;

}

.process-last:before{
    display: none;
}
.process-box p{
    z-index: 9;
    text-wrap: wrap;
}
.process-step p{
    font-size: 20px;
}
.process-step h2{
    font-size: 39px;
}
.process-step:after{
    content: "";
    border-top: 8px solid #04889800;
    border-bottom: 8px solid var(--main-color-hover);
    border-left: 8px solid #04889800;
    border-right: 8px solid var(--main-color-hover);
    display: inline-grid;
    position: absolute;
    left: -16px;
    top: 0;
}
.process-step:before{
    content: "";
    border-top: 8px solid #ff000000;
    border-bottom: 8px solid var(--main-color-hover);
    border-left: 8px solid var(--main-color-hover);
    border-right: 8px solid #ff000000;
    display: inline-grid;
    position: absolute;
    right: -16px;
    top: 0;
}
.process-line-l{
    background: var(--main-color-hover);
    height: 4px;
    position: absolute;
    width: 130px;
    right: -153px;
    top: 68px;
    z-index: 9;
}
.process-line-r{
    background: var(--main-color-hover);
    height: 4px;
    position: absolute;
    width: 130px;
    left: -153px;
    top: 68px;
    z-index: 9;
}
    </style>
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        
                   

<section class="our-blog p-0 m-0 bg-silver">
    <div class="container work-process  pb-5 pt-5">
        <div class="title mb-5 text-center">
        <h3> <span class="site-color" style="font-size: 44px;">Suivi du Connaissement N° {{ $livraison->numeroCU }}</span></h3>
    </div>
        <!-- ============ step 1 =========== -->
        <form  id="flocal" method="POST">
        @csrf
            <input type="hidden" name="id" value="{{ $id }}">
        <div class="row">
            <div class="col-md-5">
                <div class="process-box process-left" data-aos="fade-right" data-aos-duration="1000">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="process-step">
                                <p class="m-0 p-0">Etape</p>
                                <h2 class="m-0 p-0">01</h2>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h5>Pont Bascule</h5>
                            <p><small>Quantité livrée(Tonne)
                            <?php echo  Form::number('step1', @$suivi->step1, ['id' => 'resume','placeholder'=>"12452", 'style' => 'resize:none','required'=>'required','id'=>'step1','class' => 'form-control resume','maxlength' => 500]); ?>
                            </small></p>
                        </div>
                    </div>
                    <div class="process-line-l"></div>
                </div>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-5">
                <div class="process-point-right"></div>
            </div>
        </div>
        <!-- ============ step 2 =========== -->
        <div class="row">
            
            <div class="col-md-5">
                <div class="process-point-left"></div>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-5">
                <div class="process-box process-right" data-aos="fade-left" data-aos-duration="1000">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="process-step">
                                <p class="m-0 p-0">Etape</p>
                                <h2 class="m-0 p-0">02</h2>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h5>Magasin Brousse</h5>
                            <p><small><?php echo  Form::textarea('step2', @$suivi->step2, ['id' => 'resume','placeholder'=>"Lorem Ipsum is simply dummy text of the printing and typesetting industry", 'rows' => 4, 'cols' => 54,'id'=>'step2', 'style' => 'resize:none','class' => 'form-control resume','maxlength' => 500]); ?> </small></p>
                        </div>
                    </div>
                    <div class="process-line-r"></div>
                </div>
            </div>

        </div>
        <!-- ============ step 3 =========== -->
        <div class="row">
            <div class="col-md-5">
                <div class="process-box process-left" data-aos="fade-right" data-aos-duration="1000">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="process-step">
                                <p class="m-0 p-0">Etape</p>
                                <h2 class="m-0 p-0">03</h2>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h5>What is Lorem Ipsum?</h5>
                            <p><small><?php echo  Form::textarea('step3', @$suivi->step3, ['id' => 'resume','placeholder'=>"Lorem Ipsum is simply dummy text of the printing and typesetting industry", 'rows' => 4,'id'=>'step3', 'cols' => 54, 'style' => 'resize:none','class' => 'form-control resume','maxlength' => 500]); ?>  </small></p>
                        </div>
                    </div>
                    <div class="process-line-l"></div>
                </div>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-5">
                <div class="process-point-right"></div>
            </div>
        </div>
        <!-- ============ step 4 =========== -->
        <div class="row">
            <div class="col-md-5">
                <div class="process-point-left"></div>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-5">
                <div class="process-box process-right" data-aos="fade-left" data-aos-duration="1000">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="process-step">
                                <p class="m-0 p-0">Etape</p>
                                <h2 class="m-0 p-0">04</h2>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h5>What is Lorem Ipsum?</h5>
                            <p><small><?php echo  Form::textarea('step4', @$suivi->step4, ['id' => 'resume','placeholder'=>"Lorem Ipsum is simply dummy text of the printing and typesetting industry", 'rows' => 4,'id'=>'step4', 'cols' => 54, 'style' => 'resize:none','class' => 'form-control resume','maxlength' => 500]); ?> </small></p>
                        </div>
                    </div>
                    <div class="process-line-r"></div>
                </div>
            </div>
            
            
        </div>
        <!-- ============ step 3 =========== -->
        <div class="row">
            <div class="col-md-5">
                <div class="process-box process-left" data-aos="fade-right" data-aos-duration="1000">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="process-step">
                                <p class="m-0 p-0">Etape</p>
                                <h2 class="m-0 p-0">05</h2>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h5>What is Lorem Ipsum?</h5>
                            <p><small><?php echo  Form::textarea('step5', @$suivi->step5, ['id' => 'resume','placeholder'=>"Lorem Ipsum is simply dummy text of the printing and typesetting industry", 'rows' => 4,'id'=>'step5', 'cols' => 54, 'style' => 'resize:none','class' => 'form-control resume','maxlength' => 500]); ?> </small></p>
                        </div>
                    </div>
                    <div class="process-line-l"></div>
                </div>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-5">
                <div class="process-point-right process-last"></div>
            </div>
        </div>
        <!-- ============ -->
        </form>
    </div>
</section>

 

                    </div>
                </div>
               
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.livraison.usine.connaissement') }}" />
@endpush

@push('script')

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init();
  $('#step1,#step2,#step3,#step4,#step5').change('keyup change blur',function() {
    
 $.ajax({
              type:'POST',
              url: "{{ route('manager.livraison.magcentral.suivi.store')}}",
              data: $('#flocal').serialize(),
              success:function(html){
                 
                $('#step1').val(html.step1);
                $('#step2').val(html.step2);
                $('#step3').val(html.step3);
                $('#step4').val(html.step4);
                $('#step5').val(html.step5);
                //$('input[name=lastname]').val(html.lastname).attr("readonly",'readonly'); 
              }
          });
});
 
</script>
@endpush