<?php
$event = 'NAAC';
$email = 'naac@gmail.com';
$password = '123';

$credentials = [
    'NAAC' => ['email' => 'naac@gmail.com', 'password' => '123'],
    'NBA' => ['email' => 'nba@gmail.com', 'password' => '123'],
    'NCC' => ['email' => 'ncc@gmail.com', 'password' => '123'],
    'Sports' => ['email' => 'sports@gmail.com', 'password' => '123'],
    'Clubs' => ['email' => 'clubs@gmail.com', 'password' => '123'],
    'NSS' => ['email' => 'nss@gmail.com', 'password' => '123'],
    'Women_Empowerment' => ['email' => 'women@gmail.com', 'password' => '123'],
    'IIC' => ['email' => 'iic@gmail.com', 'password' => '123'],
    'PASH' => ['email' => 'pash@gmail.com', 'password' => '123'],
    'Antiragging' => ['email' => 'antiragging@gmail.com', 'password' => '123'],
    'SAC' => ['email' => 'sac@gmail.com', 'password' => '123'],
    'RnD' => ['email' => 'rnd@gmail.com', 'password' => '123'],
    'IQAC' => ['email' => 'iqac@gmail.com', 'password' => '123'],
    'Exam_Section' => ['email' => 'exam@gmail.com', 'password' => '123']
];

if (isset($credentials[$event]) &&
    $credentials[$event]['email'] === $email &&
    $credentials[$event]['password'] === $password) {
    echo "SUCCESS\n";
} else {
    echo "FAILED\n";
}
?>
