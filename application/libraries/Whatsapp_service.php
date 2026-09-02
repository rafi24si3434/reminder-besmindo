<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_service
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Setting_model');
    }

    /**
     * Send Real WhatsApp Message (single)
     *
     * @param string $targetPhone Destination phone number
     * @param string $messageBody Content of the WhatsApp message
     * @param int    $delay       Delay in seconds (for Fonnte anti-ban)
     * @return array ['success' => bool, 'message' => string, 'response' => mixed]
     */
    public function send_message($targetPhone, $messageBody, $delay = 2)
    {
        $provider    = $this->CI->Setting_model->get_val('wa_gateway_provider', 'FONNTE');
        $apiUrl      = $this->CI->Setting_model->get_val('wa_api_url', 'https://api.fonnte.com/send');
        $apiToken    = $this->CI->Setting_model->get_val('wa_api_token', '');

        // Normalize phone number
        $phone = preg_replace('/[^0-9]/', '', $targetPhone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        if (empty($phone) || empty($messageBody)) {
            return array('success' => false, 'message' => 'Nomor tujuan atau isi pesan kosong.');
        }

        switch (strtoupper($provider)) {
            case 'FONNTE':
                return $this->send_via_fonnte($phone, $messageBody, $apiToken, $apiUrl, $delay);

            case 'WABLAS':
                return $this->send_via_wablas($phone, $messageBody, $apiToken, $apiUrl);

            case 'LOCAL_NODE':
            default:
                return $this->send_via_local_gateway($phone, $messageBody, $apiUrl);
        }
    }

    /**
     * Send Bulk WhatsApp Messages via Fonnte / Gateway
     * 
     * @param array $messagesArray Array of items: [['target' => '08123...', 'message' => '...', 'delay' => '2'], ...]
     * @return array
     */
    public function send_bulk($messagesArray)
    {
        $provider = strtoupper($this->CI->Setting_model->get_val('wa_gateway_provider', 'FONNTE'));
        $apiUrl   = $this->CI->Setting_model->get_val('wa_api_url', 'https://api.fonnte.com/send');
        $apiToken = $this->CI->Setting_model->get_val('wa_api_token', '');

        if (empty($messagesArray)) {
            return array('success' => false, 'message' => 'Tidak ada pesan untuk dikirim.');
        }

        if ($provider === 'FONNTE') {
            return $this->send_bulk_via_fonnte($messagesArray, $apiToken, $apiUrl);
        }

        // Fallback for LOCAL_NODE / other providers
        $successCount = 0;
        foreach ($messagesArray as $item) {
            $res = $this->send_message($item['target'], $item['message']);
            if (!empty($res['success'])) {
                $successCount++;
            }
        }

        return array(
            'success' => ($successCount > 0),
            'message' => "{$successCount}/" . count($messagesArray) . " pesan berhasil diproses ke gateway.",
            'queued'  => $successCount
        );
    }

    /**
     * Send via Fonnte Cloud API (Single Message)
     */
    public function send_via_fonnte($phone, $message, $token, $url = 'https://api.fonnte.com/send', $delay = 2)
    {
        if (empty($url)) {
            $url = 'https://api.fonnte.com/send';
        }

        if (empty($token)) {
            return array(
                'success' => false,
                'message' => 'API Token Fonnte belum diatur. Silakan masukkan Token Fonnte Anda di menu Layanan WhatsApp Gateway.'
            );
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => array(
                'target'      => $phone,
                'message'     => $message,
                'countryCode' => '62',
                'delay'       => (string)$delay
            ),
            CURLOPT_HTTPHEADER     => array(
                'Authorization: ' . trim($token)
            ),
        ));

        $response  = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($curlError) {
            return array(
                'success' => false,
                'message' => 'Fonnte cURL Error: ' . $curlError
            );
        }

        $result = json_decode($response, true);
        if (isset($result['status']) && ($result['status'] === true || $result['status'] === 'true' || $result['status'] === 1)) {
            return array(
                'success'  => true,
                'message'  => 'Pesan WhatsApp berhasil dikirim via Fonnte ke +' . $phone,
                'response' => $result
            );
        }

        $reason = isset($result['reason']) ? $result['reason'] : (isset($result['message']) ? $result['message'] : 'Gagal kirim via Fonnte (HTTP ' . $httpCode . ')');
        return array(
            'success'  => false,
            'message'  => $reason,
            'response' => $result
        );
    }

    /**
     * Send Bulk Batch via Fonnte (using 'data' JSON array with anti-ban delay)
     *
     * Example $batchData:
     * [
     *   {"target": "08123456789", "message": "Pesan 1", "delay": "2"},
     *   {"target": "08987654321", "message": "Pesan 2", "delay": "4"}
     * ]
     */
    public function send_bulk_via_fonnte($batchData, $token, $url = 'https://api.fonnte.com/send')
    {
        if (empty($url)) {
            $url = 'https://api.fonnte.com/send';
        }

        if (empty($token)) {
            return array(
                'success' => false,
                'message' => 'API Token Fonnte belum diatur. Masukkan Token Fonnte di menu Layanan WhatsApp Gateway.'
            );
        }

        // Format each target properly
        $formattedBatch = array();
        $delayCounter = 2; // Progressive delay: 2s, 5s, 8s, 11s...
        foreach ($batchData as $item) {
            $target = preg_replace('/[^0-9]/', '', $item['target']);
            if (substr($target, 0, 1) === '0') {
                $target = '62' . substr($target, 1);
            }

            $delay = isset($item['delay']) ? (string)$item['delay'] : (string)$delayCounter;

            $formattedBatch[] = array(
                'target'  => $target,
                'message' => $item['message'],
                'delay'   => $delay
            );

            $delayCounter += rand(3, 6); // Add 3-6s delay between consecutive messages
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => array(
                'data' => json_encode($formattedBatch)
            ),
            CURLOPT_HTTPHEADER     => array(
                'Authorization: ' . trim($token)
            ),
        ));

        $response  = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($curlError) {
            return array(
                'success' => false,
                'message' => 'Fonnte cURL Error: ' . $curlError
            );
        }

        $result = json_decode($response, true);
        if (isset($result['status']) && ($result['status'] === true || $result['status'] === 'true' || $result['status'] === 1)) {
            return array(
                'success'  => true,
                'message'  => count($formattedBatch) . ' pesan berhasil dijadwalkan dan dikirim via Fonnte dengan jeda anti-ban.',
                'queued'   => count($formattedBatch),
                'response' => $result
            );
        }

        $reason = isset($result['reason']) ? $result['reason'] : (isset($result['message']) ? $result['message'] : 'Gagal mengirim batch Fonnte (HTTP ' . $httpCode . ')');
        return array(
            'success'  => false,
            'message'  => $reason,
            'response' => $result
        );
    }

    /**
     * Check Fonnte Account & Device Status
     */
    public function check_fonnte_status($token = null)
    {
        if (empty($token)) {
            $token = $this->CI->Setting_model->get_val('wa_api_token', '');
        }

        if (empty($token)) {
            return array(
                'success'   => false,
                'connected' => false,
                'message'   => 'Token Fonnte belum diisi.'
            );
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => 'https://api.fonnte.com/device',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_HTTPHEADER     => array(
                'Authorization: ' . trim($token)
            ),
        ));

        $response = curl_exec($curl);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($curlError) {
            return array(
                'success'   => false,
                'connected' => false,
                'message'   => 'Error koneksi ke Fonnte: ' . $curlError
            );
        }

        $result = json_decode($response, true);
        if (isset($result['status']) && $result['status'] === true) {
            $deviceStatus = isset($result['device_status']) ? strtolower($result['device_status']) : '';
            $isConnected = ($deviceStatus === 'connect' || $deviceStatus === 'connected');

            return array(
                'success'      => true,
                'connected'    => $isConnected,
                'deviceStatus' => $result['device_status'] ?? 'Unknown',
                'deviceNumber' => $result['device'] ?? '',
                'name'         => $result['name'] ?? '',
                'expired'      => $result['expired'] ?? '',
                'quota'        => $result['quota'] ?? '-',
                'message'      => $isConnected ? 'Perangkat Fonnte Terhubung & Aktif' : 'Perangkat Fonnte Terputus / Perlu Scan di Fonnte Dashboard',
                'raw'          => $result
            );
        }

        return array(
            'success'   => false,
            'connected' => false,
            'message'   => isset($result['reason']) ? $result['reason'] : 'Token Fonnte tidak valid atau server Fonnte sibuk.'
        );
    }

    /**
     * Send via Local Node.js Gateway
     */
    protected function send_via_local_gateway($phone, $message, $url)
    {
        if (empty($url)) {
            $url = 'http://localhost:3000/send-message';
        }

        $payload = json_encode(array(
            'number'  => $phone,
            'message' => $message
        ));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return array(
                'success' => false,
                'message' => 'Gateway lokal belum aktif di http://localhost:3000 (Error: ' . $curlError . ').',
                'response' => null
            );
        }

        $result = json_decode($response, true);
        if ($httpCode === 200 && isset($result['success']) && $result['success']) {
            return array(
                'success'  => true,
                'message'  => 'Pesan WhatsApp berhasil dikirim ke +' . $phone,
                'response' => $result
            );
        }

        return array(
            'success'  => false,
            'message'  => isset($result['message']) ? $result['message'] : 'Gagal mengirim pesan (HTTP ' . $httpCode . ')',
            'response' => $result
        );
    }

    /**
     * Send via Wablas Cloud API
     */
    protected function send_via_wablas($phone, $message, $token, $url)
    {
        if (empty($url)) {
            $url = 'https://jogja.wablas.com/api/send-message';
        }

        $data = http_build_query(array(
            'phone'   => $phone,
            'message' => $message
        ));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: ' . $token
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return array('success' => false, 'message' => 'Wablas cURL Error: ' . $curlError);
        }

        $result = json_decode($response, true);
        return array('success' => true, 'message' => 'Pesan diproses oleh Wablas.', 'response' => $result);
    }
}
