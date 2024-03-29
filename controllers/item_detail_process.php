<?php
// Kiểm tra xem tham số "id" đã được truyền qua URL hay chưa
if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    session_start();
    $_SESSION['productId'] = $productId;

    include_once "../config/config.php";
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname_database", $username_database, $password_database);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Kết nối đến cơ sở dữ liệu thất bại: " . $e->getMessage());
    }

    try {
        $sql = "SELECT *
                FROM products p
                JOIN product_detail pd ON p.id = pd.product_id
                WHERE p.id = :productId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra và xử lý kết quả truy vấn
        if ($product) {
            $productInfo = array(
                'id' => $product['id'],
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'cpu' => $product['cpu'],
                'ram' => $product['ram'],
                'storage' => $product['storage'],
                'graphic_card' => $product['graphic_card'],
                'display' => $product['display']
            );

            // Chuyển đổi mảng thông tin sản phẩm thành dữ liệu JSON
            $jsonData = json_encode($productInfo);
            echo $jsonData;

            $url = '../templates/product_detail/index.php?data=' . urlencode($jsonData);

            header('Location: ' . $url);
            exit;
        } else {
            echo 'Không tìm thấy sản phẩm.';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        if ($conn !== null) {
            $conn = null;
        }
    }
} else {
    echo 'Không tìm thấy sản phẩm.';
}
?>