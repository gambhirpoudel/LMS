<?php
// Your PHP code here (e.g., session start, fetching data, etc.)

// Base URL of your application (adjust it accordingly)
$baseUrl = "http://localhost/LMS";  // Change this to your actual URL
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khalti Payment</title>
</head>

<body>

    <!-- Your payment button -->
    <button id="payment-button">Pay Now</button>

    <!-- Include Khalti Checkout Script -->
    <script src="https://khalti.com/static/khalti-checkout.js"></script>

    <!-- Include your payment script -->
    <script>
        var config = {
            "publicKey": "your_public_key", // Add your public key here
            "productIdentity": "product_id", // Add your product identity here
            "productName": "product_name", // Add your product name here
            "productUrl": "your_product_url", // Add your product URL here
            "eventHandler": {
                onSuccess: function(payload) {
                    $.ajax({
                        url: "<?php echo $baseUrl; ?>/payment/verification.php", // Manually define the path to the verification script
                        type: 'GET',
                        data: {
                            amount: payload.amount,
                            trans_token: payload.token
                        },
                        success: function(res) {
                            console.log("Transaction succeeded");
                        },
                        error: function(error) {
                            console.log("Transaction failed");
                        }
                    });
                },
                onError: function(error) {
                    console.log(error);
                },
                onClose: function() {
                    console.log('widget is closing');
                }
            }
        };

        var checkout = new KhaltiCheckout(config);
        var btn = document.getElementById("payment-button");
        btn.onclick = function() {
            checkout.show({
                amount: 1000
            }); // Amount in paisa (1/100 of currency unit)
        }
    </script>

</body>

</html>