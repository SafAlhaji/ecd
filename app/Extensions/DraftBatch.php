<?php

namespace App\Extensions;

use Encore\Admin\Admin;

class DraftBatch
{
    protected $id;
    protected $dratf_url;

    public function __construct($id, $dratf_url)
    {
        $this->id = $id;
        $this->dratf_url = $dratf_url;
    }

    protected function script()
    {
        return <<<SCRIPT
$('.draft-batch_{$this->id}').on('click', function () {
var url = $(this).data('url');
        $.ajax({
        method: 'get',
        url: url,
        dataType : 'json',
        data:$(this).serialize(),
        success: function (data) {
            if(data.status){
                $.pjax.reload('#pjax-container');
                toastr.success(data.message);        
            }else{
                $.pjax.reload('#pjax-container');
                toastr.error(data.message);  
            }
        
        }
        });
});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='btn btn-xs btn-success fa fa-compress draft-batch_{$this->id}' data-id='{$this->id}' data-url='".url($this->dratf_url)."'></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
