const fs = require('fs');
const path = require('path');

const buildDir = path.join(__dirname, 'build');
if (!fs.existsSync(buildDir)) {
  fs.mkdirSync(buildDir, { recursive: true });
}

const imagesIconPath = path.join(__dirname, 'src', 'images', 'favicon.ico');
const buildIconPath = path.join(__dirname, 'build', 'favicon.ico');

if (fs.existsSync(imagesIconPath)) {
  fs.copyFileSync(imagesIconPath, buildIconPath);
  console.log('✓ ICO zkopírováno z src/images/favicon.ico do build/favicon.ico');
} else {
  console.error('✗ Chybí favicon.ico v src/images/');
  console.error('  Umístěte favicon.ico do složky src/images/');
  process.exit(1);
}
