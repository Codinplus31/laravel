<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Files Are Ready to Download</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Files Are Ready to Download</h2>
        <p>Someone has shared files with you. You can download them using the link below:</p>
        
        <a href="{{ $downloadLink }}" class="button">Download Files</a>
        
        <p>This link will expire soon, so please download your files as soon as possible.</p>
        
        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>