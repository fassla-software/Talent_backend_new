// user controllers
import { Request, Response } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';

import { getUsers } from './user.service';

export const getUsersHandler = asyncHandler(async (req: Request, res: Response) => {
  const response = await getUsers();
  res.status(200).json(response);
}, 'Failed to create admin user');
