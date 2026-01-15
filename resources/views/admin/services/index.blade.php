@extends('layouts.admin.admin')
@section('content')
<!-- Main content -->


<div class="page-header page-header-light">
    <div class="page-header-content header-elements-lg-inline">
        <div class="page-title d-flex">
            <h4> <span class="font-weight-semibold">{{ $data['page_title'] ?? 'Dashboard' }}</span></h4>
            <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="{{ route('categories.create') }}" type="button" class="btn btn-primary ml-1">Add new</a>

                <form style="margin-left: 5px;" action="{{ route('categories.index') }}">
                    <div class="navbar-search d-flex align-items-center py-2 py-lg-0">
                        <div class="form-group-feedback form-group-feedback-left flex-grow-1">
                            <input type="search" name="q" class="form-control my-search-box" placeholder="Search" value="{{ $data['q'] ?? '' }}">
                            <button type="submit" id="search-btn-my" class="btn btn-primary"><i class="icon-search4 fa fa-fw"></i></button>
                            <div class="form-control-feedback">
                                <i class="icon-search4 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('success-error')
    <div class="card">
        <div class="table-responsive">
            <table class="table my-datatable">
                <thead class="t-head">
                    <tr>
                        <th class="corner-left">#</th>
                        <th>Name </th>
                        <th>Image </th>
                        <th class="corner-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($result) > 0)
                    <?php $firstItemIndex = $result->firstItem(); ?>
                    @foreach($result as $key => $value)
                    <tr>
                        <td>{{ $firstItemIndex++ }}</td>
                        <td>{{ $value['name'] ?? '' }}</td>
                        <td><img class="img-thumbnail " id="preview-field" src="{{ $value['image'] ?? '' }}" width="50" height="50" alt=""></td>
                        <td>
                            <div class="list-icons">
                                <a href="{{ route('categories.edit', [$value['id'], 'page' => $data['page']]) }}" class="list-icons-item text-primary"><i class="icon-pencil7"></i></a>
                                <a href="{{ route('categories.delete', [$value['id'], 'page' => $data['page']]) }}" class="list-icons-item text-danger delete-resource"><i class="icon-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                <tfoot class="datatable">
                    <tr>
                        <td class="text" colspan="8">
                            <?php echo 'Showing ' . $result->firstItem() . ' to ' . $result->lastItem() . ' out of ' . $result->total() . ' entries'; ?>
                        </td>
                    </tr>
                </tfoot>
                @else
                <tr>
                    <td class="text-center" colspan="3">No Record Found</td>
                </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div style="float: right;">
        {{ $result->appends($_GET)->links() }}
    </div>
</div>
@endsection
@section('page_script')
<script>
    $(document).ready(function() {

    });
</script>
@endsection
@section('page_style')
<style>
    .popular-items-chart-wrapper {
        width: 50%;
        float: left;
    }
</style>
@endsection