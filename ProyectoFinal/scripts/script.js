document.addEventListener('DOMContentLoaded', function () {
    const notificaciones = document.querySelectorAll('.estado-notificacion');
    notificaciones.forEach(function(estadoNotificacion) {
        const estadoTexto = estadoNotificacion.textContent.trim();
        
        if (estadoTexto.includes('Aceptada')) {
            estadoNotificacion.classList.add('aceptada');
        } else if (estadoTexto.includes('Pendiente')) {
            estadoNotificacion.classList.add('pendiente');
        } else if (estadoTexto.includes('Rechazada')) {
            estadoNotificacion.classList.add('rechazada');
        }
    });
});

document.getElementById('tipo_notificacion').addEventListener('change', function() {
    if (this.value === 'Valoraciones') {
        window.location.href = 'valoraciones.php';
    } else {
        document.getElementById('notificacionesForm').submit();
    }
});