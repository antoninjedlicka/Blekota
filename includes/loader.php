<!-- loader.php -->
<link rel="stylesheet" href="/css/loader.css">

<div id="blkt-loader" class="blkt-loader">
    <div class="blkt-loader-wrapper">
        <div class="blkt-loader-logo">
            <svg width="120" height="120" viewBox="0 0 120 120">
                <defs>
                    <linearGradient id="loader-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#3498db;stop-opacity:1">
                            <animate attributeName="stop-color" values="#3498db;#5dade2;#3498db" dur="3s" repeatCount="indefinite" />
                        </stop>
                        <stop offset="100%" style="stop-color:#5dade2;stop-opacity:1">
                            <animate attributeName="stop-color" values="#5dade2;#3498db;#5dade2" dur="3s" repeatCount="indefinite" />
                        </stop>
                    </linearGradient>
                    <filter id="loader-glow">
                        <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
                        <feMerge>
                            <feMergeNode in="coloredBlur"/>
                            <feMergeNode in="SourceGraphic"/>
                        </feMerge>
                    </filter>
                </defs>

                <!-- Vnější rotující kruh -->
                <circle cx="60" cy="60" r="50" fill="none" stroke="url(#loader-gradient)" stroke-width="2" opacity="0.3"/>

                <!-- Tři rotující kruhy -->
                <g id="rotating-circles">
                    <circle cx="60" cy="20" r="8" fill="url(#loader-gradient)" filter="url(#loader-glow)">
                        <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" repeatCount="indefinite" />
                    </circle>
                    <circle cx="95" cy="60" r="8" fill="url(#loader-gradient)" filter="url(#loader-glow)">
                        <animate attributeName="opacity" values="0.3;1;0.3" dur="1.5s" repeatCount="indefinite" />
                    </circle>
                    <circle cx="25" cy="60" r="8" fill="url(#loader-gradient)" filter="url(#loader-glow)">
                        <animate attributeName="opacity" values="1;0.3;1" dur="1.5s" repeatCount="indefinite" />
                    </circle>
                    <animateTransform
                            attributeName="transform"
                            attributeType="XML"
                            type="rotate"
                            from="0 60 60"
                            to="360 60 60"
                            dur="2s"
                            repeatCount="indefinite"/>
                </g>

                <!-- Střední pulzující kruh -->
                <circle cx="60" cy="60" r="25" fill="none" stroke="url(#loader-gradient)" stroke-width="3" filter="url(#loader-glow)">
                    <animate attributeName="r" values="25;30;25" dur="1s" repeatCount="indefinite" />
                    <animate attributeName="opacity" values="0.5;1;0.5" dur="1s" repeatCount="indefinite" />
                </circle>

                <!-- Logo text -->
                <text x="60" y="65" text-anchor="middle" font-family="'Signika Negative', sans-serif" font-size="16" font-weight="600" fill="url(#loader-gradient)">
                    BLKT
                </text>
            </svg>
        </div>
        <div class="blkt-loader-text" id="blkt-loader-text">
            <span class="blkt-loading-text">Načítám</span>
            <span class="blkt-loading-dots">
                <span>.</span>
                <span>.</span>
                <span>.</span>
            </span>
        </div>
        <div class="blkt-loader-progress">
            <div class="blkt-loader-progress-bar"></div>
        </div>
    </div>
</div>

<script src="/js/loader.js" defer></script>
<script src="/js/intro.js" defer></script>