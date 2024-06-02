<div class="sidebar">
    <div class="logo-details">
        <img src="\img\descarga.png" alt="TeLoCambio Logo" class="icon">
        <div class="logo_name">TeLoCambio</div>
        <i class='bx bx-menu' id="btn"></i>
    </div>
    <ul class="nav-list">
        <li>
            <a href="adminMenu.php">
                <i class='bx bx-home' type="solid"></i>
                <span class="links_name">Menu Principal</span>
            </a>
            <span class="tooltip">Menu Principal</span>
        </li>
        <li>
            <a href="altaUsuarios.php">
                <i class='bx bxs-user' type="solid"></i>
                <span class="links_name">Alta nuevos usuarios</span>
            </a>
            <span class="tooltip">Alta nuevos usuarios</span>
        </li>
        <li>
            <a href="crearTurnos.php">
                <i class='bx bx-calendar'></i>
                <span class="links_name">Crear turnos</span>
            </a>
            <span class="tooltip">Crear turnos</span>
        </li>
        <li>
            <a href="departamentos.php">
                <i class='bx bx-data' type="solid"></i>
                <span class="links_name">Departamentos</span>
            </a>
            <span class="tooltip">Departamentos</span>
        </li>
        <li>
            <a href="empleados.php">
                <i class='bx bx-sitemap' type="solid"></i>
                <span class="links_name">Empleados</span>
            </a>
            <span class="tooltip">Empleados</span>
        </li>
        <li>
            <a href="valoracionesAdmin.php">
                <i class='bx bx-star'></i>
                <span class="links_name">Calificaciones</span>
            </a>
            <span class="tooltip">Calificaciones</span>
        </li>
        <li>
            <a href="gestorEventos.php">
                <i class='bx bx-blanket'></i>
                <span class="links_name">Eventos</span>
            </a>
            <span class="tooltip">Eventos</span>
        </li>
        <li>
            <a href="notificacionesAdmin.php">
                <i class='bx bx-carousel'></i>
                <span class="links_name">Historial intercambios</span>
            </a>
            <span class="tooltip">Historial intercambios</span>
        </li>
        <li class="profile">
            <div class="profile-details">
                <i class='bx bx-plus-medical'></i>
                <div class="name_job">
                <?php
                if (isset($_SESSION["usuario"])) {
                    $usuario = $_SESSION["usuario"];
                    echo "<p class='name'>$usuario</p>";
                }
                ?>
                <div class="job">Administrador</div>
                </div>
                <a href="../cerrarSesion.php" title="Cerrar sesión">
                    <i class='bx bx-log-out' id="log_out"></i>
                </a>
            </div>
        </li>
    </ul>
</div>
<script>
let sidebar = document.querySelector(".sidebar");
let closeBtn = document.querySelector("#btn");
let searchBtn = document.querySelector(".bx-search");

closeBtn.addEventListener("click", () => {
    sidebar.classList.toggle("open");
    menuBtnChange();
});

searchBtn.addEventListener("click", () => {
    sidebar.classList.toggle("open");
    menuBtnChange();
});

function menuBtnChange() {
    if (sidebar.classList.contains("open")) {
        closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");
    } else {
        closeBtn.classList.replace("bx-menu-alt-right", "bx-menu");
    }
}
</script>