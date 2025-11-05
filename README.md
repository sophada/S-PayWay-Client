# S-PayWay Client - API Access

A lightweight PHP client library for integrating S-PayWay payment gateway into your application. Handle payment processing with just a few lines of code.

## Overview

S-PayWay Client provides a simple interface to interact with the S-PayWay payment API. The library handles authentication, request formatting, error handling, and response parsing automatically.

## Requirements

- PHP 7.4 or higher
- cURL extension
- JSON extension

## Installation

Download and include the client library in your project:

```php
require_once './src/S_PayWayClient.php';
```

Or install via Composer:

```bash
composer require S-PayWay/php-client
```

## Quick Start

```php
require_once './src/S_PayWayClient.php';

// Initialize client with your access token
$client = new S_PayWayClient('SAT_your-access-token-here');

// Get available payment methods
$methods = $client->getPaymentMethods();

// Check invoice status
$status = $client->getInvoiceStatus('invoice-token-here');

// Create checkout session
$payment = $client->checkout('invoice-token-here', 'binance_c2c_usdt');
```

## Configuration

### Getting Your Access Token

Access tokens are provided in your merchant dashboard. The token format looks like:

```
SAT_7dc7dda106a0c6bf9a83e01e-ccbc726e72d11380d96eb591-31d93122a7591760110814
```

Store this securely in your environment configuration:

```php
// Using environment variables (recommended)
$accessToken = getenv('SPAYWAY_ACCESS_TOKEN');

// Or from config file
$accessToken = include 'config.php';

$client = new S_PayWayClient($accessToken);
```

### Client Options

```php
// Set custom timeout (default: 30 seconds)
$client->setTimeout(60);
```

## API Methods

### 1. Get Payment Methods

Retrieve all available payment methods for your merchant account.

```php
$response = $client->getPaymentMethods($requestId = null);
```

**Parameters:**
- `$requestId` (string, optional): Idempotency key for the request

**Sample Response:**
```php
[
    'success' => true,
    'message' => 'okay',
    'code' => 200,
    'data' => [
        'payment_method' => [
            'aba_bank_cambodia' => 1,
            'acleda_bank_cambodia' => 1,
            'bakong_khqr_usd' => 1,
            'binance_c2c_usdt' => 1,
            'binance_c2c_usdc' => 1,
            'usdt_trc20_offchain_binance' => 1,
            'usdt_bep20_offchain_binance' => 1,
            'usdt_erc20_offchain_binance' => 0,
            'usdc_bep20_offchain_binance' => 1,
            'usdc_erc20_offchain_binance' => 0,
            'usdt_trc20_onchain' => 1,
            'usdt_bep20_onchain' => 1,
            'usdt_polygon_onchain' => 0,
            'usdc_bep20_onchain' => 1,
            'usdc_polygon_onchain' => 0,
            'usd1_bep20_onchain' => 1,
            'tusd_bep20_onchain' => 0,
            'fdusd_bep20_onchain' => 1,
            'pix_brazil' => 0,
            'geniebiz' => 0,
            'vietqr' => 1
        ]
    ],
    'timestamp' => 1699564800
]
```

