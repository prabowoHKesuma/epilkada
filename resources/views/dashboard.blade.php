@extends('layouts.admin')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Ringkasan PILKADA</h1>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    
    <div class="row">
      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h3>1,500</h3>
            <p>Total Pemilih</p>
          </div>
          <div class="icon">
            <i class="fas fa-users"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
          <div class="inner">
            <h3>85<sup style="font-size: 20px">%</sup></h3>
            <p>Partisipasi Suara</p>
          </div>
          <div class="icon">
            <i class="fas fa-chart-bar"></i>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection