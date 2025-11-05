<?php
/**
 * S-Payway Payment Gateway Client
 * 
 * @author S-SERVER Pte. Lte.
 * @version 3.0.0
 */

class S_PayWayClient {
    private string $accessToken;
    private int $timeout = 30;
    private array $lastResponse = [];
    public function __construct(string $accessToken) {
        $this->apiUrl = ('https://api.s-payway.com/v3/checkout/');
        $this->accessToken = $accessToken;
    }
    public function getPaymentMethods(?string $requestId = null): array {
        return $this->request('GET', 'payment_method', [], $requestId);
    }
    public function getInvoiceStatus(string $invoiceToken, ?string $requestId = null): array {
        $params = ['invoice_token' => $invoiceToken];
        return $this->request('GET', 'status', $params, $requestId);
    }
    public function checkout(string $invoiceToken, string $paymentMethod, ?string $requestId = null): array {
        $params = [
            'invoice_token' => $invoiceToken,
            'payment_method' => $paymentMethod
        ];
        return $this->request('GET', 'checkout', $params, $requestId);
    }
    private function request(string $method, string $action, array $params = [], ?string $requestId = null): array {
        $url = $this->apiUrl . '?action=' . $action;
        
        if ($method === 'GET' && !empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        
        $headers = [
            'S-PAYWAY-ACCESS-TOKEN: ' . $this->accessToken,
            'Content-Type: application/json',
            'User-Agent: S-PayWay-Client/3.0'
        ];
        
        if ($requestId) {
            $headers[] = 'S-PAYWAY-REQUEST-ID: ' . $requestId;
        }
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response: ' . json_last_error_msg());
        }
        
        $this->lastResponse = $data;
        
        if ($httpCode !== 200) {
            $message = $data['message'] ?? 'Unknown error';
            throw new Exception("API Error ({$httpCode}): {$message}", $httpCode);
        }
        
        if (!isset($data['success']) || $data['success'] !== true) {
            $message = $data['message'] ?? 'Request failed';
            throw new Exception($message);
        }
        
        return $data;
    }
    public function getLastResponse(): array {
        return $this->lastResponse;
    }
    public static function generateRequestId(): string {
        return bin2hex(random_bytes(16)) . '_' . time();
    }
    public function setTimeout(int $seconds): void {
        $this->timeout = $seconds;
    }
}