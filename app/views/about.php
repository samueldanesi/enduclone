<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Siamo - SportEvents</title>
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
    renderNavbar('about'); 
    ?>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary-600 to-primary-700 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">Chi Siamo</h1>
            <p class="text-xl md:text-2xl text-primary-100 max-w-3xl mx-auto">
                La piattaforma italiana che connette atleti, organizzatori e appassionati di sport
            </p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">La Nostra Missione</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Rendere lo sport accessibile a tutti, creando un ponte digitale tra organizzatori e partecipanti per eventi sportivi di ogni livello e disciplina.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-8 bg-gray-50 rounded-xl">
                    <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Comunità Sportiva</h3>
                    <p class="text-gray-600">Creiamo connessioni autentiche tra atleti, organizzatori e appassionati di sport di tutta Italia.</p>
                </div>

                <div class="text-center p-8 bg-gray-50 rounded-xl">
                    <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Qualità e Sicurezza</h3>
                    <p class="text-gray-600">Garantiamo standard elevati nella gestione degli eventi e nella protezione dei dati dei nostri utenti.</p>
                </div>

                <div class="text-center p-8 bg-gray-50 rounded-xl">
                    <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Innovazione</h3>
                    <p class="text-gray-600">Utilizziamo le tecnologie più avanzate per semplificare l'organizzazione e la partecipazione agli eventi sportivi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">La Nostra Storia</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        SportEvents nasce dalla passione di un gruppo di sviluppatori e atleti che hanno identificato la necessità di una piattaforma dedicata alla gestione degli eventi sportivi in Italia.
                    </p>
                    <p class="text-lg text-gray-600 mb-6">
                        Dopo anni di esperienza nell'organizzazione di gare e competizioni, abbiamo capito quanto fosse complesso coordinare iscrizioni, comunicazioni e logistica. Da qui l'idea di creare una soluzione digitale che semplifichi la vita a organizzatori e partecipanti.
                    </p>
                    <p class="text-lg text-gray-600">
                        Oggi SportEvents è la piattaforma di riferimento per migliaia di eventi sportivi, dalle competizioni amatoriali ai campionati professionali.
                    </p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">I Nostri Numeri</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-600 mb-2 counter" data-target="15000">0</div>
                            <div class="text-gray-600">Eventi Organizzati</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-600 mb-2 counter" data-target="50000">0</div>
                            <div class="text-gray-600">Atleti Registrati</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-600 mb-2 counter" data-target="1200">0</div>
                            <div class="text-gray-600">Organizzatori</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-600 mb-2 counter" data-target="95">0</div>
                            <div class="text-gray-600">% Soddisfazione</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Il Nostro Percorso</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Dalla piccola startup alla piattaforma leader nel settore degli eventi sportivi
                </p>
            </div>

            <div class="relative">
                <!-- Timeline line -->
                <div class="absolute left-1/2 transform -translate-x-1/2 w-1 h-full bg-primary-200"></div>

                <!-- Timeline items -->
                <div class="space-y-12">
                    <!-- Item 1 -->
                    <div class="relative flex items-center justify-between">
                        <div class="w-5/12 text-right pr-8">
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <div class="text-primary-600 font-bold text-lg mb-2">2020</div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-3">L'Idea</h3>
                                <p class="text-gray-600">Nasce l'idea di creare una piattaforma unica per unire organizzatori e partecipanti di eventi sportivi.</p>
                            </div>
                        </div>
                        <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-primary-600 rounded-full border-4 border-white shadow-lg"></div>
                        <div class="w-5/12"></div>
                    </div>

                    <!-- Item 2 -->
                    <div class="relative flex items-center justify-between">
                        <div class="w-5/12"></div>
                        <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-primary-600 rounded-full border-4 border-white shadow-lg"></div>
                        <div class="w-5/12 text-left pl-8">
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <div class="text-primary-600 font-bold text-lg mb-2">2021</div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-3">Primo Lancio</h3>
                                <p class="text-gray-600">Lancio della versione beta con i primi 100 eventi e 1,000 utenti registrati.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="relative flex items-center justify-between">
                        <div class="w-5/12 text-right pr-8">
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <div class="text-primary-600 font-bold text-lg mb-2">2022</div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-3">Crescita Rapida</h3>
                                <p class="text-gray-600">Raggiungiamo 5,000 eventi organizzati e 20,000 utenti attivi. Introduciamo i pagamenti online.</p>
                            </div>
                        </div>
                        <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-primary-600 rounded-full border-4 border-white shadow-lg"></div>
                        <div class="w-5/12"></div>
                    </div>

                    <!-- Item 4 -->
                    <div class="relative flex items-center justify-between">
                        <div class="w-5/12"></div>
                        <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-primary-600 rounded-full border-4 border-white shadow-lg"></div>
                        <div class="w-5/12 text-left pl-8">
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <div class="text-primary-600 font-bold text-lg mb-2">2023</div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-3">Espansione Nazionale</h3>
                                <p class="text-gray-600">Partnership con federazioni sportive e copertura in tutte le regioni italiane.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="relative flex items-center justify-between">
                        <div class="w-5/12 text-right pr-8">
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <div class="text-primary-600 font-bold text-lg mb-2">2024</div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-3">Leadership di Mercato</h3>
                                <p class="text-gray-600">Diventiamo la piattaforma #1 in Italia con oltre 10,000 eventi e 40,000 utenti.</p>
                            </div>
                        </div>
                        <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-primary-600 rounded-full border-4 border-white shadow-lg"></div>
                        <div class="w-5/12"></div>
                    </div>

                    <!-- Item 6 -->
                    <div class="relative flex items-center justify-between">
                        <div class="w-5/12"></div>
                        <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-gradient-to-r from-primary-600 to-yellow-500 rounded-full border-4 border-white shadow-lg"></div>
                        <div class="w-5/12 text-left pl-8">
                            <div class="bg-gradient-to-br from-primary-50 to-yellow-50 p-6 rounded-xl shadow-lg border-2 border-primary-200">
                                <div class="text-primary-600 font-bold text-lg mb-2">2025</div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-3">Il Futuro</h3>
                                <p class="text-gray-600">Continuiamo a innovare con AI, realtà aumentata e espansione internazionale.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">I Nostri Valori</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Principi che guidano ogni nostra decisione e azione
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Passione</h3>
                    <p class="text-gray-600">Amiamo lo sport e crediamo nel suo potere di unire le persone e migliorare la vita.</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Fiducia</h3>
                    <p class="text-gray-600">Costruiamo relazioni basate sulla trasparenza e sull'affidabilità.</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Innovazione</h3>
                    <p class="text-gray-600">Cerchiamo sempre nuove soluzioni per migliorare l'esperienza sportiva.</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Inclusività</h3>
                    <p class="text-gray-600">Lo sport è per tutti, indipendentemente dal livello, età o background.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Il Nostro Team</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Un gruppo di professionisti appassionati di sport e tecnologia
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full mx-auto mb-6 flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">MC</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Marco Conti</h3>
                    <p class="text-primary-600 mb-3">CEO & Founder</p>
                    <p class="text-gray-600">Ex-atleta professionista con 15 anni di esperienza nell'organizzazione di eventi sportivi.</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full mx-auto mb-6 flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">LR</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Laura Rossi</h3>
                    <p class="text-primary-600 mb-3">CTO</p>
                    <p class="text-gray-600">Ingegnere software specializzata in piattaforme web scalabili e user experience.</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-lg text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full mx-auto mb-6 flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">AB</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Andrea Bianchi</h3>
                    <p class="text-primary-600 mb-3">Head of Marketing</p>
                    <p class="text-gray-600">Esperto di marketing digitale nel settore sportivo con focus su community building.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="py-20 bg-primary-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Vuoi Saperne di Più?</h2>
            <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
                Contattaci per scoprire come SportEvents può aiutarti a organizzare eventi sportivi di successo
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="mailto:info@sportevents.com" class="bg-white text-primary-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Contattaci
                </a>
                <a href="/register" class="bg-primary-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary-800 transition-colors border border-primary-500">
                    Inizia Ora
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
                        <li><a href="/careers" class="hover:text-white transition-colors">Careers</a></li>
                        <li><a href="/press" class="hover:text-white transition-colors">Stampa</a></li>
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

        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('.counter');
            const speed = 200; // Animation speed

            counters.forEach(counter => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const inc = target / speed;

                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(() => animateCounters(), 1);
                } else {
                    counter.innerText = target.toLocaleString();
                }
            });
        }

        // Intersection Observer for counter animation
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe the stats section
        const statsSection = document.querySelector('.counter').closest('.bg-white');
        if (statsSection) {
            observer.observe(statsSection);
        }

        // Timeline animation on scroll
        const timelineItems = document.querySelectorAll('.relative.flex.items-center');
        const timelineObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '0';
                    entry.target.style.transform = 'translateY(50px)';
                    entry.target.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, 100);
                }
            });
        }, { threshold: 0.3 });

        timelineItems.forEach(item => {
            timelineObserver.observe(item);
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
