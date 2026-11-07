<?php
function evax_ensure_parent_reset_requests_table($connect)
{
    $query = "CREATE TABLE IF NOT EXISTS parent_password_reset_requests (
        id bigint unsigned NOT NULL AUTO_INCREMENT,
        parent_id int(11) DEFAULT NULL,
        parent_name varchar(100) NOT NULL,
        parent_email varchar(100) NOT NULL,
        status enum('Pending','Approved','Link Generated','Sent','Completed','Rejected') NOT NULL DEFAULT 'Pending',
        status_token_hash char(64) NOT NULL,
        reset_token_hash char(64) DEFAULT NULL,
        created_at timestamp NOT NULL DEFAULT current_timestamp(),
        updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (id),
        UNIQUE KEY parent_reset_status_token (status_token_hash),
        KEY parent_reset_status_created (status, created_at),
        KEY parent_reset_parent (parent_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if (!mysqli_query($connect, $query)) {
        error_log('E-Vaccine parent reset request table could not be initialized: ' . mysqli_error($connect));
        return false;
    }
    return true;
}
