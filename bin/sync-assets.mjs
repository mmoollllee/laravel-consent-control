/*
 * Vendors the built `consent-control` runtime into resources/dist so the package
 * works without a Node/Vite build step (published to public/ via vendor:publish).
 *
 * Usage:
 *   node bin/sync-assets.mjs
 *   CONSENT_CONTROL_SRC=/path/to/consent-control/dist node bin/sync-assets.mjs
 *
 * Defaults to a sibling checkout at ../consent-control. Build that package first
 * (`npm run build` there) so dist/ is current.
 *
 * Output:
 *   dist/js/consent-control.js    – the runtime (npm bundle.min.js)
 *   dist/css/consent-message.css  – overlay styling for blocked content; always
 *                                   loaded by <x-consent-control-scripts>
 *   dist/css/consent-control.css  – banner fallback for projects WITHOUT Tailwind
 *                                   (from resources/css/consent-control-fallback.css);
 *                                   the banner Blade itself is Tailwind-styled.
 */
import { copyFileSync, mkdirSync } from 'node:fs'
import { dirname, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..')
const src = process.env.CONSENT_CONTROL_SRC || resolve(root, '../consent-control/dist')

mkdirSync(resolve(root, 'resources/dist/js'), { recursive: true })
mkdirSync(resolve(root, 'resources/dist/css'), { recursive: true })

copyFileSync(
    resolve(src, 'bundle.min.js'),
    resolve(root, 'resources/dist/js/consent-control.js'),
)

copyFileSync(
    resolve(src, 'consentmessage.main.css'),
    resolve(root, 'resources/dist/css/consent-message.css'),
)

copyFileSync(
    resolve(root, 'resources/css/consent-control-fallback.css'),
    resolve(root, 'resources/dist/css/consent-control.css'),
)

console.log(`Synced consent-control assets from ${src}`)
