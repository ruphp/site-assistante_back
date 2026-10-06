const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

const root = path.resolve(__dirname, '..');
const logo = fs.readFileSync(path.join(root, 'web', 'img', 'sitewidget-logo.svg'), 'utf8');
const logoData = Buffer.from(logo).toString('base64');

const card = `
<svg width="1200" height="630" viewBox="0 0 1200 630" xmlns="http://www.w3.org/2000/svg">
  <rect width="1200" height="630" fill="#F8F7FF"/>
  <rect x="0" y="0" width="18" height="630" fill="#7C3AED"/>
  <rect x="72" y="64" width="1056" height="502" fill="#FFFFFF" stroke="#E3DFF7" stroke-width="2"/>

  <image href="data:image/svg+xml;base64,${logoData}" x="94" y="90" width="48" height="56"/>
  <text x="160" y="118" fill="#211A52" font-family="Arial, sans-serif" font-size="30" font-weight="700">SiteWidget</text>
  <text x="94" y="259" fill="#211A52" font-family="Arial, sans-serif" font-size="55" font-weight="700">Онлайн-поддержка</text>
  <text x="94" y="326" fill="#211A52" font-family="Arial, sans-serif" font-size="55" font-weight="700">на сайт</text>
  <text x="96" y="392" fill="#615B78" font-family="Arial, sans-serif" font-size="25">Push-уведомления в приложении Android</text>
  <text x="96" y="429" fill="#615B78" font-family="Arial, sans-serif" font-size="25">Ответы из приложения Android или Telegram</text>

  <rect x="96" y="482" width="283" height="54" fill="#7C3AED"/>
  <text x="125" y="517" fill="#FFFFFF" font-family="Arial, sans-serif" font-size="21" font-weight="700">10 дней бесплатно</text>

  <rect x="844" y="176" width="204" height="236" fill="#F1EEFF"/>
  <image href="data:image/svg+xml;base64,${logoData}" x="875" y="191" width="142" height="164"/>
  <circle cx="1015" cy="391" r="39" fill="#FFC72C"/>
  <path d="M998 391l11 11 23-27" fill="none" stroke="#211A52" stroke-width="8" stroke-linecap="square" stroke-linejoin="miter"/>
</svg>`;

sharp(Buffer.from(card))
    .png({compressionLevel: 9})
    .toFile(path.join(root, 'web', 'img', 'sitewidget-og.png'))
    .then(() => console.log('Generated web/img/sitewidget-og.png'))
    .catch((error) => {
        console.error(error);
        process.exitCode = 1;
    });
