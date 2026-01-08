import express from 'express';
import {
  addSegmentHandler,
  deleteSegmentHandler,
  getAllSegmentsHandler,
  updateSegmentHandler,
  getSegmentsHandler,
} from './segment.controller';
import { authenticate } from '../../middlewares/auth.middleware';
import { addSegmentValidator, paramsValidator, updateSegmentValidator } from './segment.validation';
const router = express.Router();

router.get('/all', getSegmentsHandler);
router.get('/',authenticate, authenticate, getAllSegmentsHandler);
router.post('/', addSegmentValidator, addSegmentHandler);
router.put('/:id', updateSegmentValidator, updateSegmentHandler);
router.delete('/:id', paramsValidator, deleteSegmentHandler);

export default router;
