import { Request, Response } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import { getCertificates } from './certificate.service';

export const getCertificatesHandler = asyncHandler(async (req: Request, res: Response) => {
  const filer = req.query;
  const certificates = await getCertificates(filer);
  res.status(200).json({ certificates });
}, 'Failed to get certificates');
