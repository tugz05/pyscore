<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PyScore - Home</title>

    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">



</head>

<body>

    <!-- Desktop & Mobile Adapted Section -->
    <!-- First Section -->
    <div class="section">
        <img src="{{ asset('assets/png/homepage_background.png') }}" alt="Homepage Background">
        <div class="content">
            <h1 style="font-size: 40px; font-weight: bold; font-family: 'Montserrat', sans-serif;">
                PYSCORE
            </h1>
            <h1 style="font-size: 70px; font-weight: bold; font-family: 'Montserrat', sans-serif;">
                <br>Where <span
                    style="background-color: #ffdb31; padding: 5px 10px; border-radius: 5px;">automation</span>
                <br> meets the future of coding <br> assessments.
            </h1>
            <br>
            <div class="pt-1 mb-4">
                <a class="btn btn-info btn-lg" type="button" style="width: 30%;" data-toggle="modal" data-target="#loginModal">
                    <span>Get Started &nbsp;<i class="fas fa-arrow-right"></i></span>
                </a>
            </div>
            
            <div class="pt-1 mb-4">
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Desktop & Mobile Adapted Section -->
    <!-- Second Section-->
    <div class="section">
        <div class="container">
            <!-- Left Side: Heading and Text -->
            <div class="left-content">
                <h4 style="color: #a0a5b1; font-weight: bold; text-transform: uppercase; margin-bottom: 30px;">
                    What is PyScore?
                </h4>
                <h1 style="font-size: 3rem; font-weight: bold; color: #1e293b; line-height: 1.3;">
                    An <span style="background-color: #cde6ff; padding: 5px 10px; border-radius: 5px;">automated
                        grading</span>
                    <span>platform</span>
                    for Python programming activities.
                </h1>
            </div>

            <!-- Right Side: Features -->
            <div class="right-content">
                <ul class="features-list">
                    <li><i class="fas fa-check-circle"></i> Automatic code checking</li>
                    <li><i class="fas fa-check-circle"></i> Similar code detection</li>
                    <li><i class="fas fa-check-circle"></i> Real-time code feedback</li>
                    <li><i class="fas fa-check-circle"></i> Accessible via mobile phones</li>
                </ul>
            </div>
        </div>

    </div>
   <!-- Third Section - Stepper Design -->
    <div class="section" style="background-color: #eaf4ff; padding: 50px;">
        <div class="container stepper-container">
            <!-- Student Section -->
            <div class="stepper-section">
                <h2 class="stepper-title">Student</h2>
                <div class="stepper-content">
                    <div class="step">
                        <div class="step-number">1</div>
                        <span class="step-text">Login using your <b>NEMSU Workspace Account</b> </span><!-- Login Modal -->
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <span class="step-text">Enter Class Code</span>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <span class="step-text">View and answer posted programming activities</span>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <span class="step-text">Submit output</span>
                    </div>
                    <div class="step">
                        <div class="step-number">5</div>
                        <span class="step-text">View grade and code feedback</span>
                    </div>
                </div>
            </div>
            <!-- Instructor Section -->
            <div class="stepper-section">
                <h2 class="stepper-title">Instructor</h2>
                <div class="stepper-content">
                    <div class="step">
                        <div class="step-number">1</div>
                        <span class="step-text">Login using your NEMSU Workspace Account</span>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <span class="step-text">Create Classes</span>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <span class="step-text">Post programming activities</span>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <span class="step-text">View grade and code feedback</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login Required</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Login using NEMSU Workspace account to continue.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="{{ route('google.redirect') }}" class="btn btn-primary">Continue</a>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>


</body>

</html>
