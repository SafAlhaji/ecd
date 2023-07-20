<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class ReFundRequest
{
    protected $id;
    protected $refund_url;

    public function __construct($id, $refund_url)
    {
        $this->id = $id;
        $this->refund_url = $refund_url;
    }

    protected function script()
    {
        $trans = [
            'delete_confirm' => 'Are you sure to refund this request ?',
            'confirm' => trans('admin.confirm'),
            'cancel' => trans('admin.cancel'),
            'delete' => trans('admin.delete'),
        ];

        return <<<SCRIPT
$('.refund-request-{$this->id}').on('click', function () {
    var url = $(this).data('url');
    var id = $(this).data('id');
    swal({
        title: "{$trans['delete_confirm']}",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "{$trans['confirm']}",
        showLoaderOnConfirm: true,  
        cancelButtonText: "{$trans['cancel']}",
        preConfirm: function(reason) {
            return new Promise(function(resolve) {
        $.ajax({
        method: 'get',
        url: url,
        dataType : 'json',
        data:$(this).serialize(),
        success: function (data) {
            if(data.status){
                $.pjax.reload('#pjax-container');
                toastr.success(data.message);     
                resolve(data);   
            }else{
                $.pjax.reload('#pjax-container');
                toastr.error(data.message);  
            }
        
        }
        });                
            });
        }
    }).then(function(result) {
        var data = result.value;
        if (typeof data === 'object') {
            if (data.status) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });


});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='btn btn-xs btn-danger fa fa-retweet refund-request-{$this->id}' data-id='{$this->id}' data-url='".url($this->refund_url)."'></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
