<?php

function evax_initialize_admin_account($connect, $defaultUsername, $defaultPassword)
{
    $createTable = mysqli_query(
        $connect,
        "CREATE TABLE IF NOT EXISTS admin_accounts (
            id tinyint unsigned NOT NULL,
            username varchar(100) NOT NULL,
            password_hash varchar(255) NOT NULL,
            profile_image varchar(255) DEFAULT NULL,
            created_at timestamp NOT NULL DEFAULT current_timestamp(),
            updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (id),
            UNIQUE KEY admin_accounts_username (username)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
    );
    if (!$createTable) {
        error_log('E-Vaccine admin account table could not be initialized: ' . mysqli_error($connect));
        return false;
    }

    $imageColumn = mysqli_query($connect, "SHOW COLUMNS FROM admin_accounts LIKE 'profile_image'");
    if (!$imageColumn) {
        error_log('E-Vaccine admin profile image column lookup failed: ' . mysqli_error($connect));
        return false;
    }
    if (mysqli_num_rows($imageColumn) === 0) {
        if (!mysqli_query($connect, 'ALTER TABLE admin_accounts ADD COLUMN profile_image varchar(255) DEFAULT NULL AFTER password_hash')) {
            error_log('E-Vaccine admin profile image column could not be added: ' . mysqli_error($connect));
            mysqli_free_result($imageColumn);
            return false;
        }
    }
    mysqli_free_result($imageColumn);

    $check = mysqli_prepare($connect, 'SELECT id FROM admin_accounts WHERE id = 1 LIMIT 1');
    if (!$check) {
        error_log('E-Vaccine admin account lookup could not be prepared: ' . mysqli_error($connect));
        return false;
    }
    if (!mysqli_stmt_execute($check)) {
        error_log('E-Vaccine admin account lookup failed: ' . mysqli_stmt_error($check));
        mysqli_stmt_close($check);
        return false;
    }
    mysqli_stmt_store_result($check);
    $accountExists = mysqli_stmt_num_rows($check) === 1;
    mysqli_stmt_close($check);
    if ($accountExists) {
        return true;
    }

    $passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);
    if (!is_string($passwordHash)) {
        error_log('E-Vaccine default admin password could not be hashed.');
        return false;
    }
    $insert = mysqli_prepare(
        $connect,
        'INSERT INTO admin_accounts (id, username, password_hash) VALUES (1, ?, ?)'
    );
    if (!$insert) {
        error_log('E-Vaccine default admin account could not be prepared: ' . mysqli_error($connect));
        return false;
    }
    mysqli_stmt_bind_param($insert, 'ss', $defaultUsername, $passwordHash);
    $created = mysqli_stmt_execute($insert);
    if (!$created) {
        error_log('E-Vaccine default admin account could not be created: ' . mysqli_stmt_error($insert));
    }
    mysqli_stmt_close($insert);
    return $created;
}
