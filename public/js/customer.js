$(document).on('change', '.profession_id', function(){
    var profession_id = document.getElementsByClassName("profession_id");
    var profession_title = document.getElementsByClassName("profession_title");
    if (profession_id[0].value == 0) {
        if ( profession_title[0]) {
            profession_title[0].readOnly  = false;
        }
    }
    if (profession_id[0].value > 0) {
        if ( profession_title[0]) {
            profession_title[0].readOnly  = true;
        }

    }
});
