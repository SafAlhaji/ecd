<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
<style type="text/css">
body {
    background: #ddd3;
    height: 100vh;
    vertical-align: middle;
    display: flex;
    font-family: Muli;
    font-size: 14px
}

.card {
    margin: auto;
    width: 38%;
    max-width: 600px;
    padding: 4vh 0;
    box-shadow: 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-top: 3px solid rgb(252, 103, 49);
    border-bottom: 3px solid rgb(252, 103, 49);
    border-left: none;
    border-right: none
}

@media(max-width:768px) {
    .card {
        width: 100%
    }
}

.title {
    color: rgb(252, 103, 49);
    font-weight: 600;
    margin-bottom: 2vh;
    padding: 0 8%;
    font-size: initial
}

#details {
    font-weight: 400
}

.info {
    padding: 5% 8%
}

.info .col-5 {
    padding: 0
}

#heading {
    color: grey;
    line-height: 6vh
}

.pricing {
    background-color: #ddd3;
    padding: 2vh 8%;
    font-weight: 400;
    line-height: 2.5
}

.pricing .col-3 {
    padding: 0
}

.total {
    padding: 2vh 8%;
    color: rgb(252, 103, 49);
    font-weight: bold
}

.total .col-3 {
    padding: 0
}

.footer {
    padding: 0 8%;
    font-size: x-small;
    color: black
}

.footer img {
    height: 5vh;
    opacity: 0.2
}

.footer a {
    color: rgb(252, 103, 49)
}

.footer .col-10,
.col-2 {
    display: flex;
    padding: 3vh 0 0;
    align-items: center
}

.footer .row {
    margin: 0
}

#progressbar {
    margin-bottom: 3vh;
    overflow: hidden;
    color: rgb(252, 103, 49);
    margin-top: 3vh;
    width: 127%;
padding-left: 10px;
}

#progressbar li {
    list-style-type: none;
    font-size: x-small;
    width: 15%;
    float: left;
    position: relative;
    font-weight: 400;
    color: rgb(160, 159, 159)
}

#progressbar #step1:before {
    content: "";
    color: rgb(252, 103, 49);
    width: 14px;
    height: 14px;
    margin-left: 0px !important
}

#progressbar #step2:before {
    content: "";
    color: #fff;
    width: 14px;
    height: 14px;
    margin-left: 39%
}

#progressbar #step3:before {
    content: "";
    color: #fff;
    width: 14px;
    height: 14px;
    margin-right: 46%
}

#progressbar #step4:before {
    content: "";
    color: #fff;
    width: 14px;
    height: 14px;
    margin-right: 46%
}
#progressbar #step5:before {
    content: "";
    color: #fff;
    width: 14px;
    height: 14px;
    margin-right: 0px !important
}
#progressbar #step6:before {
    content: "";
    color: #fff;
    width: 14px;
    height: 14px;
    margin-right: 0px !important
}
#progressbar #step5:before {
    content: "";
    color: #fff;
    width: 14px;
    height: 14px;
    margin-right: 0px !important
}
#progressbar li:before {
    line-height: 29px;
    display: block;
    font-size: 12px;
    background: #ddd;
    border-radius: 50%;
    margin: auto;
    z-index: -1;
    margin-bottom: 1vh
}

#progressbar li:after {
content: '';
height: 3px;
background: #ddd;
position: absolute;
left: 0%;
right: 0%;
margin-bottom: 2vh;
top: 6px;
z-index: 1;
}

/*.progress-track {
    padding: 0 8%
}*/

#progressbar li:nth-child(2):after {
    margin-right: auto
}

#progressbar li:nth-child(1):after {
    margin: auto
}

#progressbar li:nth-child(3):after {
    float: left;
    width: 68%
}

#progressbar li:nth-child(4):after {
    margin-left: auto;
    width: 132%
}

#progressbar li.active {
    color: black
}

#progressbar li.active:before,
#progressbar li.active:after {
    background: rgb(252, 103, 49)
}
</style>
    <title>Track Request</title>
  </head>
  <body>
<div class="card">
    <div class="title">Request Reciept</div>
    <div class="title">{{\Str::upper('type of Service')}} : {{$customer_request->service->title}}</div>
    <div class="info">
        <div class="row">
            <div class="col-7"> <span id="heading">Date</span><br> <span id="details">{{\Carbon\Carbon::parse($customer_request->request_created_at)->format('d-m-Y H:i A')}}</span> </div>
            <div class="col-5 pull-right"> <span id="heading">Request No.</span><br> <span id="details">{{ $customer_request->snl }}</span> </div>
        </div>
    </div>
    <div class="pricing">
        <div class="row">
            <div class="col-9"> <span id="name">{{\Str::upper('Passport Fee :')}}</span> </div>
            <div class="col-3"> <span id="price">{{ $customer_request->service_charge }} SAR</span> </div>
        </div>
        <div class="row">
            <div class="col-9"> <span id="name">{{\Str::upper('Service Charge : ')}}</span> </div>
            <div class="col-3"> <span id="price">{{ $customer_request->embassy_charge }} SAR</span> </div>
        </div>
    </div>
    <div class="total">
        <div class="row">
            <div class="col-9"></div>
            <div class="col-9"> <span id="name">{{\Str::upper('Total Fee: ')}}</span> </div>
            <div class="col-3"><big>{{ $customer_request->amount }} SAR</big></div>
        </div>
    </div>
    <div class="tracking">
        <div class="title">Tracking Request</div>
    </div>
    <div class="progress-track">
        <ul id="progressbar">
            <li class="step0 active " id="step1">Pending</li>
            @for($i=2;$i <= 5;$i++)
            @if($i == 5)
            <li class="step0 {{($customer_request->request_status_id >= $i) ? 'active' : ''}} text-right" id="step{{$i}}">{{\App\Models\RequestStatus::request_status[$i]}}</li>
            @else
<li class="step0 {{($customer_request->request_status_id >= $i) ? 'active' : ''}} text-center" id="step{{$i}}">{{\App\Models\RequestStatus::request_status[$i]}}</li>
            @endif

            @endfor


{{--             <li class="step0 {{($customer_request->request_status_id > 2 && $customer_request->request_status_id < 4) ? 'active' : ''}} text-right" id="step3">In Embassy</li>
            <li class="step0 {{($customer_request->request_status_id > 3 && $customer_request->request_status_id < 5) ? 'active' : ''}} text-right" id="step4">Embassy Finished Request</li>
            <li class="step0 {{($customer_request->request_status_id > 4 && $customer_request->request_status_id < 6) ? 'active' : ''}} text-right" id="step5">At Office</li>
            <li class="step0 {{($customer_request->request_status_id > 5 && $customer_request->request_status_id < 7) ? 'active' : ''}} text-right" id="step6">Completed</li> --}}
        </ul>
    </div>

</div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <!-- Option 2: jQuery, Popper.js, and Bootstrap JS
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    -->
  </body>
</html>
