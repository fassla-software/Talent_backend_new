import express from 'express';
import { authenticate } from '../../../middlewares/auth.middleware';
import { createReceivedGiftsHandler, getAllReceivedGiftsHandler } from './received_gift.controller';

const router = express.Router();

router.get('/', getAllReceivedGiftsHandler);
router.post('/', authenticate, createReceivedGiftsHandler);

export default router;
