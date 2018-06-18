<!DOCTYPE html>
<html lang="en" class="route-documentation">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
          content="Bulma is an open source CSS framework based on Flexbox and built with Sass. It's 100% responsive, fully modular, and available for free.">

    <title>{{ $title  }}</title>
    <component>{{ $title }}</component>

    <link rel="stylesheet" href="../../css/bulma-docs.min.css%3Fv=201806110951.css">
    <script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
    <style>
        main.bd-main:not(:last-child) {
            padding-bottom: 3rem;
        }

        main.bd-main:not(:first-child) {
            margin-top: 5rem;
        }
    </style>
</head>
<body class="layout-documentation">
@foreach($bodies as $body)

    {!! $body !!}

    <hr class="hr">

@endforeach

</body>
</html>
