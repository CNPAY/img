<?php
die("error: Unknown error");
// 获取请求的Origin
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// 规定的Origin值
$allowedOrigin = get_http_type() . $_SERVER['SERVER_NAME'];

// 判断Origin是否是规定的值
if ($origin !== $allowedOrigin) {
    $json = json_encode(array("code" => 1, "msg" => "Unknown error"));
    die($json);
}

// 检查是否上传文件
if (isset($_FILES['file'])) {
    // 检查是否上传成功
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        // 构建 cURL 请求
        $ch = curl_init();
        $uploadUrl = 'https://telegra.ph/upload';

        // 设置 cURL 选项
        curl_setopt($ch, CURLOPT_URL, $uploadUrl); // 设置 URL
        curl_setopt($ch, CURLOPT_POST, true); // 使用 POST 请求方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('file' => new CURLFile($_FILES['file']['tmp_name'], $_FILES['file']['type'], $_FILES['file']['name']))); // 设置 POST 数据
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 将响应保存到变量中，而不是直接输出
        curl_setopt($ch, CURLOPT_HEADER, false); // 不包含响应头

        // 执行 cURL 请求
        $response = curl_exec($ch);

        // 检查是否有错误发生
        if ($response === false) {
            // 返回错误消息
            $json = json_encode(array("code" => 1, "msg" => "Upload failed: " . curl_error($ch)));
            echo $json;
        } else {
            // 解析返回的 JSON 格式数据
            $parsedResponse = json_decode($response, true);

            // 检查是否成功上传
            if (isset($parsedResponse[0]['src'])) {
                // 拼接图片 URL
                $imgSrc = get_http_type() . $_SERVER['SERVER_NAME'] . '/src.php?name=' . basename($parsedResponse[0]['src']);

                // 返回成功消息
                $json = json_encode(array("code" => 0, "msg" => $imgSrc));
                echo $json;
            } else {
                // 返回错误消息
                $json = json_encode(array("code" => 1, "msg" => "Upload failed: Unknown error"));
                echo $json;
            }
        }

        // 关闭 cURL 资源
        curl_close($ch);
    }
}

//自动获取协议头
function get_http_type()
{
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    return $http_type;
}
?>
