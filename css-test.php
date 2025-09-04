<?php
// Simple CSS test page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Test - JanataConnect</title>
    <link rel="stylesheet" href="/JanataConnect/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Force some basic styling to test */
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f8f9fa; 
            margin: 0; 
            padding: 0; 
        }
        .test-container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .test-card { 
            background: white; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 20px; 
            margin: 20px 0; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        .test-button { 
            background: #007bff; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 4px; 
            cursor: pointer; 
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>CSS Test Page</h1>
        <p>This page tests if CSS is loading correctly.</p>
        
        <div class="test-card">
            <h3>Test Card with Inline CSS</h3>
            <p>This card uses inline CSS to ensure it's styled.</p>
            <button class="test-button">Test Button</button>
        </div>
        
        <div class="card">
            <h3>Test Card with External CSS</h3>
            <p>This card should use the external style.css file.</p>
            <button class="btn btn-primary">External CSS Button</button>
        </div>
        
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> If you see this styled, CSS is working!
        </div>
    </div>
</body>
</html>
