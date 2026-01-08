import { Request, Response } from 'express';
import { asyncHandler } from '../../utils/asyncHandler';
import {
  addInspectionRequest,
  approveInspectionRequest,
  assignInspectionRequest,
  checkInspectionRequest,
  getInspectionRequest,
  getInspectionRequests,
  getUserRequests,
  bulkDeleteInspectionRequests,
} from './inspection_request.service';
import { IFilter } from './dto/filer.dto';
import { AuthenticatedRequest } from '../../@types/express';

export const getInspectionRequestsHandler = asyncHandler(async (req: Request, res: Response) => {
  const filter: IFilter = req.query;
  const requests = await getInspectionRequests(filter);
  res.status(200).json({ requests });
}, 'Failed to get requests');

export const getInspectorRequestsHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const id = req.user!.id;
  const role = req.user!.role;
  const filter = req.query;
  const requests = await getUserRequests(id, role, filter);
  res.status(200).json({ requests });
}, 'Failed to get requests');

export const getInspectionRequestHandler = asyncHandler(async (req: Request, res: Response) => {
  const id = req.params.id;
  const requests = await getInspectionRequest(id);
  res.status(200).json({ requests });
}, 'Failed to get requests');

export const addInspectionRequestHandler = asyncHandler(async (req: AuthenticatedRequest, res: Response) => {
  const id = req.user!.id;
  const role = req.user!.role;
  const data = req.body;
  const request = await addInspectionRequest(id, role, data);
  res.status(200).json({ request });
}, 'Failed to add request');

export const assignInspectionRequestHandler = asyncHandler(async (req: Request, res: Response) => {
  const data = req.body;
  const request = await assignInspectionRequest(data);
  res.status(200).json({ request });
}, 'Failed to add request');

export const checkInspectionRequestHandler = asyncHandler(async (req: Request, res: Response) => {
  const data = req.body;
  const request = await checkInspectionRequest(data);
  res.status(200).json({ request });
}, 'Failed to add request');

export const approveInspectionRequestHandler = asyncHandler(async (req: Request, res: Response) => {
  const data = req.body;
  const request = await approveInspectionRequest(data);
  res.status(200).json({ request });
}, 'Failed to add request');

export const bulkDeleteInspectionRequestsHandler = asyncHandler(async (req: Request, res: Response) => {
  const { ids } = req.body;
  const result = await bulkDeleteInspectionRequests(ids);
  res.status(200).json(result);
}, 'Failed to bulk delete requests');
