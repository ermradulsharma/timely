@extends('layouts.admin.admin')
@section('content')
<!-- Main content -->
<div class="content-wrapper">

    <div class="content-inner">

        <div class="page-header page-header-light">
            <div class="page-header-content header-elements-lg-inline">
                <div class="page-title d-flex">
                    <h4> <span class="font-weight-semibold">{{ $data['page_title'] ?? 'Dashboard' }}</span></h4>
                    <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
                </div>

                <div class="header-elements d-none">
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('interview-slot.available') }}" type="button" class="btn btn-primary">Back</a>
                        <!-- <a href="{{ route('interview-video.create') }}" type="button" class="btn btn-primary">Add</a> -->
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="card">
                @include('success-error')
                <div class="card-body">
                    <form action="{{ route('interview-slot.update', [$data['id']]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{ method_field('PUT') }}
                        <input type="hidden" id="end-time-hidden" name="end_time_hidden" value="{{ $data['end_time'] }}">
                        <input type="hidden" name="update_id" value="{{ $data['id'] }}">
                        <!-- <input type="hidden" name="qid" value="{{ $data['qid'] }}"> -->
                        <div class="form-group row">
                            <label class="col-form-label col-lg-2">Date</label>
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input name="slot_date" id="slot_date" class="form-control form-control-lg" type="Date" placeholder="Slot Date" value="{{ $data['slot_date'] }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-lg-2">Start Time</label>
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input type="text" id="start-time" name="start_time" class="form-control pickatime-my rounded-right" value="{{ $data['start_time'] }}" placeholder="Start Time" required>
                                            <!-- <input name="start_time" class="form-control form-control-lg" type="Time" placeholder="Start Time" value="{{ $data['start_time'] }}" required> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-lg-2">End Time</label>
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <!-- <input name="end_time" class="form-control form-control-lg" type="Time" placeholder="End Time" value="{{ $data['end_time'] }}" required> -->
                                            <input type="text" id="end-time" name="end_time" class="form-control pickatime-my rounded-right" placeholder="End Time" value="{{ $data['end_time'] }}" required readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-lg-2"></label>
                            <div class="col-lg-10">
                                <button type="submit" class="btn btn-primary">Submit <i class="icon-paperplane ml-2"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_script')
<script src="{{ asset('admin') }}/js/plugins/pickers/pickadate/picker.js"></script>
<script src="{{ asset('admin') }}/js/plugins/pickers/pickadate/picker.date.js"></script>
<script src="{{ asset('admin') }}/js/plugins/pickers/pickadate/picker.time.js"></script>
<script src="{{ asset('admin') }}/js/plugins/pickers/pickadate/legacy.js"></script>

<script src="{{ asset('admin') }}/js/demo_pages/picker_date.js"></script>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    $(document).ready(function() {
        // $(function(){
        //     var dtToday = new Date();

        //     var month = dtToday.getMonth() + 1;
        //     var day = dtToday.getDate();
        //     var year = dtToday.getFullYear();

        //     $('#slot_date').attr('min', maxDate);
        // });

        $(function() {
            var dtToday = new Date();

            var month = dtToday.getMonth() + 1;
            var day = dtToday.getDate();
            var year = dtToday.getFullYear();
            if (month < 10)
                month = '0' + month.toString();
            if (day < 10)
                day = '0' + day.toString();

            var maxDate = year + '-' + month + '-' + day;
            // alert(maxDate);
            $('#slot_date').attr('min', maxDate);
        });

        $(".resource-status").on("change", function() {
            const resource_id = $(this).attr('data-id');
            const is_checked = $(this).is(":checked");

            let status = '0';
            if (is_checked) {
                status = '1';
            }

            $.ajax({
                url: "{{ route('update.user.status') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    resource_id: resource_id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {

                },
                error: function(error) {

                }
            });
        });
    });
</script>

<script>
    const convertMinsToHrsMins = (mins) => {
        let h = Math.floor(mins / 60);
        let m = mins % 60;
        h = h < 10 ? '0' + h : h;
        m = m < 10 ? '0' + m : m;
        return `${h}:${m}`;
    }

    function minutesToHHMM(mins, twentyFour = false) {
        let h = Math.floor(mins / 60);
        let m = mins % 60;
        m = m < 10 ? '0' + m : m;

        if (twentyFour) {
            h = h < 10 ? '0' + h : h;
            return `${h}:${m}`;
        } else {
            let a = 'AM';
            if (h >= 12) a = 'PM';
            if (h > 12) h = h - 12;

            if (h == 0) {
                return `${12}:${m} ${a}`;
            } else {
                return `${h}:${m} ${a}`;
            }
        }
    }

    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, month, day].join('-');
    }
</script>
<script>
    $("#end-time").prop("disabled", true);
    $('#start-time').pickatime({
        onSet: function(context) {
            console.log('Just set stuff:', context)

            let startTIme = context.select;

            let endTime = startTIme + 20;

            //let endTimeText = convertMinsToHrsMins(endTime);
            let endTimeText = minutesToHHMM(endTime);

            $("#end-time").val(endTimeText).prop("disabled", true);
            $("#end-time-hidden").val(endTimeText);
        }
    });

    $('#end-time').pickatime({
        editable: false,
        onSet: function(context) {
            console.log('Just set stuff end-time:', context)
        }
    });

    $("#add-slot-submit-btn").on("click", function() {
        const startTime = $("#start-time").val();
        const endTime = $("#end-time").val();

        console.log("startTime", startTime);
        console.log("endTime", endTime);

        if (startTime == "") {
            $("#start-time-error").removeClass("d-none");

            return false;
        }

        if (endTime == "") {
            $("#end-time-error").removeClass("d-none");

            return false;
        }

        const slotDate = $("#slot-date").val();

        var startDateTime = slotDate + " " + startTime;
        var endDateTime = slotDate + " " + endTime;

        var sDateTime = new Date(startDateTime).getTime();
        var eDateTime = new Date(endDateTime).getTime();

        if (sDateTime > eDateTime) {
            $(".slot-val-error").removeClass('d-none');
            $(".slot-val-error").css('display', 'block');

            setTimeout(function() {
                $("div.alert").fadeTo(2000, 500).slideUp(500, function() {
                    $("div.alert").slideUp(500);
                });
            }, 100);

            return false;
        }

        if (startTime != "" && endTime != "") {
            $("#slot-form").submit();

            return true;
        }
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