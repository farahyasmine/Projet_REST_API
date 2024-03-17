<?php
include_once '/Applications/MAMP/htdocs/TP_REST_API/Token_rest_api/db/Database.php';  

class Users {
    private $conn;
    private $db_table = "users";

    public function __construct($db){
        $this->conn = $db;
    }

    public function authenticate($email, $password) {
        $sqlQuery = "SELECT * FROM " . $this->db_table . " WHERE email = :email";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user['id'];
        } else {
            return false;
        }
    }

    public function generateToken($userId) {
        $token = bin2hex(random_bytes(16));
        $expireTime = date("Y-m-d H:i:s", strtotime('+1 hour'));
        $sqlQuery = "INSERT INTO session (UserToken, UserID, TokenExpire) VALUES (:token, :userId, :expireTime)";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":userId", $userId);
        $stmt->bindParam(":expireTime", $expireTime);
        $stmt->execute();
        
        return $token;
    }
}

$db = new Database();
$conn = $db->getConnection();
$user = new Users($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

   
    if (!empty($data['email']) && !empty($data['password'])) {
        $userId = $user->authenticate($data['email'], $data['password']);
        if ($userId) {
            $token = $user->generateToken($userId);
            echo json_encode(['token' => $token]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Authentication Failed']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Incomplete Data']);
    }
}
function verifyToken($conn) {
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
        if (!isset($matches[1])) {
            echo json_encode(['message' => 'Token not found in request']);
            exit;
        }
        $token = $matches[1];
        $sqlQuery = "SELECT UserID FROM session WHERE UserToken = :token AND TokenExpire > NOW()";
        $stmt = $conn->prepare($sqlQuery);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC)['UserID'];
        } else {
            echo json_encode(['message' => 'Token is invalid or expired']);
            exit;
        }
    } else {
        echo json_encode(['message' => 'Authorization header missing']);
        exit;
    }
}
