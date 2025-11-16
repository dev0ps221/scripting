<?php

// ----------------------------------------
// VULNERABLE FILE UPLOAD (Race Condition)
// ----------------------------------------

if (isset($_FILES['file'])) {

    // ❌ NON-ATOMIC: Validation and move are separate
    $tmp  = $_FILES['file']['tmp_name'];
    $name = $_FILES['file']['name'];

    // --- Fake image validation (separate step) ---
    $check = @getimagesize($tmp);   // ❌ reads file at time T1
    if ($check === false) {
        echo "Invalid file";
        exit;
    }

    // --- Delay to make race easier ---
    usleep(200000);  // 200ms   ❌ gives your race window

    // --- Move uploaded file (separate step) ---
    $dest = "uploads/" . $name;
    if (move_uploaded_file($tmp, $dest)) {
        echo "Uploaded to: $dest";
    } else {
        echo "Upload failed";
    }

} else {
?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file">
        <button>Upload</button>
    </form>
<?php
}
