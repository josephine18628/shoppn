<?php
require_once __DIR__ . '/../classes/ProductClass.php';
class ProductController {
    private $productModel;
    public function __construct() {
        $this->productModel = new ProductClass();
    }

    public function addBrand($name) {
        return $this->productModel->addBrand($name);
    }

    public function getEveryBrand() {
        return $this->productModel->getEveryBrand();
    }

    public function updateBrand($id, $name) {
        return $this->productModel->updateBrand($id, $name);
    }

    public function addCategory($name) {
        return $this->productModel->addcategory($name);
    }

    public function getEveryCategory() {
        return $this->productModel->getEveryCategory();
    }

    public function updateCategory($id, $name) {
        return $this->productModel->updateCategory($id, $name);
    }

    public function brandById($id) {
        return $this->productModel->brandById($id);
    }

    public function categoryById($id) {
        return $this->productModel->categoryById($id);
    }

    public function addProduct($cat, $brand, $title, $price, $desc, $image_filename, $keywords) {
        return $this -> productModel -> addProduct($cat, $brand, $title, $price, $desc, $image_filename, $keywords);
    }

    public function productById($id) {
        return $this -> productModel -> productById($id);
    }

    public function getEveryProduct() {
        return $this -> productModel ->getEveryProduct();
    }

    public function getFeaturedProducts($limit = 6) {
        return $this -> productModel -> getFeaturedProducts($limit);
    }

    public function productsByCategory($cat_id) {
        return $this -> productModel -> productsByCategory($cat_id);
    }

    public function productsByBrand($brand_id) {
        return $this -> productModel -> productsByBrand($brand_id);
    }

    public function searchProducts($query) {
        return $this -> productModel -> searchProducts($query);
    }
}