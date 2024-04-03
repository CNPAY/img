<?php
// 获取 URL 中的文件并输出到浏览器
function getImageFromURL($url)
{
    // 初始化 cURL
    $ch = curl_init();

    // 设置 cURL 选项
    curl_setopt($ch, CURLOPT_URL, $url); // 设置 URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 将响应保存到变量中，而不是直接输出
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟随重定向
    curl_setopt($ch, CURLOPT_HEADER, false); // 不包含响应头
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 不验证 SSL 证书

    // 执行 cURL 请求
    $response = curl_exec($ch);

    // 检查是否有错误发生
    if ($response === false) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
        // 获取响应的 HTTP 状态码
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // 检查 HTTP 状态码
        if ($httpCode == 200) {
            // 获取响应的 Content-Type
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

            // 设置 Content-Type
            header('Content-Type: ' . $contentType);

            // 输出文件内容
            echo $response;
        } else {
            echo 'HTTP error: ' . $httpCode;
        }
    }

    // 关闭 cURL 资源
    curl_close($ch);
}

// 从 URL 参数中获取文件 URL
if (isset($_GET['url'])) {
    getImageFromURL($_GET['url']);
} else if(isset($_GET['imgur'])) {
    getImageFromURL('https://i.imgur.com/' . $_GET['imgur']);
} else {
    getImageFromURL('https://telegra.ph/file/11383cc5b84ba6d37a305.jpg');
}