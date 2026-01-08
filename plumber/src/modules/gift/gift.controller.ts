import { Request, Response } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import { getGifts, createGift, deleteGift, updateGift, getallGifts } from './gift.service';
import { AuthenticatedRequest } from '../../@types/express';

export const getGiftsHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const gifts = await getallGifts();
  res.status(200).json({ gifts });
}, 'Failed to get gifts');

export const getAllGiftsHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const id = req.user!.id;
  const gifts = await getGifts(id);
  res.status(200).json({ gifts });
}, 'Failed to get gifts');

export const addGiftHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const data = req.body;
  const gift = await createGift(data);
  res.status(200).json({ gift });
}, 'Failed to add gift');

export const updateGiftHandler = asyncHandler(async (req: Request, res: Response) => {
  const id = req.params.id;
  const data = req.body;
  console.log({ id, data });
  const gift = await updateGift(id, data);
  res.status(200).json({ gift });
}, 'Failed to update gift');

export const deleteGiftHandler = asyncHandler(async (req: Request, res: Response) => {
  const id = req.params.id;
  await deleteGift(id);
  res.status(200).json({ message: `Gift successfully deleted` });
}, 'Failed to delete gift');
