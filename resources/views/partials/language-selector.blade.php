<!-- Google Translate Element placeholder (hidden) -->
<div id="google_translate_element" style="display: none !important;"></div>

<!-- Beautiful Custom Language Dropdown Container -->
<div id="custom-language-selector" class="custom-lang-container" style="position: relative; display: inline-block; z-index: 1000; font-family: 'Inter', sans-serif; margin-right: 8px;">
    <button type="button" id="lang-dropdown-btn" class="lang-btn" style="
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 253, 246, 0.94);
        border: 1px solid rgba(35, 72, 23, 0.24);
        color: #1e241d;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    ">
        <i class="fa-solid fa-globe" style="color: #234817; font-size: 14px;"></i>
        <span id="current-lang-label">English</span>
        <i class="fa-solid fa-chevron-down" style="font-size: 10px; color: #667060; transition: transform 0.2s;"></i>
    </button>

    <!-- Dropdown Menu -->
    <div id="lang-dropdown-menu" class="lang-menu" style="
        display: none;
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        width: 240px;
        background: rgba(255, 253, 246, 0.98);
        border: 1px solid rgba(35, 72, 23, 0.24);
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(56, 48, 33, 0.15);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 6px;
        animation: langSlideIn 0.2s ease;
        text-align: left;
    ">
        <!-- Scrollable content -->
        <div style="max-h: 280px; overflow-y: auto; scrollbar-width: thin; padding-right: 4px;">
            <a href="javascript:void(0);" onclick="selectLanguage('en', 'English')" class="lang-item" data-lang="en">
                <span class="lang-native">English</span> <span class="lang-trans">(English)</span>
            </a>
            <a href="javascript:void(0);" onclick="selectLanguage('hi', 'हिन्दी')" class="lang-item" data-lang="hi">
                <span class="lang-native">हिन्दी</span> <span class="lang-trans">(Hindi)</span>
            </a>
            <a href="javascript:void(0);" onclick="selectLanguage('mwr', 'मारवाड़ी')" class="lang-item" data-lang="mwr">
                <span class="lang-native">मारवाड़ी</span> <span class="lang-trans">(Rajasthani)</span>
            </a>
            <a href="javascript:void(0);" onclick="selectLanguage('bho', 'भोजपुरी')" class="lang-item" data-lang="bho">
                <span class="lang-native">भोजपुरी</span> <span class="lang-trans">(Bhojpuri)</span>
            </a>
            <a href="javascript:void(0);" onclick="selectLanguage('mr', 'मराठी')" class="lang-item" data-lang="mr">
                <span class="lang-native">मराठी</span> <span class="lang-trans">(Marathi)</span>
            </a>
            <a href="javascript:void(0);" onclick="selectLanguage('ta', 'தமிழ்')" class="lang-item" data-lang="ta">
                <span class="lang-native">தமிழ்</span> <span class="lang-trans">(Tamil)</span>
            </a>
            <a href="javascript:void(0);" onclick="selectLanguage('te', 'తెలుగు')" class="lang-item" data-lang="te">
                <span class="lang-native">తెలుగు</span> <span class="lang-trans">(Telugu)</span>
            </a>
            <a href="javascript:void(0);" onclick="selectLanguage('bn', 'বাংলা')" class="lang-item" data-lang="bn">
                <span class="lang-native">বাংলা</span> <span class="lang-trans">(Bengali)</span>
            </a>
            <a href="javascript:void(0);" onclick="selectLanguage('gu', 'ગુજરાતી')" class="lang-item" data-lang="gu">
                <span class="lang-native">ગુજરાતી</span> <span class="lang-trans">(Gujarati)</span>
            </a>
            <a href="javascript:void(0);" onclick="selectLanguage('kn', 'ಕನ್ನಡ')" class="lang-item" data-lang="kn">
                <span class="lang-native">ಕನ್ನಡ</span> <span class="lang-trans">(Kannada)</span>
            </a>
            <a href="javascript:void(0);" onclick="selectLanguage('ml', 'മലയാളം')" class="lang-item" data-lang="ml">
                <span class="lang-native">മലയാളം</span> <span class="lang-trans">(Malayalam)</span>
            </a>
            <a href="javascript:void(0);" onclick="selectLanguage('pa', 'ਪੰਜਾਬੀ')" class="lang-item" data-lang="pa">
                <span class="lang-native">ਪੰਜਾਬੀ</span> <span class="lang-trans">(Punjabi)</span>
            </a>
        </div>
    </div>
</div>

