import { Request, Response } from 'express';
import { asyncHandler } from '../../../utils/asyncHandler';
import { getAllReceivedGifts, createReceivedGift } from './received_gift.service';
import { AuthenticatedRequest } from '../../../@types/express';

export const getAllReceivedGiftsHandler = asyncHandler(async (req: Request, res: Response) => {
  const filter = req.query;
  const gifts = await getAllReceivedGifts(filter);
  res.status(200).json({ gifts }); // Now includes `status`
}, 'Failed to get received gifts');

export const createReceivedGiftsHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const userId = req.user!.id;
  const gift_id = req.body.gift_id;
  const gifts = await createReceivedGift({
    gift_id,
    user_id: Number(userId),
  });
  res.status(200).json({ gifts });
}, 'Failed to get gifts');
