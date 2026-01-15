@if (session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
    {{ session('success') }}
</div>
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
    {{ session('error') }}
</div>
@endif