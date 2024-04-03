<?php
$json = json_encode(array("code" => 444, "msg" => "互联网不是法外之地！"));
die($json);
// 获取请求的Origin
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// 规定的Origin值
$allowedOrigin = get_http_type() . $_SERVER['SERVER_NAME'];

// 判断Origin是否是规定的值
if ($origin != $allowedOrigin) {
    $json = json_encode(array("code" => 1, "msg" => "Unknown error"));
    die($json);
}

// 检查是否上传文件
if (isset($_FILES['files'])) {
    $uploadedFiles = $_FILES['files'];
    $uploadResults = array();

    // 处理每个上传的文件
    foreach ($uploadedFiles['error'] as $key => $error) {
        // 检查上传是否成功
        if ($error == UPLOAD_ERR_OK) {
            $tmpName = $uploadedFiles['tmp_name'][$key];
            $name = basename($uploadedFiles['name'][$key]);

            // 构建 cURL 请求
            $ch = curl_init();
            $uploadUrl = 'https://telegra.ph/upload';

            // 设置 cURL 选项
            curl_setopt($ch, CURLOPT_URL, $uploadUrl); // 设置 URL
            curl_setopt($ch, CURLOPT_POST, true); // 使用 POST 请求方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('file' => new CURLFile($tmpName, $uploadedFiles['type'][$key], $name))); // 设置 POST 数据
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 将响应保存到变量中，而不是直接输出
            curl_setopt($ch, CURLOPT_HEADER, false); // 不包含响应头

            // 执行 cURL 请求
            $response = curl_exec($ch);

            // 检查是否有错误发生
            if ($response === false) {
                // 返回错误消息
                $uploadResults[] = array("code" => 1, "msg" => "Upload failed: " . curl_error($ch));
            } else {
                // 解析返回的 JSON 格式数据
                $parsedResponse = json_decode($response, true);

                // 检查是否成功上传
                if (isset($parsedResponse[0]['src'])) {
                    // 拼接图片 URL
                    $fileName = basename($parsedResponse[0]['src']);
                    $imgSrc = get_http_type() . $_SERVER['SERVER_NAME'] . '/src.php?src=' . $fileName;
                    writeImgUrlToLog($fileName);
                    $uploadResults[] = array("code" => 0, "msg" => $imgSrc);
                } else {
                    // 返回错误消息
                    $uploadResults[] = array("code" => 1, "msg" => "Upload failed: Unknown error");
                }
            }

            // 关闭 cURL 资源
            curl_close($ch);
        }
    }

    // 返回上传结果
    header('Content-Type: application/json');
    echo json_encode($uploadResults);
}

//自动获取协议头
function get_http_type()
{
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    return $http_type;
}
//保存日志
function writeImgUrlToLog($fileName) {
    // Get client's IP address
    $ip = $_SERVER['REMOTE_ADDR'];

    // Construct log entry
    $logEntry = $fileName . '|' . $ip . '|' . date('Y-m-d H:i:s') . PHP_EOL;

    $logFile = __DIR__ . '/log/upimg/url.log';

    // Create log file if it doesn't exist
    if (!file_exists($logFile)) {
        if (!file_exists(__DIR__ . '/log/upimg')) {
            mkdir(__DIR__ . '/log/upimg', 0755, true); // Create log directory if it doesn't exist
        }
        touch($logFile); // Create log file
        chmod($logFile, 0644);
    }

    // Check if log file exceeds 20MB
    if (file_exists($logFile) && filesize($logFile) > (20 * 1024 * 1024)) {
        // Rename the file with current date-time
        $newFilename = __DIR__ . '/log/upimg/url-' . date('YmdHis') . '.log';
        rename($logFile, $newFilename);

        // Create a new log file
        touch($logFile);
        chmod($logFile, 0644);
    }

    // Write log entry to log file
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
?>