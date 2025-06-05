<?php
// cv.php
$pageTitle = 'Antonín Jedlička - Životopis';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:ital,wght@0,100;0,300;0,400;0,500;0,600;0,700;1,100;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">

    <style>
        /* === CV SPECIFICKÉ STYLY === */
        article.blkt-cv {
            margin: 130px auto;
            max-width: 900px;
            width: 90%;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: zakladnianimace 1s ease-out forwards;
            overflow: hidden;
        }

        /* === HLAVIČKA CV === */
        .blkt-cv-hlavicka {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .blkt-cv-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid rgba(255,255,255,0.3);
            object-fit: cover;
            flex-shrink: 0;
        }

        .blkt-cv-info h1 {
            margin: 0 0 1rem 0;
            font-size: 2.5em;
            font-weight: 600;
        }

        .blkt-cv-kontakt {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .blkt-cv-kontakt li {
            margin: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1em;
        }

        .blkt-cv-kontakt .ikona {
            width: 20px;
            height: 20px;
            opacity: 0.8;
        }

        /* === OBSAH CV === */
        .blkt-cv-telo {
            padding: 3rem 2rem;
        }

        .blkt-cv-sekce {
            margin-bottom: 3rem;
            opacity: 0;
            transform: translateY(30px);
            animation: blkt-sekce-animace 0.8s ease-out forwards;
        }

        .blkt-cv-sekce:nth-child(1) { animation-delay: 0.2s; }
        .blkt-cv-sekce:nth-child(2) { animation-delay: 0.4s; }
        .blkt-cv-sekce:nth-child(3) { animation-delay: 0.6s; }
        .blkt-cv-sekce:nth-child(4) { animation-delay: 0.8s; }
        .blkt-cv-sekce:nth-child(5) { animation-delay: 1.0s; }

        @keyframes blkt-sekce-animace {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .blkt-cv-sekce h2 {
            color: #667eea;
            font-size: 1.8em;
            font-weight: 600;
            margin: 0 0 1.5rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #667eea;
            display: inline-block;
        }

        .blkt-cv-pozice {
            margin-bottom: 2.5rem;
            padding: 1.5rem;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .blkt-cv-pozice h3 {
            margin: 0 0 0.5rem 0;
            color: #333;
            font-size: 1.4em;
            font-weight: 600;
        }

        .blkt-cv-pozice .blkt-cv-firma {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .blkt-cv-pozice .blkt-cv-datum {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 1rem;
        }

        .blkt-cv-pozice .blkt-cv-popis {
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .blkt-cv-pozice ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        .blkt-cv-pozice li {
            margin: 0.5rem 0;
            line-height: 1.5;
        }

        /* === DOVEDNOSTI === */
        .blkt-cv-dovednosti {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .blkt-cv-dovednost-skupina {
            background: rgba(102, 126, 234, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .blkt-cv-dovednost-skupina h4 {
            margin: 0 0 1rem 0;
            color: #667eea;
            font-weight: 600;
        }

        .blkt-cv-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .blkt-cv-tag {
            background: #667eea;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
        }

        /* === VLASTNOSTI === */
        .blkt-cv-vlastnosti {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .blkt-cv-vlastnost {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
        }

        .blkt-cv-vlastnost h4 {
            margin: 0 0 0.5rem 0;
            font-weight: 600;
        }

        /* === RESPONZIVITA === */
        @media (max-width: 768px) {
            .blkt-cv-hlavicka {
                flex-direction: column;
                text-align: center;
                padding: 2rem 1rem;
            }

            .blkt-cv-avatar {
                width: 120px;
                height: 120px;
            }

            .blkt-cv-info h1 {
                font-size: 2em;
            }

            .blkt-cv-telo {
                padding: 2rem 1rem;
            }

            .blkt-cv-dovednosti {
                grid-template-columns: 1fr;
            }

            .blkt-cv-vlastnosti {
                grid-template-columns: 1fr;
            }

            .blkt-cv-obsah {
                margin: 160px auto 120px;
            }
        }
    </style>
</head>
<body>

<header class="blkt-hlavicka">
    <?php include __DIR__ . '/includes/header.php'; ?>
</header>

<main class="blkt-obsah-stranky">
    <article class="blkt-cv">
        <!-- HLAVIČKA CV -->
        <div class="blkt-cv-hlavicka">
            <img src="/media/autor.png" alt="Antonín Jedlička" class="blkt-cv-avatar">
            <div class="blkt-cv-info">
                <h1>Antonín Jedlička</h1>
                <ul class="blkt-cv-kontakt">
                    <li>
                        <span class="ikona">📍</span>
                        <span>Brno</span>
                    </li>
                    <li>
                        <span class="ikona">📞</span>
                        <span>+420 775 067 979</span>
                    </li>
                    <li>
                        <span class="ikona">✉️</span>
                        <span>antonin.jedlicka@outlook.cz</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- TĚLO CV -->
        <div class="blkt-cv-telo">
            <!-- PROFESNÍ ZKUŠENOSTI -->
            <section class="blkt-cv-sekce">
                <h2>Profesní zkušenosti</h2>

                <div class="blkt-cv-pozice">
                    <h3>Manažer zakázek/Marketing</h3>
                    <div class="blkt-cv-firma">ANETE spol. s r.o.</div>
                    <div class="blkt-cv-datum">10/2020 – 03/2025</div>
                    <div class="blkt-cv-popis">
                        Implementace stravovacího systému KREDIT (nemocnice, školy, podniky), školení a podpora uživatelů.
                    </div>
                    <strong>Hlavní činnosti:</strong>
                    <ul>
                        <li>Analýza stavu zákazníka a plánování implementace systému (MS Project)</li>
                        <li>Instalace, konfigurace a správa SQL Serveru, včetně přípravy vlastních scriptů dle potřeb zákazníka</li>
                        <li>Integrace s ostatními systémy (propojení s nemocničními systémy, platebními bránami, terminály)</li>
                        <li>Zpracování zákaznické dokumentace pro následnou podporu</li>
                        <li>Intenzivní uživatelská podpora při implementaci vzdáleně/osobně</li>
                        <li>Tvorba jednoduchých uživatelských manuálů</li>
                    </ul>
                    <strong>Marketing a interní podpora:</strong>
                    <ul>
                        <li>Správa obsahu webů, mailingy a kampaně (SmartEmailing)</li>
                        <li>Propagační materiály (Canva, InDesign)</li>
                        <li>Vývoj interních aplikací jako náhrada pro chybějící CMS (Power Apps, Power Automate)</li>
                        <li>Tvorba technické dokumentace (SharePoint/DevOps)</li>
                        <li>Spolupráce s AI nástroji pro automatizaci</li>
                        <li>Tvorba webů (PHP, JS, CSS, HTML)</li>
                        <li>Správa produktů M365</li>
                    </ul>
                </div>

                <div class="blkt-cv-pozice">
                    <h3>Produktový specialista</h3>
                    <div class="blkt-cv-firma">IReSoft s.r.o.</div>
                    <div class="blkt-cv-datum">04/2017 – 06/2020</div>
                    <div class="blkt-cv-popis">
                        Zajištění spokojenosti zákazníků se systémem CYGNUS 2, budování komunity, technická dokumentace.
                    </div>
                    <ul>
                        <li>Řešení kritických zákazníků</li>
                        <li>Tvorba obsáhlé nápovědy a správa fóra (WordPress, CSS, PHP)</li>
                        <li>Lektorování školení a online kurzů</li>
                        <li>Sledování spokojenosti zákazníků s produktem</li>
                    </ul>
                </div>

                <div class="blkt-cv-pozice">
                    <h3>Konzultant a analytik softwaru</h3>
                    <div class="blkt-cv-firma">IReSoft s.r.o.</div>
                    <div class="blkt-cv-datum">05/2013 – 03/2017</div>
                    <div class="blkt-cv-popis">
                        Školení uživatelů, podpora, tvorba podpůrných materiálů. Vedoucí týmu konzultantů hlavního produktu.
                    </div>
                </div>

                <div class="blkt-cv-pozice">
                    <h3>Retail obchodní zástupce</h3>
                    <div class="blkt-cv-firma">Vodafone CZ</div>
                    <div class="blkt-cv-datum">03/2011 – 04/2013</div>
                    <div class="blkt-cv-popis">
                        Prodej služeb a zařízení na značkové prodejně, péče o zákazníky.
                    </div>
                </div>

                <div class="blkt-cv-pozice">
                    <h3>Konzultant prodejny</h3>
                    <div class="blkt-cv-firma">Kiboon Electronics a.s. / Telefonica O2 CR</div>
                    <div class="blkt-cv-datum">2009 – 2011</div>
                    <div class="blkt-cv-popis">
                        Obsluha klientů, nabídka a realizace služeb O2.
                    </div>
                </div>

                <div class="blkt-cv-pozice">
                    <h3>Operátor telemarketingu</h3>
                    <div class="blkt-cv-firma">MediaServis s.r.o.</div>
                    <div class="blkt-cv-datum">07/2008 – 07/2009</div>
                    <div class="blkt-cv-popis">
                        Telefonická nabídka služeb a produktů zákazníkům.
                    </div>
                </div>
            </section>

            <!-- DOVEDNOSTI -->
            <section class="blkt-cv-sekce">
                <h2>Dovednosti a technologie</h2>
                <div class="blkt-cv-dovednosti">
                    <div class="blkt-cv-dovednost-skupina">
                        <h4>Webové technologie</h4>
                        <div class="blkt-cv-tags">
                            <span class="blkt-cv-tag">HTML</span>
                            <span class="blkt-cv-tag">CSS</span>
                            <span class="blkt-cv-tag">JavaScript</span>
                            <span class="blkt-cv-tag">PHP</span>
                            <span class="blkt-cv-tag">WordPress</span>
                        </div>
                    </div>
                    <div class="blkt-cv-dovednost-skupina">
                        <h4>Databáze & Analytics</h4>
                        <div class="blkt-cv-tags">
                            <span class="blkt-cv-tag">MS SQL Server</span>
                            <span class="blkt-cv-tag">Power Apps</span>
                            <span class="blkt-cv-tag">Power Automate</span>
                            <span class="blkt-cv-tag">SharePoint</span>
                            <span class="blkt-cv-tag">DevOps</span>
                        </div>
                    </div>
                    <div class="blkt-cv-dovednost-skupina">
                        <h4>Design & Grafika</h4>
                        <div class="blkt-cv-tags">
                            <span class="blkt-cv-tag">Canva</span>
                            <span class="blkt-cv-tag">InDesign</span>
                            <span class="blkt-cv-tag">Photoshop</span>
                        </div>
                    </div>
                    <div class="blkt-cv-dovednost-skupina">
                        <h4>AI & Automatizace</h4>
                        <div class="blkt-cv-tags">
                            <span class="blkt-cv-tag">ChatGPT</span>
                            <span class="blkt-cv-tag">Copilot</span>
                            <span class="blkt-cv-tag">MS Office</span>
                            <span class="blkt-cv-tag">MS Project</span>
                        </div>
                    </div>
                    <div class="blkt-cv-dovednost-skupina">
                        <h4>Marketing & Komunikace</h4>
                        <div class="blkt-cv-tags">
                            <span class="blkt-cv-tag">SmartEmailing</span>
                            <span class="blkt-cv-tag">M365 Admin</span>
                            <span class="blkt-cv-tag">Správa obsahu</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- VLASTNOSTI -->
            <section class="blkt-cv-sekce">
                <h2>Vlastnosti</h2>
                <div class="blkt-cv-vlastnosti">
                    <div class="blkt-cv-vlastnost">
                        <h4>🔄 Flexibilní</h4>
                        <p>Nejsem limitován rodinou či časem</p>
                    </div>
                    <div class="blkt-cv-vlastnost">
                        <h4>🚀 Nebojácný</h4>
                        <p>Jdu vždy naproti novinkám a inovacím</p>
                    </div>
                    <div class="blkt-cv-vlastnost">
                        <h4>💬 Komunikativní</h4>
                        <p>Otevřený a přímý přístup</p>
                    </div>
                </div>
            </section>

            <!-- JAZYKY -->
            <section class="blkt-cv-sekce">
                <h2>Jazyky</h2>
                <div class="blkt-cv-dovednosti">
                    <div class="blkt-cv-dovednost-skupina">
                        <h4>🇨🇿 Čeština</h4>
                        <p>Rodilý mluvčí</p>
                    </div>
                    <div class="blkt-cv-dovednost-skupina">
                        <h4>🇬🇧 Angličtina</h4>
                        <p>Mírně pokročilá</p>
                    </div>
                    <div class="blkt-cv-dovednost-skupina">
                        <h4>🇪🇸 Španělština</h4>
                        <p>Základní</p>
                    </div>
                </div>
            </section>

            <!-- VZDĚLÁNÍ A ZÁJMY -->
            <section class="blkt-cv-sekce">
                <h2>Vzdělání a zájmy</h2>
                <div class="blkt-cv-pozice">
                    <h3>Gymnázium Vyškov</h3>
                    <div class="blkt-cv-datum">2004 – 2008</div>
                    <div class="blkt-cv-popis">Všeobecné studium, maturita</div>
                </div>
                <div class="blkt-cv-pozice">
                    <h3>Zájmy</h3>
                    <div class="blkt-cv-tags">
                        <span class="blkt-cv-tag">Programování (PHP, JS)</span>
                        <span class="blkt-cv-tag">Grafika & Design</span>
                        <span class="blkt-cv-tag">Blogování</span>
                        <span class="blkt-cv-tag">Klavír & Varhany</span>
                        <span class="blkt-cv-tag">Umělá inteligence</span>
                    </div>
                </div>
            </section>
        </div>
    </article>
</main>

<footer class="blkt-paticka">
    <?php include __DIR__ . '/includes/footer.php'; ?>
</footer>

<?php include __DIR__ . '/includes/loader.php'; ?>

<script>
    // Plynulé scrollování pro kotvy
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

    // Animace sekcí při scrollu
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.blkt-cv-sekce').forEach(section => {
        observer.observe(section);
    });
</script>

</body>
</html>