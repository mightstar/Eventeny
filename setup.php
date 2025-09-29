<?php
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Eventeny Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .success { color: #059669; }
        .error { color: #dc2626; }
        .warning { color: #d97706; }
        .info { color: #2563eb; }
        pre { background: #f3f4f6; padding: 15px; border-radius: 8px; overflow-x: auto; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #3b82f6; background: #f8fafc; }
    </style>
</head>
<body>
    <h1>🎫 Eventeny Setup</h1>
    <p>This script will help you set up the Eventeny ticketing platform.</p>";

// Check PHP version
echo "<div class='step'>";
echo "<h2>1. PHP Version Check</h2>";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "<p class='success'>✅ PHP " . PHP_VERSION . " is supported</p>";
} else {
    echo "<p class='error'>❌ PHP 7.4+ is required. Current version: " . PHP_VERSION . "</p>";
    exit;
}
echo "</div>";

// Check PDO extension
echo "<div class='step'>";
echo "<h2>2. PDO Extension Check</h2>";
if (extension_loaded('pdo') && extension_loaded('pdo_mysql')) {
    echo "<p class='success'>✅ PDO MySQL extension is available</p>";
} else {
    echo "<p class='error'>❌ PDO MySQL extension is required</p>";
    exit;
}
echo "</div>";

// Database connection test
echo "<div class='step'>";
echo "<h2>3. Database Connection Test</h2>";

// Get database credentials
$host = $_POST['db_host'] ?? 'localhost';
$name = $_POST['db_name'] ?? 'eventeny_tickets';
$user = $_POST['db_user'] ?? 'root';
$pass = $_POST['db_pass'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p class='success'>✅ Database connection successful</p>";
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name`");
        $pdo->exec("USE `$name`");
        echo "<p class='success'>✅ Database '$name' created/verified</p>";
        
        // Read and execute schema
        $schema = file_get_contents('database/schema.sql');
        $statements = explode(';', $schema);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && !preg_match('/^(CREATE DATABASE|USE)/i', $statement)) {
                $pdo->exec($statement);
            }
        }
        
        echo "<p class='success'>✅ Database schema imported successfully</p>";
        
        // Update config file
        $configContent = "<?php
                // Database configuration
                define('DB_HOST', '$host');
                define('DB_NAME', '$name');
                define('DB_USER', '$user');
                define('DB_PASS', '$pass');

                class Database {
                    private \$connection;
                    
                    public function __construct() {
                        try {
                            \$this->connection = new PDO(
                                \"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=utf8mb4\",
                                DB_USER,
                                DB_PASS,
                                [
                                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                                    PDO::ATTR_EMULATE_PREPARES => false
                                ]
                            );
                        } catch (PDOException \$e) {
                            die(\"Database connection failed: \" . \$e->getMessage());
                        }
                    }
                    
                    public function getConnection() {
                        return \$this->connection;
                    }
                    
                    public function query(\$sql, \$params = []) {
                        \$stmt = \$this->connection->prepare(\$sql);
                        \$stmt->execute(\$params);
                        return \$stmt;
                    }
                    
                    public function fetchAll(\$sql, \$params = []) {
                        return \$this->query(\$sql, \$params)->fetchAll();
                    }
                    
                    public function fetchOne(\$sql, \$params = []) {
                        return \$this->query(\$sql, \$params)->fetch();
                    }
                    
                    public function lastInsertId() {
                        return \$this->connection->lastInsertId();
                    }
                }
            ?>";
        
        file_put_contents('config/database.php', $configContent);
        echo "<p class='success'>✅ Database configuration updated</p>";
        
        // Test API endpoints
        echo "<h3>4. API Endpoint Tests</h3>";
        
        // Test tickets API
        $ticketsUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . 'api/tickets.php';
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'method' => 'GET'
            ]
        ]);
        $response = @file_get_contents($ticketsUrl, false, $context);

        if ($response && json_decode($response, true)) {
            echo "<p class='success'>✅ Tickets API is working</p>";
        } else {
            echo "<p class='warning'>⚠️ Tickets API may need manual testing</p>";
        }
        
        echo "<div style='background: #d1fae5; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h2 class='success'>🎉 Setup Complete!</h2>";
        echo "<p><strong>Your Eventeny platform is ready to use!</strong></p>";
        echo "<ul>";
        echo "<li><a href='index.php' target='_blank'>🌐 Public Ticket Browser</a></li>";
        echo "<li><a href='admin/index.php' target='_blank'>👨‍💼 Organizer Dashboard</a></li>";
        echo "</ul>";
        echo "<p><strong>Next Steps:</strong></p>";
        echo "<ol>";
        echo "<li>Visit the Organizer Dashboard to create your first tickets</li>";
        echo "<li>Use the Public Browser to test the customer experience</li>";
        echo "<li>Customize the design and add your own content</li>";
        echo "</ol>";
        echo "</div>";
        
    } catch (PDOException $e) {
        echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
        echo "<p class='info'>Please check your database credentials and try again.</p>";
    }
} else {
    echo "<form method='POST' style='background: #f8fafc; padding: 20px; border-radius: 8px;'>";
    echo "<h3>Database Configuration</h3>";
    echo "<p>Please enter your database credentials:</p>";
    echo "<table>";
    echo "<tr><td><label for='db_host'>Host:</label></td><td><input type='text' name='db_host' value='$host' required></td></tr>";
    echo "<tr><td><label for='db_name'>Database:</label></td><td><input type='text' name='db_name' value='$name' required></td></tr>";
    echo "<tr><td><label for='db_user'>Username:</label></td><td><input type='text' name='db_user' value='$user' required></td></tr>";
    echo "<tr><td><label for='db_pass'>Password:</label></td><td><input type='password' name='db_pass' value='$pass'></td></tr>";
    echo "</table>";
    echo "<br><button type='submit' style='background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;'>Setup Database</button>";
    echo "</form>";
}
echo "</div>";

echo "<div class='step'>";
echo "<h2>System Information</h2>";
echo "<pre>";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "Current Directory: " . getcwd() . "\n";
echo "Available Extensions: " . implode(', ', get_loaded_extensions()) . "\n";
echo "</pre>";
echo "</div>";

echo "</body></html>";
?>


