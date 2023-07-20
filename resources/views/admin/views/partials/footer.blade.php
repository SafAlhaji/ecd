<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        @if(config('admin.show_environment'))
            <strong>Env</strong>&nbsp;&nbsp; {!! config('app.env') !!}
        @endif

        &nbsp;&nbsp;&nbsp;&nbsp;

        @if(config('admin.show_version'))
        <strong>Version</strong>&nbsp;&nbsp; {!! \Encore\Admin\Admin::VERSION !!}
        @endif

    </div>
    <!-- Default to the left -->
<?php
$branch = \App\Models\ThirdParty::find(\Encore\Admin\Facades\Admin::user()->id)->branches()->first();
if ($branch) {
    $title = $branch->title;
}else{
    $title = 'Main Branch';
}
?>
    <strong>Branch :{{$title}}</strong>

</footer>
