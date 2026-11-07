<?php
function evax_store_profile_image(array $upload, &$error)
{
    $error = '';
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($upload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $error = 'The profile image could not be uploaded. Please try again.';
        return null;
    }

    if (!isset($upload['tmp_name'], $upload['size'])
        || !is_uploaded_file($upload['tmp_name'])
        || (int)$upload['size'] < 1
        || (int)$upload['size'] > 2 * 1024 * 1024) {
        $error = 'Choose an image smaller than 2 MB.';
        return null;
    }

    $imageInfo = @getimagesize($upload['tmp_name']);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($upload['tmp_name']);
    $imageTypes = [
        IMAGETYPE_JPEG => ['mime' => 'image/jpeg', 'extension' => 'jpg'],
        IMAGETYPE_PNG => ['mime' => 'image/png', 'extension' => 'png'],
        IMAGETYPE_WEBP => ['mime' => 'image/webp', 'extension' => 'webp']
    ];

    if (!$imageInfo || !isset($imageTypes[$imageInfo[2]])
        || $imageInfo[0] > 4096 || $imageInfo[1] > 4096
        || $imageInfo[0] * $imageInfo[1] > 16777216
        || $mimeType !== $imageTypes[$imageInfo[2]]['mime']) {
        $error = 'Upload a valid JPEG, PNG, or WebP image no larger than 4096 x 4096 pixels.';
        return null;
    }

    $relativeDirectory = 'uploads/profile-images';
    $absoluteDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profile-images';
    if (!is_dir($absoluteDirectory) && !mkdir($absoluteDirectory, 0750, true) && !is_dir($absoluteDirectory)) {
        error_log('E-Vaccine could not create the profile image directory.');
        $error = 'Profile image storage is unavailable. Please try again later.';
        return null;
    }

    $fileName = bin2hex(random_bytes(16)) . '.' . $imageTypes[$imageInfo[2]]['extension'];
    if (!move_uploaded_file($upload['tmp_name'], $absoluteDirectory . DIRECTORY_SEPARATOR . $fileName)) {
        error_log('E-Vaccine could not save an uploaded profile image.');
        $error = 'The profile image could not be saved. Please try again.';
        return null;
    }

    return $relativeDirectory . '/' . $fileName;
}

function evax_remove_profile_image($relativePath)
{
    if (!is_string($relativePath) || strpos($relativePath, 'uploads/profile-images/') !== 0) {
        return;
    }

    $fileName = substr($relativePath, strlen('uploads/profile-images/'));
    if ($fileName === '' || basename($fileName) !== $fileName) {
        return;
    }

    $imagePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profile-images' . DIRECTORY_SEPARATOR . $fileName;
    if (is_file($imagePath) && !unlink($imagePath)) {
        error_log('E-Vaccine could not remove a replaced profile image: ' . $fileName);
    }
}
?>
