<?php
// Simple Frontend that calls the Backend API inside the Docker network
function call_backend() {
    $url = "http://backend/api.php";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $err = curl_error($ch);
        curl_close($ch);
        return json_encode(["error" => "Could not reach backend", "detail" => $err]);
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($status >= 200 && $status < 300) {
        return $response;
    } else {
        return json_encode(["error" => "Backend returned HTTP $status"]);
    }
}
$backend = call_backend();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>PHP Frontend</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .card { border: 1px solid #ddd; border-radius: 12px; padding: 20px; max-width: 700px; }
        code { background: #f6f8fa; padding: 2px 6px; border-radius: 6px; }
        .muted { color: #666; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Frontend Running ✅</h1>
        <p>This is the PHP/Apache frontend container.</p>
        <h3>Backend API response:</h3>
        <pre><code><?php echo htmlspecialchars($backend, ENT_QUOTES, 'UTF-8'); ?></code></pre>
        <p class="muted">Direct link to backend (from your browser): replace <code>VM_IP</code> with your server IP →
           <a href="http://VM_IP:8082/api.php" target="_blank">http://VM_IP:8082/api.php</a></p>
    </div>
</body>
</html>
