const sharp = require('sharp');
const glob = require('glob');

const [path] = process.argv.slice(2);
const png = file => sharp(file).png({ quality: 80, compressionLevel: 9 }).toBuffer().then(buffer => sharp(buffer).toFile(file));
const jpg = file => sharp(file).jpeg({ quality: 80 }).toBuffer().then(buffer => sharp(buffer).toFile(file));

glob(`${path}/**/*.png`, { ignore: `${path}/download/**/*.png` }, (error, files) => files.forEach(png));
glob(`${path}/**/*.jpg`, { ignore: `${path}/download/**/*.jpg` }, (error, files) => files.forEach(jpg));
