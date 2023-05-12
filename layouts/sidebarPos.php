<nav class="nav-sb">
    <div class="logo">
        <i class="bx bx-menu menu-icon"></i>
        <span class="logo-name">CORAVI</span>
    </div>
    <div class="button-modes">
        <input type="hidden" id="dark-mode">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cart">
            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
            </svg>
            <div id="art-count" data-count="<?php $totalItems = 0; if (isset($_SESSION['cart'])) { foreach ($_SESSION['cart'] as $item) {$totalItems += $item['quantity'];}} echo "<div id='art-count' data-count='$totalItems'>"; ?>">
                <?php
                $totalItems = 0;
                if (isset($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        $totalItems += $item['quantity'];
                    }
                }
                echo "($totalItems)";
                ?>
            </div>
        </button>
    </div>

    <div class="sidebar">
        <div class="logo">
            <i class="bx bx-menu menu-icon"></i>
            <span class="logo-name">CORAVI</span>
        </div>

        <div class="sidebar-content">
            <ul class="lists">
                <li class="list">
                    <a href="dashboard.php" class="nav-sb-link">
                        <i class="bx bx-home-alt icon"></i>
                        <span class="link">Dashboard</span>
                    </a>
                </li>
                <li class="list">
                    <a href="pos.php" class="nav-sb-link">
                        <i class="bx bx-cart icon"></i>
                        <span class="link">POS</span>
                    </a>
                </li>
                <li class="list">
                    <a href="productMenu.php" class="nav-sb-link">
                        <i class="bx bx-store-alt icon"></i>
                        <span class="link">Productos</span>
                    </a>
                </li>
                <li class="list">
                    <a href="clientMenu.php" class="nav-sb-link">
                        <i class="bx bx-user-circle icon"></i>
                        <span class="link">Clientes</span>
                    </a>
                </li>
                <li class="list">
                    <a href="clientReceipt.php" class="nav-sb-link">
                        <i class="bx bx-dollar-circle icon"></i>
                        <span class="link">Ventas</span>
                    </a>
                </li>
                <li class="list">
                    <a href="supplierMenu.php" class="nav-sb-link">
                        <i class="bx bx-box icon"></i>
                        <span class="link">Proovedores</span>
                    </a>
                </li>
                <li class="list">
                    <a href="suppPayments.php" class="nav-sb-link">
                        <i class="bx bx-edit icon"></i>
                        <span class="link">Pedidos</span>
                    </a>
                </li>
                <li class="list">
                    <a href="userMenu.php" class="nav-sb-link">
                        <i class="bx bx-user icon"></i>
                        <span class="link">Usuarios</span>
                    </a>
                </li>
            </ul>

            <ul class="listsE">
                <div class="bottom-cotent">
                    <li class="list">
                        <a href="configuration.php" class="nav-sb-link">
                            <i class="bx bx-cog icon"></i>
                            <span class="link">Ajustes</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href='db/exit.php' class="nav-sb-link">
                            <i class="bx bx-log-out icon"></i>
                            <span class="link">Salir</span>
                        </a>
                    </li>
                </div>
            </ul>
        </div>
    </div>
</nav>

<section class="overlay"></section>