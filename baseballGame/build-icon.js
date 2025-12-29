const fs = require('fs');
const path = require('path');
const toIco = require('to-ico');
const Jimp = require('jimp');

(async () => {
  const inputPath = path.join(__dirname, 'build', 'favicon.png');
  const outputPath = path.join(__dirname, 'build', 'favicon.ico');

  const image = await Jimp.read(inputPath);
  const sizes = [16, 24, 32, 48, 64, 128, 256];
  const buffers = [];

  for (let size of sizes) {
    const buf = await image.clone().resize(size, size).getBufferAsync(Jimp.MIME_PNG);
    buffers.push(buf);
  }

  const ico = await toIco(buffers);
  fs.writeFileSync(outputPath, ico);
  console.log('ICO vytvořeno s více rozměry:', outputPath);
})();
