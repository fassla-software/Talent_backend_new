import express from 'express';
import multer from 'multer';
import {
  addInspectionRequestHandler,
  getInspectionRequestsHandler,
  getInspectionRequestHandler,
  assignInspectionRequestHandler,
  checkInspectionRequestHandler,
  getInspectorRequestsHandler,
  approveInspectionRequestHandler,
  bulkDeleteInspectionRequestsHandler,
  pendingInspectionRequestHandler,
  getInspectorPendingRequestsHandler,
  getInspectorOverdueRequestsHandler,
} from './inspection_request.controller';
import { authenticate, authorize } from '../../middlewares/auth.middleware';
import { validateImages } from '../../middlewares/imageValidation.middleware';
import {
  addInspectionRequestVal,
  approveRequestVal,
  assignRequestVal,
  checkRequestVal,
  filterVal,
  paramsValidator,
  pendingRequestVal,
} from './inspect-request.validation';
import { Roles } from '../role/role.model';

// Configure multer for image uploads
const upload = multer({
  storage: multer.memoryStorage(),
  limits: {
    fileSize: 5 * 1024 * 1024, // 5MB limit
    files: 10, // Maximum 10 files
  },
});

const router = express.Router();

//  TODO: authorize(Roles.ADMIN);
router.get('/', filterVal, getInspectionRequestsHandler);
router.get('/my', authenticate, authorize(Roles.PLUMBER, Roles.Envoy), filterVal, getInspectorRequestsHandler);

//plumber
router.post(
  '/',
  authenticate,
  authorize(Roles.PLUMBER, Roles.Envoy),
  upload.array('images', 10),
  validateImages,
  addInspectionRequestVal,
  addInspectionRequestHandler,
);

//inspector - specific routes must come before parameterized routes
router.put('/assign', assignRequestVal, assignInspectionRequestHandler);
router.put('/check', authenticate, authorize(Roles.Envoy), checkRequestVal, checkInspectionRequestHandler);
router.put('/pending', authenticate, authorize(Roles.Envoy), pendingRequestVal, pendingInspectionRequestHandler);
router.get('/pending', authenticate, authorize(Roles.Envoy), filterVal, getInspectorPendingRequestsHandler);
router.get('/overdue', authenticate, authorize(Roles.Envoy), filterVal, getInspectorOverdueRequestsHandler);
router.put('/approve', approveRequestVal, approveInspectionRequestHandler);
router.delete('/bulk-delete', bulkDeleteInspectionRequestsHandler);

// Parameterized route must come after all specific routes
router.get('/:id', authenticate, paramsValidator, getInspectionRequestHandler);

export default router;
