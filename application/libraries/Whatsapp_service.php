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
     * Send Real WhatsApp Message via Gateway Mandiri
     *
     * @param string $targetPhone Destination phone number
     * @param string $messageBody Content of the WhatsApp message
     * @return array ['success' => bool, 'message' => string, 'response' => mixed]
    /**
     * Send WhatsApp Message via Gateway Mandiri (Local Node.js Baileys)
     * 
     * @param string $targetPhone Nomor HP atau ID Grup
     * @param string $messageBody Isi pesan
     * @param string $recipientName Nama penerima (opsional, untuk personalisasi)
     * @return array
     */
    public function send_message($targetPhone, $messageBody, $recipientName = null)
    {
        $apiUrl = $this->CI->Setting_model->get_val('wa_api_url', 'http://localhost:3000/send-message');

        // Handle group ID (e.g. 1203630...@g.us)
        $targetPhone = trim($targetPhone);
        if (strpos($targetPhone, '@g.us') !== false) {
            $phone = $targetPhone;
        } else {
            // Normalize phone number
            $phone = preg_replace('/[^0-9]/', '', $targetPhone);
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            }
        }

        if (empty($phone) || empty($messageBody)) {
            return array('success' => false, 'message' => 'Nomor tujuan atau isi pesan kosong.');
        }

        return $this->send_via_local_gateway($phone, $messageBody, $apiUrl, $recipientName);
    }

    /**
     * Send Bulk WhatsApp Messages via Gateway Mandiri Antrian Aman Anti-Ban
     * 
     * @param array $messagesArray Array of items: [['target' => '08123...', 'message' => '...', 'recipientName' => '...'], ...]
     * @return array
     */
    public function send_bulk($messagesArray)
    {
        if (empty($messagesArray)) {
            return array('success' => false, 'message' => 'Tidak ada pesan untuk dikirim.');
        }

        $url = 'http://localhost:3000/send-bulk';

        $payload = json_encode(array(
            'items' => $messagesArray
        ));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return array(
                'success' => false,
                'message' => 'Gateway Mandiri belum aktif di http://localhost:3000 (Error: ' . $curlError . '). Pastikan server node.js sudah dijalankan.',
                'response' => null
            );
        }

        $result = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300 && !empty($result['success'])) {
            return $result;
        }

        return array(
            'success' => false,
            'message' => !empty($result['message']) ? $result['message'] : "Gagal memproses ke antrian (HTTP {$httpCode}).",
            'response' => $result
        );
    }

    /**
     * Send via Local Node.js Gateway
     */
    public function send_via_local_gateway($phone, $message, $url = 'http://localhost:3000/send-message', $recipientName = null)
    {
        if (empty($url)) {
            $url = 'http://localhost:3000/send-message';
        }

        $payload = json_encode(array(
            'number'        => $phone,
            'message'       => $message,
            'recipientName' => $recipientName
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
                'message' => 'Gateway Mandiri belum aktif di http://localhost:3000 (Error: ' . $curlError . '). Pastikan service Gateway WhatsApp sudah dijalankan.',
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
            'message'  => isset($result['message']) ? $result['message'] : 'Gagal mengirim pesan melalui Gateway Mandiri (HTTP ' . $httpCode . ')',
            'response' => $result
        );
    }

    /**
     * Send to WhatsApp Group via Gateway Mandiri
     */
    public function send_to_group($groupId, $message)
    {
        return $this->send_message($groupId, $message);
    }

    /**
     * Check Gateway Mandiri Status
     */
    public function check_local_status()
    {
        $ch = curl_init('http://localhost:3000/status');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlError) {
            return array(
                'success'   => false,
                'connected' => false,
                'message'   => 'Service Gateway Mandiri Offline di Port 3000.'
            );
        }

        $result = json_decode($response, true);
        if ($httpCode === 200 && is_array($result)) {
            return $result;
        }

        return array(
            'success'   => false,
            'connected' => false,
            'message'   => 'Respons Gateway tidak valid.'
        );
    }
}
