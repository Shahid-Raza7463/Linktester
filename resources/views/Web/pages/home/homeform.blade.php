<div class="navbar"
    style="display: flex;
margin: 20p;
background-color: #e9e4e430;
margin-top: 14px;
margin-bottom: 50px;
border-radius: 11px;">
    <div class="text-center m-3">
        Home
    </div>
    <div style="align-self: flex-start;
    margin-right: 23px;">
        <a href="{{ route('login') }}" class="btn btn-sm  mt-3 logout-btn effect">Admin Login</a>
    </div>
</div>
<div class="title text-center">
    <h1 class="heading">Test Affiliate Offer Tracking URL</h1>
</div>

</div>
<form action="{{ url('/') }}/tester" method="post" id="tester_form" enctype="multipart/form-data">
    @csrf
    {{-- error message display --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="validationMassage mb-3">

    </div>
    {{-- success message --}}
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <div class="form-container" style="
    margin-top: 50px;">
        <div class="row mb-3 container-content">
            <div class="col-2">
                <label for="">URL</label>
            </div>
            @if (isset($url) && !empty($url))
                <div class="col-10">
                    <input type="text" name="url" class="form-control" placeholder="  Enter Url"
                        style="width: 925px;height: 49px;"value="{{ $url }}">
                </div>
            @else
                <div class="col-10">
                    <input type="text" name="url" id="originUrl" class="form-control" placeholder="  Enter Url"
                        style="width: 925px;height: 49px;"value="{{ old('url') }}">
                </div>
            @endif
        </div>
        <div class="row mb-3 container-content">
            <div class="col-2">
                <label for="">OS/Device</label>
            </div>
            <div class="col-10">
                <select class="form-select select-box" id="test_device" name="device">
                    <option value="android 7">Android 7</option>
                    <option value="android 9">Android 9</option>
                    <option value="android 10">Android 10</option>
                    <option value="android 13">Android 13</option>
                    <option value="ios 11">iOS 11</option>
                    <option value="ios 12">iOS 12</option>
                    <option value="ios 13">iOS 13</option>
                    <option value="desktop">Desktop</option>
                </select>
            </div>
        </div>
        <div class="row mb-3 container-content">
            <div class="col-2">
                <label for="">Country</label>
            </div>
            <div class="col-10">
                <select id="country" name="country" class="form-select select-box">
                    @foreach ($countriesName as $country)
                        <option value="{{ $country->country }}">{{ $country->country }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- Get google recapcha --}}
        <div class="row mb-3 container-content" style="height: 91px;">
            <div class="col-2" style="margin-left: 191px;">
                <div class="g-recaptcha" data-sitekey="6LcfD40mAAAAAAogNxAJih1P9r7q7j9xQ_EwuIpp"></div>
            </div>
        </div>
        <div class="row mb-3 container-content" style="height: 65px;">
            <div class="col-2" style="margin-left: 191px;">
                <input type="submit" value="Start Test" id="startbtn" name="submit"class="btn start-test-botton">
            </div>
        </div>
        <hr />
    </div>
</form>
{{-- it is used when share link  --}}
<div class="form-container" style="margin-top: 50px;">
    <div class="row mb-3">
        <div class="col-2">
            <h4 style="color:#0b163f;font-weight: 700;">Results</h4>
            <p class="ok"></p>
        </div>
        {{-- display data dynamically using ajax --}}
        <div class="result">
            @if (isset($report->id) && !empty($report->id))
                <table class="table table-bordered table-hover table-darks">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Domain</th>
                            <th>Redirect Url</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Get origin url --}}
                        @php
                            // Get data in array seprately
                            $originUrl = parse_url($inputUrl->url);
                            // Get domain from host index from array
                            $origindomain = isset($originUrl['host']) ? $originUrl['host'] : '';
                        @endphp
                        <tr>
                            <td>1</td>
                            <td>{{ $origindomain }}</td>
                            <td> {{ $inputUrl->url }}</td>
                        </tr>
                        @php
                            $i = 2;
                            $responseData = json_decode($report->response, true);
                        @endphp
                        @foreach ($responseData as $response)
                            @php
                                // Get data in array seprately
                                $parsedUrl = parse_url($response);
                                // Get domain from host index from array
                                $domain = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
                            @endphp
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $domain }}</td>
                                <td>{{ $response }}</td>
                            </tr>
                            @if (!$loop->last)
                                {{ "\n" }}
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>


