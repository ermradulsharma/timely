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
                        <a style="margin-right: 5px;" href="{{ route('interview-slot.booked') }}" type="button" class="btn btn-primary booked-slot-btn">Booked Slot</a>
                        <a href="{{ route('interview-slot.available') }}" type="button" class="btn btn-primary ml-1 available-slot-btn">Available Slot</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">

            <div class="row">
                <div class="col-xl-12">
                    @include('success-error')

                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_small" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Interview Slot</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('interview-slot.store') }}" id="slot-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="slot-date" name="slot_date" value="">
                <input type="hidden" id="end-time-hidden" name="end_time_hidden">

                <div class="modal-body">
                    <div class="alert bg-danger text-white alert-dismissible d-none slot-val-error">
                        <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                        Start time can't be greater than end time, please choose correct time
                    </div>
                    <div class="form-group">
                        <label>Start Time:</label>
                        <input type="text" id="start-time" name="start_time" class="form-control pickatime-my rounded-right" placeholder="Start Time" required>
                        <span class="text-danger d-none" id="start-time-error">Start time required</span>
                    </div>
                    <div class="form-group">
                        <label>End Time:</label>
                        <input type="text" id="end-time" name="end_time" class="form-control pickatime-my rounded-right" placeholder="End Time" required readonly>
                        <span class="text-danger d-none" id="end-time-error">End time required</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-link" data-dismiss="modal">Close</button>
                    <button type="button" id="add-slot-submit-btn" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- <div id="event-detail-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Detail</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label"><strong>Name:</strong></label>
                        <div class="col-lg-8 col-form-label">
                            <span id="user-name"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label"><strong>Email:</strong></label>
                        <div class="col-lg-8 col-form-label">
                            <span id="user-email"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label"><strong>Booking Time:</strong></label>
                        <div class="col-lg-8 col-form-label">
                            <span id="user-booking-time"></span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> -->

<div id="booking-detail-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Detail</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label"><strong>Name:</strong></label>
                    <div class="col-lg-8 col-form-label">
                        <span id="user-name"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label"><strong>Email:</strong></label>
                    <div class="col-lg-8 col-form-label">
                        <span id="user-email"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label"><strong>Appointment Time:</strong></label>
                    <div class="col-lg-8 col-form-label">
                        <span id="user-booking-time"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
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
    /* document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',

                eventLimit: true, // for all non-TimeGrid views
                dayMaxEvents: 2,

                selectable: true,
                dateClick: function(info) {
                    console.log('clicked ', info);
                },
                select: function(info) {
                    console.log('selected ' + info.startStr + ' to ' + info.endStr);
                },

                events: [{
                        "title": "All Day Event 1",
                        "start": "2021-08-01"
                    },
                    {
                        "title": "All Day Event 2",
                        "start": "2021-08-01"
                    },
                    {
                        "title": "All Day Event 3",
                        "start": "2021-08-01"
                    },
                    {
                        "title": "All Day Event 4",
                        "start": "2021-08-01"
                    },
                    {
                        "title": "All Day Event 4",
                        "start": "2021-08-02"
                    },
                    {
                        "title": "Long Event",
                        "start": "2020-09-07",
                        "end": "2020-09-10"
                    },
                    {
                        "id": "999",
                        "title": "Repeating Event",
                        "start": "2020-09-09T16:00:00-05:00"
                    },
                    {
                        "id": "999",
                        "title": "Repeating Event",
                        "start": "2020-09-16T16:00:00-05:00"
                    },
                    {
                        "title": "Conference",
                        "start": "2020-09-11",
                        "end": "2020-09-13"
                    },
                    {
                        "title": "Meeting",
                        "start": "2020-09-12T10:30:00-05:00",
                        "end": "2020-09-12T12:30:00-05:00"
                    },
                    {
                        "title": "Lunch",
                        "start": "2020-09-12T12:00:00-05:00"
                    },
                    {
                        "title": "Meeting",
                        "start": "2020-09-12T14:30:00-05:00"
                    },
                    {
                        "title": "Happy Hour",
                        "start": "2020-09-12T17:30:00-05:00"
                    },
                    {
                        "title": "Dinner",
                        "start": "2020-09-12T20:00:00"
                    },
                    {
                        "title": "Birthday Party",
                        "start": "2020-09-13T07:00:00-05:00"
                    },
                    {
                        "title": "Click for Google",
                        "url": "http://google.com/",
                        "start": "2020-09-28"
                    }
                ]

            });
            calendar.render();
        }); */
