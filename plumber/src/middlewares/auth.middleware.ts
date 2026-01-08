import { Response, NextFunction, RequestHandler } from 'express';
import jwt, { TokenExpiredError } from 'jsonwebtoken';
import { AuthenticatedRequest } from '../@types/express';
import HttpError from '../utils/HttpError';
import User from '../modules/user/user.model';

export const authenticate = async (req: AuthenticatedRequest, res: Response, next: NextFunction) => {
  const authHeader = req.headers.authorization;
  if (!authHeader) return next(new HttpError('Authorization header missing', 401));

  const token = authHeader.split(' ')[1];

  try {
    const payload = jwt.verify(token, process.env.KEY!) as { id: string; role: string };

    const user = await User.findByPk(payload.id);
    if (!user) return next(new HttpError('User not found', 404));

    if (user.last_login_token !== null && user.last_login_token !== token) {
      return next(new HttpError('Session expired due to login from another device', 403));
    }

    req.user = { id: payload.id, role: payload.role };
    next();
  } catch (error) {
    if (error instanceof TokenExpiredError) {
      return next(new HttpError('Token has expired', 401));
    }
    return next(new HttpError('Invalid or expired token', 403));
  }
};



export const authorize = (...roles: string[]) => {
  return (req: AuthenticatedRequest, res: Response, next: NextFunction) => {
    if (roles.includes(req.user!.role)) {
      return next();
    }
    return next(new HttpError('Forbidden: You do not have permission', 403));
  };
};

export const verifyShortLiveToken: RequestHandler = (req, res, next) => {
  const authHeader = (req as AuthenticatedRequest).headers.authorization;
  if (!authHeader) {
    return next(new HttpError('Authorization header missing', 401));
  }
  const token = authHeader.split(' ')[1];
  try {
    const payload = jwt.verify(token, process.env.KEY!) as { id: string; role: string };
    (req as AuthenticatedRequest).user = { id: payload.id, role: payload.role };
    next();
  } catch (error: unknown | TokenExpiredError) {
    if (error instanceof TokenExpiredError) {
      return next(new HttpError('Token has expired', 401));
    }
    return next(new HttpError('Invalid token', 403));
  }
};