<!-- jQuery ajax -->
{{-- form submited using ajax --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        // hare  validation function
        function displayValidatorErrors(errors) {
            // Clear existing error messages
            $('.validationMassage').empty();
            $.each(errors, function(field, messages) {
                $.each(messages, function(index, message) {
                    console.log(message);
                    var errorMessage = $(".result").text(message);
                    if (errorMessage) {
                        $(".result").text(message);
                        $("#startbtn").prop("disabled", false);
                    }
                    // var errorItem = $('<div>').addClass('error').text(message);
                    // $('.validationMassage').append(errorItem);
                });
            });
        }

        $("#tester_form").submit(function(e) {
            e.preventDefault();
            var form = $("#tester_form")[0];
            var formData = new FormData(form);

            $(".result").text("Please wait...");
            $("#startbtn").prop("disabled", true);
            var url = "{{ url('/') }}/tester";
            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                dataType: 'json',

                success: function(data) {
                    if (data.success) {
                        // Get Div with class result
                        var response = $(".result");
                        // btn remove disable
                        $("#startbtn").prop("disabled", false);
                        response.empty(); // clear previeus result

                        // Get originUrlValue
                        var originUrlValue = $("#originUrl").val();

                        // Create the table dynamically
                        var table = $("<table>").addClass(
                            "table table-bordered table-hover table-darks");
                        // Create the thead tag
                        var thead = $("<thead>");
                        var headerRow = $("<tr>");
                        var headerCell1 = $("<th>").text("#");
                        var headerCell2 = $("<th>").text("Domain");
                        var headerCell3 = $("<th>").text("Redirect Url");
                        headerRow.append(headerCell1);
                        headerRow.append(headerCell2);
                        headerRow.append(headerCell3);
                        thead.append(headerRow);
                        table.append(thead);

                        // Create the tbody tag
                        var tbody = $("<tbody>");

                        // Add origin URL in the table
                        // Get object of link url
                        var originUrlObject = new URL(originUrlValue);
                        // Get domain name frm object
                        var domainOriginUrl = originUrlObject.hostname;

                        var originRow = $("<tr>");
                        var originIndexCell = $("<td>").text(1);
                        var originDomainCell = $("<td>").text(domainOriginUrl);
                        var originUrlCell = $("<td>").text(originUrlValue);
                        originRow.append(originIndexCell);
                        originRow.append(originDomainCell);
                        originRow.append(originUrlCell);
                        tbody.append(originRow);

                        // Get redirect_chain from data object
                        var redirectionChain = data.redirection_chain;
                        // Get tester from data object
                        var tester = data.tester;
                        // Get unique_id from data tester
                        var testerUniqueId = tester.unique_id;

                        // create div with class share for share link test url
                        // var originIndexCell = $(".ok").text(1);
                        var shareDiv = $("<div>").addClass(
                            "share");
                        var h4 = $("<h4>").text("Share Test Results");
                        var input = $("<input>").addClass(
                            "form-control").val("{{ url('/') }}/link-test/" +
                            testerUniqueId +
                            "");
                        shareDiv.append(h4, input);

                        // Add redirectionChain data in the table
                        redirectionChain.forEach(function(url, index) {
                            var urlObject = new URL(url);
                            // Extract the domain from the URL
                            var domain = urlObject.hostname;

                            // Create a new table row
                            var row = $("<tr>");
                            // Create a new table cell for the index
                            var indexCell = $("<td>").text(index + 2);
                            // Create a new table cell for the domain
                            var domainCell = $("<td>").text(domain);
                            // Create a new table cell for the URL
                            var urlCell = $("<td>").text(url);

                            // Add cells to the row
                            row.append(indexCell);
                            row.append(domainCell);
                            row.append(urlCell);

                            // Add the row to the tbody
                            tbody.append(row);
                        });

                        table.append(tbody);

                        // Insert table inside div that contain class result
                        response.append(table);
                        // insert shareDiv after table tag in div that contain class result
                        table.after(shareDiv);

                    } else {

                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    // console.log(xhr.status);
                    if (xhr.status === 429) {
                        var response = $(".result");
                        response.empty();

                        var errorMessage = $("<p>").text(
                            "Too Many Requests, please try after some time");
                        response.append(errorMessage);
                    }
                    // console.log("Error: " + error);
                    displayValidatorErrors(xhr.responseJSON.errors);
                }
            });
        });
    });
</script>


<div class="footer">
    <div class="">
        <h5>TrakAff.Com</h5>
        <p>shahidraza7463@gmail.com</p>
    </div>
    <div class="mx-4">
        <h5>News</h5>
        <p>shahidraza7463@gmail.com</p>
    </div>
    <div class="mx-4">
        <h5>About Us</h5>
        <p>shahidraza7463@gmail.com</p>
    </div>
</div>
<div class="footer-2">
    <p>copy right trakaff.com</p>
</div>
