import express from 'express';
import upload from '../../middlewares/upload.middleware';
import { uploadImages } from './upload.controller';
import { authenticate } from '../../middlewares/auth.middleware';

const router = express.Router();

router.post('/', upload.array('images'), uploadImages);

export default router;
