import { Request, Response } from 'express';
import path from 'path';
import { asyncHandler } from '../../utils/asyncHandler';
import { AuthenticatedRequest } from '../../@types/express';
import { getConfig } from 'dotenv-handler';
import {
  checkIn,
  checkOut,
  submitVisitReport,
  getVisitStatus,
  getEnvoyVisits,
  getAdminVisits,
  getAdminVisitDetails,
  updateVisitStatus,
  ICheckInData,
  ICheckOutData,
  ISubmitVisitReportData,
} from './inspection-visit.service';
import { optimizeImage, deleteImage } from '../upload/upload.utils';

export const checkInHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const inspectorId = parseInt(req.user!.id);
  const data: ICheckInData = req.body;
  const visit = await checkIn(inspectorId, data);
  res.status(200).json({
    message: 'Check-in successful',
    check_in: {
      id: visit.id,
      check_in_at: visit.check_in_at,
      latitude: visit.check_in_latitude,
      longitude: visit.check_in_longitude,
    },
  });
}, 'Failed to check in');

export const submitVisitReportHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const inspectorId = parseInt(req.user!.id);
  const files = req.files as Express.Multer.File[];
  const data: ISubmitVisitReportData = req.body;

  // Process uploaded images
  let imageUrls: string[] = [];
  if (files && files.length > 0) {
    const optimizedPath = path.join(__dirname, '../../../uploads');
    const BASE_URL = getConfig('BASE_URL');

    imageUrls = await Promise.all(
      files.map(async file => {
        try {
          const fileName = `${file.filename.split('.').shift()}.webp`;
          const optimizedFullPath = path.join(optimizedPath, `optimized-${fileName}`);
          
          // Optimize and convert to webp
          await optimizeImage(file.path, optimizedFullPath);
          
          // Delete original file
          await deleteImage(file.path);
          
          // Return full URL
          return `${BASE_URL}/uploads/optimized-${fileName}`;
        } catch (error) {
          console.error('Error processing image:', error);
          // Fallback to original file if optimization fails
          return `${BASE_URL}/uploads/${file.filename}`;
        }
      }),
    );
  }

  // Add processed image URLs to data
  const dataWithImages: ISubmitVisitReportData = {
    ...data,
    images: imageUrls.length > 0 ? imageUrls : undefined,
  };

  const result = await submitVisitReport(inspectorId, dataWithImages);
  res.status(200).json({
    message: 'Visit report submitted successfully',
    ...result,
  });
}, 'Failed to submit visit report');

export const checkOutHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const inspectorId = parseInt(req.user!.id);
  const data: ICheckOutData = req.body;
  const visit = await checkOut(inspectorId, data);
  res.status(200).json({
    message: 'Check-out successful',
    check_out: {
      id: visit.id,
      check_out_at: visit.check_out_at,
      latitude: visit.check_out_latitude,
      longitude: visit.check_out_longitude,
    },
  });
}, 'Failed to check out');

export const getVisitStatusHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const inspectorId = parseInt(req.user!.id);
  const traderId = parseInt(req.params.id);
  const status = await getVisitStatus(inspectorId, traderId);
  res.status(200).json({
    message: 'Visit status retrieved',
    ...status,
  });
}, 'Failed to get visit status');

export const getEnvoyVisitsHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const inspectorId = parseInt(req.user!.id);
  const visits = await getEnvoyVisits(inspectorId);
  res.status(200).json({
    message: 'Envoy visits retrieved successfully',
    data: visits,
  });
}, 'Failed to get envoy visits');

export const getAdminVisitsHandler = asyncHandler(async (req: Request, res: Response) => {
  const page = parseInt(req.query.page as string) || 1;
  const limit = parseInt(req.query.limit as string) || 20;
  const result = await getAdminVisits(page, limit);
  res.status(200).json({
    message: 'Admin visits retrieved successfully',
    ...result,
  });
}, 'Failed to get admin visits');

export const getAdminVisitDetailsHandler = asyncHandler(async (req: Request, res: Response) => {
  const visitId = parseInt(req.params.id);
  const visit = await getAdminVisitDetails(visitId);
  res.status(200).json({
    message: 'Visit details retrieved successfully',
    data: visit,
  });
}, 'Failed to get visit details');

export const updateVisitStatusHandler = asyncHandler(async (req: Request, res: Response) => {
  const visitId = parseInt(req.params.id);
  const { status } = req.body;
  const visit = await updateVisitStatus(visitId, status);
  res.status(200).json({
    message: 'Visit status updated successfully',
    data: { id: visit.id, status: visit.status },
  });
}, 'Failed to update visit status');

