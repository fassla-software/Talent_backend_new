import { validationResult, matchedData } from 'express-validator';
import HttpError from './HttpError';
import { Request, Response, NextFunction } from 'express';
export const handleValidationErrors = (req: Request, _res: Response, next: NextFunction) => {
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    const error = new HttpError('Validation failed', 422, errors.array());
    error.setAsValidationError();
    return next(error);
  }

  next();
};

export const strict = (req: Request, _res: Response, next: NextFunction) => {
  req.body = matchedData(req, {
    locations: ['body'],
    onlyValidData: true,
    // includeOptionals: true,
  });

  req.params = matchedData(req, {
    locations: ['params'],
    onlyValidData: true,
    // includeOptionals: true,
  });

  req.query = matchedData(req, {
    locations: ['query'],
    onlyValidData: true,
    // includeOptionals: true,
  });
  next();
};
