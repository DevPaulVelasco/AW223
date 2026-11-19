(function () {
  'use strict';

  function textoCelda(celda) {
    const control = celda.querySelector('input[type="checkbox"]');
    if (control) return control.checked ? 'Activo' : 'Inactivo';

    const etiqueta = celda.querySelector('[aria-label]');
    if (etiqueta) return etiqueta.getAttribute('aria-label').trim();

    return celda.innerText.replace(/\s+/g, ' ').trim();
  }

  function filasDeTablaDiv(contenedor, soloStockBajo) {
    const encabezados = Array.from(contenedor.querySelectorAll(':scope > .table-header-row > div'));
    const columnas = encabezados
      .map(function (celda, indice) {
        return { nombre: textoCelda(celda), indice: indice };
      })
      .filter(function (columna) {
        return columna.nombre && columna.nombre.toLocaleLowerCase() !== 'acciones:';
      });

    const filas = Array.from(contenedor.querySelectorAll(':scope > .table-data-row'))
      .filter(function (fila) {
        return !fila.hidden &&
          !fila.querySelector('.filter-empty-message') &&
          (!soloStockBajo || fila.dataset.stockBajo === 'true');
      })
      .map(function (fila) {
        const celdas = Array.from(fila.children).filter(function (celda) {
          return !celda.classList.contains('table-actions') && !celda.hasAttribute('data-export-ignore');
        });
        return columnas.map(function (columna) {
          return textoCelda(celdas[columna.indice] || document.createElement('div'));
        });
      });

    return { columnas: columnas.map(function (columna) { return columna.nombre; }), filas: filas };
  }

  function filasDeTablaHtml(tabla) {
    const encabezados = Array.from(tabla.querySelectorAll('thead th')).map(textoCelda);
    const filas = Array.from(tabla.querySelectorAll('tbody tr'))
      .filter(function (fila) {
        return !fila.hidden && !fila.hasAttribute('data-dashboard-empty') && !fila.querySelector('.filter-empty-message');
      })
      .map(function (fila) {
        return Array.from(fila.querySelectorAll('td')).map(textoCelda);
      });

    return { columnas: encabezados, filas: filas };
  }

  function filasDeTarjetas(contenedor) {
    const tarjetasProveedor = Array.from(contenedor.querySelectorAll(':scope > .proveedor-card'))
      .filter(function (tarjeta) { return !tarjeta.hidden; });
    if (tarjetasProveedor.length || contenedor.classList.contains('proveedores-grid')) {
      return {
        columnas: ['Proveedor', 'Contacto', 'Teléfono', 'Correo'],
        filas: tarjetasProveedor.map(function (tarjeta) {
          return [
            tarjeta.querySelector('.proveedor-nombre')?.innerText.trim() || '',
            ...Array.from(tarjeta.querySelectorAll('.proveedor-detalle')).map(function (detalle) {
              return detalle.innerText.replace(/^[^:]+:\s*/, '').trim();
            })
          ];
        })
      };
    }

    const comics = Array.from(contenedor.querySelectorAll(':scope > .comic-card'))
      .filter(function (tarjeta) { return !tarjeta.hidden; });
    return {
      columnas: ['Producto', 'Precio'],
      filas: comics.map(function (tarjeta) {
        return [
          tarjeta.querySelector('.comic-card-title')?.innerText.trim() || '',
          tarjeta.querySelector('.comic-card-price')?.innerText.trim() || ''
        ];
      })
    };
  }

  function escaparCSV(valor) {
    let texto = String(valor ?? '').replace(/\r?\n/g, ' ').trim();
    if (/^[=+\-@]/.test(texto)) texto = "'" + texto;
    return '"' + texto.replace(/"/g, '""') + '"';
  }

  function descargarCSV(contenedor, nombreArchivo, soloStockBajo) {
    let datos;
    const tablaDiv = contenedor.querySelector(':scope > .table-header-row');
    const tablaHtml = contenedor.matches('table') ? contenedor : contenedor.querySelector('table');

    if (tablaDiv) {
      datos = filasDeTablaDiv(contenedor, soloStockBajo);
    } else if (tablaHtml) {
      datos = filasDeTablaHtml(tablaHtml);
    } else {
      datos = filasDeTarjetas(contenedor);
    }

    if (!datos.columnas.length || !datos.filas.length) {
      if (typeof KomixAlert !== 'undefined') KomixAlert.warning('No hay datos visibles para descargar.');
      else window.alert('No hay datos visibles para descargar.');
      return;
    }

    const contenido = [datos.columnas, ...datos.filas]
      .map(function (fila) { return fila.map(escaparCSV).join(','); })
      .join('\r\n');
    const archivo = new Blob(['\uFEFF', contenido], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(archivo);
    const enlace = document.createElement('a');
    enlace.href = url;
    enlace.download = nombreArchivo;
    document.body.appendChild(enlace);
    enlace.click();
    enlace.remove();
    window.setTimeout(function () {
      URL.revokeObjectURL(url);
    }, 1000);
  }

  document.addEventListener('click', function (evento) {
    const boton = evento.target.closest('[data-export-target]');
    if (!boton) return;

    const contenedor = document.querySelector(boton.dataset.exportTarget);
    if (!contenedor) {
      console.error('No se encontró el contenido para exportar:', boton.dataset.exportTarget);
      if (typeof KomixAlert !== 'undefined') KomixAlert.error('No se encontró la lista para descargar.');
      else window.alert('No se encontró la lista para descargar.');
      return;
    }

    descargarCSV(
      contenedor,
      boton.dataset.exportFilename || 'komix-datos.csv',
      boton.dataset.exportOnly === 'stock-bajo'
    );
  });
})();
