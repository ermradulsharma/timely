<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body class="bg-light">

    <div class="container">
        <div class="row" style="justify-content: center;">
            <div class="col-lg-8" style="background: white; padding: 10px 45px; padding-bottom: 40px;">


                <div class="row" style="justify-content: center;">
                    <div class="col-lg-12" style="padding: 20px 0;">
                        <div class="text-center" style="text-align: center;">
                            <span style="font-size: 38px;"><b>{{ $userObj['first_name'] . " " .$userObj['last_name'] }}</b></span>
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content: center;">
                    <div class="col-lg-12">
                        <div style="padding: 0px 0; padding-bottom: 0px;">
                            <p style="color: #4b4b4b;">
                                <!-- <span style="font-size: 31px;"><b>{{ $userObj['first_name'] . " " .$userObj['last_name'] }}</b></span><br> -->
                                @if(!empty('city'))
                                <span style="font-size: 25px;">{{ $userObj['city'] }}</span>
                                @endif
                                @if(!empty('state'))
                                <span style="font-size: 25px;">, {{ $userObj['state'] }}</span>,
                                @endif
                                @if(!empty('mobile'))
                                <span style="font-size: 25px;">{{ $userObj['mobile'] }}</span>
                                @endif
                            </p>
                        </div>

                    </div>
                </div>

                @if(!empty($userObj->summary))
                <div class="row" style="justify-content: center;">
                    <div class="col-lg-12">
                        <div style="padding: 25px 0; padding-bottom: 10px;">
                            <span style="font-size: 30px;"><b>Summary</b></span>
                            <p style="color: #4b4b4b;font-size: 20px;">{{ $userObj->summary }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(count($userObj->work_experiences) > 0)
                <div class="row" style="justify-content: center;">
                    <div class="col-lg-12">
                        <div style="padding: 25px 0; padding-bottom: 10px;">
                            <span style="font-size: 35px;"><b>Job experience</b></span>

                        </div>
                    </div>
                </div>

                <div class="row" style="justify-content: center;">
                    @foreach($userObj->work_experiences as $experience)
                    <div class="col-lg-6 col-md-6">
                        <div>
                            <span style="font-size: 29px;"><b>{{ $experience->title }}</b></span>
                            <p style="color: #4b4b4b;">
                                <span style="font-size: 25px;">
                                    <b>{{ $experience->employer ?? "" }} &nbsp;&nbsp;&nbsp;&nbsp; {{ date('M Y', strtotime($experience->start_date)) }} – {{ !empty($experience->end_date) ? date('M Y', strtotime($experience->end_date)) : "Present" }}</b>
                                </span>
                                <br>
                                <span style="font-size: 25px;">
                                    @if(!empty($experience->city))
                                    {{ $experience->city }}
                                    @endif
                                    @if(!empty($experience->state))
                                    , {{ $experience->state }}
                                    @endif
                                </span><br>
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                @if(count($userObj->educations) > 0)
                <div class="row" style="justify-content: center;">
                    <div class="col-lg-12">
                        <div style="padding: 25px 0; padding-bottom: 10px;">
                            <span style="font-size: 35px;"><b>Education</b></span>

                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content: center;">
                    @foreach($userObj->educations as $educations)
                    <div class="col-lg-6 col-md-6">
                        <div>
                            <span style="font-size: 29px;"><b>{{ $educations->institute }}, {{ $educations->city }}, {{ $educations->state }}</b></span>
                            <p style="color: #4b4b4b;">
                                <span style="font-size: 25px;">
                                    <b>{{ $educations->qualification ?? "" }}</b> &nbsp;&nbsp;&nbsp;&nbsp; {{ date('M Y', strtotime($educations->start_date)) }} – {{ !empty($educations->end_date) ? date('M Y', strtotime($educations->end_date)) : "Present" }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                @if(count($userObj->skills) > 0)
                <div class="row" style="justify-content: center;">
                    <div class="col-lg-12">
                        <div style="padding: 25px 0; padding-bottom: 0px;">
                            <span style="font-size: 35px;"><b>Skills</b></span>
                            <ul style="color: #4b4b4b; padding: 0 15px;">
                                @foreach($userObj->skills as $skill)
                                <li><span style="font-size: 25px;">{{ $skill->skill_detail->title }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>


</body>

</html>