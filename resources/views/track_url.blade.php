<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Include CryptoJS CDN for AES encryption -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js"></script>
  <!-- Include JSEncrypt CDN for RSA encryption -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.3.2/jsencrypt.min.js"></script>
</head>
<body>
 
        
  <!-- <div>
    <p id="ilurl"></p>
  </div> -->

  <script src="{{ asset('public/urlEncryption/ilencryption.js') }}"></script>
  <script src="{{ asset('public/urlEncryption/InvokeSample.js') }}"></script>

  <script>
    // Call encryptData function on page load with Laravel's passed data
    document.addEventListener('DOMContentLoaded', function () {
        const allParams = @json($all_params); // Convert PHP array to JavaScript object
        console.log(allParams);
      encryptData(allParams);
    });
  </script>

</body>
</html>