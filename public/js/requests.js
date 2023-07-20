$(document).on('change', '.customer_id', function(){
    var customer_id = document.getElementsByClassName("customer_id");
    var customer_full_name = document.getElementsByClassName("customer_full_name");
    var customer_phone_number = document.getElementsByClassName("customer_phone_number");
    var customer_alt_phone_number = document.getElementsByClassName("customer_alt_phone_number");
    var customer_passport_number = document.getElementsByClassName("customer_passport_number");
    // var customer_profession_id = document.getElementsByClassName("customer_profession_id");
// console.log(customer_id[0].style.display = "none");
let searchParams = window.location.pathname.split('/');
if(searchParams.includes('requests' )){
       if (customer_id[0].value == 0) {
        // customer_full_name[0].readOnly  = false;
        customer_full_name[0].value ='';
        // customer_phone_number[0].readOnly  = false;
        customer_phone_number[0].value ='';
        // customer_alt_phone_number[0].readOnly  = false;
        customer_alt_phone_number[0].value ='';
        // customer_passport_number[0].readOnly  = false;
        customer_passport_number[0].value ='';

    }else{
        // customer_full_name[0].readOnly  = true;
        // customer_phone_number[0].readOnly  = true;
        // customer_alt_phone_number[0].readOnly  = true;
        // customer_passport_number[0].readOnly  = true;
        $.ajax({
            method: 'get',
            url: '../../customer?q='+customer_id[0].value,
            dataType : 'json',
            data:$(this).serialize(),
            success: function (data) {
                customer_full_name[0].value = data.full_name;
                customer_phone_number[0].value = data.phone_number;
               customer_alt_phone_number[0].value = data.alt_phone_number;
                customer_passport_number[0].value = data.passport_number;
                // customer_snl[0].value = data.snl;
            }
        });
    }
}

});
