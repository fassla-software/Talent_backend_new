import { Request, Response } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import { addSegment, calcUserPoints, deleteSegment, getSegments, updateSegment } from './segment.service';
import { AuthenticatedRequest } from '../../@types/express';

export const getSegmentsHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const segments = await getSegments();
  res.status(200).json({ segments });
}, 'Failed to get segment');

export const getAllSegmentsHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const id = req.user!.id;
  const segment = await getSegments();
  const pointsValue = await calcUserPoints(id);
  res.status(200).json({ ...pointsValue, segment });
}, 'Failed to get segment');

export const addSegmentHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const data = req.body;
  const segment = await addSegment(data);
  res.status(200).json({ segment });
}, 'Failed to add segment');

export const updateSegmentHandler = asyncHandler(async (req: Request, res: Response) => {
  const id = req.params.id;
  const data = req.body;
  console.log({ id, data });
  const gift = await updateSegment(id, data);
  res.status(200).json({ gift });
}, 'Failed to update gift');

export const deleteSegmentHandler = asyncHandler(async (req: Request, res: Response) => {
  const id = req.params.id;
  await deleteSegment(id);
  res.status(200).json({ message: `Segment successfully deleted` });
}, 'Failed to delete segment');
