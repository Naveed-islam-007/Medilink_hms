<?php

require_once __DIR__ . '/../config/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediLink</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app">

    <aside class="sidebar">
        <div class="logo">Medi<span>Link</span></div>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="patients.php">Patients</a>
            <a href="doctors.php">Doctors</a>
            <a href="hospitals.php">Hospitals</a>
            <a href="pharmacies.php">Pharmacies</a>
            <a href="medicines.php">Medicines</a>
            <a href="donors.php">Blood Donors</a>
            <a href="prescriptions.php">Prescriptions</a>
            <a href="consultations.php">Consultations</a>
            <a href="search.php">Specialty Search</a>
        </nav>
        <a class="logout" href="logout.php">Logout</a>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <h1><?= htmlspecialchars($page_title ?? 'Dashboard') ?></h1>
                <p>Healthcare information management system</p>
            </div>
            <div class="user-badge"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></div>
        </header>

        <div class="content">
