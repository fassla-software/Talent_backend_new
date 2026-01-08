// user controllers
import { Request, Response } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import { createContact, listContacts } from './contactus.service';

export const sendContactUsHandler = asyncHandler(async (req: Request, res: Response) => {
  const body = req.body;
  const response = await createContact(body);
  res.status(201).json(response);
}, 'Failed to add contact us');

export const getContactUsHandler = asyncHandler(async (req: Request, res: Response) => {
  const messages = await listContacts();
  res.status(200).json({ messages });
}, 'Failed to get contact us');
