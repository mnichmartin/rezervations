<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'slot' => $_POST['slot'] ?? '',
        'school' => $_POST['school'] ?? '',
        'address' => $_POST['address'] ?? '',
        'principal' => $_POST['principal'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'it_contact' => $_POST['it_contact'] ?? '',
        'email' => $_POST['email'] ?? '',
        'note' => $_POST['note'] ?? '',
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $file = 'reservations.json';
    $reservations = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    foreach ($reservations as $r) {
        if ($r['slot'] === $data['slot']) {
            http_response_code(409);
            echo 'Slot already booked.';
            exit;
        }
    }

    $reservations[] = $data;
    file_put_contents($file, json_encode($reservations, JSON_PRETTY_PRINT));

    $toSchool = $data['email'];
    $toAdmin = "skolitel@npi.cz";
    $subject = "Potvrzení rezervace termínu: {$data['slot']}";
    $message = "Děkujeme za rezervaci.\n\nDetaily rezervace:\n" . print_r($data, true);
    $headers = "From: rezervace@npi.cz";

    mail($toSchool, $subject, $message, $headers);
    mail($toAdmin, $subject, $message, $headers);

    echo 'Rezervace přijata.';
} else {
    http_response_code(405);
    echo 'Method not allowed';
}
?>
