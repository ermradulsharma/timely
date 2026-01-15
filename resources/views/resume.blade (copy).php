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
                        <div class="text-center" style="">
                            <h1>Your Resume</h1>
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content: center;">
                    <div class="col-lg-12" style="border-top: 2px solid #1F2D43;">
                        <div style="padding: 25px 0; padding-bottom: 0px;">
                            <h2>Personal information</h2>
                            <p style="color: #4b4b4b;">
                                <span style="font-size: 24px;"><b>{{ $userObj['first_name'] . " " .$userObj['last_name'] }}</b></span><br>
                                {{ $userObj['mobile'] }}<br>
                                {{ $userObj['city'] ?? "" }}
                            </p>
                        </div>
                    </div>
                </div>

                @if(count($userObj->work_experiences) > 0)
                <div class="row" style="justify-content: center;">
                    <div class="col-lg-12">
                        <div style="padding: 25px 0; padding-bottom: 10px;">
                            <h2>Job experience</h2>

                        </div>
                    </div>
                </div>

                <div class="row" style="justify-content: center;">
                    @foreach($userObj->work_experiences as $experience)
                    <div class="col-lg-6 col-md-6">
                        <div>
                            <strong>{{ $experience->title }}</strong>
                            <p style="color: #4b4b4b;">
                                {{ $experience->employer ?? "" }}<br>
                                {{ $experience->city }}, {{ $experience->state }}<br>
                                {{ $experience->start_date }} – {{ $experience->end_date ?? "Present" }}</span>
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
                            <h2>Education</h2>

                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content: center;">
                    @foreach($userObj->educations as $educations)
                    <div class="col-lg-6 col-md-6">
                        <div>
                            <strong>{{ $educations->qualification }}</strong>
                            <p style="color: #4b4b4b;">
                                {{ $educations->institute ?? "" }}<br>
                                {{ $educations->city }}, {{ $educations->state }}<br>
                                {{ $educations->start_date }} – {{ $educations->end_date ?? "Present" }}</span>
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
                            <h2>Skills</h2>
                            <ul style="color: #4b4b4b; padding: 0 15px;">
                                @foreach($userObj->skills as $skill)
                                <li>{{ $skill->skill_detail->title }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                @if(!empty($userObj->summary))
                <div class="row" style="justify-content: center;">
                    <div class="col-lg-12">
                        <div style="padding: 25px 0;">
                            <h2>Summary</h2>
                            <p style="color: #4b4b4b;">{{ $userObj->summary }}</p>
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