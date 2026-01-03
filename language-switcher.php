<?php
// Include translations if not already included
if (!function_exists('t')) {
    include_once('translations.php');
}

$current_lang = getLang();
$current_page = $_SERVER['REQUEST_URI'];
$separator = strpos($current_page, '?') !== false ? '&' : '?';
?>
<style>
    .language-switcher {
        position: relative;
        display: inline-block;
        z-index: 1000;
    }
    
    .lang-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 700;
        font-size: 14px;
        color: #ffffff;
        text-decoration: none;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.2);
    }
    
    .lang-toggle:hover {
        background: linear-gradient(135deg, #5568d3 0%, #653a8f 100%);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.3);
        color: #ffffff;
    }
    
    .lang-toggle:active {
        transform: translateY(0);
    }
    
    .lang-icon {
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    .lang-toggle:hover .lang-icon {
        background: rgba(255, 255, 255, 0.35);
        transform: rotate(360deg) scale(1.1);
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.3);
    }
    
    .lang-text {
        font-weight: 700;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .lang-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%);
        backdrop-filter: blur(20px);
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        min-width: 180px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        border: 2px solid rgba(255, 255, 255, 0.3);
        overflow: hidden;
    }
    
    .language-switcher:hover .lang-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .lang-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        color: #ffffff;
        text-decoration: none;
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .lang-option:last-child {
        border-bottom: none;
    }
    
    .lang-option::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: linear-gradient(135deg, #ffffff 0%, rgba(255, 255, 255, 0.7) 100%);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }
    
    .lang-option:hover {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.1) 100%);
        padding-left: 22px;
        color: #ffffff;
    }
    
    .lang-option:hover::before {
        transform: scaleY(1);
    }
    
    .lang-option.active {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.15) 100%);
        color: #ffffff;
        font-weight: 700;
    }
    
    .lang-option-icon {
        font-size: 20px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .lang-option:hover .lang-option-icon {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }
    
    .lang-option-name {
        flex: 1;
        font-weight: 600;
        font-size: 14px;
    }
    
    .lang-check {
        color: #ffffff;
        font-size: 16px;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s ease;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .lang-option.active .lang-check {
        opacity: 1;
        transform: scale(1);
    }
    
    /* RTL Support */
    [dir="rtl"] .lang-dropdown {
        right: auto;
        left: 0;
    }
    
    [dir="rtl"] .lang-option::before {
        left: auto;
        right: 0;
    }
    
    [dir="rtl"] .lang-option:hover {
        padding-left: 18px;
        padding-right: 22px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .lang-toggle {
            padding: 6px 12px;
            font-size: 13px;
        }
        
        .lang-icon {
            width: 20px;
            height: 20px;
            font-size: 16px;
        }
        
        .lang-dropdown {
            min-width: 160px;
        }
        
        .lang-option {
            padding: 10px 15px;
        }
    }
</style>

<div class="language-switcher">
    <a href="#" class="lang-toggle">
        <span class="lang-icon"><?php echo $current_lang === 'ar' ? '🇸🇦' : '🇬🇧'; ?></span>
        <span class="lang-text"><?php echo $current_lang === 'ar' ? 'العربية' : 'English'; ?></span>
    </a>
    <div class="lang-dropdown">
        <a href="<?php echo $current_page . $separator . 'lang=ar'; ?>" class="lang-option <?php echo $current_lang === 'ar' ? 'active' : ''; ?>">
            <span class="lang-option-icon">🇸🇦</span>
            <span class="lang-option-name">العربية</span>
            <?php if ($current_lang === 'ar'): ?>
                <span class="lang-check">✓</span>
            <?php endif; ?>
        </a>
        <a href="<?php echo $current_page . $separator . 'lang=en'; ?>" class="lang-option <?php echo $current_lang === 'en' ? 'active' : ''; ?>">
            <span class="lang-option-icon">🇬🇧</span>
            <span class="lang-option-name">English</span>
            <?php if ($current_lang === 'en'): ?>
                <span class="lang-check">✓</span>
            <?php endif; ?>
        </a>
    </div>
</div>


