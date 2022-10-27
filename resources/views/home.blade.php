<?php 
//dd(config('constants.ROLES.SUPER'));
//dd($breadcrumbs);

?>

@extends('layouts.dynamic_pg')
@section('content_dynamic')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
   <!-- breadcrumbs -->
   @include('layouts.common_breadcrumbs')
   
   @if(session()->has('success_message') )
        <div class="alert alert-success">
            {{ session()->get('success_message') }}
        </div>
    @endif
    @if (Session::has('err_msg'))
       <div class="alert alert-warning" role="alert">
           {{Session::get('err_msg')}}
       </div>
  @endif

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ $user_count }}</h3>

                <p>Users Registrations</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="{{ route('users_view') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ $category_count }}</h3>

                <p>Categories</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="{{ route('cat_view') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ $product_count }}</h3>

                <p>Products</p>
              </div>
              <div class="icon">
                  <i class="ion ion-bag"></i>
              </div>
              <a href="{{ route('pd_view') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    
      <!-- Calender -->
      <div class="card bg-gradient-success" style= "display:none;">
        <div class="card-header border-0 ui-sortable-handle" style="cursor: move;">

        <h3 class="card-title">
            <i class="far fa-calendar-alt"></i>
            Calendar
        </h3>
        <!-- tools card -->
        <div class="card-tools">
            <!-- button with a dropdown -->
            <div class="btn-group">
            <!-- <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52">
                <i class="fas fa-bars"></i>
            </button> -->
            <!-- <div class="dropdown-menu" role="menu">
                <a href="#" class="dropdown-item">Add new event</a>
                <a href="#" class="dropdown-item">Clear events</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">View calendar</a>
            </div> -->
            </div>
            <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
            </button>
            <!-- <button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
            <i class="fas fa-times"></i>
            </button> -->
        </div>
        <!-- /. tools -->
        </div>
        <!-- /.card-header -->
        <div class="card-body pt-0">
        <!--The calendar -->
            <div id="calendar" style="width: 100%"><div class="bootstrap-datetimepicker-widget usetwentyfour"><ul class="list-unstyled"><li class="show"><div class="datepicker"><div class="datepicker-days" style=""><table class="table table-sm"><thead><tr><th class="prev" data-action="previous"><span class="fa fa-chevron-left" title="Previous Month"></span></th><th class="picker-switch" data-action="pickerSwitch" colspan="5" title="Select Month">October 2022</th><th class="next" data-action="next"><span class="fa fa-chevron-right" title="Next Month"></span></th></tr><tr><th class="dow">Su</th><th class="dow">Mo</th><th class="dow">Tu</th><th class="dow">We</th><th class="dow">Th</th><th class="dow">Fr</th><th class="dow">Sa</th></tr></thead><tbody><tr><td data-action="selectDay" data-day="09/25/2022" class="day old weekend">25</td><td data-action="selectDay" data-day="09/26/2022" class="day old">26</td><td data-action="selectDay" data-day="09/27/2022" class="day old">27</td><td data-action="selectDay" data-day="09/28/2022" class="day old">28</td><td data-action="selectDay" data-day="09/29/2022" class="day old">29</td><td data-action="selectDay" data-day="09/30/2022" class="day old">30</td><td data-action="selectDay" data-day="10/01/2022" class="day weekend">1</td></tr><tr><td data-action="selectDay" data-day="10/02/2022" class="day weekend">2</td><td data-action="selectDay" data-day="10/03/2022" class="day">3</td><td data-action="selectDay" data-day="10/04/2022" class="day">4</td><td data-action="selectDay" data-day="10/05/2022" class="day">5</td><td data-action="selectDay" data-day="10/06/2022" class="day">6</td><td data-action="selectDay" data-day="10/07/2022" class="day">7</td><td data-action="selectDay" data-day="10/08/2022" class="day weekend">8</td></tr><tr><td data-action="selectDay" data-day="10/09/2022" class="day weekend">9</td><td data-action="selectDay" data-day="10/10/2022" class="day">10</td><td data-action="selectDay" data-day="10/11/2022" class="day">11</td><td data-action="selectDay" data-day="10/12/2022" class="day">12</td><td data-action="selectDay" data-day="10/13/2022" class="day">13</td><td data-action="selectDay" data-day="10/14/2022" class="day">14</td><td data-action="selectDay" data-day="10/15/2022" class="day weekend">15</td></tr><tr><td data-action="selectDay" data-day="10/16/2022" class="day weekend">16</td><td data-action="selectDay" data-day="10/17/2022" class="day">17</td><td data-action="selectDay" data-day="10/18/2022" class="day">18</td><td data-action="selectDay" data-day="10/19/2022" class="day active today">19</td><td data-action="selectDay" data-day="10/20/2022" class="day">20</td><td data-action="selectDay" data-day="10/21/2022" class="day">21</td><td data-action="selectDay" data-day="10/22/2022" class="day weekend">22</td></tr><tr><td data-action="selectDay" data-day="10/23/2022" class="day weekend">23</td><td data-action="selectDay" data-day="10/24/2022" class="day">24</td><td data-action="selectDay" data-day="10/25/2022" class="day">25</td><td data-action="selectDay" data-day="10/26/2022" class="day">26</td><td data-action="selectDay" data-day="10/27/2022" class="day">27</td><td data-action="selectDay" data-day="10/28/2022" class="day">28</td><td data-action="selectDay" data-day="10/29/2022" class="day weekend">29</td></tr><tr><td data-action="selectDay" data-day="10/30/2022" class="day weekend">30</td><td data-action="selectDay" data-day="10/31/2022" class="day">31</td><td data-action="selectDay" data-day="11/01/2022" class="day new">1</td><td data-action="selectDay" data-day="11/02/2022" class="day new">2</td><td data-action="selectDay" data-day="11/03/2022" class="day new">3</td><td data-action="selectDay" data-day="11/04/2022" class="day new">4</td><td data-action="selectDay" data-day="11/05/2022" class="day new weekend">5</td></tr></tbody></table></div><div class="datepicker-months" style="display: none;"><table class="table-condensed"><thead><tr><th class="prev" data-action="previous"><span class="fa fa-chevron-left" title="Previous Year"></span></th><th class="picker-switch" data-action="pickerSwitch" colspan="5" title="Select Year">2022</th><th class="next" data-action="next"><span class="fa fa-chevron-right" title="Next Year"></span></th></tr></thead><tbody><tr><td colspan="7"><span data-action="selectMonth" class="month">Jan</span><span data-action="selectMonth" class="month">Feb</span><span data-action="selectMonth" class="month">Mar</span><span data-action="selectMonth" class="month">Apr</span><span data-action="selectMonth" class="month">May</span><span data-action="selectMonth" class="month">Jun</span><span data-action="selectMonth" class="month">Jul</span><span data-action="selectMonth" class="month">Aug</span><span data-action="selectMonth" class="month">Sep</span><span data-action="selectMonth" class="month active">Oct</span><span data-action="selectMonth" class="month">Nov</span><span data-action="selectMonth" class="month">Dec</span></td></tr></tbody></table></div><div class="datepicker-years" style="display: none;"><table class="table-condensed"><thead><tr><th class="prev" data-action="previous"><span class="fa fa-chevron-left" title="Previous Decade"></span></th><th class="picker-switch" data-action="pickerSwitch" colspan="5" title="Select Decade">2020-2029</th><th class="next" data-action="next"><span class="fa fa-chevron-right" title="Next Decade"></span></th></tr></thead><tbody><tr><td colspan="7"><span data-action="selectYear" class="year old">2019</span><span data-action="selectYear" class="year">2020</span><span data-action="selectYear" class="year">2021</span><span data-action="selectYear" class="year active">2022</span><span data-action="selectYear" class="year">2023</span><span data-action="selectYear" class="year">2024</span><span data-action="selectYear" class="year">2025</span><span data-action="selectYear" class="year">2026</span><span data-action="selectYear" class="year">2027</span><span data-action="selectYear" class="year">2028</span><span data-action="selectYear" class="year">2029</span><span data-action="selectYear" class="year old">2030</span></td></tr></tbody></table></div><div class="datepicker-decades" style="display: none;"><table class="table-condensed"><thead><tr><th class="prev" data-action="previous"><span class="fa fa-chevron-left" title="Previous Century"></span></th><th class="picker-switch" data-action="pickerSwitch" colspan="5">2000-2090</th><th class="next" data-action="next"><span class="fa fa-chevron-right" title="Next Century"></span></th></tr></thead><tbody><tr><td colspan="7"><span data-action="selectDecade" class="decade old" data-selection="2006">1990</span><span data-action="selectDecade" class="decade" data-selection="2006">2000</span><span data-action="selectDecade" class="decade" data-selection="2016">2010</span><span data-action="selectDecade" class="decade active" data-selection="2026">2020</span><span data-action="selectDecade" class="decade" data-selection="2036">2030</span><span data-action="selectDecade" class="decade" data-selection="2046">2040</span><span data-action="selectDecade" class="decade" data-selection="2056">2050</span><span data-action="selectDecade" class="decade" data-selection="2066">2060</span><span data-action="selectDecade" class="decade" data-selection="2076">2070</span><span data-action="selectDecade" class="decade" data-selection="2086">2080</span><span data-action="selectDecade" class="decade" data-selection="2096">2090</span><span data-action="selectDecade" class="decade old" data-selection="2106">2100</span></td></tr></tbody></table></div></div></li><li class="picker-switch accordion-toggle"></li></ul></div></div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- Calender ends here-->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection