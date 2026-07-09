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
 */
import { copyFileSync, mkdirSync, readFileSync, writeFileSync } from 'node:fs'
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

// Concatenate the runtime CSS: banner layout + Bootstrap-free switch styling +
// consent-message styling. Drop the bootstrap layer if your project uses Bootstrap.
const css = ['consentcontrol.main.css', 'consentcontrol.bootstrap.css', 'consentmessage.main.css']
    .map((file) => `/* ${file} */\n${readFileSync(resolve(src, file), 'utf8')}`)
    .join('\n\n')

writeFileSync(resolve(root, 'resources/dist/css/consent-control.css'), css)

console.log(`Synced consent-control assets from ${src}`)
