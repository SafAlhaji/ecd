<?php

namespace App\Extensions;

use Encore\Admin\Admin;

class EditRequestStatus
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT
    $(document).off('change').on('change', '.grid_check_request', function(){
    var request_id = $(this).data('id');
    var request_status_id = $('#request_status'+$(this).data('id')).val();
    var url = $(this).data('url');
    var complete_url = url+'?request_status='+request_status_id+'&request_id='+request_id;
        $.ajax({
            method: 'get',
            url: complete_url,
            dataType : 'json',
            data:$(this).serialize(),
            success: function (data) {
                // console.log(data);
                $.pjax.reload('#pjax-container');
                if (typeof data === 'object') {
                    if (data.status) {
                            Swal.fire({
                              position: 'top-end',
                              type: 'success',
                              title: data.message,
                              showConfirmButton: false,
                              timer: 1500
                            });
                    }else{
                            Swal.fire({
                              position: 'top-end',
                              type: 'danger',
                              title: data.message,
                              showConfirmButton: false,
                              timer: 1500
                            });
                    }
                }
            }
        });
});
SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
        return "<select class='grid_check_request input-sm' id='request_status$this->id'  data-url='".url('admin/requests/request_status/')."' data-id='{$this->id}'>
                                    <option selected='true' disabled='disabled'>SELECT</option>
                                    <option value='1'>PENDING</option>
                                    <option value='2'>Processing</option>
                                    <option value='3'>In Embassy</option>
                                    <option value='4'>At Office</option>
                                    <option value='5'>COMPELETED</option>
                                </select>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
