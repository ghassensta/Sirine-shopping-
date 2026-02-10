{{-- Bouton WhatsApp flottant - Composant réutilisable --}}
{{-- Utilisation : @include('layouts.components.whatsapp-button') --}}

@once

<style>
/* =======================================
   BOUTON WHATSAPP FLOTTANT
   ======================================= */

.whatsapp-float-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 62px;
  height: 62px;
  background-color: #25D366;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15), 0 2px 6px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  z-index: 10000;
  opacity: 0;
  transform: translateY(20px) scale(0.8);
  animation: whatsapp-appear 0.6s ease-out 1.5s forwards;
  text-decoration: none;
  color: white;
  cursor: pointer;
}

.whatsapp-float-btn:hover {
  background-color: #20b958;
  transform: scale(1.15);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2), 0 4px 12px rgba(0, 0, 0, 0.15);
}

.whatsapp-float-btn:active {
  transform: scale(1.08);
}

.whatsapp-float-btn:focus {
  outline: 2px solid #25D366;
  outline-offset: 2px;
  background-color: #20b958;
}

.whatsapp-icon {
  display: block;
  color: white;
  width: 24px;
  height: 24px;
  transition: color 0.3s ease;
}

/* Animation d'apparition progressive */
@keyframes whatsapp-appear {
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Animation pulse discrète toutes les 7 secondes */
@keyframes whatsapp-pulse {
  0%, 100% {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15), 0 2px 6px rgba(0, 0, 0, 0.1);
  }
  50% {
    box-shadow: 0 4px 16px rgba(37, 211, 102, 0.3), 0 2px 8px rgba(37, 211, 102, 0.2);
  }
}

.whatsapp-float-btn {
  animation: whatsapp-appear 0.6s ease-out 1.5s forwards, whatsapp-pulse 7s ease-in-out infinite 4s;
}

/* Responsive : mobile */
@media (max-width: 768px) {
  .whatsapp-float-btn {
    bottom: 20px;
    right: 20px;
    width: 56px;
    height: 56px;
  }

  .whatsapp-icon {
    width: 22px;
    height: 22px;
  }
}

/* Support iOS Safari et navigation mobile */
@media (max-width: 768px) and (orientation: landscape) {
  .whatsapp-float-btn {
    bottom: 15px;
  }
}

/* Haute résolution */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .whatsapp-float-btn {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2), 0 2px 6px rgba(0, 0, 0, 0.15);
  }
}
</style>

@endonce

{{-- Bouton WhatsApp HTML --}}
<a href="https://wa.me/21626686286?text=Bonjour%2C%20je%20vous%20contacte%20depuis%20le%20site.%20Je%20souhaiterais%20des%20renseignements%20sur%20vos%20produits%20ou%20services.%20Merci%20%21"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="Contacter nous sur WhatsApp"
   title="Discuter sur WhatsApp - Service client"
   class="whatsapp-float-btn">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="whatsapp-icon">
    <path d="M16.004 0C7.174 0 0 7.176 0 16c0 2.87.756 5.63 2.168 8.088L.208 32l8.12-2.144C11.392 31.024 13.66 32 16.004 32 24.834 32 32 24.824 32 16S24.834 0 16.004 0zm9.176 22.464c-.384.96-2.112 1.824-2.784 1.92-.672.096-1.472.192-4.8-1.024-4.224-1.536-6.912-5.376-7.104-5.568-.192-.192-1.536-2.016-1.536-3.84 0-1.824.96-2.688 1.344-3.072.384-.384.96-.48 1.344-.384.192 0 .384 0 .576.096.192.096.288.288.288.576 0 .192-.096.384-.192.576-.672 1.92-1.056 3.84-.96 4.128.192.672.864 1.344 1.536 1.728.672.384 3.84 1.728 4.512 1.824.672.096 1.056-.096 1.44-.672.384-.576.864-1.344.864-1.344.192-.192.384-.288.672-.192.288.096 1.824.864 1.824.864.192.096.288.288.288.48-.096.672-.384 1.44-.768 2.112z" fill="currentColor"/>
  </svg>
</a>
