$(document).on('change', '#request_type_id select', function(){
    var request_type_id = document.getElementsByClassName('request_type_id')[0].value;
    var profession_id = document.getElementById('profession_id');
    var embassy_id = document.getElementsByClassName("embassy_id");
    let searchParams = window.location.pathname.split('/');
    if(searchParams.includes('requests' )){
        $.ajax({
            method: 'get',
            url: '/../../get_service_providers_by_request_type',
            dataType : 'json',
            data:{q:request_type_id},
            success: function (data) {
                $('.embassy_id').empty();
              var embassy_array = data.service_provider;
              if (typeof embassy_array !== 'undefined' && embassy_array.length > 0 && embassy_array != '')
              {
                for (var i=0; i < embassy_array.length; i++) {
                  $(".embassy_id").append('<option value="' + embassy_array[i].id + '">' + embassy_array[i].title + '</option>');
                }
              }
        if (request_type_id == 1 && profession_id) {
            profession_id.style.display = 'block';
                $('.profession_id').empty();
              var profession_array = data.professions;
              if (typeof profession_array !== 'undefined' && profession_array.length > 0 && profession_array != '')
              {
                for (var i=0; i < profession_array.length; i++) {
                  $(".profession_id").append('<option value="' + profession_array[i].id + '">' + profession_array[i].title + '</option>');
                }
              }
            }else if (request_type_id == 2 && profession_id) {
            profession_id.style.display = 'none';
            }
        }
    });

    }

});
