import { Request, Response, NextFunction } from 'express';
import HttpError from './HttpError';

export const asyncHandler =
  // eslint-disable-next-line @typescript-eslint/ban-types
  (fn: Function, errorMessage: string) => (req: Request, res: Response, next: NextFunction) => {
    Promise.resolve(fn(req, res, next)).catch(err => {
      if (err instanceof HttpError) {
        next(err);
      } else {
        next(new HttpError(errorMessage, 500, err));
      }
    });
  };