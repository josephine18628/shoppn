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

    public function getEverycategory() {
        return $this->productModel->getEverycategory();
    }

    public function updateCategory($id, $name) {
        return $this->productModel->updatecategory($id, $name);
    }

    public function brandById($id) {
        return $this->productModel->brandById($id);
    }

    public function categoryById($id) {
        return $this->productModel->categoryById($id);
    }
}