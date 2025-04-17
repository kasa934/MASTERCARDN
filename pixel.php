<?php
function getClientIPs() {
    $externalIP = 'غير متوفر';
    $internalIP = $_SERVER['REMOTE_ADDR'] ?? 'غير معروف';

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // قد يحتوي على عدة IPs: نأخذ الأول (عادة هو الخارجي)
        $forwardedIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        foreach ($forwardedIps as $ip) {
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                $externalIP = $ip;
                break;
            }
        }
    } else {
        $externalIP = $internalIP; // إذا لا يوجد Proxy
    }

    return [$externalIP, $internalIP];
}

list($externalIP, $internalIP) = getClientIPs();
$agent = $_SERVER['HTTP_USER_AGENT'] ?? 'غير معروف';
$time = date("Y-m-d H:i:s");

$log = <<<EOL
فتح الرسالة:
الـ IP الخارجي (Forwarded): $externalIP
الـ IP الداخلي (REMOTE_ADDR): $internalIP
User Agent: $agent
الوقت: $time

----------------------------

EOL;

// حفظ في ملف
file_put_contents("opens.txt", $log, FILE_APPEND);

// إرسال صورة 1x1 (pixel)
header('Content-Type: image/gif');
readfile('pixel.gif');
exit;
?>

