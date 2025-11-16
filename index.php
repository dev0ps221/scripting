<pre>
    
<?php
$host = 'localhost';
$dbname = 'sqli';
$username = 'postgres';
$password = 'root';

$dsn = "pgsql:host=$host;dbname=$dbname";

function login($pdo,$user,$pass)
{
    $querystring = "select * from users where username = '{$user}' and pass = '{$pass}'";
    // echo $querystring;
    $query      =   $pdo->query($querystring);
    $results    =   $query->execute();
    $results    =   $query->fetchAll();
    if(count($results))
    {
        success();
        print_r($results);
    }
    else
    {
        error();
    }
}
function success()
{
    echo "request returned success<br/>";
}
function error()
{
    echo "request returned error<br/>";
}
try {
    $pdo = new PDO($dsn, $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $user_param = isset($_REQUEST['user']) ? $_REQUEST['user'] : null;
    $pwd_param = isset($_REQUEST['pwd']) ? $_REQUEST['pwd'] : null;
    if($user_param && $pwd_param)
    {

        login($pdo,$user_param,$pwd_param);
    }
} catch (PDOException $e) {
    // The PDOException class is the default handler for SQL errors
    die("Connection failed: " . $e->getMessage());
}
?>
</pre>