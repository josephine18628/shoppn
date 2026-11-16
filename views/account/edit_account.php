<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/CustomerController.php';
require_login();

$controller = new CustomerController();
$customer = $controller -> getCustomerById((int)$_SESSION['customer_id']);
if (!$customer) {
    redirect('my_account.php');
}

$countries = ['Ghana', 'Nigeria', 'Kenya', 'South Africa', 'United States', 'United Kingdom', 'Canada', 'Other'];

require_once __DIR__ . '/../layout/header.php';
?>

<h1>Edit My Details</h1>
<p><a href="my_account.php">&larr; Back to My Account</a></p>

<?php if (isset($_SESSION['error'])): ?>
    <p class="error-message"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
<?php endif; ?>

<form id="edit-account-form" class="admin-form" action="../../actions/update_account_action.php" method="POST" enctype="multipart/form-data" novalidate>
    <div class="form-group">
        <label for="name-input">Full Name</label>
        <input type="text" id="name-input" name="customer_name" maxlength="100" required value="<?= htmlspecialchars($customer['customer_name']) ?>">
        <small class="field-error" id="name-error"></small>
    </div>

    <div class="form-group">
        <label for="email-input">Email</label>
        <input type="email" id="email-input" name="customer_email" maxlength="50" required value="<?= htmlspecialchars($customer['customer_email']) ?>">
        <small class="field-error" id="email-error"></small>
    </div>

    <div class="form-group">
        <label for="country-input">Country</label>
        <select id="country-input" name="customer_country" required>
            <?php foreach ($countries as $country): ?>
                <option value="<?= $country ?>" <?= $customer['customer_country'] === $country ? 'selected' : '' ?>><?= $country ?></option>
            <?php endforeach; ?>
        </select>
        <small class="field-error" id="country-error"></small>
    </div>

    <div class="form-group">
        <label for="city-input">City</label>
        <input type="text" id="city-input" name="customer_city" maxlength="30" required value="<?= htmlspecialchars($customer['customer_city']) ?>">
        <small class="field-error" id="city-error"></small>
    </div>

    <div class="form-group">
        <label for="contact-input">Phone Number (with country code, e.g. +233241234567)</label>
        <input type="tel" id="contact-input" name="customer_contact" maxlength="15" required value="<?= htmlspecialchars($customer['customer_contact']) ?>">
        <small class="field-error" id="contact-error"></small>
    </div>

    <div class="form-group">
        <label for="image-input">Profile Picture (optional: JPG, PNG, GIF or WEBP, max 2 MB)</label>
        <?php if (!empty($customer['customer_image'])): ?>
            <img class="thumb" src="../../images/customers/<?= htmlspecialchars($customer['customer_image']) ?>" alt="Current profile picture">
        <?php endif; ?>
        <input type="file" id="image-input" name="customer_image" accept="image/jpeg,image/png,image/gif,image/webp">
        <small class="field-error" id="image-error"></small>
    </div>

    <button type="submit" id="edit-account-button">Save Changes</button>
</form>

<script src="../../js/validate.js"></script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>