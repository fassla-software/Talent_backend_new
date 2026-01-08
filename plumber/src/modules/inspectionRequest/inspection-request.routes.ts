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

router.delete('/bulk-delete', bulkDeleteInspectionRequestsHandler);

router.get('/:id', authenticate, paramsValidator, getInspectionRequestHandler);

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

//inspector
router.put('/assign', assignRequestVal, assignInspectionRequestHandler);
router.put('/check', authenticate, authorize(Roles.Envoy), checkRequestVal, checkInspectionRequestHandler);
router.put('/approve', approveRequestVal, approveInspectionRequestHandler);

export default router;