<style>
    /* Custom CSS to prevent dependencies on framework grid/flex styles */
    .lang-btn:hover {
        transform: translateY(-1px);
        border-color: #234817 !important;
        box-shadow: 0 4px 12px rgba(35, 72, 23, 0.12) !important;
        background-color: #fffdf6 !important;
    }
    .lang-btn:focus {
        outline: none !important;
    }
    .lang-item {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 8px 12px !important;
        margin: 2px 0 !important;
        border-radius: 10px !important;
        color: #5f685b !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        transition: all 0.15s ease !important;
        background: transparent !important;
        border: none !important;
        cursor: pointer !important;
    }
    .lang-item:hover {
        background: rgba(35, 72, 23, 0.08) !important;
        color: #234817 !important;
        padding-left: 16px !important;
    }
    .lang-item.active {
        background: rgba(35, 72, 23, 0.12) !important;
        color: #234817 !important;
        font-weight: 700 !important;
    }
    .lang-native {
        font-weight: 600 !important;
    }
    .lang-trans {
        font-size: 11px !important;
        opacity: 0.65 !important;
        margin-left: 6px !important;
    }
    
    /* Animations */
    @keyframes langSlideIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Absolute hide for Google Translate widgets and browser header additions */
    body {
        top: 0px !important;
    }
    .skiptranslate, iframe.skiptranslate, .goog-te-banner-frame, .goog-te-banner {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        width: 0 !important;
    }
    iframe#\:2\.container, iframe[id*="translate"], .goog-te-spinner-pos {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        width: 0 !important;
    }
    .goog-tooltip, .goog-tooltip:hover {
        display: none !important;
        visibility: hidden !important;
    }
    .goog-text-highlight {
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }
    /* Disable translate option top notification bar standard in chrome */
    html {
        content: normal !important;
    }
</style>

<script type="text/javascript">
    // Cookie management helpers
    function setGoogleTransCookie(langCode) {
        // Format: /sourceLang/targetLang (e.g., /en/hi)
        const cookieVal = '/en/' + langCode;
        
        // Clear existing cookies first
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + window.location.hostname + ";";
        
        if (langCode !== 'en') {
            // Set for current path and root path
            document.cookie = "googtrans=" + cookieVal + "; path=/;";
            document.cookie = "googtrans=" + cookieVal + "; path=/; domain=" + window.location.hostname + ";";
            
            // Fallback for subdomains/local developments
            const parts = window.location.hostname.split('.');
            if (parts.length > 2) {
                const domain = '.' + parts.slice(-2).join('.');
                document.cookie = "googtrans=" + cookieVal + "; path=/; domain=" + domain + ";";
            }
        }
    }

    // Define Google Translate initialization function globally
    window.googleTranslateElementInit = function() {
        new google.translate.TranslateElement({
            pageLanguage: 'en',
            includedLanguages: 'en,hi,mr,ta,te,bho,mwr,bn,gu,kn,ml,pa',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
            autoDisplay: false
        }, 'google_translate_element');
    };

    // Toggle Dropdown menu visibility
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('lang-dropdown-btn');
        const menu = document.getElementById('lang-dropdown-menu');
        const chevron = btn.querySelector('.fa-chevron-down');

        if (btn && menu) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = menu.style.display === 'block';
                menu.style.display = isOpen ? 'none' : 'block';
                chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
            });

            document.addEventListener('click', function(e) {
                const container = document.getElementById('custom-language-selector');
                if (container && !container.contains(e.target)) {
                    menu.style.display = 'none';
                    chevron.style.transform = 'rotate(0deg)';
                }
            });
        }

        // Initialize from localStorage on page load
        const storedLang = localStorage.getItem('preferred_language') || 'en';
        const storedLabel = localStorage.getItem('preferred_language_label') || 'English';
        
        updateUI(storedLang, storedLabel);

        // Ensure googtrans cookie is in sync with localStorage
        const cookies = document.cookie.split(';');
        let googtransCookie = '';
        for (let i = 0; i < cookies.length; i++) {
            let c = cookies[i].trim();
            if (c.indexOf('googtrans=') === 0) {
                googtransCookie = decodeURIComponent(c.substring('googtrans='.length, c.length));
                break;
            }
        }

        const expectedValue = '/en/' + storedLang;
        if (storedLang !== 'en' && googtransCookie !== expectedValue) {
            setGoogleTransCookie(storedLang);
            window.location.reload();
        } else if (storedLang === 'en' && googtransCookie !== '') {
            setGoogleTransCookie('en');
            window.location.reload();
        }
    });

    // Select language function
    window.selectLanguage = function(langCode, label) {
        localStorage.setItem('preferred_language', langCode);
        localStorage.setItem('preferred_language_label', label);
        
        // Write the googtrans cookie immediately
        setGoogleTransCookie(langCode);
        
        // Reload page to force the Google Translate script to translate 100% of the content on load
        window.location.reload();
    };

    // Update UI Elements
    function updateUI(langCode, label) {
        const labelEl = document.getElementById('current-lang-label');
        if (labelEl) {
            labelEl.textContent = label;
        }

        // Add active class in list items
        document.querySelectorAll('.lang-item').forEach(item => {
            if (item.getAttribute('data-lang') === langCode) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }
</script>

<!-- Load Google Translate script asynchronously -->
<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async defer></script>

