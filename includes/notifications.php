<?php

function evax_create_notification($connect, $recipientType, $recipientId, $title, $message, $targetUrl, $eventKey = null)
{
    $statement = mysqli_prepare(
        $connect,
        'INSERT INTO notifications (recipient_type, recipient_id, title, message, target_url, event_key)
         VALUES (?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE id = id'
    );
    if (!$statement) {
        error_log('E-Vaccine notification could not be prepared: ' . mysqli_error($connect));
        return false;
    }

    mysqli_stmt_bind_param(
        $statement,
        'sissss',
        $recipientType,
        $recipientId,
        $title,
        $message,
        $targetUrl,
        $eventKey
    );
    $created = mysqli_stmt_execute($statement);
    if (!$created) {
        error_log('E-Vaccine notification insert failed: ' . mysqli_stmt_error($statement));
    }
    mysqli_stmt_close($statement);
    return $created;
}

function evax_notify_booking_status($connect, $bookingId, $parentId, $hospitalId, $childName, $status)
{
    $messages = [
        'Hospital Approved' => ['Appointment approved', 'Your appointment for %s has been approved by the hospital.'],
        'Hospital Rejected' => ['Appointment declined', 'The hospital declined the appointment request for %s.'],
        'Vaccinated' => ['Vaccination recorded', 'The hospital recorded a vaccination for %s. The record is awaiting administrator review.'],
        'Not Vaccinated' => ['Vaccination not completed', 'The hospital reported that the appointment for %s did not result in a vaccination.'],
        'Admin Approved' => ['Vaccination verified', 'The vaccination record for %s has been approved by an administrator.'],
        'Admin Rejected' => ['Vaccination record declined', 'An administrator rejected the vaccination record for %s.']
    ];
    if (!isset($messages[$status])) {
        return;
    }

    [$title, $messageTemplate] = $messages[$status];
    $message = sprintf($messageTemplate, $childName);
    $eventKey = 'booking-status-' . (int)$bookingId . '-' . strtolower(str_replace(' ', '-', $status));

    evax_create_notification(
        $connect,
        'parent',
        (int)$parentId,
        $title,
        $message,
        'parents/vaccination_report.php',
        $eventKey . '-parent'
    );

    if (in_array($status, ['Admin Approved', 'Admin Rejected'], true) && (int)$hospitalId > 0) {
        evax_create_notification(
            $connect,
            'hospital',
            (int)$hospitalId,
            $title,
            $message,
            'hospital/hospital_dashboard.php',
            $eventKey . '-hospital'
        );
    }

    if ($status === 'Vaccinated') {
        evax_create_notification(
            $connect,
            'admin',
            0,
            'Vaccination record needs review',
            'The hospital recorded a vaccination for ' . $childName . '. Please review the record.',
            'admin/vaccination_report.php',
            $eventKey . '-admin'
        );
    }
}

function evax_generate_vaccine_reminders($connect, $parentId)
{
    $childrenStatement = mysqli_prepare(
        $connect,
        'SELECT id, child_name, date_of_birth FROM children WHERE parent_id = ?'
    );
    if (!$childrenStatement) {
        error_log('E-Vaccine reminder child query could not be prepared: ' . mysqli_error($connect));
        return;
    }
    mysqli_stmt_bind_param($childrenStatement, 'i', $parentId);
    if (!mysqli_stmt_execute($childrenStatement)) {
        error_log('E-Vaccine reminder child query failed: ' . mysqli_stmt_error($childrenStatement));
        mysqli_stmt_close($childrenStatement);
        return;
    }
    $childrenResult = mysqli_stmt_get_result($childrenStatement);
    if (!$childrenResult) {
        error_log('E-Vaccine reminder child results could not be read: ' . mysqli_stmt_error($childrenStatement));
        mysqli_stmt_close($childrenStatement);
        return;
    }
    $children = mysqli_fetch_all($childrenResult, MYSQLI_ASSOC);
    mysqli_stmt_close($childrenStatement);

    $vaccineResult = mysqli_query(
        $connect,
        "SELECT Vaccine_Name, Age_Limit FROM vaccines WHERE Status = 'Active' ORDER BY Vaccine_ID"
    );
    if (!$vaccineResult) {
        error_log('E-Vaccine reminder vaccine query failed: ' . mysqli_error($connect));
        return;
    }
    $vaccines = mysqli_fetch_all($vaccineResult, MYSQLI_ASSOC);
    mysqli_free_result($vaccineResult);

    $today = new DateTimeImmutable('today');
    $reminderWindowEnd = $today->modify('+7 days');
    foreach ($children as $child) {
        $birthDate = DateTimeImmutable::createFromFormat('!Y-m-d', $child['date_of_birth']);
        if (!$birthDate) {
            continue;
        }

        $milestones = [];
        foreach ($vaccines as $vaccine) {
            $ageLabel = trim((string)$vaccine['Age_Limit']);
            if (preg_match('/^(?:at birth|birth)$/i', $ageLabel)) {
                $dueDate = $birthDate;
            } elseif (preg_match('/^(\d+)\s*(day|week|month|year)s?$/i', $ageLabel, $ageMatch)) {
                $dueDate = $birthDate->modify('+' . (int)$ageMatch[1] . ' ' . $ageMatch[2]);
            } else {
                continue;
            }

            if ($dueDate <= $today || $dueDate > $reminderWindowEnd) {
                continue;
            }
            $milestoneKey = $ageLabel . '|' . $dueDate->format('Y-m-d');
            if (!isset($milestones[$milestoneKey])) {
                $milestones[$milestoneKey] = [
                    'age' => $ageLabel,
                    'date' => $dueDate,
                    'vaccines' => []
                ];
            }
            $milestones[$milestoneKey]['vaccines'][] = $vaccine['Vaccine_Name'];
        }

        foreach ($milestones as $milestone) {
            $vaccineNames = implode(', ', $milestone['vaccines']);
            $dateLabel = $milestone['date']->format('M j, Y');
            $message = $child['child_name'] . "'s schedule lists " . $vaccineNames
                . ' at ' . $milestone['age'] . ' (estimated date: ' . $dateLabel
                . '). This date-of-birth reminder may include doses already received.';
            $eventKey = 'vaccine-reminder-' . (int)$child['id'] . '-' . hash(
                'sha256',
                $milestone['age'] . '|' . $milestone['date']->format('Y-m-d')
            );
            evax_create_notification(
                $connect,
                'parent',
                $parentId,
                'Upcoming vaccine milestone',
                $message,
                'awareness.php',
                $eventKey
            );
        }
    }
}
