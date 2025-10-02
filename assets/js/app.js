// Funzioni utili per SportEvents
class SportEvents {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupFormValidation();
    }

    setupEventListeners() {
        // Menu mobile toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');
        
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }

        // Smooth scrolling per i link anchor
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Gestione upload file
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', this.handleFileUpload);
        });
    }

    setupFormValidation() {
        // Validazione email
        const emailInputs = document.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            input.addEventListener('blur', this.validateEmail);
        });

        // Validazione telefono
        const phoneInputs = document.querySelectorAll('input[name="phone"], input[name="cellulare"]');
        phoneInputs.forEach(input => {
            input.addEventListener('blur', this.validatePhone);
        });

        // Validazione password
        const passwordInputs = document.querySelectorAll('input[type="password"]');
        passwordInputs.forEach(input => {
            input.addEventListener('input', this.validatePassword);
        });
    }

    validateEmail(e) {
        const email = e.target.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValid = emailRegex.test(email);
        
        SportEvents.showFieldValidation(e.target, isValid, 'Inserisci un indirizzo email valido');
    }

    validatePhone(e) {
        const phone = e.target.value;
        const phoneRegex = /^[+]?[\d\s\-\(\)]{8,}$/;
        const isValid = phoneRegex.test(phone);
        
        SportEvents.showFieldValidation(e.target, isValid, 'Inserisci un numero di telefono valido');
    }

    validatePassword(e) {
        const password = e.target.value;
        const isValid = password.length >= 8;
        
        SportEvents.showFieldValidation(e.target, isValid, 'La password deve contenere almeno 8 caratteri');
    }

    static showFieldValidation(field, isValid, message) {
        // Rimuovi messaggi di errore esistenti
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }

        // Aggiorna lo stile del campo
        field.classList.remove('error', 'success');
        field.classList.add(isValid ? 'success' : 'error');

        // Aggiungi messaggio di errore se necessario
        if (!isValid && message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }
    }

    handleFileUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Controlla dimensione file (5MB max)
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('Il file è troppo grande. Dimensione massima: 5MB');
            e.target.value = '';
            return;
        }

        // Controlla tipo file per certificati medici
        if (e.target.name === 'certificato_medico') {
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Formato file non supportato. Usa PDF, JPG o PNG');
                e.target.value = '';
                return;
            }
        }

        // Mostra preview per immagini
        if (file.type.startsWith('image/')) {
            SportEvents.showImagePreview(e.target, file);
        }
    }

    static showImagePreview(input, file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = input.parentNode.querySelector('.file-preview');
            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'file-preview';
                input.parentNode.appendChild(preview);
            }
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px;">`;
        };
        reader.readAsDataURL(file);
    }

    // Funzioni AJAX
    static async apiCall(url, method = 'GET', data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            }
        };

        if (data) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            return await response.json();
        } catch (error) {
            console.error('Errore API:', error);
            return { success: false, message: 'Errore di connessione' };
        }
    }

    // Notifiche toast
    static showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        
        const container = document.querySelector('.toast-container') || SportEvents.createToastContainer();
        container.appendChild(toast);

        // Animazione di entrata
        setTimeout(() => toast.classList.add('show'), 100);

        // Rimozione automatica
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    static createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }

    // Formattazione date
    static formatDate(date, locale = 'it-IT') {
        return new Date(date).toLocaleDateString(locale, {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    // Formattazione valuta
    static formatCurrency(amount, currency = 'EUR') {
        return new Intl.NumberFormat('it-IT', {
            style: 'currency',
            currency: currency
        }).format(amount);
    }
}

// Inizializza l'applicazione quando il DOM è pronto
document.addEventListener('DOMContentLoaded', () => {
    new SportEvents();
});

// CSS aggiuntivo per validazione e toast (da aggiungere al CSS)
const additionalCSS = `
.form-input.error {
    border-color: var(--error-color);
}

.form-input.success {
    border-color: var(--success-color);
}

.field-error {
    color: var(--error-color);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
}

.toast {
    background: white;
    border-left: 4px solid var(--primary-color);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    padding: 1rem;
    margin-bottom: 1rem;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    max-width: 300px;
}

.toast.show {
    transform: translateX(0);
}

.toast-success {
    border-left-color: var(--success-color);
}

.toast-error {
    border-left-color: var(--error-color);
}

.toast-warning {
    border-left-color: var(--warning-color);
}
`;

// Aggiungi CSS aggiuntivo
const style = document.createElement('style');
style.textContent = additionalCSS;
document.head.appendChild(style);
