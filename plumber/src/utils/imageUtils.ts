import { getConfig } from 'dotenv-handler';

const BASE_URL = getConfig('BASE_URL');

export const saveImages = (images: string | string[]) => {
  if (typeof images === 'string') {
    return images.split('/').pop();
  } else if (Array.isArray(images)) {
    return images.map(img => {
      return img.split('/').pop();
    });
  }
  return null;
};

// Function to remover url from images
const processImageUrl = (img: string) => {
  return process.env.NODE_ENV === 'development' ? `${BASE_URL}/uploads/${img}` : `${BASE_URL}/uploads/${img}`;
};

export const viewImages = (images: string | string[]): string | string[] | null => {
  if (typeof images === 'string') {
    const newImageUrl = processImageUrl(images);
    return newImageUrl;
  } else if (Array.isArray(images)) {
    const newImagesUrls = images.map(img => processImageUrl(img));
    return newImagesUrls;
  }
  return null;
};
