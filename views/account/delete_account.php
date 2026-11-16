<?php
require_once __DIR__ . '/../../core/core.php';
require_login();
require_once __DIR__ . '/../layout/header.php';
?>

<h1>Change Password</h1>
<p><a href="my_account.php">&larr; Back to My Account</a></p>

<?php if (isset($_SESSION['error'])): ?>
    <p class="error-message"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
<?php endif; ?>

<form id="change-pass-form" class="admin-form" action="../../actions/change_pass_action.php" method="POST" novalidate>
    <div class="form-group">
        <label for="current-password-input">Current Password</label>
        <input type="password" id="current-password-input" name="current_password" required>
        <small class="field-error" id="current-password-error"></small>
    </div>

    <div class="form-group">
        <label for="password-input">New Password (8+ characters, with a letter, a number and a symbol)</label>
        <input type="password" id="password-input" name="new_password" required>
        <small class="field-error" id="password-error"></small>
    </div>

    <div class="form-group">
        <label for="confirm-password-input">Confirm New Password</label>
        <input type="password" id="confirm-password-input" name="confirm_password" required>
        <small class="field-error" id="confirm-password-error"></small>
    </div>

    <button type="submit" id="change-pass-button">Change Password</button>
</form>

<script src="../../js/validate.js"></script>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>