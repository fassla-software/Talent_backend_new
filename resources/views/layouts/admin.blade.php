<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Add your CSS/JS links here -->
</head>
<body>
    <div class="container">
        <!-- Navigation bar or sidebar -->
        <nav>
            <ul>
                <!-- Example links -->
            </ul>
        </nav>

        <div class="content">
            @yield('content')  <!-- This will render the content from the child views -->
        </div>
    </div>
</body>
</html>
