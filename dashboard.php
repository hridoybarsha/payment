<?php
include "../config.php";

error_reporting(E_ALL);
ini_set('display_errors',1);

/* TOTAL USERS */
$total = $conn->query("
SELECT COUNT(*) total
FROM subscriptions
")->fetch(PDO::FETCH_ASSOC);

$totalUsers = $total['total'] ?? 0;

/* ACTIVE USERS */
$active = $conn->query("
SELECT COUNT(*) total
FROM subscriptions
WHERE expiry_date>=CURDATE()
")->fetch(PDO::FETCH_ASSOC);

$activeUsers = $active['total'] ?? 0;

/* EXPIRED USERS */
$expired = $conn->query("
SELECT COUNT(*) total
FROM subscriptions
WHERE expiry_date<CURDATE()
")->fetch(PDO::FETCH_ASSOC);

$expiredUsers = $expired['total'] ?? 0;

/* EXPIRING SOON */
$soon = $conn->query("
SELECT COUNT(*) total
FROM subscriptions
WHERE expiry_date
BETWEEN CURDATE()
AND DATE_ADD(CURDATE(),INTERVAL 3 DAY)
")->fetch(PDO::FETCH_ASSOC);

$expiringSoon = $soon['total'] ?? 0;

/* TOTAL REVENUE */
try{
$rev = $conn->query("
SELECT COALESCE(SUM(amount),0) total
FROM payments
WHERE UPPER(status)='APPROVED'
")->fetch(PDO::FETCH_ASSOC);

$totalRevenue = $rev['total'] ?? 0;
}catch(Exception $e){
$totalRevenue = 0;
}

/* PENDING PAYMENT */
try{
$pen = $conn->query("
SELECT COUNT(*) total
FROM payments
WHERE UPPER(status)='PENDING'
")->fetch(PDO::FETCH_ASSOC);

$pendingPayment = $pen['total'] ?? 0;
}catch(Exception $e){
$pendingPayment = 0;
}

/* LATEST USERS */
$users = $conn->query("
SELECT *
FROM subscriptions
ORDER BY id DESC
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

/* LATEST PAYMENTS */
$payments = $conn->query("
SELECT *
FROM payments
ORDER BY id DESC
LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>SUPER IPTV DASHBOARD</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head><body class="bg-dark text-white">

<?php include "../includes/sidebar.php"; ?>

<div class="container-fluid" style="margin-left:270px;padding:20px;">

<h2 class="text-center mb-4">
📺 SUPER IPTV DASHBOARD V2
</h2>

<div class="row g-3">

<div class="col-md-3">
<div class="card bg-primary text-center text-white shadow">
<div class="card-body">
<h6>Total Users</h6>
<h2><?= $totalUsers ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card bg-success text-center text-white shadow">
<div class="card-body">
<h6>Active Users</h6>
<h2><?= $activeUsers ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card bg-danger text-center text-white shadow">
<div class="card-body">
<h6>Expired Users</h6>
<h2><?= $expiredUsers ?></h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card bg-warning text-center text-dark shadow">
<div class="card-body">
<h6>Expiring Soon</h6>
<h2><?= $expiringSoon ?></h2>
</div>
</div>
</div>

</div>

<div class="row mt-4">

<div class="col-md-6">
<div class="card bg-secondary shadow">
<div class="card-body text-center">
<h5>💰 Total Revenue</h5>
<h2>₹<?= number_format($totalRevenue,2) ?></h2>
</div>
</div>
</div>

<div class="col-md-6">
<div class="card bg-info shadow">
<div class="card-body text-center">
<h5>⏳ Pending Payments</h5>
<h2><?= $pendingPayment ?></h2>
</div>
</div>
</div>

</div>

<div class="card bg-dark border-secondary mt-4">

<div class="card-header">
<h4 class="mb-0">👥 Latest Users</h4>
</div>

<div class="table-responsive">

<table class="table table-dark table-hover table-bordered mb-0">

<thead>

<tr>
<th>ID</th>
<th>Phone</th>
<th>Username</th>
<th>Plan</th>
<th>Expiry</th>
</tr>

</thead>

<tbody>

<?php foreach($users as $u): ?>

<tr>

<td><?= $u['id'] ?></td>

<td><?= htmlspecialchars($u['user_id']) ?></td>

<td><?= htmlspecialchars($u['username']) ?></td>

<td><?= htmlspecialchars($u['plan_id']) ?></td>

<td><?= htmlspecialchars($u['expiry_date']) ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>
