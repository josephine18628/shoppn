// js/cart.js — the − / + buttons on the cart page
// Each click changes the quantity by one and submits the update form,
// so the server (update_qty_action.php) saves it and the totals refresh.
document.addEventListener('DOMContentLoaded', function () {
    var updateForm = document.getElementById('update-form');
    var removeForm = document.getElementById('remove-form');
    var MIN = 1;
    var MAX = 999;

    document.querySelectorAll('.qty-control').forEach(function (control) {
        var input = control.querySelector('.qty-input');

        control.querySelectorAll('.qty-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                var current = parseInt(input.value, 10);
                if (isNaN(current)) current = MIN;

                var next = current + parseInt(button.dataset.step, 10);
                if (next < MIN || next > MAX) return;

                input.value = next;
                control.querySelectorAll('.qty-btn').forEach(function (b) { b.disabled = true; });
                updateForm.submit();
            });
        });

        // Typing a number then pressing Enter saves it too
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (validQty(input.value)) updateForm.submit();
            }
        });
    });

    // Block the Update button if any quantity is not a whole number from 1 to 999
    updateForm.addEventListener('submit', function (e) {
        var bad = Array.prototype.some.call(document.querySelectorAll('.qty-input'), function (input) {
            return !validQty(input.value);
        });
        if (bad) {
            e.preventDefault();
            alert('Quantities must be whole numbers from 1 to 999.');
        }
    });

    // Confirm before removing anything
    removeForm.addEventListener('submit', function (e) {
        var clicked = e.submitter && e.submitter.name === 'remove[]';
        var ticked = document.querySelectorAll('input[name="remove[]"]:checked').length;
        if (!clicked && ticked === 0) {
            e.preventDefault();
            alert('Tick at least one item to remove.');
            return;
        }
        if (!confirm('Remove from your cart?')) e.preventDefault();
    });

    function validQty(value) {
        return /^[0-9]{1,3}$/.test(value) && parseInt(value, 10) >= MIN;
    }
});