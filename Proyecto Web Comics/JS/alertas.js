(function (global) {
  'use strict';

  let temporizadorToast;

  const KomixAlert = {
    toast: function (mensaje, tipo = 'info', duracion = 3500) {
      let toast = document.getElementById('komix-toast');
      if (!toast) {
        toast = document.createElement('div');
        toast.id = 'komix-toast';
        toast.className = 'komix-toast';
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        document.body.appendChild(toast);
      }

      toast.className = `komix-toast komix-toast-${tipo}`;
      toast.textContent = String(mensaje).replace(/<[^>]*>/g, '');
      clearTimeout(temporizadorToast);
      temporizadorToast = setTimeout(function () {
        toast.remove();
      }, Math.min(Math.max(duracion, 1000), 5000));
    },

    confirm: function (opciones) {
      const config = Object.assign(
        {
          titulo: '¿Estás seguro?',
          mensaje: 'Esta acción no se puede deshacer.',
          tipo: 'danger',
          confirmText: 'Sí, confirmar',
          cancelText: 'Cancelar',
          onConfirm: null,
          onCancel: null,
        },
        opciones
      );

      const modalAnterior = document.getElementById('komix-confirm-modal');
      if (modalAnterior) modalAnterior.remove();

      const overlay = document.createElement('div');
      overlay.id = 'komix-confirm-modal';
      overlay.className = 'komix-modal-alert-overlay';
      overlay.setAttribute('role', 'presentation');

      const tarjeta = document.createElement('div');
      tarjeta.className = 'komix-modal-alert-card';
      tarjeta.setAttribute('role', 'dialog');
      tarjeta.setAttribute('aria-modal', 'true');
      tarjeta.setAttribute('aria-labelledby', 'komix-confirm-title');
      tarjeta.setAttribute('aria-describedby', 'komix-confirm-message');

      const titulo = document.createElement('h2');
      titulo.id = 'komix-confirm-title';
      titulo.className = 'komix-alert-title';
      titulo.textContent = config.titulo;

      const texto = document.createElement('p');
      texto.id = 'komix-confirm-message';
      texto.className = 'komix-alert-body';
      texto.textContent = String(config.mensaje).replace(/<[^>]*>/g, '');

      const acciones = document.createElement('div');
      acciones.className = 'komix-alert-footer';

      const cancelar = document.createElement('button');
      cancelar.type = 'button';
      cancelar.className = 'komix-btn-alert cancel';
      cancelar.textContent = config.cancelText;

      const confirmar = document.createElement('button');
      confirmar.type = 'button';
      confirmar.className = `komix-btn-alert confirm ${config.tipo}`;
      confirmar.textContent = config.confirmText;

      acciones.append(cancelar, confirmar);
      tarjeta.append(titulo, texto, acciones);
      overlay.appendChild(tarjeta);
      document.body.appendChild(overlay);

      let cerrado = false;
      const cerrarModal = function (aceptado) {
        if (cerrado) return;
        cerrado = true;
        document.removeEventListener('keydown', teclaEscape);
        overlay.remove();

        const callback = aceptado ? config.onConfirm : config.onCancel;
        if (typeof callback === 'function') callback();
      };

      const teclaEscape = function (evento) {
        if (evento.key === 'Escape') cerrarModal(false);
      };

      cancelar.addEventListener('click', function () {
        cerrarModal(false);
      });
      confirmar.addEventListener('click', function () {
        cerrarModal(true);
      });
      overlay.addEventListener('click', function (evento) {
        if (evento.target === overlay) cerrarModal(false);
      });
      document.addEventListener('keydown', teclaEscape);
      cancelar.focus();
      return true;
    },

    // Atajos rápidos
    success: function (msg, duracion) {
      this.toast(msg, 'success', duracion);
    },
    error: function (msg, duracion) {
      this.toast(msg, 'error', duracion);
    },
    warning: function (msg, duracion) {
      this.toast(msg, 'warning', duracion);
    },
    info: function (msg, duracion) {
      this.toast(msg, 'info', duracion);
    },
  };

  // Exponer globalmente
  global.KomixAlert = KomixAlert;

  // Interceptar o proveer window.alerta / window.showKomixAlert
  global.showKomixAlert = function (msg, tipo = 'info') {
    KomixAlert.toast(msg, tipo);
  };
})(typeof window !== 'undefined' ? window : this);