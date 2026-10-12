<?php
// classes/ProductClass.php — Model: all SQL for brands, categories and products.
require_once __DIR__ . '/../core/db_class.php';

class ProductClass extends Database {

    /* ---------------- Brands ---------------- */

    public function addBrand($name) {
        $stmt = $this->conn->prepare('INSERT INTO brands (brand_name) VALUES (?)');
        $stmt->bind_param('s', $name);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getEveryBrand() {
        $result = $this->conn->query('SELECT * FROM brands ORDER BY brand_name ASC');
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function brandById($id) {
        $stmt = $this->conn->prepare('SELECT * FROM brands WHERE brand_id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    public function updateBrand($id, $name) {
        $stmt = $this->conn->prepare('UPDATE brands SET brand_name = ? WHERE brand_id = ?');
        $stmt->bind_param('si', $name, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /* ---------------- Categories ---------------- */

    public function addCategory($name) {
        $stmt = $this->conn->prepare('INSERT INTO categories (cat_name) VALUES (?)');
        $stmt->bind_param('s', $name);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getEveryCategory() {
        $result = $this->conn->query('SELECT * FROM categories ORDER BY cat_name ASC');
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function categoryById($id) {
        $stmt = $this->conn->prepare('SELECT * FROM categories WHERE cat_id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    public function updateCategory($id, $name) {
        $stmt = $this->conn->prepare('UPDATE categories SET cat_name = ? WHERE cat_id = ?');
        $stmt->bind_param('si', $name, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /* ---------------- Products ---------------- */

    // Shared SELECT that also returns the category and brand names.
    private $productSelect =
        'SELECT p.*, c.cat_name, b.brand_name
           FROM products p
           JOIN categories c ON p.product_cat = c.cat_id
           JOIN brands b     ON p.product_brand = b.brand_id';

    public function addProduct($cat, $brand, $title, $price, $desc, $image_filename, $keywords) {
        $stmt = $this->conn->prepare(
            'INSERT INTO products
                (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('iisdsss', $cat, $brand, $title, $price, $desc, $image_filename, $keywords);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image_filename, $keywords) {
        $stmt = $this->conn->prepare(
            'UPDATE products SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?,  product_desc = ?, product_image = ?, product_keywords = ? WHERE product_id = ?'
        );
        $stmt->bind_param('iisdsssi', $cat, $brand, $title, $price, $desc, $image_filename, $keywords, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function productById($id) {
        $stmt = $this->conn->prepare($this->productSelect . ' WHERE p.product_id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    public function getEveryProduct() {
        $result = $this->conn->query($this->productSelect . ' ORDER BY p.product_id DESC');
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getFeaturedProducts($limit = 6) {
        $stmt = $this->conn->prepare($this->productSelect . ' ORDER BY RAND() LIMIT ?');
        $stmt->bind_param('i', $limit);
        return $this->fetchAll($stmt);
    }

    public function productsByCategory($cat_id) {
        $stmt = $this->conn->prepare($this->productSelect . ' WHERE p.product_cat = ? ORDER BY p.product_title ASC');
        $stmt->bind_param('i', $cat_id);
        return $this->fetchAll($stmt);
    }

    public function productsByBrand($brand_id) {
        $stmt = $this->conn->prepare($this->productSelect . ' WHERE p.product_brand = ? ORDER BY p.product_title ASC');
        $stmt->bind_param('i', $brand_id);
        return $this->fetchAll($stmt);
    }

    public function searchProducts($query) {
        $like = '%' . $query . '%';
        $stmt = $this->conn->prepare(
            $this->productSelect . ' WHERE p.product_title LIKE ? OR p.product_keywords LIKE ? ORDER BY p.product_title ASC'
        );
        $stmt->bind_param('ss', $like, $like);
        return $this->fetchAll($stmt);
    }

    // Runs a prepared statement and always returns an array (never false).
    private function fetchAll($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }
}