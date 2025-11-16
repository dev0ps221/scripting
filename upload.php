<?php

if (isset($_FILES['file'])) {

    // ❌ no checks at all
    $dest = "uploads/" . $_FILES['file']['name'];
    $dest = "uploads/" . $_FILES['file']['full_path'];
    if (move_uploaded_file($_FILES['file']['tmp_name'], $dest)) 
    {
        echo "Uploaded: $dest";
    } 
    else 
    {
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
