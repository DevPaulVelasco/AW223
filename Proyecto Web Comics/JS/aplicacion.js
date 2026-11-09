(function () {
  'use strict';

  function aplicarFiltros() {
    const camposBusqueda = document.querySelectorAll('.input-busqueda-pill');
    const consulta = camposBusqueda.length ? camposBusqueda[0].value.trim().toLowerCase() : '';
    const editorial = document.querySelector('[data-filter="editorial"]');
    const ano = document.querySelector('[data-filter="ano"]');
    const stockBajo = document.querySelector('[data-filter="stock-bajo"]');
    const rango = document.querySelector('[data-range-filter]');
    const desde = rango ? rango.querySelector('[name="desde"]').value : '';
    const hasta = rango ? rango.querySelector('[name="hasta"]').value : '';

    document.querySelectorAll('.table-data-row, .comic-card, .proveedor-card').forEach(function (elemento) {
      const texto = elemento.textContent.toLowerCase();
      const coincideTexto = !consulta || texto.includes(consulta);
      const coincideEditorial = !editorial || !editorial.value || elemento.dataset.editorial === editorial.value;
      const coincideAno = !ano || !ano.value || elemento.dataset.ano === ano.value;
      const coincideStock = !stockBajo || !stockBajo.checked || elemento.dataset.stockBajo === 'true';
      const fecha = elemento.dataset.fecha || '';
      const coincideDesde = !desde || !fecha || fecha >= desde;
      const coincideHasta = !hasta || !fecha || fecha <= hasta;

      elemento.hidden = !(coincideTexto && coincideEditorial && coincideAno && coincideStock && coincideDesde && coincideHasta);
    });

    document.querySelectorAll('.komix-table-container, .catalogo-grid, .proveedores-grid').forEach(function (contenedor) {
      const elementos = contenedor.querySelectorAll(':scope > .table-data-row, :scope > .comic-card, :scope > .proveedor-card');
      if (!elementos.length) return;

      let mensaje = contenedor.querySelector(':scope > .filter-empty-message');
      if (!mensaje) {
        mensaje = document.createElement('p');
        mensaje.className = 'filter-empty-message';
        mensaje.textContent = 'No se encontraron resultados.';
        contenedor.appendChild(mensaje);
      }
      mensaje.hidden = Array.from(elementos).some(function (elemento) {
        return !elemento.hidden;
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    const camposBusqueda = document.querySelectorAll('.input-busqueda-pill');
    camposBusqueda.forEach(function (campo) {
      campo.addEventListener('input', function () {
        aplicarFiltros();
      });
    });

    const menuToggle = document.querySelector('.menu-toggle');
    const menuSidebar = document.getElementById('app-sidebar');
    const menuBackdrop = document.querySelector('.sidebar-backdrop');

    function cerrarMenu() {
      document.body.classList.remove('menu-abierto');
      if (menuToggle) {
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.setAttribute('aria-label', 'Abrir menú de navegación');
      }
      if (menuBackdrop) menuBackdrop.hidden = true;
    }

    if (menuToggle && menuSidebar && menuBackdrop) {
      menuToggle.addEventListener('click', function () {
        const abierto = document.body.classList.toggle('menu-abierto');
        menuToggle.setAttribute('aria-expanded', String(abierto));
        menuToggle.setAttribute('aria-label', abierto ? 'Cerrar menú de navegación' : 'Abrir menú de navegación');
        menuBackdrop.hidden = !abierto;
      });

      menuBackdrop.addEventListener('click', cerrarMenu);
      menuSidebar.querySelectorAll('a').forEach(function (enlace) {
        enlace.addEventListener('click', cerrarMenu);
      });

      document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape') cerrarMenu();
      });
    }

    document.querySelectorAll('[data-filter], [data-filter="stock-bajo"]').forEach(function (filtro) {
      filtro.addEventListener('change', aplicarFiltros);
    });

    const rango = document.querySelector('[data-range-filter]');
    if (rango) {
      rango.addEventListener('submit', function (evento) {
        evento.preventDefault();
        aplicarFiltros();
        if (typeof KomixAlert !== 'undefined') KomixAlert.success('Filtro de fechas aplicado.');
      });
    }

    const notificaciones = document.querySelector('[data-action="notificaciones"]');
    if (notificaciones) {
      notificaciones.addEventListener('click', function () {
        if (typeof KomixAlert !== 'undefined') KomixAlert.info('No tienes notificaciones nuevas.');
      });
    }

    const perfil = document.querySelector('[data-action="perfil"]');
    if (perfil) {
      perfil.addEventListener('click', function () {
        const nombre = perfil.dataset.nombre || 'Usuario de demostración';
        const rol = perfil.dataset.rol || 'Vendedor';
        if (typeof KomixAlert !== 'undefined') KomixAlert.info(nombre + ' · Rol: ' + rol, 5000);
      });
    }

    const periodo = document.getElementById('dashboard-periodo');
    if (periodo) {
      const metricas = {
        mes: {
          valores: ['$45,000,000', '328 ventas', '$138.05'],
          series: [
            'M 60 140 Q 90 90 120 120 T 180 110 T 240 100 T 300 130 T 360 80 T 420 110 T 480 75 T 540 120 T 600 70 T 660 110 T 720 90 T 780 120 T 840 60',
            'M 60 150 Q 100 110 140 130 T 220 95 T 310 115 T 400 65 T 490 135 T 580 85 T 670 70 T 750 115 T 840 85'
          ]
        },
        semana: {
          valores: ['$12,000,000', '89 ventas', '$134.83'],
          series: [
            'M 60 135 Q 120 85 180 115 T 300 95 T 420 120 T 540 75 T 680 100 T 840 55',
            'M 60 150 Q 130 120 200 130 T 340 90 T 480 110 T 620 65 T 760 95 T 840 75'
          ]
        },
        ano: {
          valores: ['$320,000,000', '2,360 ventas', '$135.59'],
          series: [
            'M 60 145 Q 100 115 150 125 T 260 95 T 360 110 T 470 65 T 580 120 T 690 80 T 840 55',
            'M 60 155 Q 120 125 190 135 T 310 100 T 430 115 T 550 75 T 680 95 T 840 70'
          ]
        },
        todo: {
          valores: ['$720,000,000', '5,210 ventas', '$138.20'],
          series: [
            'M 60 135 Q 100 95 160 110 T 280 125 T 390 75 T 520 105 T 650 60 T 760 90 T 840 50',
            'M 60 155 Q 130 110 210 125 T 340 90 T 470 130 T 590 80 T 720 105 T 840 65'
          ]
        }
      };
      periodo.addEventListener('change', function () {
        const datos = metricas[periodo.value];
        const valores = datos.valores;
        document.querySelectorAll('[data-metrica]').forEach(function (elemento, indice) {
          elemento.textContent = valores[indice];
        });
        document.getElementById('serie-ventas-1').setAttribute('d', datos.series[0]);
        document.getElementById('serie-ventas-2').setAttribute('d', datos.series[1]);
        const dias = periodo.value === 'semana' ? 7 : 30;
        document.querySelectorAll('.etiqueta-dia').forEach(function (etiqueta, indice) {
          etiqueta.style.display = indice < dias ? '' : 'none';
          if (indice < dias) {
            etiqueta.textContent = String(indice + 1);
            etiqueta.setAttribute('x', String(60 + indice * (800 / (dias - 1))));
          }
        });
        const titulo = document.getElementById('titulo-grafico');
        if (titulo) titulo.textContent = 'Gráfico de Ventas (Ventas en el Tiempo) · ' + periodo.options[periodo.selectedIndex].text;
      });
    }

    aplicarFiltros();
  });
})();
