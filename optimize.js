const sharp = require('sharp');
const glob = require('glob');

const [path] = process.argv.slice(2);

function optimizeAll(files, options = { quality: 80 }) {
  const { length } = files;
  let cores = sharp.concurrency();

  console.info(`Optimizing ${length} files with ${cores} cores...`);

  sharp.cache({ memory: 1000 });

  const next = () => optimize(files.shift(), options, file => {
    if (files.length) {
      const treated = length - files.length;

      process.stdout.clearLine();
      process.stdout.cursorTo(0);
      process.stdout.write(`File ${treated}/${length}: ${(treated/length * 100).toFixed(2)}%  -  ${file}`);

      next();
    } else {
      cores--;

      if (cores === 1) {
        process.stdout.clearLine();
        process.stdout.cursorTo(0);
        console.info('Done!');
      }
    }
  });

  for (let i = 0; i < cores; i++) {
    next();
  }
}

function optimize(file, options = {}, callback = undefined) {
  const write = (error, buffer) => sharp(buffer).toFile(file, () => callback(file));

  switch (file.split('.').pop()) {
    case 'jpeg':
    case 'jpg':
      return sharp(file).jpeg(options).toBuffer(write);

    case 'png':
      return sharp(file).png({ compressionLevel: 9, ...options }).toBuffer(write);

    case 'gif':
      return sharp(file).gif(options).toBuffer(write);

    case 'webp':
      return sharp(file).webp({ /*lossless: true, reductionEffort: 6,*/ ...options }).toBuffer(write);

    default:
      throw new Error(`Unsupported image type "${file}".`);
  }
}

glob(
  `${path}/**/*.@(png|jpg|jpeg|gif)`, //|webp
  { ignore: `${path}/download/**/*` },
  (error, files) => optimizeAll(files)
);
