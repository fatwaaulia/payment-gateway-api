<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway API</title>
</head>
<body>
    <h3>Keranjang:</h3>
    <ul>
        <li>Buku A | Rp 50.000</li>
        <li>Buku B | Rp 100.000</li>
    </ul>

    <h4>Total: Rp 150.000</h4>

    <button id="pay-button">Bayar</button>
    

    <?php
    $env = parse_ini_file('.env');
    
    require 'vendor\midtrans\midtrans-php\Midtrans.php';
    
    // Set your Merchant Server Key
    \Midtrans\Config::$serverKey = $env['server_key'];
    // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
    \Midtrans\Config::$isProduction = false;
    // Set sanitization on (default)
    \Midtrans\Config::$isSanitized = true;
    // Set 3DS transaction for credit card to true
    \Midtrans\Config::$is3ds = true;
 
    $transaction_details = [
        'order_id'    => rand(),
        'gross_amount'  => 150000
    ];
    $items = [
        [
            'id'       => 'item1',
            'price'    => 50000,
            'quantity' => 1,
            'name'     => 'Buku A'
        ],
        [
            'id'       => 'item2',
            'price'    => 100000,
            'quantity' => 1,
            'name'     => 'Buku B'
        ],
    ];
    $customer_details = [
        'first_name'       => "Andri",
        'last_name'        => "Setiawan",
        'email'            => "test@test.com",
        'phone'            => "081322311801",
        'billing_address'  => 
        [
            'first_name'   => "Andri",
            'last_name'    => "Setiawan",
            'address'      => "Karet Belakang 15A, Setiabudi.",
            'city'         => "Jakarta",
            'postal_code'  => "51161",
            'phone'        => "081322311801",
            'country_code' => 'IDN'
        ],
        'shipping_address' => 
        [
            'first_name'   => "John",
            'last_name'    => "Watson",
            'address'      => "Bakerstreet 221B.",
            'city'         => "Jakarta",
            'postal_code'  => "51162",
            'phone'        => "081322311801",
            'country_code' => 'IDN'
        ],
    ];

    $transaction = array(
        // 'enabled_payments' => ['credit_card'],
        'transaction_details' => $transaction_details,
        'customer_details' => $customer_details,
        'item_details' => $items,
    );

    $snap_token = '';
    try {
        $snap_token = \Midtrans\Snap::getSnapToken($transaction);
    }
    catch (\Exception $e) {
        echo $e->getMessage();
    }
    echo "snapToken = ".$snap_token;

    ?>

    <pre><div id="result-json">JSON result will appear here after payment:<br></div></pre> 

    <!-- TODO: Remove ".sandbox" from script src URL for production environment. Also input your client key in "data-client-key" -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= $env['client_key'] ?>"></script>
    <script type="text/javascript">
      document.getElementById('pay-button').onclick = () => {
        snap.pay('<?= $snap_token ?>', {
          onSuccess: result => {
            document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
          },
          onPending: result => {
            document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
          },
          onError: result => {
            document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
          }
        });
      };
    </script>
</body>
</html>