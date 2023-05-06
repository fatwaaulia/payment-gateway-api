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
        <li>Buku A | Rp 10.000</li>
        <li>Buku B | Rp 5.000</li>
    </ul>

    <h4>Total: Rp 15.000</h4>

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

    $params = [
        'transaction_details' => [
            'order_id'    => rand(),
            'gross_amount'  => 15000,
        ],
    ];
    $snap_token = \Midtrans\Snap::getSnapToken($params);
 
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
                // document.getElementById('result-json').innerHTML += Object.entries(result);
                fetch('#', {
                    method: 'POST',
                    header: {
                        'Content-Type': 'application/json'
                    },
                    body: Object.entries(result)
                })
                console.log(Object.assign(result))
                // .then(res => console.log(res))
            },
            onError: result => {
                fetch('#', {
                    method: 'POST',
                    header: {
                        'Content-Type': 'application/json'
                    },
                    body: Object.entries(result)
                })
                .then(res => console.log(res))
            }
        });
    };
    </script>

    <br>
    <h3>Data transaksi by id</h3>

    <?php
    $status = \Midtrans\Transaction::status('858651142');
    print_r($status);

    ?>

</body>
</html>