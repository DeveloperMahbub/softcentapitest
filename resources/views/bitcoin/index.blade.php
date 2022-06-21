@extends('welcome')
@section('main-content')
<div id="error" class="text-center"  style="display: none; height: 33%;width: 85%;background-color: rgb(192, 44, 39);position: fixed;z-index: 999;top: 31%;bottom: 31%;left: 9%;border-radius: 32px 32px 32px 32px;opacity: 82%;"><h1 style="color: white;padding: 7%;">The currency code provided is not supported!</h1></div>
<div class="row">
    <div class="container-fluid mt-3">
        <div class="card col-md-12">
            <div class="card-header">
                <h3 class="text-center mt-3 text-uppercase" style="color: #6777ef"><b>Bitcoin Information</b></h3>
                {{--  error message  --}}
                @if ($errors->any())
                    <div id="error" class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                 @endif
                   {{--  success msg show  --}}
                   @if(session()->has('success'))
                   <div id="success" class="alert alert-success alert-dismissible fade show" role="alert">
                       {{ session()->get('success') }}
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                   </div>
               @endif
            </div>

            
            <div class="card-body">
                {{-- <form action="" method="POST">
                    @csrf --}}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="companyName"><b>Currency Code</b></label>
                            <select class="form-control" name="bitCointCode" id="bitCointCode">
                                <option value="">--Select--</option>
                                <option value="usd">USD</option>
                                <option value="eur">EUR</option>
                                <option value="gbr">GBR</option>
                            </select>
                        </div>
                    </div>
                {{-- </form> --}}
            </div>
            
        </div>
    </div>
</div>
<!---- form-end ---->
<div class="row mt-4">
    <div class="container-fluid">
        <div class="card">
            <div class="col-md-12 mb-3">
                <span>Currency Code is: </span><span id="currencyCode" class="badge badge-info"></span>
            </div>
            <div class="col-md-12 mb-3">
                <span>Current Bitcoin rate is: </span><span id="currentrate" class="badge badge-primary"></span>
            </div>
        </div>
        
    </div>
</div>

<div class="row mt-4">
    <div class="container-fluid">
        <div class="card">
            <div class="col-md-12 mb-3">
                <span>Highest Bitcoin rate in the last 30 days : </span><span id="highrate" class="badge badge-success"></span>
            </div>
            <div class="col-md-12 mb-3">
                <span>Lowest Bitcoin rate in the last 30 days : </span><span id="lowrate" class="badge badge-warning"></span>
            </div>
        </div>
        
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function(){
    $('#bitCointCode').change(function(){
        if($(this).val() != ''){
            var code = $(this).val();
            var url = "https://api.coindesk.com/v1/bpi/currentprice/"+code+".json";

            fetch(url)
            .then(function (response) {
                return response.json();
            }).then(function (data) {

                if (data) {
                    // Log the data to the console
                 upper = code.toUpperCase();
                // console.log(data.bpi[upper]['rate']);
                document.getElementById("currencyCode").innerHTML = data.bpi[upper]['code'];
                document.getElementById("currentrate").innerHTML = data.bpi[upper]['rate'] +" | "+data.bpi[upper]['description'];

                var date = new Date();
                dd = String(date.getDate()).padStart(2,'0');
                mm = String(date.getMonth()+1).padStart(2,'0');
                yy = date.getFullYear();
                today = yy + '-'+mm+'-' + dd;
                // console.log(today);
                
                var  backdate = new Date();
                backdate.setDate(backdate.getDate()-30);
                bdd = String(backdate.getDate()).padStart(2,'0');
                bmm = String(backdate.getMonth()+1).padStart(2,'0');
                byy = backdate.getFullYear();
                backday = byy + '-'+bmm+'-' + bdd;
                }else{
                    document.getElementById("currencyCode").innerHTML = '';
                    document.getElementById("currentrate").innerHTML = '';
                }

                

                return fetch("https://api.coindesk.com/v1/bpi/historical/close.json?start="+backday+"&end="+today+"&currency="+code);

            }).then(function (response) {
                // Get a JSON object from the response
                return response.json();

            }).then(function (data) {

                // Log the data to the console
                // console.log(Math.min(...Object.values(data.bpi)));
                // console.log(Math.max(...Object.values(data.bpi)));
                if (data) {
                    document.getElementById("highrate").innerHTML = Math.max(...Object.values(data.bpi));
                    document.getElementById("lowrate").innerHTML = Math.min(...Object.values(data.bpi));
                }else{
                    document.getElementById("highrate").innerHTML ='';
                    document.getElementById("lowrate").innerHTML = '';
                }
            }).catch(function (error) {
                console.log(error);

                if (error.name == "TypeError") {
                    $("#error").fadeIn().delay(2000).fadeOut('slow');
                    
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                    
                }

            });
        }
    });
});
</script>
@endsection