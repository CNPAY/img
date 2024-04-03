<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>最近上传 - 404888图床</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            grid-gap: 20px;
        }

        .image-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .image-card:hover {
            transform: translateY(-5px);
        }

        .image-card img,
        .image-card video {
            width: 100%;
            height: auto;
            max-height: 125px;
            border-bottom: 1px solid #ddd;
        }

        .image-details {
            padding: 10px;
        }

        .ip-address {
            font-size: 14px;
            color: #666;
        }

        .upload-time {
            font-size: 12px;
            color: #999;
        }

        .centered {
            text-align: center;
        }

        .link {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>最近上传</h1>
        </div>
        <div class="image-grid">
            <?php
            $logFile = __DIR__ . '/log/upimg/url.log';
            if (file_exists($logFile)) {
                $logLines = file($logFile);
                $logLines = array_reverse($logLines);

                $count = 0;
                foreach ($logLines as $line) {
                    if ($count >= 15) break;
                    $count++;

                    $parts = explode('|', $line);
                    $fileName = trim($parts[0]);
                    $ip = trim($parts[1]);
                    $uploadTime = trim($parts[2]);

                    $ip = preg_replace('/(\d+)\.(\d+)\.(\d+)\.(\d+)/', '$1.$2.**.**', $ip);

                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                    echo '<div class="image-card">';
                    if ($fileExtension == 'mp4') {
                        echo '<video controls><source src="https://img.404888.xyz/src.php?name=' . $fileName . '" type="video/mp4"></video>';
                    } else {
                        echo '<img src="https://img.404888.xyz/src.php?name=' . $fileName . '">';
                    }
                    echo '<div class="image-details">';
                    echo '<p class="ip-address">IP: ' . $ip . '</p>';
                    echo '<p class="upload-time">Upload Time: ' . $uploadTime . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No uploads found.</p>';
            }
            ?>
        </div>
    </div>
    <div class="centered">
        <p><a href='https://img.404888.xyz/' class="link">返回404888图床</a></p>
    </div>
</body>
</html>