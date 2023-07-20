$(document).on('change', '.service_type_id' , function(){
    var request_type_id = document.getElementsByClassName('request_type_id');//[0].value;
    var service_id = document.getElementsByClassName("service_id");
    var service_type_id = document.getElementsByClassName("service_type_id");
    var profession_id = document.getElementsByClassName("profession_id");
    var service_charge = document.getElementsByClassName("service_charge");
    var embassy_charge = document.getElementsByClassName("embassy_charge");
    var tax_amount = document.getElementsByClassName("tax_amount");
    var service_total_charge = document.getElementsByClassName("amount");
    let searchParams = window.location.pathname.split('/');
    if(searchParams.includes('requests' )){
        var url = '../../get_service';
        if (request_type_id[0].value == 1) {
        // console.log(profession_id[0].value);
        if (profession_id[0] !== undefined) {
            if ( profession_id[0].value > 0) {
            $.ajax({
                method: 'get',
                url: url,
                dataType : 'json',
                data:{type_id:service_type_id[0].value, service_id:service_id[0].value, profession_id:profession_id[0].value},
                success: function (data) {
                    $('.profession_id').empty();
                    var professions_array = data.professions;
                if (typeof professions_array !== 'undefined' && professions_array.length > 0 && professions_array != '')
                {
                    for (var i=0; i < professions_array.length; i++) {
                      $(".profession_id").append('<option value="' + professions_array[i].id + '">' + professions_array[i].title + '</option>');
                  }
                }
                if (data !== null
                    && typeof data.amount_service_type !== 'undefined'
                    && typeof data.embassy_charge !== 'undefined'
                    && typeof data.tax_amount !== 'undefined'
                    && typeof data.total_amount !== 'undefined') {
                service_charge[0].value = data.amount_service_type;
                embassy_charge[0].value = data.embassy_charge;
                tax_amount[0].value = data.tax_amount;
                service_total_charge[0].value = data.total_amount;
                }else{
                    service_charge[0].value = 0;
                    embassy_charge[0].value = 0;
                    tax_amount[0].value = 0;
                    service_total_charge[0].value = 0
                }
            }
            });                
            }

}
}
if (request_type_id[0].value == 2) {
    $.ajax({
        method: 'get',
        url: url,
        dataType : 'json',
        data:{type_id:service_type_id[0].value, service_id:service_id[0].value},
        success: function (data) {
            $('.profession_id').empty();
            var professions_array = data.professions;
            if (typeof professions_array !== 'undefined' && professions_array.length > 0 && professions_array != '')
            {
                for (var i=0; i < professions_array.length; i++) {
                  $(".profession_id").append('<option value="' + professions_array[i].id + '">' + professions_array[i].title + '</option>');
              }
          }
              // console.log(data);
              if (data !== null
                && typeof data.amount_service_type !== 'undefined'
                && typeof data.embassy_charge !== 'undefined'
                && typeof data.tax_amount !== 'undefined'
                && typeof data.total_amount !== 'undefined') {
                service_charge[0].value = data.amount_service_type;
            embassy_charge[0].value = data.embassy_charge;
            tax_amount[0].value = data.tax_amount;
            service_total_charge[0].value = data.total_amount;
        }else{
            service_charge[0].value = 0;
            embassy_charge[0].value = 0;
            tax_amount[0].value = 0;
            service_total_charge[0].value = 0;
        }
    }
});
}
}



});
$(document).on('change', '.profession_id', function(){
     // alert('Change Happened');
     var service_id = document.getElementsByClassName("service_id");
     var service_type_id = document.getElementsByClassName("service_type_id");
     var profession_id = document.getElementsByClassName("profession_id");
     var service_charge = document.getElementsByClassName("service_charge");
     var embassy_charge = document.getElementsByClassName("embassy_charge");
     var tax_amount = document.getElementsByClassName("tax_amount");
     var service_total_charge = document.getElementsByClassName("amount");
     let searchParams = window.location.pathname.split('/');
     if(searchParams.includes('requests' )){
        if (profession_id[0].value > 0) {

 setTimeout( function() {
$.ajax({
    method: 'get',
    url: '../../get_service',
    dataType : 'json',
    data:{type_id:service_type_id[0].value, service_id:service_id[0].value, profession_id:profession_id[0].value},
    success: function (data) {
                console.log(data);

                if (data !== null
                    && typeof data.amount_service_type !== 'undefined'
                    && typeof data.embassy_charge !== 'undefined'
                    && typeof data.tax_amount !== 'undefined'
                    && typeof data.total_amount !== 'undefined') {
                    service_charge[0].value = data.amount_service_type;
                embassy_charge[0].value = data.embassy_charge;
                tax_amount[0].value = data.tax_amount;
                service_total_charge[0].value = data.total_amount;
            }else{
                service_charge[0].value = 0;
                embassy_charge[0].value = 0
                tax_amount[0].value = 0;
                service_total_charge[0].value = 0
            }
    //     $.ajax({
    //         method: 'get',
    //         url: '../../get_service',
    //         dataType : 'json',
    //         data:{type_id:service_type_id[0].value, service_id:service_id[0].value, profession_id:profession_id[0].value},
    //         success: function (data) {

    //     }
    // });
}
});
}, 200);

}
}

});
