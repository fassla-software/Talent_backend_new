// user controllers
import { Request, Response } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import { getallConfigService, getConfigService, setConfigService } from './config.service';

export const getConfigsHandler = asyncHandler(async (req: Request, res: Response) => {
  const response = await getallConfigService();
  res.status(201).json(response);
}, 'Failed to get config');

export const getConfigHandler = asyncHandler(async (req: Request, res: Response) => {
  const key = req.body.key;
  const response = await getConfigService(key);
  res.status(201).json(response);
}, 'Failed to get config');

export const updateConfigHandler = asyncHandler(async (req: Request, res: Response) => {
  const messages = await setConfigService(req.body);
  res.status(200).json({ messages });
}, 'Failed to add config');