</script>
<script>
    let eventData = '<?php echo $result; ?>'
    eventData = JSON.parse(eventData);
    console.log(eventData);

    $(document).ready(function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',

            eventLimit: true, // for all non-TimeGrid views
            dayMaxEvents: 2,

            selectable: true,
            dateClick: function(info) {
                console.log('clicked ', info);

                let selectedDate = info.dateStr;

                let currentDate = new Date()
                currentDate = formatDate(currentDate);

                console.log("currentDate", currentDate)

                var d1 = Date.parse(selectedDate);
                var d2 = Date.parse(currentDate);

                if (d1 < d2) {
                    swal("Please don't select previous date");
                } else {
                    $("#slot-date").val(info.dateStr);
                    $("#modal_small").modal('show');
                }
            },
            select: function(info) {
                console.log('selected ' + info.startStr + ' to ' + info.endStr);
            },

            height: 600,

            eventDidMount: function(info) {
                $(info.el).tooltip({
                    title: info.event.extendedProps.description,
                    container: 'body',
                    delay: {
                        "show": 50,
                        "hide": 50
                    }
                });
            },

            /* eventRender: function(info) {
                var tooltip = new Tooltip(info.el, {
                    title: info.event.extendedProps.description,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }, */

            /* eventMouseEnter: function(info) {
                console.log("mouseEnterInfo", info)

                var tooltip = new Tooltip(info.el, {
                    title: info.event.extendedProps.description,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }, */

            /* events: [{
                    "title": "All Day Event 1",
                    "start": "2021-08-01",
                    "description": "Test Description"
                },
                {
                    "title": "All Day Event 2",
                    "start": "2021-08-01"
                },
                {
                    "title": "All Day Event 3",
                    "start": "2021-08-01"
                },
                {
                    "title": "All Day Event 4",
                    "start": "2021-08-01"
                },
                {
                    "title": "All Day Event 4",
                    "start": "2021-08-02"
                },
                {
                    "title": "Long Event",
                    "start": "2020-09-07",
                    "end": "2020-09-10"
                },
                {
                    "id": "999",
                    "title": "Repeating Event",
                    "start": "2020-09-09T16:00:00-05:00"
                },
                {
                    "id": "999",
                    "title": "Repeating Event",
                    "start": "2020-09-16T16:00:00-05:00"
                },
                {
                    "title": "Conference",
                    "start": "2020-09-11",
                    "end": "2020-09-13"
                },
                {
                    "title": "Meeting",
                    "start": "2020-09-12T10:30:00-05:00",
                    "end": "2020-09-12T12:30:00-05:00"
                },
                {
                    "title": "Lunch",
                    "start": "2020-09-12T12:00:00-05:00"
                },
                {
                    "title": "Meeting",
                    "start": "2020-09-12T14:30:00-05:00"
                },
                {
                    "title": "Happy Hour",
                    "start": "2020-09-12T17:30:00-05:00"
                },
                {
                    "title": "Dinner",
                    "start": "2020-09-12T20:00:00"
                },
                {
                    "title": "Birthday Party",
                    "start": "2020-09-13T07:00:00-05:00"
                },
                {
                    "title": "Click for Google",
                    "url": "http://google.com/",
                    "start": "2020-09-28"
                }
            ] */
            eventOrder: 'order',
            events: eventData,

            eventClick: function(event) {
                const booking_slot_time = event.event.extendedProps.booking_slot_time;
                const userDetail = event.event.extendedProps.user_detail;
                console.log("eventClick", event.event.extendedProps, booking_slot_time, userDetail);

                if (userDetail) {
                    $("#user-name").text(userDetail.first_name + ' ' + userDetail.last_name);
                    $("#user-email").text(userDetail.email);
                    $("#user-booking-time").text(booking_slot_time);

                    $("#booking-detail-modal").modal('show');
                } else {
                    //$("#event-detail-modal").modal('show');
                }
            },

        });
        calendar.render();

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
    });
</script>
@endsection
@section('page_style')
<style>
    .popular-items-chart-wrapper {
        width: 50%;
        float: left;
    }

    .picker {
        /* position: inherit !important; */
        top: auto;
    }

    table.fc-scrollgrid.fc-scrollgrid-liquid {
        background-color: white;
    }

    th.fc-col-header-cell.fc-day {
        background-color: #ea9e00;
    }

    a.fc-col-header-cell-cushion {
        color: white !important;
    }

    a.btn.btn-primary.booked-slot-btn {
        color: white;
        background-color: red !important;
        border-color: red !important;
    }

    a.btn.btn-primary.ml-1.available-slot-btn {
        color: white;
        background-color: green !important;
        border-color: green !important;
    }
</style>
@endsection