import { NextFunction, Request, Response } from 'express';
import HttpError from '../utils/HttpError';
// eslint-disable-next-line @typescript-eslint/no-unused-vars
export const errorHandler = (err: HttpError, req: Request, res: Response, next: NextFunction) => {
  console.error(err);
  const statusCode = err.statusCode || 500;
  const message = err.message || 'Internal Server Error';
  res.status(statusCode).json({
    status: 'error',
    statusCode,
    message,
    error: err.isValidationError ? err.error : undefined,
  });
};
