<?php
/**
 * S-PayWay Client - API Examples
 * 
 * This demonstrates the 3 core API actions:
 * 1. payment_method - Get available payment methods
 * 2. status - Check invoice payment status
 * 3. checkout - Create payment session
 */

require_once './src/S_PayWayClient.php';

// Configuration
$accessToken = 'SAT_7dc7dda106a0c6bf9a83e01e-ccbc726e72d11380d96eb591-31d93122a7591760110814'; // Replace your Access Token, you can find it in the settings of S-PayWay merchant panel.

$client = new S_PayWayClient($accessToken);

function getPaymentMethods() {
    global $client;
    try {
        return $client->getPaymentMethods();
    } catch (Exception $e) {
        echo "Error: {$e->getMessage()}<br>";
        return [];
    }
}
function checkInvoiceStatus($invoiceToken) {
    global $client;
    try {
        $result = $client->getInvoiceStatus($invoiceToken);
        $invoice = $result['data'];
        
        echo "<br>Invoice Details:<br>";
        echo "  ID: {$invoice['id']}<br>";
        echo "  Status: {$invoice['status']}<br>";
        echo "  Amount: \${$invoice['total']}<br>";
        echo "  Customer: {$invoice['customer']['full_name']}<br>";
        echo "  Product: {$invoice['goods']['name']}<br>";
        
        return $invoice;
    } catch (Exception $e) {
        echo "Error: {$e->getMessage()}<br>";
        return null;
    }
}
function createCheckout($invoiceToken, $paymentMethod) {
    global $client;
    try {
        $requestId = S_PayWayClient::generateRequestId();
        $result = $client->checkout($invoiceToken, $paymentMethod, $requestId);
        $payment = $result['data'];
        
        echo "<br>Payment Information:<br>";
        echo "  Method: {$payment['payment_method']}<br>";
        echo "  Amount: \${$payment['amount']}<br>";
        echo "  Fee: \${$payment['processingFee']}<br>";
        echo "  Receiver: {$payment['receiver_name']}<br>";
        echo "  Remark: {$payment['remark_code']}<br>";
        echo '<img src="' . $payment['qrcode_base64'] . '">';
        
        return $payment;
    } catch (Exception $e) {
        echo "Error: {$e->getMessage()}<br>";
        return null;
    }
}

function completePaymentFlow($invoiceToken) {
    echo "=== S-PayWay Payment Flow ===<br><br>";
    
    echo "Step 1: Getting payment methods...<br>";
    $methods = getPaymentMethods();
    if (empty($methods)) {
        echo "No payment methods available!<br>";
        return;
    }
    
    echo "<br>Step 2: Checking invoice status...<br>";
    $invoice = checkInvoiceStatus($invoiceToken);
    if (!$invoice) {
        echo "Invoice not found!<br>";
        return;
    }
    
    if ($invoice['status'] === 'Paid') {
        echo "<br>✓ Invoice already paid!<br>";
        return;
    }
    
    echo "<br>Step 3: Creating checkout...<br>";
    $selectedMethod = 'binance_c2c_usdt';
    $payment = createCheckout($invoiceToken, $selectedMethod);
    
    if ($payment) {
        echo "<br>✓ Payment session created successfully!<br>";
    }
}

echo "╔═════════════════╗<br>";
echo "║ S-PayWay Client - Examples ║<br>";
echo "╚═════════════════╝<br><br>";

// Run example
$invoiceToken = 'fc22ccf6-2ed2-412a-80ff-d0fbb5f1684d'; // Replace with your own invoice token. Invoice tokens from other merchants are not supported.
completePaymentFlow($invoiceToken);
