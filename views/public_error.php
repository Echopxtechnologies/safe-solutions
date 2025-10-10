<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Error'; ?> - Safe Legal Solutions</title>
    
    <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Arial', sans-serif;
            padding: 20px 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .error-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .error-header {
            background: linear-gradient(135deg, #d9534f 0%, #c9302c 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .error-header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .error-body {
            padding: 40px;
            text-align: center;
        }
        .error-message {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="error-container">
    <div class="error-header">
        <div class="error-icon">
            <i class="fa fa-exclamation-triangle"></i>
        </div>
        <h1><?php echo isset($title) ? htmlspecialchars($title) : 'Error'; ?></h1>
    </div>
    
    <div class="error-body">
        <p class="error-message">
            <?php echo isset($error) ? htmlspecialchars($error) : 'An error occurred. Please try again.'; ?>
        </p>
        
        <p>
            <i class="fa fa-envelope"></i> For assistance, please contact support.
        </p>
    </div>
</div>

</body>
</html>