**Usage Example:**
```php
try {
    $result = $client->getPaymentMethods();
    
    if ($result['success']) {
        $methods = $result['data']['payment_method'];
        
        echo "Available Payment Methods:\n";
        foreach ($methods as $method => $status) {
            if ($status === 1) {
                echo "  - $method\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### 2. Check Invoice Status

Retrieve the current payment status of an invoice.

```php
$response = $client->getInvoiceStatus($invoiceToken, $requestId = null);
```

**Parameters:**
- `$invoiceToken` (string, required): The invoice identifier
- `$requestId` (string, optional): Idempotency key for the request

**Sample Response:**
```php
[
    'success' => true,
    'message' => 'Okay',
    'code' => 200,
    'data' => [
        'id' => 12345,
        'invoice_token' => 'fc22ccf6-2ed2-412a-80ff-d0fbb5f1684d',
        'qty' => 1,
        'price' => '100.00',
        'subtotal' => '100.00',
        'total' => '100.00',
        'goods' => [
            'name' => 'Premium Subscription',
            'description' => '1 month access to all premium features',
            'reference_id' => 'PROD-12345'
        ],
        'customer' => [
            'full_name' => 'John Doe',
            'username' => 'johndoe123',
            'email' => 'john.doe@example.com'
        ],
        'status' => 'Unpaid'  // Possible values: 'Paid', 'Unpaid'
    ],
    'timestamp' => 1699564800
]
```

**Usage Example:**
```php
try {
    $invoiceToken = 'fc22ccf6-2ed2-412a-80ff-d0fbb5f1684d';
    $result = $client->getInvoiceStatus($invoiceToken);
    
    if ($result['success']) {
        $invoice = $result['data'];
        
        echo "Invoice #{$invoice['id']}\n";
        echo "Status: {$invoice['status']}\n";
        echo "Amount: \${$invoice['total']}\n";
        echo "Customer: {$invoice['customer']['full_name']}\n";
        echo "Product: {$invoice['goods']['name']}\n";
        
        if ($invoice['status'] === 'Paid') {
            echo "\nPayment completed successfully!\n";
            // Process order fulfillment
        } else {
            echo "\nPayment is still pending.\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### 3. Create Checkout Session

Initialize a payment session for a specific payment method.

```php
$response = $client->checkout($invoiceToken, $paymentMethod, $requestId = null);
```

**Parameters:**
- `$invoiceToken` (string, required): The invoice to process
- `$paymentMethod` (string, required): Selected payment method identifier
- `$requestId` (string, optional): Idempotency key for the request

**Available Payment Methods:**
- `aba_bank_cambodia`
- `acleda_bank_cambodia`
- `bakong_khqr_usd`
- `binance_c2c_usdt`
- `binance_c2c_usdc`
- `usdt_trc20_offchain_binance`
- `usdt_bep20_offchain_binance`
- `usdt_erc20_offchain_binance`
- `usdc_bep20_offchain_binance`
- `usdc_erc20_offchain_binance`
- `usdt_trc20_onchain`
- `usdt_bep20_onchain`
- `usdt_polygon_onchain`
- `usdc_bep20_onchain`
- `usdc_polygon_onchain`
- `usd1_bep20_onchain`
- `tusd_bep20_onchain`
- `fdusd_bep20_onchain`
- `pix_brazil`
- `geniebiz`
- `vietqr`

**Sample Response (Binance C2C USDT):**
```php
[
    'success' => true,
    'message' => 'okay',
    'code' => 200,
    'data' => [
        'payment_method' => 'binance_c2c_usdt',
        'amount' => '100.50',
        'processingFee' => '0.50',
        'receiver_name' => 'Merchant Trading Co.',
        'remark_code' => 'S12345',
        'qrcode_data' => 'https://app.binance.com/qr/dplk8a7d6f8b8c9e1f2g3h4i5j6k7l8m',
        'qrcode_img' => 'https://api.qrserver.com/v1/create-qr-code/?data=https://app.binance.com/qr/dplk8a7d6f8b8c9e1f2g3h4i5j6k7l8m',
        'qrcode_base64' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNDAiIGhlaWdodD0iMjQwIj4uLi48L3N2Zz4='
    ],
    'timestamp' => 1699564800
]
```

**Sample Response (Already Paid):**
```php
[
    'success' => true,
    'message' => 'okay',
    'code' => 200,
    'data' => [
        'id' => 12345,
        'invoice_token' => 'fc22ccf6-2ed2-412a-80ff-d0fbb5f1684d',
        'status' => 'Paid'
    ],
    'timestamp' => 1699564800
]
```

**Usage Example:**
```php
try {
    $invoiceToken = 'fc22ccf6-2ed2-412a-80ff-d0fbb5f1684d';
    $paymentMethod = 'binance_c2c_usdt';
    
    // Generate unique request ID for idempotency
    $requestId = S_PayWayClient::generateRequestId();
    
    $result = $client->checkout($invoiceToken, $paymentMethod, $requestId);
    
    if ($result['success']) {
        $payment = $result['data'];
        
        // Check if already paid
        if (isset($payment['status']) && $payment['status'] === 'Paid') {
            echo "This invoice has already been paid!\n";
            exit;
        }
        
        // Display payment information
        echo "Payment Method: {$payment['payment_method']}\n";
        echo "Amount to Pay: \${$payment['amount']}\n";
        echo "Processing Fee: \${$payment['processingFee']}\n";
        echo "Receiver: {$payment['receiver_name']}\n";
        echo "Remark Code: {$payment['remark_code']}\n\n";
        
        echo "Scan this QR code in Binance:\n";
        echo "<img src=\"{$payment['qrcode_base64']}\" alt=\"Payment QR Code\">\n\n";
        
        echo "Or open this link:\n";
        echo $payment['qrcode_data'] . "\n\n";
        
        echo "IMPORTANT: Include remark code '{$payment['remark_code']}' in your transfer!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    
    // Handle specific errors
    if ($e->getCode() === 403) {
        echo "Payment method may be unavailable or invoice expired\n";
    } elseif ($e->getCode() === 404) {
        echo "Invoice not found\n";
    } elseif ($e->getCode() === 429) {
        echo "Too many requests. Please try again later.\n";
    }
}
```

## Complete Payment Flow Example

```php
<?php
require_once './src/S_PayWayClient.php';

// Initialize client
$accessToken = 'SAT_your-access-token-here';
$client = new S_PayWayClient($accessToken);

function processPayment($invoiceToken) {
    global $client;
    
    try {
        // Step 1: Get available payment methods
        echo "Fetching available payment methods...\n";
        $methodsResponse = $client->getPaymentMethods();
        $availableMethods = $methodsResponse['data']['payment_method'];
        
        // Filter only enabled methods
        $enabledMethods = array_filter($availableMethods, function($status) {
            return $status === 1;
        });
        
        if (empty($enabledMethods)) {
            throw new Exception("No payment methods available");
        }
        
        echo "Available methods: " . implode(', ', array_keys($enabledMethods)) . "\n\n";
        
        // Step 2: Check invoice status
        echo "Checking invoice status...\n";
        $statusResponse = $client->getInvoiceStatus($invoiceToken);
        $invoice = $statusResponse['data'];
        
        echo "Invoice: {$invoice['goods']['name']}\n";
        echo "Amount: \${$invoice['total']}\n";
        echo "Status: {$invoice['status']}\n\n";
        
        if ($invoice['status'] === 'Paid') {
            echo "Invoice already paid. No action needed.\n";
            return;
        }
        
        // Step 3: Select payment method (for this example, we'll use binance_c2c_usdt)
        $selectedMethod = 'binance_c2c_usdt';
        
        if (!isset($enabledMethods[$selectedMethod])) {
            throw new Exception("Selected payment method is not available");
        }
        
        // Step 4: Create checkout session
        echo "Creating checkout session for {$selectedMethod}...\n";
        $requestId = S_PayWayClient::generateRequestId();
        $checkoutResponse = $client->checkout($invoiceToken, $selectedMethod, $requestId);
        $payment = $checkoutResponse['data'];
        
        // Display payment instructions
        echo "\n=== PAYMENT INSTRUCTIONS ===\n";
        echo "Amount: \${$payment['amount']}\n";
        echo "Fee: \${$payment['processingFee']}\n";
        echo "Receiver: {$payment['receiver_name']}\n";
        echo "Remark Code: {$payment['remark_code']}\n\n";
        
        echo "QR Code URL: {$payment['qrcode_img']}\n";
        echo "Payment Link: {$payment['qrcode_data']}\n\n";
        
        echo "Please complete the payment and include the remark code.\n";
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo "Error Code: " . $e->getCode() . "\n";
    }
}

// Example usage
$invoiceToken = 'fc22ccf6-2ed2-412a-80ff-d0fbb5f1684d';
processPayment($invoiceToken);
```

## Error Handling

The client throws exceptions for all error conditions. Always wrap API calls in try-catch blocks.

**Common Error Codes:**

| Code | Description | Suggested Action |
|------|-------------|------------------|
| 400 | Bad Request | Check parameters format and values |
| 401 | Authentication Failed | Verify your access token |
| 403 | Access Denied | Check IP whitelist or payment method availability |
| 404 | Not Found | Verify invoice token exists |
| 413 | Request Too Large | Reduce request size |
| 429 | Rate Limit Exceeded | Wait and retry with exponential backoff |
| 500 | Internal Server Error | Contact support if persists |

**Error Response Format:**
```php
[
    'success' => false,
    'message' => 'Rate limit exceeded',
    'code' => 429,
    'data' => [],
    'retry_after' => 45,  // Seconds to wait (only for 429 errors)
    'timestamp' => 1699564800
]
```

**Handling Errors Example:**
```php
try {
    $result = $client->checkout($invoiceToken, $paymentMethod);
} catch (Exception $e) {
    $code = $e->getCode();
    $message = $e->getMessage();
    
    switch ($code) {
        case 400:
            echo "Invalid request: $message\n";
            break;
        case 401:
            echo "Authentication failed. Check your access token.\n";
            break;
        case 403:
            echo "Access denied: $message\n";
            break;
        case 404:
            echo "Invoice not found. Verify the invoice token.\n";
            break;
        case 429:
            $response = $client->getLastResponse();
            $retryAfter = $response['retry_after'] ?? 60;
            echo "Rate limit exceeded. Retry after {$retryAfter} seconds.\n";
            break;
        case 500:
            echo "Server error. Please try again later.\n";
            break;
        default:
            echo "Unexpected error: $message\n";
    }
}
```

## Idempotency

Use request IDs to ensure operations are idempotent. This prevents duplicate payments if requests are retried.

```php
// Generate unique request ID
$requestId = S_PayWayClient::generateRequestId();

// Same request ID will return cached response within 24 hours
$result1 = $client->checkout($invoiceToken, $paymentMethod, $requestId);
$result2 = $client->checkout($invoiceToken, $paymentMethod, $requestId);

// $result1 and $result2 will be identical (from cache)
```

**Best Practices:**
- Generate unique request IDs for each new operation
- Store request IDs with transactions for troubleshooting
- Reuse request IDs when retrying failed requests
- Request IDs must be 16-128 characters, alphanumeric with dashes/underscores

## Rate Limiting

The API enforces rate limits based on your merchant plan:

| Plan | Limit |
|------|-------|
| Standard | 30 requests/minute |
| Basic | 60 requests/minute |
| Premium | 120 requests/minute |
| Enterprise | 6000 requests/minute |

Global limit: 300 requests/minute per IP

**Handling Rate Limits:**
```php
function makeRequestWithRetry($callable, $maxRetries = 3) {
    $attempt = 0;
    
    while ($attempt < $maxRetries) {
        try {
            return $callable();
        } catch (Exception $e) {
            if ($e->getCode() === 429) {
                $attempt++;
                $waitTime = pow(2, $attempt); // Exponential backoff
                echo "Rate limited. Waiting {$waitTime} seconds...\n";
                sleep($waitTime);
            } else {
                throw $e;
            }
        }
    }
    
    throw new Exception("Max retries exceeded");
}

// Usage
$result = makeRequestWithRetry(function() use ($client, $invoiceToken) {
    return $client->getInvoiceStatus($invoiceToken);
});
```

## Security Best Practices

1. **Store Access Tokens Securely**
```php
// Use environment variables
$accessToken = getenv('SPAYWAY_ACCESS_TOKEN');

// Or encrypted configuration
$accessToken = decrypt(file_get_contents('config.encrypted'));
```

2. **Validate Responses**
```php
$result = $client->getPaymentMethods();

if (!isset($result['success']) || $result['success'] !== true) {
    throw new Exception("Invalid API response");
}
```

3. **Use HTTPS Only**
```php
// The client enforces HTTPS automatically
// Never disable SSL verification in production
```

4. **IP Whitelist**

Add your server IP to the whitelist in your merchant dashboard to prevent unauthorized access.

5. **Log API Interactions**
```php
try {
    $result = $client->checkout($invoiceToken, $paymentMethod, $requestId);
    error_log("Checkout success: " . json_encode($result));
} catch (Exception $e) {
    error_log("Checkout error: " . $e->getMessage());
}
```

## Troubleshooting

### Connection Timeout

```php
// Increase timeout for slow networks
$client->setTimeout(60);
```

### SSL Certificate Issues

Make sure your PHP installation has up-to-date CA certificates:

```bash
# Update CA certificates (Ubuntu/Debian)
sudo apt-get update && sudo apt-get install ca-certificates

# Update CA certificates (CentOS/RHEL)
sudo yum update ca-certificates
```

### Invoice Not Found

Verify the invoice token is correct and belongs to your merchant account.

### Payment Method Unavailable

Check that the payment method is enabled in your merchant settings and supported for the invoice amount.

## Support

For technical support or merchant account inquiries:
- Documentation: https://docs.s-payway.com
- Email: contact@sophada.com
- Business Hours: Monday-Friday, 9:00-18:00 GMT+7

## License

Proprietary - All rights reserved by S-SERVER Pte. Ltd.

## Changelog

### Version 3.0.0
- Initial release
- Support for 20+ payment methods (and many more upcoming)
- Idempotency support
- Comprehensive error handling
- Rate limiting integration

---

**Version:** 3.0.0  
**Last Updated:** November 2025
