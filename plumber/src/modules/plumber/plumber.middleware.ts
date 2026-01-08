import { NextFunction, Request, Response } from 'express';
import User from '../user/user.model';

export const checkPhoneUnique = async (req: Request, res: Response, next: NextFunction) => {
  const { phone } = req.body;
  const exist = await User.findOne({ where: { phone } });
  if (exist) {
    return res.status(400).json({ message: 'Phone number already exists' });
  }
  next();
};


export const checkReferralCode = async (req: Request, res: Response, next: NextFunction) => {
  const { referralCode } = req.body;

  if (!referralCode) {
    const referrer = await User.findOne({ where: { refer_code: referralCode } });
    if (referrer) {
      return res.status(400).json({ message: 'Referral code is invalid' });
    }
  }

  next();
};