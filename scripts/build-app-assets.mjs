/**
 * Genera les imatges base de l'app (icona i splash) a partir del logo vectorial.
 *
 *   node scripts/build-app-assets.mjs
 *
 * Surten a assets/, que es el que llegeix @capacitor/assets per generar totes
 * les mides d'iOS i Android:
 *
 *   npx capacitor-assets generate --ios
 *   npx capacitor-assets generate --android
 *
 * Important: l'icona d'iOS ha de ser OPACA. Si te canal alpha, App Store
 * Connect rebutja el binari amb "Invalid Image - can't contain an alpha
 * channel or transparency".
 */
import sharp from 'sharp';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { mkdirSync } from 'node:fs';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const logo = join(root, 'resources/images/logo.svg');
const outDir = join(root, 'assets');

// brand-black del disseny de l'app (tailwind.config.js)
const BG = { r: 10, g: 10, b: 10, alpha: 1 };

/**
 * Retalla el logo just al seu contingut visible.
 *
 * No fem servir sharp().trim(): amb aquest SVG no retallava res i el logo
 * quedava descentrat dins la icona. Calculem la capsa a partir del canal alpha,
 * que es deterministic i no depen de la versio de sharp.
 */
async function renderTrimmedLogo(density) {
    const base = sharp(logo, { density });
    const { data, info } = await base.png().raw().toBuffer({ resolveWithObject: true });

    const { width, height, channels } = info;
    let minX = width, minY = height, maxX = -1, maxY = -1;

    for (let y = 0; y < height; y++) {
        for (let x = 0; x < width; x++) {
            // Ultim byte del pixel = alpha (o el color si no hi ha alpha)
            const alpha = channels === 4 ? data[(y * width + x) * channels + 3] : 255;
            if (alpha > 8) {
                if (x < minX) minX = x;
                if (x > maxX) maxX = x;
                if (y < minY) minY = y;
                if (y > maxY) maxY = y;
            }
        }
    }

    if (maxX < 0) throw new Error('El logo s\'ha rasteritzat buit: revisa ' + logo);

    return sharp(logo, { density })
        .png()
        .extract({ left: minX, top: minY, width: maxX - minX + 1, height: maxY - minY + 1 })
        .toBuffer();
}

/**
 * @param {string} out       fitxer de sortida
 * @param {number} size      costat del quadrat en px
 * @param {number} logoRatio quant del costat ocupa el logo (0-1)
 */
async function compose(out, size, logoRatio) {
    // density alta: el SVG es rasteritza net encara que despres l'ampliem
    const trimmed = await renderTrimmedLogo(900);

    const box = Math.round(size * logoRatio);
    const scaled = await sharp(trimmed)
        .resize({ width: box, height: box, fit: 'inside', withoutEnlargement: false })
        .png()
        .toBuffer();

    const { width, height } = await sharp(scaled).metadata();

    const composed = await sharp({ create: { width: size, height: size, channels: 4, background: BG } })
        .composite([{
            input: scaled,
            left: Math.round((size - width) / 2),
            top: Math.round((size - height) / 2),
        }])
        .png()
        .toBuffer();

    // Segona passada a proposit: sharp aplica flatten ABANS de composite dins la
    // mateixa pipeline, per tant no treia l'alpha. Sense aquest pas, App Store
    // Connect rebutja el binari per icona amb transparencia.
    await sharp(composed)
        .flatten({ background: BG })
        .removeAlpha()
        .png({ compressionLevel: 9 })
        .toFile(out);

    const meta = await sharp(out).metadata();
    if (meta.hasAlpha) throw new Error(`${out} encara te canal alpha`);

    console.log(`  ${out.replace(root, '.')}  ${size}x${size}  alpha=${meta.hasAlpha}`);
}

mkdirSync(outDir, { recursive: true });

console.log('Generant imatges base de Clutch Control...');

// Icona: el logo ocupa el 68% per deixar aire dins la mascara arrodonida d'iOS.
await compose(join(outDir, 'icon.png'), 1024, 0.68);

// Splash: 2732x2732 perque cobreixi qualsevol pantalla en qualsevol orientacio.
// El logo hi va petit; es una pantalla de carrega, no un cartell.
await compose(join(outDir, 'splash.png'), 2732, 0.30);
await compose(join(outDir, 'splash-dark.png'), 2732, 0.30);

console.log('Fet. Ara: npx capacitor-assets generate --ios');
