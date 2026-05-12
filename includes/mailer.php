<?php
require_once __DIR__ . '/config.php';

function smtp_send_mail(string $to, string $subject, string $htmlBody, string $textBody = ''): bool {
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $host = MAIL_HOST;
    $port = (int)MAIL_PORT;
    $transport = (MAIL_SECURITY === 'ssl') ? 'ssl://' : '';

    $errno = 0;
    $errstr = '';
    $fp = @stream_socket_client($transport . $host . ':' . $port, $errno, $errstr, 20);
    if (!$fp) {
        error_log('[MAIL] Connect failed: ' . $errstr);
        return false;
    }

    stream_set_timeout($fp, 20);

    $expect = static function($conn, array $codes): bool {
        $response = '';
        while (($line = fgets($conn, 515)) !== false) {
            $response .= $line;
            if (strlen($line) < 4 || $line[3] !== '-') {
                break;
            }
        }
        if ($response === '') return false;
        $code = (int)substr($response, 0, 3);
        return in_array($code, $codes, true);
    };

    $send = static function($conn, string $cmd): void {
        fwrite($conn, $cmd . "\r\n");
    };

    if (!$expect($fp, [220])) { fclose($fp); return false; }

    $send($fp, 'EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
    if (!$expect($fp, [250])) { fclose($fp); return false; }

    $send($fp, 'AUTH LOGIN');
    if (!$expect($fp, [334])) { fclose($fp); return false; }

    $send($fp, base64_encode(MAIL_USERNAME));
    if (!$expect($fp, [334])) { fclose($fp); return false; }

    $send($fp, base64_encode(MAIL_PASSWORD));
    if (!$expect($fp, [235])) { fclose($fp); return false; }

    $send($fp, 'MAIL FROM:<' . MAIL_FROM_EMAIL . '>');
    if (!$expect($fp, [250])) { fclose($fp); return false; }

    $send($fp, 'RCPT TO:<' . $to . '>');
    if (!$expect($fp, [250, 251])) { fclose($fp); return false; }

    $send($fp, 'DATA');
    if (!$expect($fp, [354])) { fclose($fp); return false; }

    $boundary = 'b_' . bin2hex(random_bytes(8));
    $safeSubject = function_exists('mb_encode_mimeheader')
        ? mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n")
        : $subject;

    $headers = [];
    $headers[] = 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_EMAIL . '>';
    $headers[] = 'To: <' . $to . '>';
    $headers[] = 'Subject: ' . $safeSubject;
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

    $plain = $textBody !== '' ? $textBody : strip_tags($htmlBody);

    $body = [];
    $body[] = '--' . $boundary;
    $body[] = 'Content-Type: text/plain; charset=UTF-8';
    $body[] = 'Content-Transfer-Encoding: 8bit';
    $body[] = '';
    $body[] = $plain;
    $body[] = '';
    $body[] = '--' . $boundary;
    $body[] = 'Content-Type: text/html; charset=UTF-8';
    $body[] = 'Content-Transfer-Encoding: 8bit';
    $body[] = '';
    $body[] = $htmlBody;
    $body[] = '';
    $body[] = '--' . $boundary . '--';

    $data = implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $body);
    $data = preg_replace('/(?m)^\./', '..', $data); // dot-stuffing

    fwrite($fp, $data . "\r\n.\r\n");
    if (!$expect($fp, [250])) { fclose($fp); return false; }

    $send($fp, 'QUIT');
    $expect($fp, [221]);
    fclose($fp);
    return true;
}
