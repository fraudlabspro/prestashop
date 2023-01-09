const fs = require('fs');
const archiver = require('archiver');
const path = require('path');

const main = async () => {
  const output = fs.createWriteStream(path.join(__dirname, '../fraudlabspro.zip'));
  const archive = archiver('zip', {
    zlib: { level: 9 } // Sets the compression level.
  });
  output.on('end', () => {
    console.log('Module fraudlabspro has been zipped');
  });
  archive.on('error', function(err) {
    console.error('Error', err);
    throw err;
  });

  archive.append(fs.createReadStream(path.join(__dirname, '../config.xml')), { name: 'fraudlabspro/config.xml' });
  archive.append(fs.createReadStream(path.join(__dirname, '../fraudlabspro.php')), { name: 'fraudlabspro/fraudlabspro.php' });
  archive.directory(path.join(__dirname, '../config'), 'fraudlabspro/config');
  archive.directory(path.join(__dirname, '../vendor'), 'fraudlabspro/vendor');
  archive.directory(path.join(__dirname, '../views'), 'fraudlabspro/views');


  archive.pipe(output);
  await archive.finalize();
}
main().then(() => {
  console.log('Module has been created');
  process.exit(0);
}).catch(error => {
  console.error(error);
  process.exit(1);
})
