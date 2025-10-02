<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contatti - SportEvents</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', sans-serif;
            margin: 0;
            min-height: 100vh;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Includi navbar unificata -->
    <?php 
    require_once __DIR__ . '/components/navbar.php';
    renderNavbar('contact'); 
    ?>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary-600 to-primary-700 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">Contattaci</h1>
            <p class="text-xl md:text-2xl text-primary-100 max-w-3xl mx-auto">
                Siamo qui per aiutarti. Scrivici per qualsiasi domanda o richiesta di supporto
            </p>
        </div>
    </section>

    <!-- Contact Information & Form -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16">
                <!-- Contact Information -->
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Informazioni di Contatto</h2>
                    
                    <div class="space-y-8">
                        <!-- Address -->
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Sede Principale</h3>
                                <p class="text-gray-600 mt-1">
                                    Via Roma 123<br>
                                    20121 Milano, Italia
                                </p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Telefono</h3>
                                <p class="text-gray-600 mt-1">
                                    <a href="tel:+390212345678" class="hover:text-primary-600 transition-colors">+39 02 1234 5678</a><br>
                                    <span class="text-sm text-gray-500">Lun-Ven 9:00-18:00</span>
                                </p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Email</h3>
                                <p class="text-gray-600 mt-1">
                                    <a href="mailto:info@sportevents.com" class="hover:text-primary-600 transition-colors">info@sportevents.com</a><br>
                                    <a href="mailto:support@sportevents.com" class="hover:text-primary-600 transition-colors text-sm">support@sportevents.com</a>
                                </p>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 4v10a2 2 0 002 2h6a2 2 0 002-2V8M7 8H5a2 2 0 00-2 2v8a2 2 0 002 2h2m0-12h10m-5 4v4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Social Media</h3>
                                <div class="flex space-x-4 mt-2">
                                    <a href="#" class="text-gray-600 hover:text-primary-600 transition-colors">
                                        <span class="sr-only">Facebook</span>
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="text-gray-600 hover:text-primary-600 transition-colors">
                                        <span class="sr-only">Instagram</span>
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987c6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.15-3.316-.704c-.868-.555-1.297-1.297-1.297-2.231c0-.704.188-1.297.555-1.777c.368-.48.889-.704 1.555-.704c.297 0 .555.037.777.111c.222.074.407.185.555.333c.148.148.259.333.333.555c.074.222.111.48.111.777v.37c0 .297-.037.555-.111.777c-.074.222-.185.407-.333.555c-.148.148-.333.259-.555.333c-.222.074-.48.111-.777.111z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="text-gray-600 hover:text-primary-600 transition-colors">
                                        <span class="sr-only">Twitter</span>
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="mt-12 p-6 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-center mb-3">
                            <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-red-800">Emergenze durante gli Eventi</h3>
                        </div>
                        <p class="text-red-700 mb-2">
                            Per emergenze durante gli eventi sportivi, contatta:
                        </p>
                        <p class="text-red-800 font-semibold">
                            📞 <a href="tel:+393331234567" class="hover:underline">+39 333 123 4567</a><br>
                            📧 <a href="mailto:emergency@sportevents.com" class="hover:underline">emergency@sportevents.com</a>
                        </p>
                    </div>
                </div>

                <!-- Contact Form -->
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Scrivici</h2>
                    
                    <form id="contact-form" class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">Nome *</label>
                                <input type="text" id="nome" name="nome" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                                       placeholder="Il tuo nome">
                            </div>
                            <div>
                                <label for="cognome" class="block text-sm font-medium text-gray-700 mb-2">Cognome *</label>
                                <input type="text" id="cognome" name="cognome" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                                       placeholder="Il tuo cognome">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" id="email" name="email" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                                   placeholder="la.tua.email@esempio.com">
                        </div>

                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">Telefono</label>
                            <input type="tel" id="telefono" name="telefono"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
                                   placeholder="+39 333 123 4567">
                        </div>

                        <div>
                            <label for="argomento" class="block text-sm font-medium text-gray-700 mb-2">Argomento *</label>
                            <select id="argomento" name="argomento" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                                <option value="">Seleziona un argomento</option>
                                <option value="supporto_tecnico">Supporto Tecnico</option>
                                <option value="organizzazione_eventi">Organizzazione Eventi</option>
                                <option value="iscrizione_problemi">Problemi di Iscrizione</option>
                                <option value="pagamenti">Pagamenti e Rimborsi</option>
                                <option value="partnership">Partnership e Collaborazioni</option>
                                <option value="feedback">Feedback e Suggerimenti</option>
                                <option value="altro">Altro</option>
                            </select>
                        </div>

                        <div>
                            <label for="messaggio" class="block text-sm font-medium text-gray-700 mb-2">Messaggio *</label>
                            <textarea id="messaggio" name="messaggio" rows="6" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors resize-y"
                                      placeholder="Scrivi qui il tuo messaggio..."></textarea>
                        </div>

                        <!-- Privacy Checkbox -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="privacy" name="privacy" type="checkbox" required
                                       class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="privacy" class="text-gray-700">
                                    Accetto l'<a href="/privacy" class="text-primary-600 hover:text-primary-700 underline">informativa sulla privacy</a> e autorizzo il trattamento dei miei dati personali. *
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" 
                                    class="w-full bg-primary-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Invia Messaggio
                                </span>
                            </button>
                        </div>
                    </form>

                    <!-- Success Message (Hidden by default) -->
                    <div id="success-message" class="hidden mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-green-800">
                                Grazie per il tuo messaggio! Ti risponderemo entro 24 ore.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Domande Frequenti</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Trova rapidamente le risposte alle domande più comuni
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-xl p-8 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Come posso iscrivermi a un evento?</h3>
                    <p class="text-gray-600">
                        È semplicissimo! Trova l'evento che ti interessa nella sezione Eventi, clicca su "Dettagli" e poi su "Iscriviti Ora". Dovrai creare un account se non lo hai già fatto.
                    </p>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-xl p-8 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Posso annullare la mia iscrizione?</h3>
                    <p class="text-gray-600">
                        Sì, puoi annullare la tua iscrizione fino a 48 ore prima dell'evento. I rimborsi vengono elaborati entro 5-7 giorni lavorativi.
                    </p>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white rounded-xl p-8 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Come posso organizzare un evento?</h3>
                    <p class="text-gray-600">
                        Registrati come organizzatore e accedi alla dashboard dedicata. Potrai creare eventi, gestire iscrizioni e monitorare le statistiche.
                    </p>
                </div>

                <!-- FAQ Item 4 -->
                <div class="bg-white rounded-xl p-8 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Che documenti servono per partecipare?</h3>
                    <p class="text-gray-600">
                        Generalmente è richiesto un certificato medico per attività sportiva non agonistica. Alcuni eventi potrebbero richiedere documenti aggiuntivi.
                    </p>
                </div>

                <!-- FAQ Item 5 -->
                <div class="bg-white rounded-xl p-8 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Come funzionano i pagamenti?</h3>
                    <p class="text-gray-600">
                        Accettiamo carte di credito, PayPal e bonifici bancari. Tutti i pagamenti sono sicuri e protetti da crittografia SSL.
                    </p>
                </div>

                <!-- FAQ Item 6 -->
                <div class="bg-white rounded-xl p-8 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Riceverò aggiornamenti sull'evento?</h3>
                    <p class="text-gray-600">
                        Sì! Ti invieremo email con tutti gli aggiornamenti importanti, mappe del percorso e informazioni logistiche prima dell'evento.
                    </p>
                </div>
            </div>

            <div class="text-center mt-12">
                <p class="text-gray-600 mb-4">Non hai trovato la risposta che cercavi?</p>
                <a href="#contact-form" class="bg-primary-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors">
                    Contattaci Direttamente
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="text-2xl font-bold mb-4">SportEvents</div>
                    <p class="text-gray-400">La piattaforma italiana per eventi sportivi di ogni livello.</p>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Prodotto</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/events" class="hover:text-white transition-colors">Eventi</a></li>
                        <li><a href="/organizers" class="hover:text-white transition-colors">Per Organizzatori</a></li>
                        <li><a href="/athletes" class="hover:text-white transition-colors">Per Atleti</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Azienda</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/about" class="hover:text-white transition-colors">Chi Siamo</a></li>
                        <li><a href="/contact" class="hover:text-white transition-colors">Contatti</a></li>
                        <li><a href="/careers" class="hover:text-white transition-colors">Careers</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Supporto</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/help" class="hover:text-white transition-colors">Centro Assistenza</a></li>
                        <li><a href="/contact" class="hover:text-white transition-colors">Contattaci</a></li>
                        <li><a href="/privacy" class="hover:text-white transition-colors">Privacy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 SportEvents. Tutti i diritti riservati.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            // Add mobile menu functionality here
            console.log('Mobile menu clicked');
        });

        // Contact form handling
        document.getElementById('contact-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic form validation
            const nome = document.getElementById('nome').value.trim();
            const cognome = document.getElementById('cognome').value.trim();
            const email = document.getElementById('email').value.trim();
            const argomento = document.getElementById('argomento').value;
            const messaggio = document.getElementById('messaggio').value.trim();
            const privacy = document.getElementById('privacy').checked;
            
            if (!nome || !cognome || !email || !argomento || !messaggio || !privacy) {
                alert('Per favore compila tutti i campi obbligatori e accetta la privacy policy.');
                return;
            }
            
            // Simulate form submission
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            submitButton.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Invio in corso...</span>';
            submitButton.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                // Show success message
                document.getElementById('success-message').classList.remove('hidden');
                
                // Reset form
                this.reset();
                
                // Reset button
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
                
                // Scroll to success message
                document.getElementById('success-message').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }, 2000);
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
