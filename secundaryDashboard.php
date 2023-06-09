<?php
session_start();
if (!isset($_SESSION['logged'])) {
    header('location: index.php');
}

date_default_timezone_set('America/Mexico_City');

//<!-- HTML HEADER -->
require('db/conection.php');

function getBestSellerP($conn)
{
    $sql = "SELECT p.productName, SUM(c.cReceiptQuantity) AS total_sold FROM clientreceiptproducts c JOIN products p ON c.cReceiptProductId = p.productId GROUP BY c.cReceiptProductId ORDER BY total_sold DESC LIMIT 3";
    $result = $conn->query($sql);
    $bestSellerProducts = array();
    while ($row = $result->fetch_assoc()) {
        $bestSellerProducts[] = $row;
    }
    return $bestSellerProducts;
}

function sellsDay($conn)
{
    $userId = $_SESSION['userId'];
    $sql = "SELECT SUM(cReceiptTotal) AS daily_sales FROM clientreceipts WHERE DATE(cReceiptDate) = CURDATE() AND cReceiptUserId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $sellsToday = $result->fetch_assoc();
    return $sellsToday;
}

function sellsMonth($conn)
{
    $userId = $_SESSION['userId'];
    $sql = "SELECT SUM(cReceiptTotal) AS monthly_sales FROM clientreceipts WHERE MONTH(cReceiptDate) = MONTH(CURDATE()) AND YEAR(cReceiptDate) = YEAR(CURDATE()) AND cReceiptUserId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $sellMonth = $result->fetch_assoc();
    return $sellMonth;
}

function nameUser($conn)
{
    $sql = "SELECT userName, userLastName FROM users WHERE userEmail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['currentEmail']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user;
}

function salesPerDay($conn)
{
    $year = date('Y');
    $month = date('m');
    $userId = $_SESSION['userId'];
    $query = "SELECT DAY(cReceiptDate) AS day, SUM(cReceiptPrice * cReceiptQuantity) AS total_sales FROM clientreceiptproducts JOIN clientreceipts ON clientreceiptproducts.cReceipReceiptId = clientreceipts.cReceiptId WHERE YEAR(cReceiptDate) = ? AND MONTH(cReceiptDate) = ? AND cReceiptUserId = ? GROUP BY day ORDER BY day;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $year, $month, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Crear un arreglo con todos los días del mes
    $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $labels = [];
    $data = array_fill(1, $numDays, 0);
    for ($i = 1; $i <= $numDays; $i++) {
        $labels[] = 'Día ' . $i;
    }

    // Llenar el arreglo con los datos de la consulta
    while ($row = $result->fetch_assoc()) {
        $data[$row['day']] = $row['total_sales'];
    }

    return ['labels' => $labels, 'data' => array_values($data)];
}

include("templates/header.php");
include('layouts/sidebar.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="base-dashboard">
    <div class="container-bottom-dashboard">
        <?php $user = nameUser($conn); ?>
        <h1>Bienvenido <?= htmlspecialchars($user['userName']) . ' ' . htmlspecialchars($user['userLastName']); ?></h1>
    </div>
    <div class="row row-cols-1 row-cols-md-2 g-4" style="margin: 0 5px 10px;">


    <?php if ($_SESSION['roleId'] == 2) : ?>

        <div class="col" style="width: 100%;">
            <div class="card" style="width: 100%;">
                <div class="card-header" style="text-align:middle;">
                    <h3 class="card-title">Tus ventas</h3>
                </div>
                <?php
                // Llamar a la función salesPerDay
                $result = salesPerDay($conn);

                // Obtener los datos para la gráfica
                $labels = $result['labels'];
                $data = $result['data'];
                ?>
                <canvas id="myChart"></canvas>
                <script>
                    // Datos para la gráfica
                    var labels = <?php echo json_encode($labels); ?>;
                    var data = <?php echo json_encode($data); ?>;

                    // Crear la gráfica
                    var ctx = document.getElementById('myChart').getContext('2d');
                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Ventas',
                                data: data,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                tension: 0.1
                            }]
                        },
                        options: {
                            aspectRatio: 2.7,
                            animation: {
                                duration: 2000,
                                easing: 'easeOutBounce'
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Total de ventas'
                                    }
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Ventas por día'
                                },
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                </script>
                <div class="card-footer" style="width: 100%;">
                    <h3 class="card-title">Grafica mensual</h3>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ventas de hoy</h3>
                </div>
                <div class="card-body">
                    <?php $sellsToday = sellsDay($conn); ?>
                    <h5 class="card-text">$<?= number_format($sellsToday['daily_sales'], 2) ?></h5>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ventas del mes</h3>
                </div>
                <div class="card-body">
                    <?php $sellsMonth = sellsMonth($conn); ?>
                    <h5 class="card-text">$<?= number_format($sellsMonth['monthly_sales'], 2) ?></h5>
                </div>
            </div>
        </div>

        <?php endif; ?>

        <div class="col" style="width: 100%;">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Productos más vendidos</h3>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr align="center" class="active">
                                    <th>Producto</th>
                                    <th>Ventas</th>
                                </tr>
                            </thead>
                            <tbody class="table-light">
                                <?php try { ?>
                                    <?php
                                    $bestSellerProducts = getBestSellerP($conn);
                                    foreach ($bestSellerProducts as $product) : ?>
                                        <tr align="center">
                                            <td><?= htmlspecialchars($product['productName']) ?></td>
                                            <td><?= htmlspecialchars($product['total_sold']) ?> Pzs</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php } catch (Exception $e) {
                                    $error_modal = true;
                                    $errormsg = $e->getMessage();
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HTML END BODY -->
    <?php include "templates/footer.php"; ?>