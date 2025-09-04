<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'JanataConnect'; ?></title>
    <link rel="stylesheet" href="/JanataConnect/public/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="/JanataConnect/" class="logo">
                <i class="fas fa-bridge"></i> JanataConnect
            </a>
            
            <nav>
                <ul class="nav-menu">
                    <?php if (Config::isLoggedIn()): ?>
                        <li><a href="/JanataConnect/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a></li>
                        
                        <?php if ($_SESSION['user_role'] === 'citizen'): ?>
                            <li><a href="/JanataConnect/submit-suggestion">
                                <i class="fas fa-plus"></i> Submit Suggestion
                            </a></li>
                            <li><a href="/JanataConnect/my-submissions">
                                <i class="fas fa-list"></i> My Submissions
                            </a></li>
                        <?php elseif ($_SESSION['user_role'] === 'official'): ?>
                            <li><a href="/JanataConnect/official/submissions">
                                <i class="fas fa-clipboard-list"></i> Review Submissions
                            </a></li>
                        <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                            <li><a href="/JanataConnect/admin">
                                <i class="fas fa-cog"></i> Admin Panel
                            </a></li>
                        <?php endif; ?>
                        
                        <li><a href="/JanataConnect/logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                    <?php else: ?>
                        <li><a href="/JanataConnect/login">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a></li>
                        <li><a href="/JanataConnect/register">
                            <i class="fas fa-user-plus"></i> Register
                        </a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
