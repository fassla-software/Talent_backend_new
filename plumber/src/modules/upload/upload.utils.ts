import sharp from 'sharp';
import fs from 'fs/promises';

export const optimizeImage = async (filePath: string, outputFilePath: string) => {
  return sharp(filePath, { failOn: 'none' })
    .rotate()
    .resize({ width: 800 })
    .toFormat('webp')
    .toFile(outputFilePath)
    .then(() => {
      console.log(`Image processing completed: ${filePath}`);
      return outputFilePath;
    })
    .catch(err => {
      console.error(`Error processing image: ${filePath}`, err);
      throw err; // re-throw the error so it can be handled upstream
    });
};

export const deleteImage = async (filePath: string) => {
  try {
    // await fs.access(filePath, fs.constants.F_OK | fs.constants.W_OK);
    await fs.unlink(filePath);
    console.log(`File deleted: ${filePath}`);
  } catch (error) {
    console.error(`Error deleting file: ${filePath}`, error);
  }
};

export const deleteImages = async (filePaths: string[]) => {
  try {
    await Promise.all(
      filePaths.map(async filePath => {
        await fs.unlink(filePath);
        console.log(`File deleted: ${filePath}`);
      }),
    );
  } catch (error) {
    console.error('Error deleting files:', error);
  }
};

export const deleteImagesNotInDB = async (files: string[], imagesInDB: string[]) => {
  const imagesInDBName = imagesInDB.map(image => image.split('/').pop());
  const imagesToDelete = files.filter(file => !imagesInDBName.includes(file));
  await deleteImages(imagesToDelete);
};
