import express from 'express';
import multer from 'multer';
import path from 'path';
import {
  checkInHandler,
  checkOutHandler,
  submitVisitReportHandler,
  getVisitStatusHandler,
  getEnvoyVisitsHandler,
  getAdminVisitsHandler,
  getAdminVisitDetailsHandler,
  updateVisitStatusHandler,
} from './inspection-visit.controller';
import { authenticate, authorize } from '../../middlewares/auth.middleware';
import { validateImages } from '../../middlewares/imageValidation.middleware';
import {
  checkInValidation,
  checkOutValidation,
  submitVisitReportValidation,
  getVisitStatusValidation,
} from './inspection-visit.validation';
import { Roles } from '../role/role.model';

// Configure multer for image uploads with disk storage
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, 'uploads/');
  },
  filename: (req, file, cb) => {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1e9);
    cb(null, `images-${uniqueSuffix}${path.extname(file.originalname)}`);
  },
});

const upload = multer({
  storage,
  limits: {
    fileSize: 5 * 1024 * 1024, // 5MB limit
    files: 10, // Maximum 10 files
  },
  fileFilter: (req, file, cb) => {
    const filetypes = /jpeg|jpg|png|webp/;
    const mimetype = filetypes.test(file.mimetype);
    const extname = filetypes.test(path.extname(file.originalname).toLowerCase());

    if (mimetype && extname) {
      return cb(null, true);
    }
    cb(new Error('Only images are allowed'));
  },
});

const router = express.Router();

// Inspection Visit Routes (Check-in/Check-out)
router.post(
  '/check-in',
  authenticate,
  authorize(Roles.Envoy),
  checkInValidation,
  checkInHandler,
);
router.post(
  '/report',
  authenticate,
  authorize(Roles.Envoy),
  upload.array('images', 10),
  validateImages,
  submitVisitReportValidation,
  submitVisitReportHandler,
);
router.post(
  '/check-out',
  authenticate,
  authorize(Roles.Envoy),
  checkOutValidation,
  checkOutHandler,
);
router.get(
  '/status/:id',
  authenticate,
  authorize(Roles.Envoy),
  getVisitStatusValidation,
  getVisitStatusHandler,
);

router.get(
  '/envoy',
  authenticate,
  authorize(Roles.Envoy),
  getEnvoyVisitsHandler,
);

// Admin routes
router.get(
  '/admin',
  getAdminVisitsHandler,
);

router.get(
  '/admin/:id',
  getAdminVisitDetailsHandler,
);

router.put(
  '/admin/:id/status',
  updateVisitStatusHandler,
);

export default router;

