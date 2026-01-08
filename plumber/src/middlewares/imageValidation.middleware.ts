import { Request, Response, NextFunction } from 'express';
import HttpError from '../utils/HttpError';

export const validateImages = (req: Request, res: Response, next: NextFunction) => {
  const files = req.files as Express.Multer.File[];

  if (files && files.length > 0) {
    // Check file count
    if (files.length > 10) {
      return res.status(400).json({
        message: 'You can not upload more than 10 images'
      });
    }

    // Validate each file
    for (const file of files) {
      // Check file type
      if (!file.mimetype.startsWith('image/')) {
        return res.status(400).json({
          message: 'All files must be images'
        });
      }

      // Check file size (5MB limit)
      // if (file.size > 5 * 1024 * 1024) {
      //   return res.status(400).json({
      //     message: 'Image size must be less than 5MB'
      //   });
      // }

      // Check allowed formats
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
      if (!allowedTypes.includes(file.mimetype)) {
        return res.status(400).json({
          message: 'Images must be PNG, JPEG, JPG, or WebP format'
        });
      }
    }
  }

  next();
};