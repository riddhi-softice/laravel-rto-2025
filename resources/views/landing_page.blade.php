<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RTO Vehicle Information</title>
      <!-- App-related meta tags -->
      <meta name="apple-mobile-web-app-title" content="RTO Vehicle Information">
      <link rel="apple-touch-icon" href="{{ asset('public/assets/landing_page/img/logo.png') }}">

      <!-- Icons -->
      <link rel="icon" href="{{ asset('public/assets/landing_page/img/logo.png') }}" type="image/x-icon">

      <!-- SEO Meta Tags -->
      <meta name="description" content="Rtovehicleinfo is your go-to platform for vehicle registration and RTO-related information. Get real-time data, easy access, and updates.">
      <meta name="keywords" content="RTO, vehicle information, vehicle registration, RTO services, vehicle lookup, Rtovehicleinfo">
      <meta name="author" content="Your Company Name">
      <meta name="robots" content="index, follow">

      <!-- Open Graph Meta Tags (for social media sharing) -->
      <meta property="og:title" content="Rtovehicleinfo - Vehicle Registration and RTO Information">
      <meta property="og:description" content="Access detailed RTO-related data and vehicle registration information with Rtovehicleinfo.">
      <meta property="og:image" content="{{ asset('public/assets/landing_page/img/logo.png') }}">
      <meta property="og:url" content="https://yourwebsite.com">
      <meta property="og:type" content="website">

      <!-- Twitter Card Meta Tags -->
      <meta name="twitter:card" content="summary_large_image">
      <meta name="twitter:title" content="Rtovehicleinfo - Vehicle Registration and RTO Information">
      <meta name="twitter:description" content="Find detailed vehicle registration and RTO information on Rtovehicleinfo.">
      <meta name="twitter:image" content="{{ asset('public/assets/landing_page/img/logo.png') }}">

    <link rel="icon" href="{{ asset('public/assets/landing_page/img/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('public/assets/landing_page/final.css') }}">
</head>
<body>
    <header>
        <div class="logo">
            <img src="{{ asset('public/assets/landing_page/img/logo.png') }}" alt="Logo">
        </div>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="http://rtovehicleinfo.com/privacypolicy">Privacy Policy</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero">
        <div class="container">
            <div class="content">
                <h1>RTO Vehicle Information</h1>
                <div class="app-buttons">
                    <a href="https://play.google.com/store/apps/details?id=com.rto.vehicle.info.status" class="playstore">
                        <img src="{{ asset('public/assets/landing_page/img/image.png') }}" alt="Google Play">
                    </a>

                </div>
            </div>
            <div class="phone">
                <img src="{{ asset('public/assets/landing_page/img/phone-mockup.png') }}" alt="Phone App Mockup">
            </div>
        </div>
    </section>
</body>
<script>
    document.addEventListener('contextmenu', function(event) {
        event.preventDefault();
    });
</script>
</html>
