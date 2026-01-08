import path from 'path';
import { NextFunction, Response } from 'express';
import { AuthenticatedRequest } from '../../@types/express';
import HttpError from '../../utils/HttpError';
import { getConfig } from 'dotenv-handler';
import { deleteImage, optimizeImage } from './upload.utils';

export const uploadImages = async (req: AuthenticatedRequest, res: Response, next: NextFunction) => {
  if (!req.files || req.files.length === 0) {
    return next(new HttpError('Please upload a file', 400));
  }
  const files = req.files as Express.Multer.File[];

  try {
    // FIXME: This code is working, but deleteImage is not working
    const optimizedPath = path.join(__dirname, '../../../uploads');
    const images = await Promise.all(
      files.map(async file => {
        const fileName = `${file.filename.split('.').shift()}.webp`;
        const optimizedFullPath = path.join(optimizedPath, `optimized-${fileName}`);
        try {
          await optimizeImage(file.path, optimizedFullPath);
          deleteImage(file.path);
          console.log(getConfig('BASE_URL'));
          return `${getConfig('BASE_URL')}/uploads/optimized-${fileName}`;
        } catch (error) {
          console.error('Error processing image:', error);
          return file.filename;
        }
      }),
    );

    res.status(200).json({ images });
  } catch (error) {
    console.error('Error optimizing images:', error);
    next(new HttpError('Error processing images', 500));
  }
};
